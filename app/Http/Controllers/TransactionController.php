<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function create()
    {
        $products = Product::paginate(12);

        return view('pos.create', ['products' => $products]);
    }

    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();

        // 1. Kumpulkan total qty yang diminta per produk
        $requestedQtys = [];
        foreach ($validated['items'] as $item) {
            $productId = $item['product_id'];
            $requestedQtys[$productId] = ($requestedQtys[$productId] ?? 0) + $item['qty'];
        }

        // 2. Cek ketersediaan stok di database
        foreach ($requestedQtys as $productId => $totalQty) {
            $product = Product::find($productId);
            if ($totalQty > $product->stock) {
                // Tolak transaksi dan kembalikan pesan error menyebutkan nama produk
                return back()->withErrors([
                    'items' => "Transaksi gagal: Stok produk '{$product->name}' tidak mencukupi! (Sisa stok: {$product->stock}, Diminta: {$totalQty})."
                ]);
            }
        }

        // 3. Jika stok aman, lanjutkan penyimpanan
        DB::transaction(function () use ($validated) {
            $transaction = Transaction::create([
                'user_id' => 1,
                'total' => 0,
            ]);

            $total = 0;
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['qty'];
                $total += $subtotal;

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                ]);
                
                // Opsional: Kurangi stok produk setelah dibeli 
                $product->decrement('stock', $item['qty']);
            }

            $transaction->update(['total' => $total]);
        });

        return redirect()
            ->route('pos.create')
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function index()
    {
        $transactions = Transaction::with(['details.product', 'user'])
            ->latest()
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function show(string $id)
    {
        return "Detail transaksi #{$id}";
    }
}