<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/logo/Logo_png.png') }}" type="image/png">
    <title>System Action Result</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 28px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            padding: 40px;
            max-width: 560px;
            width: 100%;
            text-align: left;
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .success .eyebrow {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .error .eyebrow {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }
        .error .icon {
            color: #ef4444;
        }
        h1 {
            color: #1f2937;
            margin-bottom: 16px;
            font-size: 32px;
            line-height: 1.15;
            letter-spacing: -0.03em;
        }
        .message {
            color: #4b5563;
            margin-bottom: 20px;
            line-height: 1.6;
            font-size: 16px;
        }
        .timestamp {
            color: #9ca3af;
            font-size: 14px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 24px;
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s, background-color 0.2s, border-color 0.2s;
            padding: 12px 16px;
            border-radius: 16px;
            border: 1px solid #dbeafe;
            background: #eff6ff;
        }
        .back-link:hover {
            color: #1e40af;
            background: #dbeafe;
            border-color: #bfdbfe;
        }
    </style>
</head>
<body>
    <div class="container {{ $success ? 'success' : 'error' }}">
        <div class="eyebrow">
            @if($success)
                <span>Success</span>
            @else
                <span>Error</span>
            @endif
        </div>
        <h1>{{ $success ? 'System action completed successfully.' : 'System action could not be completed.' }}</h1>
        <div class="message">
            {!! $message !!}
        </div>
        @if(isset($timestamp))
            <div class="timestamp">
                Completed at: {{ $timestamp }}
            </div>
        @endif
        <a href="{{ route('admin.settings.index') }}" class="back-link">← Back to settings</a>
    </div>
</body>
</html>
