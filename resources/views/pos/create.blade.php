@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
<h1 class="text-lg font-semibold mb-4">Transaksi Kasir</h1>
<div x-data="{
    cart: [],
    highlightedId: null,
    addToCart(id, name, price) {
        this.highlightedId = id;

        const existing = this.cart.find((item) => item.id === id);

        if (existing) {
            existing.qty++;
            return;
        }

        this.cart.push({ id, name, price, qty: 1 });
    },
    removeFromCart(id) {
        this.cart = this.cart.filter((item) => item.id !== id);
    },
    lineTotal(item) {
        return item.price * item.qty;
    },
    subtotal() {
        return this.cart.reduce((sum, item) => sum + this.lineTotal(item), 0);
    }
}">
    <div class="grid grid-cols-3 gap-4">
        @foreach ($products as $product)
        <div class="border rounded-md p-3 cursor-pointer"
            :class="highlightedId === {{ $product->id }} ? 'ring-2 ring-blue-500' : ''"
            @click="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">
            <p class="font-medium">{{ $product->name }}</p>
            <p class="text-sm text-slate-500">Rp {{ number_format($product->price) }}</p>
        </div>
        @endforeach
    </div>
    <div class="mt-4 border-t pt-3">
        <template x-for="item in cart" :key="item.id">
            <div class="flex items-center gap-3 mb-1">
                <p x-text="item.name + ' x' + item.qty + ' - Rp ' + lineTotal(item)"></p>
                <button type="button"
                    class="text-sm text-red-600 hover:underline"
                    @click="removeFromCart(item.id)">Hapus</button>
            </div>
        </template>
        <p class="font-semibold mt-2">Subtotal: Rp <span x-text="subtotal()"></span></p>
    </div>
</div>
@endsection
