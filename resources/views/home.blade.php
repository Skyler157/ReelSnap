<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReelSnap</title>
    <link rel="icon" href="/reelsnap-icon.svg?v=2" sizes="any" type="image/svg+xml">
    <link rel="shortcut icon" href="/reelsnap-icon.svg?v=2" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #1f1147;
            --muted: #5f5a7a;
            --surface: rgba(255, 255, 255, 0.92);
            --primary: #f58529;
            --primary-strong: #dd2a7b;
            --accent: #8134af;
            --success-bg: #dcfce7;
            --success-text: #166534;
            --error-bg: #fee2e2;
            --error-text: #991b1b;
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
                radial-gradient(circle at 12% 15%, #feda75 0%, transparent 35%),
                radial-gradient(circle at 85% 20%, #d62976 0%, transparent 30%),
                radial-gradient(circle at 70% 85%, #4f5bd5 0%, transparent 30%),
                linear-gradient(150deg, #fff4fb 0%, #fff7ef 45%, #f5f2ff 100%);
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .shell {
            width: min(760px, 100%);
            background: var(--surface);
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 20px;
            box-shadow: 0 28px 70px rgba(15, 23, 42, 0.14);
            overflow: hidden;
            animation: rise .45s ease-out;
            backdrop-filter: blur(8px);
        }

        .hero {
            padding: 28px 28px 16px;
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        }

        .badge {
            display: inline-block;
            background: rgba(221, 42, 123, 0.14);
            color: #9d174d;
            font-weight: 700;
            border-radius: 999px;
            padding: 6px 11px;
            font-size: 12px;
            letter-spacing: .02em;
        }

        h1 {
            margin: 10px 0 6px;
            font-size: clamp(2rem, 5vw, 3rem);
            line-height: 1.15;
        }

        .subtitle {
            margin: 0;
            color: var(--muted);
            font-size: 0.98rem;
        }

        .content {
            padding: 20px 28px 28px;
        }

        .alert {
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 14px;
            font-size: 0.94rem;
            font-weight: 600;
        }

        .alert.success {
            background: var(--success-bg);
            color: var(--success-text);
        }

        .alert.error {
            background: var(--error-bg);
            color: var(--error-text);
        }

        .form-wrap {
            display: grid;
            gap: 12px;
        }

        input[type="url"] {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 14px 15px;
            font-size: 0.95rem;
            color: var(--ink);
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
        }

        input[type="url"]:focus {
            outline: none;
            border-color: #c13584;
            box-shadow: 0 0 0 4px rgba(193, 53, 132, 0.2);
        }

        .primary-btn {
            border: 0;
            border-radius: 12px;
            padding: 13px 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-strong));
            color: #fff;
            font-weight: 800;
            font-size: 0.96rem;
            letter-spacing: .01em;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s, opacity .2s;
        }

        .primary-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(221, 42, 123, 0.3);
        }

        .primary-btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .loading {
            display: none;
            text-align: center;
            font-weight: 700;
            color: #a21caf;
            margin-top: 8px;
        }

        .spinner {
            width: 23px;
            height: 23px;
            border: 3px solid #fbcfe8;
            border-top-color: #c13584;
            border-radius: 999px;
            animation: spin .9s linear infinite;
            margin: 0 auto 8px;
        }

        .video-section {
            margin-top: 22px;
            padding-top: 20px;
            border-top: 1px solid rgba(15, 23, 42, 0.08);
            animation: rise .4s ease-out;
        }

        .video-meta {
            margin: 8px 0 14px;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .video-meta strong {
            color: #1f2937;
        }

        video {
            width: 100%;
            border-radius: 14px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            background: #000;
        }

        .actions {
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-link {
            text-decoration: none;
            border-radius: 11px;
            padding: 11px 14px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: transform .2s, box-shadow .2s, background .2s, color .2s;
        }

        .action-link.download {
            background: #4f5bd5;
            color: #f5f7ff;
        }

        .action-link.download:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(79, 91, 213, 0.28);
        }

        .action-link.preview {
            background: #e2e8f0;
            color: #0f172a;
        }

        .action-link.preview:hover {
            background: #cbd5e1;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 620px) {
            .hero, .content {
                padding-left: 18px;
                padding-right: 18px;
            }

            .actions {
                flex-direction: column;
            }

            .action-link {
                text-align: center;
            }
        }
    </style>
</head>
<body>
<main class="shell">
    <section class="hero">
        <h1>ReelSnap</h1>
        <p class="subtitle">Paste your Instagram Reel or Post URL, then download your video instantly.</p>
    </section>

    <section class="content">
        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('download', [], false) }}" id="downloadForm" class="form-wrap">
            @csrf
            <input
                type="url"
                name="url"
                value="{{ old('url') }}"
                placeholder="https://www.instagram.com/reel/..."
                required
                inputmode="url"
            >
            <button type="submit" id="submitBtn" class="primary-btn">Download</button>
        </form>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            Fetching your video...
        </div>

        @if(session('video'))
            <div class="video-section">
                <h3>{{ session('video.title') }}</h3>
                <p class="video-meta">
                    <strong>Author:</strong> {{ session('video.author') }}
                    &nbsp;|&nbsp;
                    <strong>Duration:</strong> {{ session('video.duration') }}
                </p>

                <video controls playsinline>
                    <source src="{{ session('video.video_url') }}" type="video/mp4">
                </video>

                <div class="actions">
                    @if(session('download_link'))
                        <a class="action-link download" href="{{ session('download_link') }}">
                            Download Video
                        </a>
                    @endif
                    @if(session('preview_link'))
                        <a class="action-link preview" href="{{ session('preview_link') }}">
                            Preview
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </section>
</main>

<script>
const form = document.getElementById('downloadForm');
const submitBtn = document.getElementById('submitBtn');
const loading = document.getElementById('loading');

form.addEventListener('submit', function () {
    submitBtn.disabled = true;
    loading.style.display = 'block';
});
</script>
</body>
</html>
