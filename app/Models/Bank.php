<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = ['name', 'branch_name', 'account_title', 'account_number', 'iban', 'routing_number', 'address', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }
}