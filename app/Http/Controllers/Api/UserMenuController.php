<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WeeklyMenu;
use Illuminate\Http\Request;

class UserMenuController extends Controller
{
    /**
     * Get the current or next available weekly menu.
     */
    public function current()
    {
        // Simple logic: Get the first menu that has a start date 
        // greater than or equal to today (or simply the latest one created).
        // For this project, let's just grab the latest active one.

        $menu = WeeklyMenu::with([
            'dailyMenus.soup',
            'dailyMenus.entreeA',
            'dailyMenus.entreeB',
            'dailyMenus.dessert'
        ])
        ->where('is_active', true)
        ->orderBy('week_start_date', 'asc') // Get the upcoming one
        ->first();

        if (!$menu) {
            return response()->json(['message' => 'No menu available for this week.'], 404);
        }

        return response()->json($menu);
    }
}