<?php

namespace MiladSarli\CartSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:0'
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('cart.models.user'));
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(config('cart.models.address'));
    }

    public function wallet(): HasMany
    {
        return $this->hasMany(config('cart.models.wallet'));
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
        $this->cart->update(['status' => 'paid']);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
        $this->cart->update(['status' => 'pending']);
    }
}
