<?php

namespace App\Services;

use App\Models\Student;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookService
{
    public function processWebhookPayload(array $payload): void
    {
        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];
                if (!empty($value['messages']) && is_array($value['messages'])) {
                    foreach ($value['messages'] as $msg) {
                        $this->storeInboundMessage($value, $msg);
                    }
                }
                if (!empty($value['statuses']) && is_array($value['statuses'])) {
                    foreach ($value['statuses'] as $status) {
                        $this->applyStatusUpdate($status);
                    }
                }
            }
        }
    }

    private function storeInboundMessage(array $value, array $msg): void
    {
        $from = $msg['from'] ?? null;
        if (!$from || !is_string($from)) {
            return;
        }

        $phone = preg_replace('/\D/', '', $from);
        if ($phone === '') {
            return;
        }

        $metaMessageId = $msg['id'] ?? null;
        if ($metaMessageId && WhatsAppMessage::where('meta_message_id', $metaMessageId)->exists()) {
            return;
        }

        $student = $this->findStudentByPhoneDigits($phone);
        $defaultTp = config('services.whatsapp.inbox_default_training_partner_id');

        $conversation = WhatsAppConversation::firstOrNew(['phone' => $phone]);
        if (!$conversation->exists) {
            $conversation->training_partner_id = $student?->training_partner_id ?? $defaultTp;
            $conversation->student_id = $student?->id;
            $conversation->unread_count = 0;
        } else {
            if ($student) {
                $conversation->student_id = $student->id;
                if ($student->training_partner_id) {
                    $conversation->training_partner_id = $student->training_partner_id;
                }
            } elseif (!$conversation->training_partner_id && $defaultTp) {
                $conversation->training_partner_id = $defaultTp;
            }
        }

        $type = $msg['type'] ?? 'unknown';
        [$body, $mediaUrl, $storedType] = $this->extractMessageContent($msg, $type);

        try {
            DB::transaction(function () use ($conversation, $metaMessageId, $storedType, $body, $mediaUrl, $msg) {
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
        }
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
