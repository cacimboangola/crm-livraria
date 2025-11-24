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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // email, sms, desconto, evento, etc.
            $table->text('description')->nullable();
            $table->text('content'); // conteúdo da campanha (mensagem, oferta, etc.)
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('status')->default('draft'); // draft, active, completed, cancelled
            $table->json('target_criteria')->nullable(); // critérios para segmentação de clientes
            $table->json('metrics')->nullable(); // métricas de desempenho da campanha
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Tabela pivot para relacionar campanhas com clientes
        Schema::create('campaign_customer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->boolean('sent')->default(false);
            $table->dateTime('sent_at')->nullable();
            $table->boolean('opened')->default(false);
            $table->dateTime('opened_at')->nullable();
            $table->boolean('clicked')->default(false);
            $table->dateTime('clicked_at')->nullable();
            $table->boolean('converted')->default(false);
            $table->dateTime('converted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_customer');
        Schema::dropIfExists('campaigns');
    }
};
