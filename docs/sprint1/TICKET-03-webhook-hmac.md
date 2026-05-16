# TICKET-03 — WhatsApp webhook HMAC (ops)

## Env / config

| Variable | Purpose |
|----------|---------|
| `WHATSAPP_APP_SECRET` | Meta **App secret** (App Dashboard → Settings → Basic). Used to verify `X-Hub-Signature-256` on **POST** `/webhook/whatsapp`. |
| `WHATSAPP_WEBHOOK_VERIFY_TOKEN` | Unchanged: Meta **GET** subscription verification (`hub.verify_token`). |

**Production:** Set `WHATSAPP_APP_SECRET`. Unsigned or invalid POSTs return **403** and do not run inbox logic.

**Local / staging without Meta POST traffic:** Leave `WHATSAPP_APP_SECRET` empty → verification is **skipped** (warning logged). Do not use this mode on internet-exposed URLs.

## Rollout

1. Add secret to `.env` / secrets manager.
2. Run `php artisan config:cache` after deploy.
3. Confirm Meta webhook test / real message delivers **200** with `success: true`.
4. Wrong secret → Meta retries; fix secret quickly.

## References

- Middleware: `App\Http\Middleware\VerifyWhatsAppWebhookSignature`
- Route: `POST /webhook/whatsapp` → `whatsapp.signature` alias in `bootstrap/app.php`
