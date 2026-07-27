<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <p>Halo,</p>
    <p>Kami menerima permintaan untuk mereset password akun Anda. Klik tombol di bawah ini untuk membuat password baru:</p>
    <p>
        <a href="{{ $resetUrl }}" style="display:inline-block;padding:10px 20px;background:#2563eb;color:#fff;text-decoration:none;border-radius:6px;">
            Reset Password
        </a>
    </p>
    <p>Atau salin link berikut ke browser Anda:</p>
    <p>{{ $resetUrl }}</p>
    <p>Link ini berlaku selama 24 jam. Jika Anda tidak meminta reset password, abaikan email ini.</p>
</body>
</html>
