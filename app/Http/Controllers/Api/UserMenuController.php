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
    public function current(Request $request)
    {
        $date = $request->query('date', now()->format('Y-m-d'));

        // GREEDY SEARCH: Find the newest menu that started ON or BEFORE the clicked date
        $menu = WeeklyMenu::where('week_start_date', '<=', $date)
            ->with([
                'dailyMenus.soup', 
                'dailyMenus.entree_a', 
                'dailyMenus.entree_b', 
                'dailyMenus.dessert'
            ])
            ->orderBy('week_start_date', 'desc') // Get the closest one
            ->first();

        if (!$menu) {
            return response()->json(['message' => 'No menu found'], 404);
        }

        return $menu;
    }
}