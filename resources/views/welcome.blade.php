<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stock Rekapan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Segoe UI', 'Tahoma', 'Geneva', 'Verdana', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-blue-800 min-h-screen flex items-center justify-center p-5">
    <div class="bg-white rounded-3xl shadow-2xl p-10 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-semibold text-gray-800 mb-2">Upload Rekapan</h1>
            <p class="text-sm text-gray-600">Kelola data stock dengan mudah</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-5 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm">
                @foreach($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <form action="{{ route('upload-rekapan') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            <input type="hidden" name="_token" value="{{ csrf_token() }}" id="csrfToken">
            <div class="mb-5">
                <label for="csv_file" class="block text-gray-700 font-medium mb-2 text-sm">File CSV Rekapan</label>
                <input 
                    type="file" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-300" 
                    id="csv_file" 
                    name="csv_file" 
                    accept=".csv" 
                    required
                >
                <div class="mt-2">
                    <a 
                        href="{{ route('download-template') }}" 
                        class="inline-flex items-center text-xs text-blue-500 hover:text-blue-700 transition-colors duration-300"
                    >
                        📥 Download Template CSV
                    </a>
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-xl font-semibold text-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 mb-5"
            >
                Upload Rekapan
            </button>
        </form>

    <script>
        // Refresh CSRF token sebelum submit
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get fresh CSRF token
            fetch('/csrf-token')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('csrfToken').value = data.token;
                    document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', data.token);
                    // Submit form setelah token diupdate
                    this.submit();
                })
                .catch(() => {
                    // Jika gagal refresh token, submit langsung
                    this.submit();
                });
        });
    </script>
</body>
</html>