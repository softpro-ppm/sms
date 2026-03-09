# Pending WhatsApp Templates (create in Meta with _2 suffix)

Create these 3 templates in Meta WhatsApp Manager. All use **Category: Utility**, **Language: English**.

---

## 1. fully_paid_2

**When:** Student has paid all fees for an enrollment.

### Body
```
Hello {{student_name}},

All fees for your enrollment have been paid.

Course: {{course_name}}
Batch: {{batch_name}}

You can now access your course materials and continue learning.
```

### Footer (optional)
```
Softpro Skill Solutions
```

### Button (optional)
- **Type:** Visit website
- **Button text:** Access portal
- **URL:** Static `https://softpro.co.in` or Dynamic `{{1}}` → `https://sms.softpromis.com`

### Variables
| # | Variable     | Sample   |
|---|--------------|----------|
| 1 | student_name | Rajesh Gulla |
| 2 | course_name  | MS Office |
| 3 | batch_name   | MSO-1 |
| 4 | Button URL   | (if dynamic) |

---

## 2. assessment_result_2

**When:** Student completes an assessment and gets results.

### Body
```
Hello {{student_name}},

Your assessment result is ready.

Course: {{course_name}}
Correct: {{correct_answers}} / {{total_questions}}
Score: {{percentage}}%
Status: {{status}}

View your result in the student portal.
```

### Footer (optional)
```
Softpro Skill Solutions
```

### Button (optional)
- **Type:** Visit website
- **Button text:** View result
- **URL:** Dynamic `{{1}}` → `https://sms.softpromis.com`

### Variables
| # | Variable       | Sample   |
|---|----------------|----------|
| 1 | student_name   | Rajesh Gulla |
| 2 | course_name    | MS Office |
| 3 | correct_answers| 45 |
| 4 | total_questions| 50 |
| 5 | percentage     | 90 |
| 6 | status         | Passed |
| 7 | Button URL     | https://sms.softpromis.com |

---

## 3. certificate_issued_2

**When:** Certificate is issued to a student.

### Body
```
Hello {{student_name}},

Your certificate has been issued.

Course: {{course_name}}
Certificate No: {{certificate_number}}

Tap the button below to view your certificate.
```

### Footer (optional)
```
Softpro Skill Solutions
```

### Button (required – dynamic URL)
- **Type:** Visit website
- **Button text:** View certificate
- **URL:** Dynamic `{{1}}` – each certificate has a unique URL
- **Sample:** `https://sms.softpromis.com/student/certificates/1/view`

### Variables
| # | Variable         | Sample   |
|---|------------------|----------|
| 1 | student_name     | Rajesh Gulla |
| 2 | course_name      | MS Office |
| 3 | certificate_number| CERT-001 |
| 4 | Button URL       | (unique per certificate) |

---

## After approval

Once all 3 are approved, run `deploy` to update the code and deploy to the server.
