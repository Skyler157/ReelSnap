<!DOCTYPE html>
<html>
<head>
    <title>ReelSnap - Instagram Video Downloader</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">

    <div class="bg-gray-800 p-8 rounded-xl shadow-xl w-full max-w-lg">
        <h1 class="text-3xl font-bold text-center mb-6">
            🎬 ReelSnap
        </h1>

        <p class="text-center text-gray-400 mb-6">
            Download Instagram videos instantly
        </p>

        @if(session('success'))
            <div class="bg-green-600 p-3 rounded mb-4 text-center">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('download') }}">
            @csrf

            <input 
                type="text" 
                name="url" 
                placeholder="Paste Instagram video URL here..."
                class="w-full p-3 rounded bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500"
            >

            @error('url')
                <p class="text-red-500 mt-2">{{ $message }}</p>
            @enderror

            <button 
                type="submit"
                class="w-full mt-4 bg-purple-600 hover:bg-purple-700 transition p-3 rounded font-semibold"
            >
                Download
            </button>
        </form>

        {{-- Video Preview Section --}}
        @if(session('video') && session('video')['success'])
            <div class="mt-6 bg-gray-700 p-4 rounded">

                {{-- Thumbnail --}}
                @if(session('video')['thumbnail'])
                    <img src="{{ session('video')['thumbnail'] }}" 
                         class="rounded mb-4 w-full">
                @endif

                {{-- Title --}}
                <h2 class="text-lg font-semibold mb-1">
                    {{ session('video')['title'] }}
                </h2>

                {{-- Author --}}
                @if(session('video')['author'])
                    <p class="text-gray-300 mb-2">By: {{ session('video')['author'] }}</p>
                @endif

                {{-- Download Button --}}
                <a href="{{ session('video')['video_url'] }}" 
                   class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded"
                   download>
                    Download Video
                </a>
            </div>
        @endif

    </div>

</body>
</html>