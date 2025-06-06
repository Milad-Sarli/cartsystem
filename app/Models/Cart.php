<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'product_id',
        'product_attr_value_id',
        'quantity',
        'price',
        'status',
        'ip_address'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:0',
    ];

    protected $attributes = [
        'status' => 'pending'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->with(['productPictures', 'activeOffer']);
    }

    public function productAttrValue(): BelongsTo
    {
        return $this->belongsTo(ProductAttrValue::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    // Helper methods
    public function getTotalPrice(): float
    {
        $price = $this->productAttrValue?->price ?? $this->product->price;
        $offer = $this->product->activeOffer ?? $this->productAttrValue?->activeOffer;

        if ($offer) {
            if ($offer->per) {
                $price = $price - (($offer->per * $price) / 100);
            } elseif ($offer->cash) {
                $price = $price - $offer->cash;
            }
        }

        return $price * $this->quantity;
    }

    public function updatePrice(): void
    {
        $this->update(['price' => $this->getTotalPrice()]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
