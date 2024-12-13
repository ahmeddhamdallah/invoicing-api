<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Customer;
use App\Models\InvoiceEvent;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'start_date',
        'end_date',
        'total_amount',
        'total_users',
        'active_users',
        'registered_users',
        'appointment_users',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'total_users' => 'integer',
        'active_users' => 'integer',
        'registered_users' => 'integer',
        'appointment_users' => 'integer',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(InvoiceEvent::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
