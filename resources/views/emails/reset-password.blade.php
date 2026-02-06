<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
            color: #333333;
            line-height: 1.6;
        }
        .content p {
            margin: 0 0 20px;
            font-size: 16px;
        }
        .button {
            display: inline-block;
            padding: 16px 40px;
            margin: 20px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .footer {
            background-color: #f8f8f8;
            padding: 30px;
            text-align: center;
            color: #666666;
            font-size: 14px;
            border-top: 1px solid #eeeeee;
        }
        .footer p {
            margin: 5px 0;
        }
        .link-text {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f8f8;
            border-radius: 6px;
            font-size: 13px;
            color: #666666;
            word-break: break-all;
        }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <h1>Password Reset Request</h1>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $username }}</strong>,</p>

        <p>
            We received a request to reset the password for your account.
            If you initiated this request, click the button below to set a new password.
        </p>

        <div class="button-container">
            <a href="{{ $resetUrl }}" class="button">
                Reset Password
            </a>
        </div>

        <p>
            This password reset link is valid for the next <strong>60 minutes</strong>.
            After that, you will need to request a new one.
        </p>

        <p>
            If you did <strong>not</strong> request a password reset, no further action is required.
            Your account remains secure.
        </p>

        <div class="link-text">
            <strong>Or copy and paste this link into your browser:</strong><br>
            {{ $resetUrl }}
        </div>
    </div>

    <div class="footer">
        <p><strong>{{ config('app.name') }}</strong></p>
        <p>This is an automatically generated email. Please do not reply.</p>
        <p style="margin-top: 15px; color: #999999; font-size: 12px;">
            © {{ date('Y') }} All rights reserved.
        </p>
    </div>

</div>
</body>
</html>
