# 📧 Email Configuration for Local Testing

## 🚀 Quick Setup Guide

### **Option 1: Gmail SMTP (Recommended)**

1. **Enable 2-Factor Authentication** on your Gmail account
2. **Generate App Password**:
   - Go to Google Account Settings
   - Security → 2-Step Verification → App passwords
   - Generate password for "Mail"
3. **Update .env file**:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-character-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="Softpro"
```

### **Option 2: Mailtrap (Testing)**

1. **Sign up** at [mailtrap.io](https://mailtrap.io)
2. **Get credentials** from your inbox
3. **Update .env file**:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@edumanage.com"
MAIL_FROM_NAME="Softpro"
```

### **Option 3: Log Driver (Development)**

For testing without sending real emails:
```env
MAIL_MAILER=log
```

Emails will be saved to `storage/logs/laravel.log`

## 🔧 Testing Email Functionality

### **1. Test Email Sending**
```bash
php artisan tinker
```

```php
use App\Models\Student;
use App\Mail\StudentRegistrationMail;
use Illuminate\Support\Facades\Mail;

// Get a student
$student = Student::first();

// Send test email
Mail::to($student->email)->send(new StudentRegistrationMail($student));
```

### **2. Test via Student Creation**
1. Go to `http://localhost:8000/admin/students/create`
2. Fill the form and submit
3. Check email inbox or logs

### **3. Check Email Logs**
```bash
tail -f storage/logs/laravel.log
```

## 📱 Email Features

### **Registration Email Includes:**
- ✅ Welcome message
- ✅ Student details (name, Aadhar, email, WhatsApp)
- ✅ Login credentials (email + WhatsApp number as password)
- ✅ Registration status
- ✅ Next steps based on approval status
- ✅ Professional HTML design
- ✅ Responsive layout

### **Email Template Features:**
- 🎨 Beautiful gradient design
- 📱 Mobile responsive
- 🔐 Secure credential display
- 📋 Complete student information
- 🚀 Clear next steps
- 💡 Important notes section

## 🛠️ Troubleshooting

### **Common Issues:**

1. **"Connection could not be established"**
   - Check internet connection
   - Verify SMTP credentials
   - Ensure 2FA is enabled for Gmail

2. **"Authentication failed"**
   - Use App Password, not regular password
   - Check username format (email address)

3. **"Email not received"**
   - Check spam folder
   - Verify email address
   - Check Mailtrap inbox (if using Mailtrap)

4. **"Template not found"**
   - Clear cache: `php artisan optimize:clear`
   - Check file exists: `resources/views/emails/student-registration.blade.php`

### **Debug Commands:**
```bash
# Clear all caches
php artisan optimize:clear

# Check configuration
php artisan config:show mail

# Test mail configuration
php artisan tinker
>>> config('mail')
```

## 🎯 Production Setup (Hostinger)

When ready for production:

1. **Use Hostinger SMTP**:
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Softpro"
```

2. **Configure DNS** (SPF, DKIM, DMARC)
3. **Test thoroughly** before going live
4. **Monitor email delivery** rates

## 📊 Email Analytics (Future)

- Track email open rates
- Monitor delivery success
- A/B test email templates
- Implement email preferences

---

**Ready to test!** 🚀
