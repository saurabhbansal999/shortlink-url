<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Member Invitation</title>
</head>
<body>
    <p>Hello,</p>

    <p>You have been invited to join <strong>{{ $companyName }}</strong> as {{ $roleLabel === 'Admin' ? 'an Admin' : 'a Member' }}.</p>

    <p>Click the button below to set your name and password and activate your account.</p>

    <p>
        <a href="{{ $inviteUrl }}"
           style="display:inline-block;padding:12px 24px;background:#4f46e5;color:#fff;text-decoration:none;border-radius:6px;"
        >Activate Account</a>
    </p>

    <p>If the button does not work, copy and paste this link into your browser:</p>
    <p><a href="{{ $inviteUrl }}">{{ $inviteUrl }}</a></p>

    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
