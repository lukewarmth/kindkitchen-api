<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    /**
     * Display a listing of all menu items.
     */
    public function index()
    {
        // Return all items, ordered by type
        return MenuItem::orderBy('type')->orderBy('name')->get();
    }

    /**
     * Store a new menu item in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate the data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:soup,entree,dessert', // Ensures type is one of these
        ]);

        // 2. Create and save the item
        $menuItem = MenuItem::create($validatedData);

        // 3. Return the new item with a 201 (Created) status
        return response()->json($menuItem, 201);
    }

    /**
     * Remove the specified menu item from storage.
     */
    public function destroy(MenuItem $menuItem)
    {
        // Delete the menu item
        $menuItem->delete();
    }
}