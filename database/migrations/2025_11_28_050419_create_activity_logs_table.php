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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who did the action
            $table->string('action_type'); // e.g., 'Item Added', 'Transfer', 'Dispose'
            $table->text('details');       // e.g., "Added 5 Fire Hoses..."
            $table->timestamps();
        });
    }
};
