<?php
// database/migrations/2025_08_21_000100_create_clientes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id(); // pk interna
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->unsignedBigInteger('correlativo_abonado');  // el de la sucursal
            $table->string('rut')->nullable();
            $table->string('nombre')->nullable();
            $table->string('paterno')->nullable();
            $table->string('materno')->nullable();
            $table->string('plan')->nullable();                 // opcional si lo traes aquí
            $table->boolean('corporativo')->default(false);     // flag útil para tus consultas
            $table->json('extra')->nullable();                  // por si quieres guardar payload crudo
            $table->timestamps();

            // Unicidad por sucursal: ¡aquí está la protección!
            $table->unique(['sucursal_id','correlativo_abonado'], 'clientes_sucursal_correlativo_unique');

            // Índices útiles de búsqueda
            $table->index('rut');
            $table->index(['corporativo']);
        });
    }
    public function down(): void { Schema::dropIfExists('clientes'); }
};
