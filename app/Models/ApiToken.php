<?php

namespace App\Models;

use Database\Factories\ApiTokenFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static \Database\Factories\ApiTokenFactory factory(...$parameters)
 */
class ApiToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'token',
        'name',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function newFactory(): Factory
    {
        return ApiTokenFactory::new();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isValid(): bool
    {
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }
}
