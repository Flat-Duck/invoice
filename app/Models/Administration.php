<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Administration extends Model
{
    protected $fillable = ['name', 'notes'];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
