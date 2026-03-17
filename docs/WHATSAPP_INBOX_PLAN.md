# WhatsApp Admin Inbox – Plan & Design

## Overview

Build an admin inbox in SMS to receive, view, and reply to WhatsApp messages from students. Uses WhatsApp Cloud API webhooks for incoming messages and existing `WhatsAppService` for replies.

---

## 1. Architecture

```
┌─────────────────┐     webhook POST      ┌──────────────────┐
│  Meta WhatsApp  │ ──────────────────►  │  SMS Backend      │
│  Cloud API      │                       │  /webhook/whatsapp│
└─────────────────┘                       └────────┬─────────┘
        │                                          │
        │  send message                            │ store
        │  (reply)                                 ▼
        │                                 ┌──────────────────┐
        │                                 │  whatsapp_messages│
        │                                 │  table            │
        └────────────────────────────────┴────────┬─────────┘
                                                 │
                                                 ▼
                                        ┌──────────────────┐
                                        │  Admin Inbox UI  │
                                        │  /admin/whatsapp │
                                        └──────────────────┘
```

---

## 2. Data Model

### Table: `whatsapp_conversations`
| Column | Type | Description |
|--------|------|--------------|
| id | bigint PK | |
| phone | string | E.164 format (e.g. 919876543210) |
| student_id | bigint FK nullable | Link to students if matched |
| last_message_at | timestamp | For sorting conversations |
| created_at, updated_at | timestamps | |

- **Index:** `phone` (unique), `student_id`, `last_message_at`
- **Purpose:** One row per unique phone number (conversation thread)

### Table: `whatsapp_messages`
| Column | Type | Description |
|--------|------|--------------|
| id | bigint PK | |
| conversation_id | bigint FK | |
| direction | enum | `inbound`, `outbound` |
| meta_message_id | string nullable | Meta's message ID |
| type | string | `text`, `image`, `audio`, `document`, etc. |
| body | text nullable | For text messages |
| media_url | string nullable | For media (downloaded or Meta URL) |
| status | string | `sent`, `delivered`, `read`, `failed` |
| metadata | json nullable | Raw webhook payload for debugging |
| created_at | timestamp | |

- **Index:** `conversation_id`, `created_at`, `direction`
- **Purpose:** Store every message (incoming + outgoing)

### Student matching
- When a message arrives from `phone`, try to match `Student` by `whatsapp_number` (normalize to 10 digits).
- If match found, set `conversation.student_id`. Admin sees student name, enrollments, etc.

---

## 3. Webhook Flow

### Meta setup (manual)
1. **Meta Developer Console** → App → WhatsApp → Configuration
2. **Webhook URL:** `https://sms.softpromis.com/webhook/whatsapp`
3. **Verify token:** Random string stored in `.env` as `WHATSAPP_WEBHOOK_VERIFY_TOKEN`
4. **Subscribe to:** `messages`

### Webhook endpoint: `POST /webhook/whatsapp`
- **GET** (verification): Meta sends `hub.mode`, `hub.verify_token`, `hub.challenge`. Return `hub.challenge` if token matches.
- **POST** (events): Parse `entry[].changes[].value`:
  - `messages` → incoming message
  - `statuses` → delivery/read receipts (optional, for UI)
  - `errors` → log

### Incoming message payload (simplified)
```json
{
  "entry": [{
    "changes": [{
      "value": {
        "messages": [{
          "id": "wamid.xxx",
          "from": "919876543210",
          "timestamp": "1234567890",
          "type": "text",
          "text": { "body": "Hello, I have a question" }
        }]
      }
    }]
  }]
}
```

### Supported message types (Phase 1)
- `text` – store `body`
- `image`, `audio`, `video`, `document` – store `media_url` (or download and store locally)

---

## 4. UI Design

### Layout: Two-panel inbox (like email/chat)

```
┌─────────────────────────────────────────────────────────────────┐
│  WhatsApp Inbox                                    [Search] [🔔] │
├──────────────────────┬──────────────────────────────────────────┤
│  CONVERSATIONS       │  CHAT AREA                                │
│  (left panel)        │  (right panel)                            │
│                      │                                            │
│  ┌────────────────┐ │  ┌──────────────────────────────────────┐ │
│  │ Rajesh Gulla   │ │  │  Rajesh Gulla  •  9550755039          │ │
│  │ 9550755039     │ │  │  Student • MS Office                  │ │ │
│  │ "Thanks!"      │ │  ├──────────────────────────────────────┤ │
│  │ 2 min ago  ●   │ │  │                                      │ │
│  └────────────────┘ │  │  [Student]  Hello, I have a question │ │
│  ┌────────────────┐ │  │              10:30 AM               │ │
│  │ Priya Sharma   │ │  │                                      │ │
│  │ 9876543210     │ │  │  [Admin]     Hi! How can we help?    │ │
│  │ "When is..."   │ │  │              10:32 AM  ✓✓            │ │
│  │ 1 hr ago       │ │  │                                      │ │
│  └────────────────┘ │  │  [Student]  Thanks!                  │ │
│  ...                │  │              10:35 AM               │ │
│                      │  │                                      │ │
│                      │  ├──────────────────────────────────────┤ │
│                      │  │ [Type a message...        ] [Send]   │ │
│                      │  └──────────────────────────────────────┘ │
└──────────────────────┴──────────────────────────────────────────┘
```

