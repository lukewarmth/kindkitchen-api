<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status', // e.g., 'pending', 'completed'
        'donate_meal',
        'consent_unclaimed_donation',
    ];

    // An order belongs to a user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // An order has many specific items (e.g., Mon Soup, Tue Dessert)
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}