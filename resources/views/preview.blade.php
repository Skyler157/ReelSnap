<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview | ReelSnap</title>
    <link rel="icon" href="/reelsnap-icon.svg?v=2" sizes="any" type="image/svg+xml">
    <link rel="shortcut icon" href="/reelsnap-icon.svg?v=2" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #1f1147;
            --muted: #5f5a7a;
            --panel: rgba(255, 255, 255, 0.94);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Manrope", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 15% 12%, #feda75 0%, transparent 36%),
                radial-gradient(circle at 86% 18%, #d62976 0%, transparent 30%),
                radial-gradient(circle at 70% 88%, #4f5bd5 0%, transparent 33%),
                linear-gradient(150deg, #fff4fb 0%, #fff7ef 45%, #f5f2ff 100%);
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .panel {
            width: min(860px, 100%);
            background: var(--panel);
            border-radius: 20px;
            border: 1px solid rgba(31, 17, 71, 0.1);
            box-shadow: 0 30px 70px rgba(31, 17, 71, 0.16);
            padding: 18px;
            position: relative;
        }

        .close {
            position: absolute;
            top: 12px;
            right: 12px;
            text-decoration: none;
            width: 38px;
            height: 38px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: rgba(31, 17, 71, 0.08);
            color: #1f1147;
            font-size: 20px;
            font-weight: 800;
            transition: background .2s, transform .2s;
        }

        .close:hover {
            background: rgba(31, 17, 71, 0.16);
            transform: scale(1.04);
        }

        h1 {
            margin: 8px 36px 4px 0;
            font-size: clamp(1.3rem, 3vw, 1.8rem);
        }

        p {
            margin: 0 0 12px;
            color: var(--muted);
            font-size: 0.94rem;
        }

        video {
            width: 100%;
            border-radius: 14px;
            border: 1px solid rgba(31, 17, 71, 0.1);
            background: #000;
        }
    </style>
</head>
<body>
<main class="panel">
    <a class="close" href="{{ route('home') }}" aria-label="Close preview">X</a>
    <h1>{{ $title }}</h1>
    <p><strong>Author:</strong> {{ $author }} | <strong>Duration:</strong> {{ $duration }}</p>
    <video controls autoplay playsinline>
        <source src="{{ $videoUrl }}" type="video/mp4">
    </video>
</main>
</body>
</html>
