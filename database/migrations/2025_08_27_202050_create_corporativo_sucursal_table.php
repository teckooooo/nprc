<?php
// database/migrations/2025_08_22_000020_create_corporativo_sucursal_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('corporativo_sucursal', function (Blueprint $t) {
            $t->id();
            $t->foreignId('corporativo_id')->constrained('corporativos')->cascadeOnDelete();
            $t->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $t->timestamps();

            $t->unique(['corporativo_id','sucursal_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('corporativo_sucursal'); }
};
