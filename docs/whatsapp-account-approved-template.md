# WhatsApp `account_approved` Template

## Button URL Fix (Duplicate URL)

If the "Click to Login" button opens `https://sms.softpromis.com/loginhttps://sms.softpromis.com/login` (duplicated URL), your template has the base URL + dynamic suffix. Add to `.env`:

```env
WHATSAPP_BUTTON_URL_EMPTY_SUFFIX=true
```

This passes `?` for the button parameter (results in `https://sms.softpromis.com/login?`). If your template uses `{{1}}` as the full URL (no base), set to `false`.

---

## Template Structure (WhatsApp Manager)

**Name:** `account_approved`  
**Language:** English  
**Category:** Utility → Default  

---

### Header (Optional)
```
Account approved
```

### Body
```
Hello {{customer_name}},

Your SoftPro student account has been approved.

Email: {{email}}
Phone number: {{phone_number}}

You can now access the student portal.
```

### Footer (Optional)
```
Softpro Skill Solutions
```

### Button
- **Type:** Visit website
- **Button text:** Click to Login
- **URL type:** Dynamic
- **Website URL:** `{{1}}` (use Add variable, sample: `https://sms.softpromis.com/login`)

---

## Variable Order (for reference)

| # | Variable Name    | Sample Value        | Sent by App        |
|---|------------------|---------------------|--------------------|
| 1 | `{{customer_name}}` | Rajesh gulla       | Student full name  |
| 2 | `{{email}}`        | rajesh@testmail.com | Student email      |
| 3 | `{{phone_number}}` | 9876543210          | Student phone/WhatsApp |
| - | Button URL         | https://sms.softpromis.com/login | Login page URL |

---

## App Integration

The app sends:
- **Body:** customer_name, email, phone_number (3 params)
- **Button:** login_url (dynamic URL for "Click to Login")
