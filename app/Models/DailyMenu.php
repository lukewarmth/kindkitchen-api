<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyMenu extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'weekly_menu_id',
        'day_of_week',
        'soup_item_id',
        'entree_a_item_id',
        'entree_b_item_id',
        'dessert_item_id',
    ];

    /**
     * Get the weekly menu that this daily menu belongs to.
     */
    public function weeklyMenu(): BelongsTo
    {
        return $this->belongsTo(WeeklyMenu::class);
    }

    // These relationships let us easily fetch the item details
    // app/Models/DailyMenu.php
    public function soup() { return $this->belongsTo(MenuItem::class, 'soup_item_id'); }
    public function entree_a() { return $this->belongsTo(MenuItem::class, 'entree_a_item_id'); }
    public function entree_b() { return $this->belongsTo(MenuItem::class, 'entree_b_item_id'); }
    public function dessert() { return $this->belongsTo(MenuItem::class, 'dessert_item_id'); }
}