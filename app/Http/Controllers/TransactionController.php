<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function create()
    {
        $products = collect([
            (object) [
                'id' => 1,
                'name' => 'Kopi Susu Gula Aren',
                'sku' => 'KPG-001',
                'price' => 18000,
                'stock' => 12,
            ],
            (object) [
                'id' => 2,
                'name' => 'Roti Bakar Cokelat',
                'sku' => 'RBC-002',
                'price' => 15000,
                'stock' => 5,
            ],
            (object) [
                'id' => 3,
                'name' => 'Es Teh Manis',
                'sku' => 'ETM-003',
                'price' => 5000,
                'stock' => 25,
            ],
        ]);

        return view('pos.create', compact('products'));
    }
}