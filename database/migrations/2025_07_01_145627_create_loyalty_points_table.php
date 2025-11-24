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
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->integer('points')->default(0);
            $table->integer('points_spent')->default(0);
            $table->integer('points_expired')->default(0);
            $table->integer('current_balance')->default(0);
            $table->string('level')->default('bronze'); // bronze, silver, gold, platinum
            $table->date('level_expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('customer_id');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
