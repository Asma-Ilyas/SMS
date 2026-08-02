<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['name', 'type', 'bank_id', 'account_number', 'qr_code_url', 'instructions', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}