<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
</head>
<body>
    <h2>Welcome to Our Platform!</h2>
    <p>Please verify your email by clicking the button below:</p>
    <a href="{{ $url }}" style="display: inline-block; padding: 10px 20px; color: #fff; background: #007bff; text-decoration: none; border-radius: 5px;">
        Verify Email
    </a>
    <p>If the button doesn't work, copy and paste this URL into your browser:</p>
    <p>{{ $url }}</p>
</body>
</html>
