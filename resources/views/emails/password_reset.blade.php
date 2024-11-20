<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('emails.password_reset_subject') }}</title>
</head>
<body>
    <p>{{ __('emails.password_reset_message') }}</p>
    <p><a href="{{ $url }}">{{ __('emails.reset_link') }}</a></p>
</body>
</html>
