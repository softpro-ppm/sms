# WhatsApp `registration_complete` Template

## Template Structure (Meta)

- **Header:** Empty (none)
- **Body:** `Hello {{customer_name}},` + welcome text
- **Buttons:** None configured

## App Integration

The app sends **body only** with 1 parameter: `customer_name` (student full name).

No header or button components are sent.

## Adding a Login Button (Optional)

If you want "Tap the button below to get started" to open the login page:

1. In Meta WhatsApp Manager → Edit `registration_complete`
2. Click **+ Add button** → **Visit website**
3. Set button text (e.g. "Get started") and dynamic URL variable
4. After approval, update the app to pass button params for this template
