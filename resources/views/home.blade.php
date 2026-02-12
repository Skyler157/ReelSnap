<!DOCTYPE html>
<html>
<head>
    <title>ReelSnap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #6366f1, #9333ea);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 35px;
            border-radius: 16px;
            width: 100%;
            max-width: 650px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.4s ease-in-out;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #111827;
        }

        input[type="text"] {
            width: 100%;
            padding: 14px;
            border-radius: 8px;
            border: 1px solid #ddd;
            outline: none;
            font-size: 14px;
            transition: 0.3s;
        }

        input[type="text"]:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #6366f1;
            color: white;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #4f46e5;
        }

        button:disabled {
            background: gray;
            cursor: not-allowed;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .video-section {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        video {
            border-radius: 10px;
            margin-top: 10px;
        }

        .download-btn {
            margin-top: 15px;
            background: #16a34a;
        }

        .download-btn:hover {
            background: #15803d;
        }

        .loading {
            display: none;
            margin-top: 15px;
            text-align: center;
            color: #6366f1;
            font-weight: 500;
        }

        .spinner {
            border: 4px solid #eee;
            border-top: 4px solid #6366f1;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            animation: spin 1s linear infinite;
            margin: 10px auto;
        }

        .stats {
            margin-top: 25px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>

<div class="card">

    <h2>🎬 ReelSnap</h2>
    <p style="text-align:center; margin-bottom:20px; color:#6b7280;">
        Download Instagram Reels instantly
    </p>

    @if(session('success'))
        <div class="alert success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('download') }}" id="downloadForm">
        @csrf
        <input type="text" name="url" placeholder="Paste Instagram Reel URL here..." required>
        <br><br>
        <button type="submit" id="submitBtn">Download Reel</button>
    </form>

    <div class="loading" id="loading">
        <div class="spinner"></div>
        Fetching video...
    </div>

    @if(session('video'))
        <div class="video-section">
            <h3>{{ session('video.title') }}</h3>
            <p><strong>Author:</strong> {{ session('video.author') }}</p>
            <p><strong>Duration:</strong> {{ session('video.duration') }}</p>

            <video controls width="100%">
                <source src="{{ session('video.video_url') }}" type="video/mp4">
            </video>

            <a href="{{ session('video.video_url') }}" download>
                <button class="download-btn">⬇ Download Video</button>
            </a>
        </div>
    @endif

    <div class="stats">
        Total Downloads: <strong>{{ \App\Models\Download::count() }}</strong>
    </div>

</div>

<script>
document.getElementById('downloadForm').addEventListener('submit', function() {
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('loading').style.display = 'block';
});
</script>

</body>
</html>