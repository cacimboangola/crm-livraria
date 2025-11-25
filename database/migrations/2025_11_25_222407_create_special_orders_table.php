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
        Schema::create('special_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Funcionário que criou
            
            // Dados do livro solicitado
            $table->string('book_title');
            $table->string('book_author')->nullable();
            $table->string('book_isbn')->nullable();
            $table->string('book_publisher')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('estimated_price', 10, 2)->nullable();
            
            // Notas e observações
            $table->text('customer_notes')->nullable(); // Observações do cliente
            $table->text('supplier_notes')->nullable(); // Notas sobre fornecedor/encomenda
            
            // Status do pedido
            $table->enum('status', ['pending', 'ordered', 'received', 'notified', 'delivered', 'cancelled'])
                  ->default('pending');
            
            // Datas de acompanhamento
            $table->timestamp('ordered_at')->nullable();   // Quando foi encomendado
            $table->timestamp('received_at')->nullable();  // Quando chegou na loja
            $table->timestamp('notified_at')->nullable();  // Quando cliente foi notificado
            $table->timestamp('delivered_at')->nullable(); // Quando foi entregue/retirado
            
            // Preferência de entrega
            $table->enum('delivery_preference', ['pickup', 'delivery'])->default('pickup');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_orders');
    }
};
