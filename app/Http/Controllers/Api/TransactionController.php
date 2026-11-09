<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();

        $transaction = DB::transaction(function () use ($validated, $request) {
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
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
            }

            $transaction->update(['total' => $total]);

            return $transaction;
        });

        return response()->json([
            'data' => [
                'id' => $transaction->id,
                'total' => $transaction->total,
            ],
        ], 201);

    }

    public function show(Request $request, $id)
    {
    $transaction = \App\Models\Transaction::with('user', 'details.product')->find($id);

    if (!$transaction) {
        return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
    }

    if ($request->user()->id !== $transaction->user_id && $request->user()->role !== 'admin'){
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    return response()->json([
        'success' => true,
        'data' => $transaction
    ], 200);
    }

}
