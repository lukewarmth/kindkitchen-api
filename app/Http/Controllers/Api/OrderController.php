<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * List the authenticated user's orders.
     */
    public function index(Request $request)
    {
        // Get orders for the CURRENT user only
        return $request->user()
            ->orders()
            ->with(['items.dailyMenu']) // Load the details
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Place a new order.
     */
    public function store(Request $request)
    {
        // 1. Validate
        $validated = $request->validate([
            'donate_meal' => 'boolean',
            'consent_unclaimed_donation' => 'boolean',

            // The 'items' array contains what they chose
            'items' => 'required|array',
            'items.*.daily_menu_id' => 'required|exists:daily_menus,id',
            'items.*.item_type' => 'required|in:soup,entree_a,entree_b,dessert',
        ]);

        try {
            return DB::transaction(function () use ($validated, $request) {
                // 2. Create the Order "Receipt"
                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'status' => 'pending',
                    'donate_meal' => $validated['donate_meal'] ?? false,
                    'consent_unclaimed_donation' => $validated['consent_unclaimed_donation'] ?? false,
                ]);

                // 3. Add the individual items
                foreach ($validated['items'] as $item) {
                    $order->items()->create([
                        'daily_menu_id' => $item['daily_menu_id'],
                        'item_type' => $item['item_type'],
                    ]);
                }

                // 4. Return the result
                return response()->json($order->load('items'), 201);
            });
        } catch (\Exception $e) {
            Log::error('Order failed: ' . $e->getMessage());
            return response()->json(['message' => 'Order failed.'], 500);
        }
    }
}