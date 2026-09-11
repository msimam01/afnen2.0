<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to AFNEN</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e5128 0%, #2d7a3e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .welcome-section {
            background-color: #f8f9fa;
            border-left: 4px solid #2d7a3e;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .credentials-section {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 20px;
            margin: 20px 0;
        }
        .credentials-section h3 {
            margin-top: 0;
            color: #856404;
        }
        .credential-item {
            margin: 10px 0;
            padding: 10px;
            background-color: white;
            border-radius: 4px;
        }
        .credential-label {
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }
        .credential-value {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            color: #333;
            margin-top: 5px;
        }
        .cta-button {
            display: inline-block;
            background-color: #2d7a3e;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .cta-button:hover {
            background-color: #1e5128;
        }
        .security-warning {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-size: 14px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        .footer a {
            color: #2d7a3e;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>AFNEN</h1>
            <p>Agricultural Finance Network</p>
        </div>

        <div class="content">
            <h2>Welcome to AFNEN, {{ $administratorName }}!</h2>

            <div class="welcome-section">
                <p>Your organization <strong>{{ $tenantName }}</strong> has been successfully set up in the AFNEN system.</p>
                <p>You are designated as the administrator with full access to manage your organization's agricultural finance operations.</p>
            </div>

            <h3>Your Account Credentials</h3>
            <div class="credentials-section">
                <h3>⚠️ Important: Temporary Password</h3>
                <p>Please use the following credentials to access your account for the first time:</p>

                <div class="credential-item">
                    <div class="credential-label">Organization Name</div>
                    <div class="credential-value">{{ $tenantName }}</div>
                </div>

                <div class="credential-item">
                    <div class="credential-label">Login URL</div>
                    <div class="credential-value">{{ $loginUrl }}</div>
                </div>

                <div class="credential-item">
                    <div class="credential-label">Email Address</div>
                    <div class="credential-value">{{ $administratorEmail }}</div>
                </div>

                <div class="credential-item">
                    <div class="credential-label">Temporary Password</div>
                    <div class="credential-value">{{ $temporaryPassword }}</div>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="cta-button">Access AFNEN</a>
            </div>

            <div class="security-warning">
                <strong>Security Notice:</strong>
                <ul>
                    <li>This is a <strong>temporary password</strong> that must be changed immediately after your first login.</li>
                    <li>You will be required to set a new password before accessing the dashboard.</li>
                    <li>Never share your password with anyone.</li>
                    <li>If you did not request this account, please contact your system administrator immediately.</li>
                </ul>
            </div>

            <h3>Next Steps</h3>
            <ol>
                <li>Click the "Access AFNEN" button above or visit your organization's login URL</li>
                <li>Enter your email address and temporary password</li>
                <li>You will be prompted to create a new secure password</li>
                <li>Complete the email verification process if required</li>
                <li>Access your dashboard and begin managing your agricultural operations</li>
            </ol>

            <p>If you have any questions or need assistance, please contact your AFNEN system administrator.</p>

            <p>Welcome to the AFNEN community!</p>

            <p>
                Best regards,<br>
                AFNEN Administration Team
            </p>
        </div>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} AFNEN - Agricultural Finance Network. All rights reserved.</p>
        </div>
    </div>
</body>
</html>