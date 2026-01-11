<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'daily_menu_id',
        'item_type', // 'soup', 'entree_a', 'entree_b', 'dessert'
    ];

    // Belongs to the main Order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Links to the specific day (so we know if it's Monday or Tuesday)
    public function dailyMenu(): BelongsTo
    {
        return $this->belongsTo(DailyMenu::class);
    }
}