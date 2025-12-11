<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $companyName }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #4F46E5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
        .credentials-box {
            background-color: #e0e7ff;
            border-left: 4px solid #4F46E5;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .credential-item {
            margin: 10px 0;
            font-size: 14px;
        }
        .credential-label {
            font-weight: bold;
            color: #4F46E5;
            display: inline-block;
            width: 100px;
        }
        .credential-value {
            background-color: #fff;
            padding: 8px 12px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            color: #333;
            display: inline-block;
        }
        .warning-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to {{ $companyName }}!</h1>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $user->name }}</strong>,</p>

        <p>You have been invited by <strong>{{ $invitedByName }}</strong> to join <strong>{{ $companyName }}</strong> as a <strong>{{ $user->getRoleNames()->first() }}</strong>.</p>

        <p>Your account has been created successfully! Below are your login credentials:</p>

        <div class="credentials-box">
            <h3 style="margin-top: 0;">Your Login Credentials</h3>

            <div class="credential-item">
                <span class="credential-label">Email:</span>
                <span class="credential-value">{{ $user->email }}</span>
            </div>

            <div class="credential-item">
                <span class="credential-label">Password:</span>
                <span class="credential-value">{{ $password }}</span>
            </div>

            <div class="credential-item">
                <span class="credential-label">Company:</span>
                <span class="credential-value">{{ $companyName }}</span>
            </div>

            <div class="credential-item">
                <span class="credential-label">Role:</span>
                <span class="credential-value">{{ $user->getRoleNames()->first() }}</span>
            </div>
        </div>

        <div class="warning-box">
            <strong>⚠️ Important Security Notice:</strong>
            <p style="margin: 5px 0 0 0;">Please change your password immediately after your first login for security purposes.</p>
        </div>

        <p>Click the button below to login to your account:</p>

        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">Login Now</a>
        </div>

        <p>Or copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #4F46E5;">{{ $loginUrl }}</p>

        <p><strong>Need Help?</strong> If you have any questions or issues logging in, please contact your system administrator.</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} URL Shortener. All rights reserved.</p>
        <p>This is an automated email. Please do not reply to this message.</p>
    </div>
</body>
</html>
