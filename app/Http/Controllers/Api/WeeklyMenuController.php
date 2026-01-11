<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WeeklyMenu;
use App\Models\DailyMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WeeklyMenuController extends Controller
{
    /**
     * Display a listing of all weekly menus.
     * We use "with('dailyMenus')" to load the 7 daily menus for each week.
     */
    public function index()
    {
        return WeeklyMenu::with('dailyMenus', 'week-start-date')
            ->orderBy('week_start_date', 'desc')
            ->get();
    }

    /**
     * Store a new weekly menu and its 7 daily menus.
     * This is a "database transaction" - if any part fails, it all rolls back.
     */
    public function store(Request $request)
    {
        // 1. Validate the top-level data
        $validatedData = $request->validate([
            'week_start_date' => 'required|date|unique:weekly_menus,week_start_date',
            'is_active' => 'sometimes|boolean',
            'days' => 'required|array|size:7', // Must have 7 days
            'days.*.day_of_week' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'days.*.soup_item_id' => 'nullable|integer|exists:menu_items,id',
            'days.*.entree_a_item_id' => 'nullable|integer|exists:menu_items,id',
            'days.*.entree_b_item_id' => 'nullable|integer|exists:menu_items,id',
            'days.*.dessert_item_id' => 'nullable|integer|exists:menu_items,id',
        ]);

        try {
            // Use a transaction
            $weeklyMenu = DB::transaction(function () use ($validatedData) {
                
                // 2. Create the parent WeeklyMenu
                $weeklyMenu = WeeklyMenu::create([
                    'week_start_date' => $validatedData['week_start_date'],
                    'is_active' => $validatedData['is_active'] ?? false,
                ]);

                // 3. Loop through the 7 days and create a DailyMenu for each
                foreach ($validatedData['days'] as $dayData) {
                    $weeklyMenu->dailyMenus()->create([
                        'day_of_week' => $dayData['day_of_week'],
                        'soup_item_id' => $dayData['soup_item_id'] ?? null,
                        'entree_a_item_id' => $dayData['entree_a_item_id'] ?? null,
                        'entree_b_item_id' => $dayData['entree_b_item_id'] ?? null,
                        'dessert_item_id' => $dayData['dessert_item_id'] ?? null,
                    ]);
                }

                // 4. Return the complete weekly menu with its daily menus loaded
                return $weeklyMenu->load('dailyMenus');
            });

            // 5. If successful, return the new menu
            return response()->json($weeklyMenu, 201);

        } catch (\Exception $e) {
            // 6. If anything went wrong, log the error and return a server error
            Log::error('Failed to create weekly menu: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create menu. Please try again.'], 500);
        }
    }

    /**
     * Display the specified weekly menu.
     * We "eager load" all the relationships to get the *names* of the food,
     * not just their IDs. This is what the frontend will need.
     */
    public function show(WeeklyMenu $weeklyMenu)
    {
        return $weeklyMenu->load([
            'dailyMenus',
            'dailyMenus.soup',
            'dailyMenus.entreeA',
            'dailyMenus.entreeB',
            'dailyMenus.dessert'
        ]);
    }
}