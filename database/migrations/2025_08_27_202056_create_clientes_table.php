<?php

// database/migrations/2025_08_22_000030_create_clientes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('clientes', function (Blueprint $t) {
            $t->id();

            $t->foreignId('corporativo_id')->constrained('corporativos')->cascadeOnDelete();
            $t->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();

            // Identificador del cliente dentro de la sucursal
            $t->unsignedBigInteger('correlativo_abonado');   // del campo Correlativo_abonado
            $t->string('plan')->nullable();                   // c.[Plan]

            // Datos personales
            $t->string('rut')->nullable()->index();           // "Rut"
            $t->string('nombres')->nullable();
            $t->string('paterno')->nullable();
            $t->string('materno')->nullable();
            $t->string('nacionalidad')->nullable();
            $t->string('sexo')->nullable();
            $t->date('fecha_nacimiento')->nullable();         // normalizada desde "25/06/1969"

            // Contacto
            $t->string('telefono1')->nullable();
            $t->string('telefono2')->nullable();
            $t->string('email')->nullable();
            $t->string('email_comercial')->nullable();
            $t->string('telefono_comercial1')->nullable();
            $t->string('telefono_comercial2')->nullable();
            $t->string('fax1')->nullable();
            $t->string('fax_comercial')->nullable();

            // Dirección / empresa / giro
            $t->string('direccion_comercial')->nullable();
            $t->string('empresa')->nullable();
            $t->string('giro')->nullable();                   // "Rep. Vehiculos" en tu ejemplo

            // Bancarios / varios
            $t->string('banco')->nullable();
            $t->string('ctacte_banco')->nullable();
            $t->string('tipo_tarjeta')->nullable();
            $t->string('numero_tarjeta')->nullable();
            $t->string('tipo_cliente')->nullable();

            $t->json('raw')->nullable();

            $t->timestamps();

            $t->unique(['sucursal_id', 'correlativo_abonado'], 'clientes_suc_correlativo_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('clientes'); }
};
