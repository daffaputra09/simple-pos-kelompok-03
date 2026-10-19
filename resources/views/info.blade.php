<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Informasi Publik - Simple POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-slate-100 p-4">
    <div class="bg-white p-8 rounded-md shadow max-w-md text-center">
        <h1 class="text-2xl font-bold text-slate-900 mb-4">Selamat Datang di Simple POS</h1>
        <p class="text-slate-600 mb-6">
            Halaman ini adalah halaman informasi publik. Hanya pengunjung yang <b>belum login (Guest)</b> yang diizinkan melihat halaman ini.
        </p>
        <a href="{{ route('login') }}" class="inline-block bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition">
            Ke Halaman Login
        </a>
    </div>
</body>
</html>