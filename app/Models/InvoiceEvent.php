<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceEvent extends Model
{
    protected $fillable = [
        'invoice_id',
        'user_id',
        'type',
        'date',
        'price'
    ];

    protected $casts = [
        'date' => 'datetime',
        'price' => 'decimal:2'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
