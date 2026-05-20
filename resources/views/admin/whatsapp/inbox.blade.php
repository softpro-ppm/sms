@extends('layouts.admin')

@section('title', 'WhatsApp Inbox')
@section('page-title', 'WhatsApp Inbox')

@section('content')
<div class="space-y-5 px-6 pb-8" x-data="whatsappInbox()" x-init="init()">
    @if(!empty($tableMissing))
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Run <code class="bg-amber-100 px-1 rounded">php artisan migrate</code> to create inbox tables (<code class="bg-amber-100 px-1 rounded">whatsapp_conversations</code>, <code class="bg-amber-100 px-1 rounded">whatsapp_messages</code>).
        </div>
    @else
    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fab fa-whatsapp text-[10px]"></i>
                    WhatsApp inbox
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Review conversations and send replies within the active window.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Free-text replies are available only during the customer-care window from the contact’s last message. After that, use an approved Meta template.</p>
            </div>
            <div class="flex gap-2">
                <input type="search"
                       x-model="searchQ"
                       @input.debounce.400ms="loadConversations()"
                       placeholder="Search name or phone…"
                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100 sm:w-64">
            </div>
        </div>
        <div class="border-t border-slate-200 px-6 py-3 text-xs text-slate-500">
            Webhook: <code class="rounded bg-slate-100 px-1.5 py-0.5">GET/POST /webhook/whatsapp</code>
        </div>
    </section>

    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
    <div class="flex rounded-[28px] overflow-hidden min-h-[calc(100vh-14rem)]">
        {{-- Conversation list --}}
        <div class="w-full border-r border-gray-200 flex flex-col md:w-80 lg:w-96 shrink-0"
             :class="{ 'hidden md:flex': selectedId, 'flex': true }">
            <div class="border-b border-gray-200 bg-gray-50 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Conversations</div>
            <div class="flex-1 overflow-y-auto">
                <template x-if="loadingList && conversations.length === 0">
                    <div class="p-6 text-center text-gray-500 text-sm">Loading…</div>
                </template>
                <template x-if="!loadingList && conversations.length === 0">
                    <div class="p-6 text-center text-gray-500 text-sm">No conversations yet. When students message your WhatsApp number, threads appear here.</div>
                </template>
                <template x-for="c in conversations" :key="c.id">
                    <button type="button"
                            @click="openConversation(c.id)"
                            class="w-full text-left px-3 py-3 border-b border-gray-100 hover:bg-primary-50/60 transition flex flex-col gap-0.5"
                            :class="{ 'bg-primary-50 border-l-4 border-l-primary-500': selectedId === c.id }">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-semibold text-gray-900 truncate" x-text="c.display_name"></span>
                            <span x-show="c.unread_count > 0" class="shrink-0 bg-primary-600 text-white text-xs px-2 py-0.5 rounded-full" x-text="c.unread_count"></span>
                        </div>
                        <div class="text-xs text-gray-500 font-mono" x-text="c.phone"></div>
                        <div class="text-xs text-gray-600 truncate mt-0.5" x-text="c.last_message_preview || '—'"></div>
                    </button>
                </template>
            </div>
        </div>

        {{-- Chat panel --}}
        <div class="flex-1 flex flex-col min-w-0"
             :class="{ 'hidden md:flex': !selectedId, 'flex': true }">
            <template x-if="!selectedId">
                <div class="flex-1 flex items-center justify-center text-gray-500 text-sm p-8">
                    Select a conversation
                </div>
            </template>
            <template x-if="selectedId">
                <div class="flex flex-col h-full min-h-[420px]">
                    <div class="shrink-0 border-b border-gray-200 bg-gray-50 px-4 py-3 flex flex-col gap-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <button type="button" class="md:hidden text-primary-600 text-sm font-medium mb-1" @click="selectedId = null">← Back</button>
                                <h2 class="text-lg font-bold text-gray-900 truncate" x-text="chatMeta.display_name || '—'"></h2>
                                <p class="text-xs font-mono text-gray-500" x-text="chatMeta.phone"></p>
                                <p class="text-xs text-gray-600 mt-1" x-show="chatMeta.enrollment_summary" x-text="chatMeta.enrollment_summary"></p>
                            </div>
                            <div class="flex flex-col items-end gap-2 shrink-0">
                                <a :href="chatMeta.student_url" class="text-sm text-primary-600 hover:underline" x-show="chatMeta.student_url">View student</a>
                                <div class="flex items-center gap-1" x-show="!chatMeta.student_url">
                                    <input type="number"
                                           x-model.number="linkStudentId"
                                           placeholder="Student ID"
                                           class="w-24 rounded border border-gray-300 px-2 py-1 text-xs">
                                    <button type="button" @click="linkStudent" class="text-xs bg-gray-800 text-white px-2 py-1 rounded hover:bg-gray-900">Link</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 space-y-3 overflow-y-auto bg-slate-50 p-4" id="chat-scroll">
                        <template x-for="m in messages" :key="m.id">
                            <div class="flex" :class="m.direction === 'outbound' ? 'justify-end' : 'justify-start'">
                                <div class="max-w-[85%] rounded-2xl px-3 py-2 text-sm shadow-sm"
                                     :class="m.direction === 'outbound' ? 'bg-primary-600 text-white rounded-br-md' : 'bg-white text-gray-900 border border-gray-200 rounded-bl-md'">
                                    <template x-if="m.type !== 'text' && m.media_url">
                                        <p class="text-xs opacity-80 mb-1" x-text="'[' + m.type + '] Media (see Meta dashboard for download)'"></p>
                                    </template>
                                    <div class="whitespace-pre-wrap break-words" x-text="m.body || ''"></div>
                                    <div class="text-[10px] mt-1 opacity-75 flex items-center gap-1 justify-end"
                                         :class="m.direction === 'outbound' ? 'text-primary-100' : 'text-gray-500'">
                                        <span x-text="formatTime(m.created_at)"></span>
                                        <template x-if="m.direction === 'outbound'">
                                            <span x-text="statusIcon(m.status)"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="shrink-0 border-t border-gray-200 bg-white p-3">
                        <div x-show="chatMeta.can_reply_freeform === false"
                             class="rounded-lg bg-amber-50 border border-amber-200 text-amber-950 text-xs px-3 py-2 mb-2"
                             x-cloak>
                            <strong>Reply window closed.</strong> The customer&rsquo;s last message was more than <span x-text="chatMeta.freeform_reply_hours || 24"></span> hours ago. This form is disabled so we don&rsquo;t call Meta with free text that will fail. Use an approved WhatsApp template from Meta Business for outreach outside this window (billing is per Meta&rsquo;s rules).
                        </div>
                        <p class="text-red-600 text-xs mb-2" x-show="sendError" x-text="sendError"></p>
                        <div class="flex gap-2">
                            <textarea x-model="draft"
                                      rows="2"
                                      :placeholder="chatMeta.can_reply_freeform === false ? 'Window closed — use a Meta template' : 'Type a reply…'"
                                      :disabled="chatMeta.can_reply_freeform !== true"
                                      :class="chatMeta.can_reply_freeform !== true ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : ''"
                                      class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 resize-none"></textarea>
                            <button type="button"
                                    @click="sendReply"
                                    :disabled="sending || !draft.trim() || chatMeta.can_reply_freeform !== true"
                                    class="self-end bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 disabled:opacity-50">
                                Send
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
    </section>
    @endif
