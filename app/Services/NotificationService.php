<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendNotification($user, $type, $title, $message, $data = [], $deliveryMethods = ['database'])
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'delivery_method' => implode(',', $deliveryMethods),
            'status' => 'pending'
        ]);

        // Send via different methods
        foreach ($deliveryMethods as $method) {
            try {
                switch ($method) {
                    case 'email':
                        $this->sendEmail($user, $title, $message, $data);
                        break;
                    case 'whatsapp':
                        $this->sendWhatsApp($user, $message, $data);
                        break;
                    case 'sms':
                        $this->sendSMS($user, $message, $data);
                        break;
                }
            } catch (\Exception $e) {
                Log::error("Failed to send {$method} notification: " . $e->getMessage());
                $notification->markAsFailed();
                return false;
            }
        }

        $notification->markAsSent();
        return true;
    }

    public function sendBulkNotification($userIds, $type, $title, $message, $data = [], $deliveryMethods = ['database'])
    {
        $successCount = 0;
        $failureCount = 0;

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                if ($this->sendNotification($user, $type, $title, $message, $data, $deliveryMethods)) {
                    $successCount++;
                } else {
                    $failureCount++;
                }
            }
        }

        return [
            'success' => $successCount,
            'failed' => $failureCount,
            'total' => count($userIds)
        ];
    }

    private function sendEmail($user, $title, $message, $data = [])
    {
        // Email notification logic
        Mail::send('emails.notification', [
            'user' => $user,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ], function ($mail) use ($user, $title) {
            $mail->to($user->email)
                 ->subject($title);
        });
    }

    private function sendWhatsApp($user, $message, $data = [])
    {
        // WhatsApp notification logic
        // This would integrate with WhatsApp Business API
        $phoneNumber = $user->phone ?? $user->whatsapp_number;
        
        if ($phoneNumber) {
            // Implement WhatsApp API call here
            Log::info("WhatsApp message sent to {$phoneNumber}: {$message}");
        }
    }

    private function sendSMS($user, $message, $data = [])
    {
        // SMS notification logic
        $phoneNumber = $user->phone;
        
        if ($phoneNumber) {
            // Implement SMS API call here
            Log::info("SMS sent to {$phoneNumber}: {$message}");
        }
    }

    // Specific notification methods
    public function sendPaymentConfirmation($user, $payment)
    {
        return $this->sendNotification(
            $user,
            'payment_confirmation',
            'Payment Confirmed',
            "Your payment of ₹{$payment->amount} has been confirmed successfully.",
            ['payment_id' => $payment->id],
            ['email', 'whatsapp']
        );
    }

    public function sendAssessmentResult($user, $result)
    {
        $status = $result->is_passed ? 'Passed' : 'Failed';
        return $this->sendNotification(
            $user,
            'assessment_result',
            'Assessment Result Available',
            "Your assessment result is now available. You have {$status} with {$result->percentage}% marks.",
            ['result_id' => $result->id],
            ['email', 'whatsapp']
        );
    }

    public function sendCertificateIssued($user, $certificate)
    {
        return $this->sendNotification(
            $user,
            'certificate_issued',
            'Certificate Issued',
            "Congratulations! Your certificate for {$certificate->course->name} has been issued.",
            ['certificate_id' => $certificate->id],
            ['email', 'whatsapp']
        );
    }

    public function sendCourseReminder($user, $course, $daysLeft)
    {
        return $this->sendNotification(
            $user,
            'course_reminder',
            'Course Reminder',
            "Reminder: Your {$course->name} course ends in {$daysLeft} days.",
            ['course_id' => $course->id],
            ['email', 'whatsapp']
        );
    }

    public function sendBatchStartNotification($user, $batch)
    {
        return $this->sendNotification(
            $user,
            'batch_start',
            'Batch Starting Soon',
            "Your batch {$batch->batch_name} for {$batch->course->name} starts on {$batch->start_date->format('M d, Y')}.",
            ['batch_id' => $batch->id],
            ['email', 'whatsapp']
        );
    }
}
