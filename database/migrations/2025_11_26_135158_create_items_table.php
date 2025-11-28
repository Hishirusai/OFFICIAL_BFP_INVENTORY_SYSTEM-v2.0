<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            // Link to Station
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            
            // Item Details
            $table->string('product_code'); // The main identifier
            $table->string('name');
            $table->string('type'); // e.g., "PPE", "Hose", "Nozzle"
            $table->text('description')->nullable();
            
            // Numbers
            $table->integer('quantity')->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            
            // Dates & Status
            $table->date('date_acquired')->nullable();
            $table->date('date_expiry')->nullable();
            $table->string('condition')->default('Serviceable'); // Serviceable, Unserviceable, BER
            
            $table->softDeletes(); // Allows "Restore" function
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};