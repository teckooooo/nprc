<?php
// database/migrations/2025_08_21_000000_create_sucursales_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();                                   // pk interna
            $table->string('codigo', 10)->unique();         // "10","20","30", etc. (texto para no perder ceros)
            $table->string('nombre');
            $table->string('ip')->nullable();               // por si quieres guardarla
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('sucursales'); }
};