### Left panel (conversation list)
- Sorted by `last_message_at` DESC
- Show: contact name (student name if linked, else phone), last message preview, time, unread badge
- Search by phone or student name
- Empty state: "No conversations yet"

### Right panel (chat)
- Header: Contact name, phone, student link (if matched)
- Message list: scrollable, newest at bottom
- Inbound: left-aligned, gray bubble
- Outbound: right-aligned, primary color bubble
- Timestamp, delivery status (✓ sent, ✓✓ delivered)
- Input: textarea + Send button
- Optional: "Link to student" if phone not matched

### Responsive
- Desktop: side-by-side panels
- Mobile: list view → tap to open chat (full screen)

---

## 5. API Endpoints (for inbox UI)

| Method | Route | Purpose |
|--------|-------|---------|
| GET | `/admin/whatsapp/inbox` | Inbox page |
| GET | `/admin/api/whatsapp/conversations` | List conversations (paginated) |
| GET | `/admin/api/whatsapp/conversations/{id}/messages` | Messages for a conversation |
| POST | `/admin/api/whatsapp/conversations/{id}/reply` | Send reply (body: `{ "message": "..." }`) |
| POST | `/webhook/whatsapp` | Meta webhook (no auth; verify via token) |

---

## 6. Implementation Phases

### Phase 1: Webhook + storage (backend)
- [ ] Migration: `whatsapp_conversations`, `whatsapp_messages`
- [ ] `WhatsAppWebhookController`: GET verify, POST handle
- [ ] `WhatsAppConversation`, `WhatsAppMessage` models
- [ ] Service: `WhatsAppWebhookService` to parse and store
- [ ] Config: `WHATSAPP_WEBHOOK_VERIFY_TOKEN`
- [ ] Route: `POST /webhook/whatsapp` (exclude from CSRF, auth)

### Phase 2: Inbox UI (admin)
- [ ] `WhatsAppInboxController`
- [ ] Blade views: inbox layout, conversation list, chat panel
- [ ] API routes for conversations + messages
- [ ] Reply action: call `WhatsAppService::sendMessage()`, store outbound in DB
- [ ] Student matching on incoming message
- [ ] Sidebar nav: add "WhatsApp" under admin

### Phase 3: Polish
- [ ] Unread count badge
- [ ] Real-time updates (polling or Laravel Echo)
- [ ] Media display (images, docs)
- [ ] Link conversation to student manually if auto-match fails

---

## 7. Security

- **Webhook:** No auth; Meta doesn't support it. Verify using `hub.verify_token` on GET.
- **Admin inbox:** Require `auth` + `role:admin` (or `reception`).
- **Rate limit:** Webhook endpoint – Meta can send bursts; ensure quick 200 response, process async if needed.

---

## 8. Meta Configuration Checklist

1. App Dashboard → WhatsApp → Configuration
2. Webhook URL: `https://sms.softpromis.com/webhook/whatsapp`
3. Verify token: set in `.env` as `WHATSAPP_WEBHOOK_VERIFY_TOKEN`
4. Subscribe to: `messages` (and optionally `message_template_status_update`, `message_status_update`)

---

## 9. File Structure (new/modified)

```
app/
  Http/Controllers/
    Api/WhatsAppInboxApiController.php   # API for inbox
    WhatsAppWebhookController.php       # Webhook handler (no auth)
  Models/
    WhatsAppConversation.php
    WhatsAppMessage.php
  Services/
    WhatsAppWebhookService.php          # Parse & store incoming
database/migrations/
  xxxx_create_whatsapp_conversations_table.php
  xxxx_create_whatsapp_messages_table.php
resources/views/admin/whatsapp/
  inbox.blade.php
  partials/conversation-list.blade.php
  partials/chat-panel.blade.php
routes/
  web.php     # /webhook/whatsapp, /admin/whatsapp/*
  api.php     # or web.php for /admin/api/whatsapp/*
```

---

## 10. Estimated Effort

| Phase | Effort |
|-------|--------|
| Phase 1 (Webhook + DB) | 2–3 hours |
| Phase 2 (Inbox UI) | 3–4 hours |
| Phase 3 (Polish) | 1–2 hours |
| **Total** | ~6–9 hours |

---

## Next Step

Start with **Phase 1**: migrations, webhook controller, and webhook service. Once incoming messages are stored, Phase 2 (UI) can be built.
