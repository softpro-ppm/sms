<?php

namespace App\Services;

use App\Models\Student;
use App\Models\TrainingPartner;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookService
{
    /**
     * @return int Number of inbound messages stored (0 if duplicate or skipped)
     */
    public function processWebhookPayload(array $payload): int
    {
        $stored = 0;
        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];
                if (!empty($value['messages']) && is_array($value['messages'])) {
                    foreach ($value['messages'] as $msg) {
                        if ($this->storeInboundMessage($value, $msg)) {
                            $stored++;
                        }
                    }
                }
                if (!empty($value['statuses']) && is_array($value['statuses'])) {
                    foreach ($value['statuses'] as $status) {
                        $this->applyStatusUpdate($status);
                    }
                }
            }
        }

        return $stored;
    }

    /**
     * @return bool True if a new inbound row was written
     */
    private function storeInboundMessage(array $value, array $msg): bool
    {
        $from = $msg['from'] ?? null;
        if (!$from || !is_string($from)) {
            return false;
        }

        $phone = preg_replace('/\D/', '', $from);
        if ($phone === '') {
            return false;
        }

        $metaMessageId = $msg['id'] ?? null;
        if ($metaMessageId && WhatsAppMessage::where('meta_message_id', $metaMessageId)->exists()) {
            return false;
        }

        $student = $this->findStudentByPhoneDigits($phone);
        $tpId = $this->resolveTrainingPartnerId($student);

        $conversation = WhatsAppConversation::firstOrNew(['phone' => $phone]);
        if (!$conversation->exists) {
            $conversation->training_partner_id = $tpId;
            $conversation->student_id = $student?->id;
            $conversation->unread_count = 0;
        } else {
            if ($student) {
                $conversation->student_id = $student->id;
                if ($student->training_partner_id) {
                    $conversation->training_partner_id = $student->training_partner_id;
                }
            } elseif (!$conversation->training_partner_id && $tpId) {
                $conversation->training_partner_id = $tpId;
            }
        }

        $type = $msg['type'] ?? 'unknown';
        [$body, $mediaUrl, $storedType] = $this->extractMessageContent($msg, $type);

        try {
            DB::transaction(function () use ($conversation, $metaMessageId, $storedType, $body, $mediaUrl, $msg, $value) {
                $conversation->last_message_at = now();
                $conversation->unread_count = (int) $conversation->unread_count + 1;
                $conversation->save();

                WhatsAppMessage::create([
                    'conversation_id' => $conversation->id,
                    'direction' => 'inbound',
                    'meta_message_id' => $metaMessageId,
                    'type' => $storedType,
                    'body' => $body,
                    'media_url' => $mediaUrl,
                    'status' => 'received',
                    'metadata' => ['raw' => $msg, 'value_metadata' => $value['metadata'] ?? null],
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook: failed to store inbound message', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return false;
        }

        Log::info('WhatsApp inbox: inbound message stored', [
            'conversation_id' => $conversation->id,
            'phone' => $phone,
            'training_partner_id' => $conversation->training_partner_id,
            'student_id' => $conversation->student_id,
        ]);

        return true;
    }

    private function resolveTrainingPartnerId(?Student $student): ?int
    {
        if ($student?->training_partner_id) {
            return (int) $student->training_partner_id;
        }

        $default = config('services.whatsapp.inbox_default_training_partner_id');
        if ($default !== null && $default !== '') {
            return (int) $default;
        }

        /** Single-centre installs: orphan chats still show in inbox without extra .env */
        if (TrainingPartner::query()->count() === 1) {
            $id = TrainingPartner::query()->orderBy('id')->value('id');

            return $id !== null ? (int) $id : null;
        }

        return null;
    }

    /**
     * @return array{0: ?string, 1: ?string, 2: string}
     */
    private function extractMessageContent(array $msg, string $type): array
    {
        $body = null;
        $mediaUrl = null;

        switch ($type) {
            case 'text':
                $body = $msg['text']['body'] ?? null;
                break;
            case 'image':
                $body = $msg['image']['caption'] ?? null;
                $mediaUrl = isset($msg['image']['id']) ? 'meta-media:' . $msg['image']['id'] : null;
                break;
            case 'video':
                $body = $msg['video']['caption'] ?? null;
                $mediaUrl = isset($msg['video']['id']) ? 'meta-media:' . $msg['video']['id'] : null;
                break;
            case 'audio':
                $mediaUrl = isset($msg['audio']['id']) ? 'meta-media:' . $msg['audio']['id'] : null;
                break;
            case 'document':
                $body = $msg['document']['caption'] ?? null;
                $mediaUrl = isset($msg['document']['id']) ? 'meta-media:' . $msg['document']['id'] : null;
                break;
            default:
                break;
        }

        return [$body, $mediaUrl, $type];
    }

    private function applyStatusUpdate(array $status): void
    {
        $id = $status['id'] ?? null;
        $rawStatus = $status['status'] ?? null;
        if (!$id || !$rawStatus) {
            return;
        }

        $map = [
            'sent' => 'sent',
            'delivered' => 'delivered',
            'read' => 'read',
            'failed' => 'failed',
        ];
        $normalized = $map[$rawStatus] ?? $rawStatus;

        WhatsAppMessage::where('meta_message_id', $id)->update(['status' => $normalized]);
    }

    private function findStudentByPhoneDigits(string $phoneDigits): ?Student
    {
        $ten = strlen($phoneDigits) >= 10 ? substr($phoneDigits, -10) : null;
        if (!$ten) {
            return null;
        }

        $driver = Student::query()->getConnection()->getDriverName();
        if ($driver === 'mysql') {
            $strip = "REGEXP_REPLACE(IFNULL(%s, ''), '[^0-9]', '')";

            return Student::query()
                ->whereRaw('RIGHT(' . sprintf($strip, 'whatsapp_number') . ', 10) = ?', [$ten])
                ->orWhereRaw('RIGHT(' . sprintf($strip, 'phone') . ', 10) = ?', [$ten])
                ->first();
        }

        return Student::query()
            ->where(function ($q) use ($ten) {
                $q->where('whatsapp_number', $ten)
                    ->orWhere('phone', $ten)
                    ->orWhere('whatsapp_number', '91' . $ten)
                    ->orWhere('phone', '91' . $ten);
            })
            ->first();
    }
}
