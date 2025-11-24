<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Para MySQL, precisamos modificar a coluna enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'cashier', 'employee', 'customer') NOT NULL DEFAULT 'employee'");
        } 
        // Para outros bancos de dados, podemos usar uma abordagem diferente
        else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 20)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Para MySQL, voltamos ao enum original
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'cashier', 'employee') NOT NULL DEFAULT 'employee'");
        }
        // Para outros bancos de dados, não fazemos nada, pois o campo string pode continuar
    }
};
