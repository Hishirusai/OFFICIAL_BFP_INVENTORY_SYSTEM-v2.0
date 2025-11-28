<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Who did it
            
            // Snapshot of item details (in case item is deleted later)
            $table->string('product_code'); 
            $table->string('item_name');
            
            // Action Type: 'Created', 'Added', 'Deducted', 'Disposed', 'Transferred', 'Restored'
            $table->string('movement_type'); 
            
            // Locations
            $table->foreignId('from_station_id')->nullable()->constrained('stations');
            $table->foreignId('to_station_id')->nullable()->constrained('stations');
            
            // Math
            $table->integer('quantity_change'); // e.g., +10 or -5
            $table->integer('previous_quantity');
            $table->integer('new_quantity');
            $table->decimal('cost_impact', 15, 2)->default(0); 
            
            $table->timestamp('created_at'); // Date it happened
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};