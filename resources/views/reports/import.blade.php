@extends('layouts.app')
@section('title', 'Import Produk')
@section('content')
<h1 class="text-lg font-semibold mb-4">Import Produk dari CSV</h1>
@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded-md mb-4">
        {{ session('success') }}
    </div>
@endif
@if ($importErrors->isNotEmpty())
    <div class="bg-red-50 text-red-700 p-3 rounded-md mb-4 max-w-md">
        <p class="font-medium mb-2">Baris yang gagal diimpor:</p>
        <ul class="list-disc pl-5 text-sm">
            @foreach ($importErrors as $error)
                <li>{{ $error->message }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data" class="max-w-md">
    @csrf
    <label class="block mb-3">
        <span class="text-sm font-medium">File CSV</span>
        <input type="file" name="csv" accept=".csv" class="mt-1 block w-full text-sm">
        @error('csv')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </label>
    <p class="text-xs text-slate-500 mb-3">Column yang dibutuhkan: <code>name</code>, <code>category_id</code>, <code>price</code>.</p>
    <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm">Import</button>
</form>

<div class="mt-6 border-t pt-4 max-w-md">
    <h2 class="text-lg font-semibold mb-3">
        Import Produk Menggunakan Laravel Excel
    </h2>

    <form action="{{ route('reports.import.excel') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label class="block mb-3">
            <span class="text-sm font-medium">File CSV</span>
            <input type="file" name="csv" accept=".csv" required class="border rounded-md p-2 w-full mb-3 text-sm">
            @error('csv')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </label>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
            Import dengan Laravel Excel
        </button>
    </form>
</div>
@endsection
