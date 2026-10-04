<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    public function Category(): BelongsTo{
        return $this->belongsTo(Category::class);
    }

    public function transactions(): BelongsToMany
    {
    return $this->belongsToMany(Transaction::class, 'transaction_details');
    }
}
