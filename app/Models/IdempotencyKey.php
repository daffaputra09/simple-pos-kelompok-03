<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable; // 1. Import trait Prunable
use Illuminate\Support\Carbon;

class IdempotencyKey extends Model
{
    use Prunable; // 2. Gunakan trait di dalam kelas

    protected $fillable = [
      'key',
      'user_id',
      'request_hash',
      'response_status',
      'response_body',
    ];

    /**
     * Tentukan query untuk model yang akan diprune (dihapus otomatis).
     * Menghapus kunci yang dibuat lebih dari 24 jam yang lalu.
     */
    public function prunable()
    {
        return static::where('created_at', '<=', Carbon::now()->subHours(24));
    }
}
