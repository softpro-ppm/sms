<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cache Clear Result</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        .success .icon {
            color: #10b981;
        }
        .error .icon {
            color: #ef4444;
        }
        h1 {
            color: #1f2937;
            margin-bottom: 20px;
            font-size: 24px;
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
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="container {{ $success ? 'success' : 'error' }}">
        <div class="icon">
            @if($success)
                ✓
            @else
                ✗
            @endif
        </div>
        <h1>{{ $success ? 'Cache Cleared Successfully!' : 'Cache Clear Failed' }}</h1>
        <div class="message">
            {!! $message !!}
        </div>
        @if(isset($timestamp))
            <div class="timestamp">
                Cleared at: {{ $timestamp }}
            </div>
        @endif
        <a href="{{ route('admin.dashboard') }}" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
