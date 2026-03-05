<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Softpro')</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa; }
        .email-container { background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; padding: 24px; text-align: center; }
        .header img { height: 50px; width: auto; max-width: 180px; display: block; margin: 0 auto 12px; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .header p { margin: 8px 0 0; opacity: 0.95; font-size: 14px; }
        .content { padding: 28px; }
        .info-box { background: #f8fafc; border-radius: 8px; padding: 16px; margin: 16px 0; border-left: 4px solid #2563eb; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #475569; }
        .info-value { color: #64748b; }
        .cta-button { display: inline-block; background: #2563eb; color: white !important; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 16px 0; }
        .cta-button:hover { background: #1d4ed8; }
        .footer { background: #f1f5f9; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { margin: 4px 0; color: #64748b; font-size: 12px; }
        .status-pass { background: #dcfce7; color: #166534; }
        .status-fail { background: #fee2e2; color: #991b1b; }
        .status-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .signature { margin-top: 28px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 14px; color: #475569; }
        .signature strong { color: #1e293b; }
        .signature a { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ config('app.url') }}/images/logo/softpro-logo.PNG" alt="Softpro">
            @yield('header')
        </div>
        <div class="content">
            @yield('content')
            <div class="signature">
                <p style="margin: 0 0 4px;"><strong>Rajesh G</strong></p>
                <p style="margin: 0 0 4px;">Director</p>
                <p style="margin: 0 0 6px;">Softpro Skill Solutions</p>
                <p style="margin: 0;"><a href="tel:7799773656">Ph: 7799773656</a></p>
                <p style="margin: 4px 0 0;"><a href="mailto:info@softpro.co.in">Email: info@softpro.co.in</a></p>
            </div>
        </div>
        <div class="footer">
            <p><strong>Softpro</strong> – Your Learning Journey Starts Here</p>
            <p>This is an automated email. Please do not reply.</p>
            <p>© {{ date('Y') }} Softpro. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
