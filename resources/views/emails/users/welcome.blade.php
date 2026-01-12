<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to {{ $appName }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4f46e5; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 5px 5px; }
        .credentials { background: #e0e7ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .button { display: inline-block; background: #4f46e5; color: white; padding: 12px 24px; 
                 text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to {{ $appName }}</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $user->full_name }},</p>
            
            <p>Your account has been successfully created. Here are your login details:</p>
            
            <div class="credentials">
                <p><strong>Email:</strong> {{ $user->email }}</p>
                @if($tempPassword)
                <p><strong>Temporary Password:</strong> {{ $tempPassword }}</p>
                <p><small>Please change your password after first login.</small></p>
                @endif
            </div>
            
            <p>You can login to your account using the button below:</p>
            
            <a href="{{ $loginUrl }}" class="button">Login to Your Account</a>
            
            <p>If you have any questions or need assistance, please contact the system administrator.</p>
            
            <p>Best regards,<br>
            The {{ $appName }} Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>