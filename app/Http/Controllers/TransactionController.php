<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller {
    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();
        $transaction = DB::transaction(function () use ($validated, $request) {
            $transaction = Transaction::create(['user_id' => $request->user()->id,'total' => 0,]);
            $total = 0;

            foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $subtotal = $product->price * $item['qty'];
            $total += $subtotal;

                TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product->id,
                'qty' => $item['qty'],
                'subtotal' => $subtotal,]);
            }

            $transaction->update(['total' => $total]);
            return $transaction;
        });
    
        return response()->json([
        'data' => ['id' => $transaction->id,'total' => $transaction->total,],], 201);
    }
    
    public function show(Transaction $transaction) {
        Gate::authorize('view', $transaction);
        return new TransactionResource($transaction);
    }

   public function create()
   {
    // Ambil data produk atau data lain yang dibutuhkan untuk halaman POS
    $products = \App\Models\Product::all();

    return view('pos.create', compact('products'));
    }
 
   public function index()
   {
    // Ubah ->get() menjadi ->paginate(10) agar mendukung fungsi links() di view
    $transactions = \App\Models\Transaction::with('user', 'details.product')
        ->latest()
        ->paginate(10);

    return view('transactions.index', compact('transactions'));
   }

}
