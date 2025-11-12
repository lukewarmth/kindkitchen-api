<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_menu_id')->constrained()->onDelete('cascade');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);

            $table->foreignId('soup_item_id')->nullable()->constrained('menu_items')->onDelete('set null');
            $table->foreignId('entree_a_item_id')->nullable()->constrained('menu_items')->onDelete('set null');
            $table->foreignId('entree_b_item_id')->nullable()->constrained('menu_items')->onDelete('set null');
            $table->foreignId('dessert_item_id')->nullable()->constrained('menu_items')->onDelete('set null');

            $table->timestamps();
            
            // Ensures we can't have two "Mondays" for the same week
            $table->unique(['weekly_menu_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_menus');
    }
};
