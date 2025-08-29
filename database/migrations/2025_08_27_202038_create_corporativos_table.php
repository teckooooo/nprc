<?php
// database/migrations/2025_08_27_202038_create_corporativos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('corporativos', function (Blueprint $t) {
            $t->id();

            // Identificadores del corporativo
            $t->string('codigo')->nullable()->index();   // código interno (si existe)
            $t->string('giro')->nullable()->index();     // razón social / giro
            $t->string('rut')->nullable()->index();      // RUT (si viene)
            $t->string('email')->nullable()->index();    // <-- agregado

            // Slug opcional para búsquedas amigables (NULL permitido y único cuando exista)
            $t->string('slug', 191)->nullable()->unique();

            // Credenciales opcionales
            $t->string('cred_user_1')->nullable();
            $t->string('cred_pass_1')->nullable();
            $t->string('cred_user_2')->nullable();
            $t->string('cred_pass_2')->nullable();

            $t->timestamps();

            // Evita duplicados lógicos cuando ambos valores existen
            $t->unique(['codigo', 'giro'], 'corp_codigo_giro_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporativos');
    }
};
