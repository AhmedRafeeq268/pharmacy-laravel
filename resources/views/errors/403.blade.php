<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>403 Forbidden</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- استدعاء الخطوط من جوجل --}}
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Mono|Sedgwick+Ave+Display" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/error-403.css') }}">
</head>
<body>
    <div class="scene">
        <div class="overlay"></div>
        <div class="overlay"></div>
        <div class="overlay"></div>
        <div class="overlay"></div>

        <span class="bg-403">403</span>

        <div class="text">
            <span class="hero-text"></span>
            <span class="msg">can't let <span>you</span> in.</span>
            <span class="support">
                <span>unexpected?</span>
                <a href="{{ route('farmacy.index') }}">Go Home</a>
            </span>
        </div>

        <div class="lock"></div>
    </div>
</body>
</html>
