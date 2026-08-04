<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = ['name', 'business_field', 'address', 'phone', 'email', 'contact_person', 'notes'];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
