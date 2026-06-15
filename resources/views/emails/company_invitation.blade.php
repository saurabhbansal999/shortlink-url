<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Company Invitation</title>
</head>
<body>
    <p>Hello,</p>

    <p>The admin has invited you to create the company "{{ $companyName }}".</p>

    <p>Click the button below to complete company registration, add the company phone, and create the company admin account.</p>

    <p>
        <a href="{{ $inviteUrl }}"
           style="display:inline-block;padding:12px 24px;background:#4f46e5;color:#fff;text-decoration:none;border-radius:6px;"
        >Complete Company Registration</a>
    </p>

    <p>If the button does not work, copy and paste this link into your browser:</p>
    <p><a href="{{ $inviteUrl }}">{{ $inviteUrl }}</a></p>

    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