</div>

@section('scripts')
<script>
function whatsappInbox() {
    return {
        conversations: [],
        selectedId: null,
        messages: [],
        chatMeta: {},
        draft: '',
        searchQ: '',
        loadingList: false,
        sending: false,
        sendError: '',
        linkStudentId: '',
        pollTimer: null,
        csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',

        init() {
            if (@json(empty($tableMissing))) {
                this.loadConversations();
            }
        },

        async loadConversations() {
            this.loadingList = true;
            try {
                const q = new URLSearchParams({ q: this.searchQ, per_page: 40 });
                const res = await fetch(@json(route('admin.api.whatsapp.conversations')) + '?' + q.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                const data = await res.json();
                this.conversations = data.data || [];
            } catch (e) {
                console.error(e);
            } finally {
                this.loadingList = false;
            }
        },

        async openConversation(id) {
            this.selectedId = id;
            this.sendError = '';
            this.draft = '';
            this.linkStudentId = '';
            await this.loadMessages();
            this.startPoll();
        },

        startPoll() {
            if (this.pollTimer) clearInterval(this.pollTimer);
            this.pollTimer = setInterval(() => {
                if (this.selectedId) this.loadMessages(true);
            }, 12000);
        },

        async loadMessages(silent) {
            if (!this.selectedId) return;
            try {
                const base = @json(url('/admin/api/whatsapp/conversations'));
                const res = await fetch(`${base}/${this.selectedId}/messages?per_page=150`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                const data = await res.json();
                if (data.error) return;
                this.chatMeta = data.conversation || {};
                this.messages = data.messages || [];
                if (!silent) await this.loadConversations();
                this.$nextTick(() => {
                    const el = document.getElementById('chat-scroll');
                    if (el) el.scrollTop = el.scrollHeight;
                });
            } catch (e) {
                console.error(e);
            }
        },

        formatTime(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            return d.toLocaleString(undefined, { hour: '2-digit', minute: '2-digit', day: 'numeric', month: 'short' });
        },

        statusIcon(s) {
            if (s === 'read') return '✓✓';
            if (s === 'delivered') return '✓✓';
            if (s === 'sent') return '✓';
            if (s === 'failed') return '⚠';
            return '';
        },

        async sendReply() {
            const text = this.draft.trim();
            if (!text || !this.selectedId || this.chatMeta.can_reply_freeform !== true) return;
            this.sending = true;
            this.sendError = '';
            try {
                const base = @json(url('/admin/api/whatsapp/conversations'));
                const res = await fetch(`${base}/${this.selectedId}/reply`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ message: text }),
                });
                const data = await res.json();
                if (!res.ok) {
                    this.sendError = data.error || 'Send failed';
                    return;
                }
                this.draft = '';
                this.messages.push(data.message);
                await this.loadConversations();
                this.$nextTick(() => {
                    const el = document.getElementById('chat-scroll');
                    if (el) el.scrollTop = el.scrollHeight;
                });
            } catch (e) {
                this.sendError = 'Network error';
            } finally {
                this.sending = false;
            }
        },

        async linkStudent() {
            if (!this.linkStudentId || !this.selectedId) return;
            try {
                const base = @json(url('/admin/api/whatsapp/conversations'));
                const res = await fetch(`${base}/${this.selectedId}/link-student`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ student_id: this.linkStudentId }),
                });
                const data = await res.json();
                if (!res.ok) {
                    alert(data.error || 'Could not link');
                    return;
                }
                await this.loadMessages();
                await this.loadConversations();
            } catch (e) {
                alert('Network error');
            }
        },
    };
}
</script>
@endsection
@endsection
