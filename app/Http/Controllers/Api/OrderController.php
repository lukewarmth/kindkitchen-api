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
        return $request->user()
            ->orders()
            ->with([
                'items.dailyMenu.soup', 
                'items.dailyMenu.entreeA', 
                'items.dailyMenu.entreeB', 
                'items.dailyMenu.dessert'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Place a new order.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'donate_meal' => 'required|boolean',
        'consent_unclaimed_donation' => 'required|boolean',
        'items' => 'required|array',
        'items.*.daily_menu_id' => 'required|exists:daily_menus,id',
        'items.*.item_type' => 'required|string'
    ]);

    // Create the main Order
    $order = $request->user()->orders()->create([
        'donate_meal' => $validated['donate_meal'],
        'consent_unclaimed_donation' => $validated['consent_unclaimed_donation'],
    ]);

    // Save EACH item selected
    foreach ($validated['items'] as $item) {
        $order->items()->create([
            'daily_menu_id' => $item['daily_menu_id'],
            'item_type' => $item['item_type'],
        ]);
    }

    return response()->json($order->load('items'), 201);
    }
}
