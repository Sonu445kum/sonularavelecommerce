<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset Request</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 30px;">

    <div style="max-width: 600px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2 style="color: #333; text-align: center;">🔒 Password Reset Request</h2>
        <p style="color: #555; font-size: 16px;">Hello,</p>

        <p style="color: #555; font-size: 16px;">
            You recently requested to reset your password for your account.  
            Click the button below to reset it. This password reset link will expire in 60 minutes.
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" 
               style="background-color: #4f46e5; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold;">
                Reset My Password
            </a>
        </div>

        <p style="color: #555; font-size: 16px;">
            If you didn’t request a password reset, please ignore this email.
        </p>

        <hr style="margin-top: 30px;">
        <p style="color: #888; font-size: 13px; text-align: center;">
            &copy; {{ date('Y') }} YourAppName. All rights reserved.
        </p>
    </div>

</body>
</html>
