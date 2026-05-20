<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use App\Models\StudentPushSubscription;
use App\Services\StudentPushNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentPwaController extends Controller
{
    public function __construct(
        private StudentPushNotificationService $pushNotifications
    ) {}

    public function config(): JsonResponse
    {
        $student = Auth::user()?->student;

        return response()->json([
            'pushEnabled' => $this->pushNotifications->isConfigured(),
            'publicKey' => $this->pushNotifications->publicKey(),
            'subscribed' => $student?->pushSubscriptions()->enabled()->exists() ?? false,
        ]);
    }

    public function preferences()
    {
        $user = Auth::user();
        $preferences = NotificationPreference::getUserPreferences($user->id);
        $notificationTypes = $this->notificationTypes();

        return view('student.notifications', compact('preferences', 'notificationTypes'));
    }

    public function updatePreferences(Request $request)
    {
        $user = Auth::user();

        $types = array_keys($this->notificationTypes());

        foreach ($types as $type) {
            $existing = NotificationPreference::query()
                ->where('user_id', $user->id)
                ->where('type', $type)
                ->first();

            $defaults = NotificationPreference::getDefaultPreferences()[$type] ?? [
                'email_enabled' => true,
                'whatsapp_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => true,
            ];

            NotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => $type,
                ],
                [
                    'email_enabled' => $existing?->email_enabled ?? $defaults['email_enabled'],
                    'whatsapp_enabled' => $existing?->whatsapp_enabled ?? $defaults['whatsapp_enabled'],
                    'sms_enabled' => $existing?->sms_enabled ?? $defaults['sms_enabled'],
                    'push_enabled' => $request->boolean('push_enabled.'.$type),
                ]
            );
        }

        return redirect()
            ->route('student.notifications')
            ->with('success', 'Notification preferences updated.');
    }

    public function storeSubscription(Request $request): JsonResponse
    {
        $student = Auth::user()?->student;
        abort_if(! $student, 403);

        $payload = $request->validate([
            'endpoint' => ['required', 'url'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
            'contentEncoding' => ['nullable', 'string', 'max:50'],
        ]);

        $endpointHash = hash('sha256', $payload['endpoint']);

        StudentPushSubscription::updateOrCreate(
            ['endpoint_hash' => $endpointHash],
            [
                'student_id' => $student->id,
                'endpoint' => $payload['endpoint'],
                'public_key' => $payload['keys']['p256dh'],
                'auth_token' => $payload['keys']['auth'],
                'content_encoding' => $payload['contentEncoding'] ?? 'aes128gcm',
                'user_agent' => (string) $request->userAgent(),
                'last_seen_at' => now(),
                'enabled' => true,
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function destroySubscription(Request $request): JsonResponse
    {
        $student = Auth::user()?->student;
        abort_if(! $student, 403);

        $payload = $request->validate([
            'endpoint' => ['required', 'url'],
        ]);

        $student->pushSubscriptions()
            ->where('endpoint', $payload['endpoint'])
            ->delete();

        return response()->json(['ok' => true]);
    }

    private function notificationTypes(): array
    {
        return [
            'payment_confirmation' => [
                'title' => 'Payment updates',
                'description' => 'Approved payments and fully-paid confirmations.',
            ],
            'payment_due' => [
                'title' => 'Fee reminders',
                'description' => 'Outstanding balance reminders for active enrollments.',
            ],
            'assessment_result' => [
                'title' => 'Exam alerts',
                'description' => 'Exam-ready reminders and assessment result updates.',
            ],
            'certificate_issued' => [
                'title' => 'Certificate notifications',
                'description' => 'Issued certificate alerts when your document is ready.',
            ],
            'batch_start' => [
                'title' => 'Enrollment updates',
                'description' => 'Enrollment confirmations and course-start related notices.',
            ],
        ];
    }
}
