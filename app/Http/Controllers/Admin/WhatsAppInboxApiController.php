<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class WhatsAppInboxApiController extends Controller
{
    public function conversations(Request $request)
    {
        if (!Schema::hasTable('whatsapp_conversations')) {
            return response()->json(['data' => [], 'meta' => ['current_page' => 1, 'last_page' => 1]]);
        }

        $perPage = min(max((int) $request->get('per_page', 25), 1), 100);
        $q = trim((string) $request->get('q', ''));

        $query = $this->visibleConversationQuery()->with(['student:id,full_name,whatsapp_number,phone', 'lastMessage']);

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('phone', 'like', '%' . $q . '%')
                    ->orWhereHas('student', function ($s) use ($q) {
                        $s->where('full_name', 'like', '%' . $q . '%')
                            ->orWhere('whatsapp_number', 'like', '%' . $q . '%');
                    });
            });
        }

        $paginator = $query
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        $data = $paginator->getCollection()->map(function (WhatsAppConversation $c) {
            $last = $c->lastMessage;
            $name = $c->student?->full_name ?? $c->phone;

            return [
                'id' => $c->id,
                'phone' => $c->phone,
                'display_name' => $name,
                'student_id' => $c->student_id,
                'last_message_preview' => $last ? mb_substr(strip_tags((string) $last->body), 0, 80) : null,
                'last_message_at' => optional($c->last_message_at)?->toIso8601String(),
                'unread_count' => (int) $c->unread_count,
                'last_direction' => $last?->direction,
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function messages(Request $request, int $conversationId)
    {
        if (!Schema::hasTable('whatsapp_messages')) {
            return response()->json(['conversation' => null, 'messages' => []]);
        }

        $conversation = $this->visibleConversationQuery()->whereKey($conversationId)->with(['student.enrollments.batch.course'])->first();
        if (!$conversation) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $conversation->unread_count = 0;
        $conversation->save();

        $limit = min(max((int) $request->get('per_page', 150), 1), 250);
        $total = WhatsAppMessage::query()->where('conversation_id', $conversationId)->count();
        $messageCollection = WhatsAppMessage::query()
            ->where('conversation_id', $conversationId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->sortBy('created_at')
            ->values();

        $enrollmentSummary = '';
        if ($conversation->student && $conversation->student->enrollments->isNotEmpty()) {
            $enrollmentSummary = $conversation->student->enrollments
                ->map(fn ($e) => $e->batch?->course?->name ?? 'Course')
                ->unique()
                ->implode(', ');
        }

        $messageRows = $messageCollection->map(fn (WhatsAppMessage $m) => [
            'id' => $m->id,
            'direction' => $m->direction,
            'type' => $m->type,
            'body' => $m->body,
            'media_url' => $m->media_url,
            'status' => $m->status,
            'created_at' => optional($m->created_at)?->toIso8601String(),
        ]);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'phone' => $conversation->phone,
                'display_name' => $conversation->student?->full_name ?? $conversation->phone,
                'student_id' => $conversation->student_id,
                'student_url' => $conversation->student_id
                    ? route('admin.students.show', $conversation->student_id)
                    : null,
                'enrollment_summary' => $enrollmentSummary,
            ],
            'messages' => $messageRows,
            'meta' => [
                'returned' => $messageRows->count(),
                'total' => $total,
                'truncated' => $total > $limit,
            ],
        ]);
    }

    public function reply(Request $request, int $conversationId, WhatsAppService $whatsapp)
    {
        $request->validate([
            'message' => 'required|string|max:4096',
        ]);

        if (!Schema::hasTable('whatsapp_messages')) {
            return response()->json(['error' => 'Inbox not migrated'], 503);
        }

        $conversation = $this->visibleConversationQuery()->whereKey($conversationId)->first();
        if (!$conversation) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $text = $request->input('message');
        $ten = strlen($conversation->phone) >= 10 ? substr($conversation->phone, -10) : $conversation->phone;
        $result = $whatsapp->sendMessage($ten, $text, 'text');

        if (!$result['success']) {
            return response()->json(['error' => $result['error'] ?? 'Send failed'], 422);
        }

        $msg = WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'meta_message_id' => $result['message_id'],
            'type' => 'text',
            'body' => $text,
            'status' => 'sent',
        ]);

        $conversation->last_message_at = now();
        $conversation->save();

        return response()->json([
            'message' => [
                'id' => $msg->id,
                'direction' => $msg->direction,
                'type' => $msg->type,
                'body' => $msg->body,
                'status' => $msg->status,
                'created_at' => optional($msg->created_at)?->toIso8601String(),
            ],
        ]);
    }

    public function linkStudent(Request $request, int $conversationId)
    {
        $request->validate([
            'student_id' => 'required|integer|exists:students,id',
        ]);

        $conversation = $this->visibleConversationQuery()->whereKey($conversationId)->first();
        if (!$conversation) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $student = Student::query()->whereKey($request->integer('student_id'))->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $user = $request->user();
        if (!$user->is_super_admin && (int) $student->training_partner_id !== (int) $user->training_partner_id) {
            return response()->json(['error' => 'Student does not belong to your centre'], 403);
        }

        $conversation->student_id = $student->id;
        $conversation->training_partner_id = $student->training_partner_id;
        $conversation->save();

        return response()->json([
            'student_id' => $student->id,
            'display_name' => $student->full_name,
        ]);
    }

    private function visibleConversationQuery()
    {
        $user = auth()->user();
        if ($user->is_super_admin) {
            return WhatsAppConversation::query();
        }

        $tpId = $user->training_partner_id;
        if (!$tpId) {
            return WhatsAppConversation::query()->whereRaw('1 = 0');
        }

        return WhatsAppConversation::forTrainingPartner((int) $tpId);
    }
}
