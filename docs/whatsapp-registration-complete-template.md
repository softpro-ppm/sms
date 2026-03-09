# WhatsApp `registration_complete` Template

## Error #132018

Error `(#132018) There's an issue with the parameters in your template` means the parameters we send don't match what the template expects.

## Verify Template Structure in Meta

1. Go to **Meta Business Suite** → **WhatsApp Manager** → **Message templates**
2. Find `registration_complete`
3. Note the **exact** structure:
   - **Header:** Does it have variables? (e.g. `Hello {{1}},` or `Hello {{customer_name}},`)
   - **Body:** Which variables? (e.g. `Welcome {{1}}. Your registration is complete...`)
   - **Button:** Type (Visit website) and URL format (full `{{1}}` or base URL + suffix)

## Current App Assumption

We assume:
- **Header:** `Hello {{customer_name}},` (1 text param)
- **Body:** No variables
- **Button:** Dynamic URL (we pass `?` when `WHATSAPP_BUTTON_URL_EMPTY_SUFFIX=true`)

## Debug the Payload

Run to see exactly what we send:

```bash
php artisan whatsapp:test 9550755039 --template=registration_complete --debug
```

Compare the output with your template in Meta. The `components` array must match:
- Same number of components (header, body, button)
- Same number of parameters per component
- Correct order (header → body → button)

## If Template Differs

| If your template has... | Change in code |
|-------------------------|----------------|
| `customer_name` in **body** only | Use body params, no header params |
| `customer_name` in **both** header and body | Pass same value in both |
| Different variable names | Update `parameterNames` to match |
| Button expects full URL | Set `WHATSAPP_BUTTON_URL_EMPTY_SUFFIX=false` |
