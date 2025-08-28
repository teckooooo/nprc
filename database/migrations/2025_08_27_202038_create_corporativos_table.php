<?php

// database/migrations/2025_08_22_000010_create_corporativos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('corporativos', function (Blueprint $t) {
            $t->id();

            // Identificadores del corporativo (opcionales si tu API no siempre los envía)
            $t->string('codigo')->nullable()->index();   // p.ej. código interno del corporativo
            $t->string('giro')->nullable()->index();     // razón social / giro
            $t->string('rut')->nullable()->index();      // si en el futuro viene el RUT

            // Credenciales opcionales
            $t->string('cred_user_1')->nullable();
            $t->string('cred_pass_1')->nullable();
            $t->string('cred_user_2')->nullable();
            $t->string('cred_pass_2')->nullable();

            $t->timestamps();

            // Clave única lógica por (codigo, giro) cuando ambos existen.
            // Nota: en MySQL los campos NULL no violan UNIQUE; con ambos NULL
            // se permiten múltiples filas, que es el comportamiento deseado.
            $t->unique(['codigo', 'giro'], 'corp_codigo_giro_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporativos');
    }
};
