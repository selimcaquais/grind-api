<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('emails.email_change_subject') }}</title>
</head>
<body>
    <p>{{ __('emails.email_change_message') }}</p>
    <p><a href="{{ $url }}">{{ __('emails.change_email_link') }}</a></p>
</body>
</html>
