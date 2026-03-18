<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Preview</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            background: #e5e7eb;
            min-height: 100vh;
            padding: 16px;
        }
        .preview-container {
            max-width: 100%;
            overflow-x: auto;
            padding: 16px;
            display: flex;
            justify-content: center;
        }
        .preview-inner {
            background: #fff;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            /* Scaled size: 297mm*0.4 = 118.8mm, 210mm*0.4 = 84mm */
            width: 118.8mm;
            height: 84mm;
            overflow: hidden;
        }
        /* Certificate 297mm x 210mm; scale to 40% to fit admin panel */
        .preview-scaled {
            width: 297mm;
            height: 210mm;
            transform: scale(0.4);
            transform-origin: top left;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="preview-inner">
            <div class="preview-scaled">
                {!! $certificateHtml !!}
            </div>
        </div>
    </div>
</body>
</html>
