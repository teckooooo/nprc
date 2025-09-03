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
            $t->string('codigo')->nullable()->index();
            $t->string('giro')->nullable()->index();
            $t->string('rut')->nullable()->index();
            $t->string('email')->nullable()->index();

            // Slug opcional
            $t->string('slug', 191)->nullable()->unique();

            // ===== Par de credenciales #1 =====
            $t->string('cred_user_1')->nullable();
            $t->string('cred_pass_1')->nullable();
            $t->string('cred_rut_1')->nullable()->index(); // ← RUT asociado al user_1

            // ===== Par de credenciales #2 =====
            $t->string('cred_user_2')->nullable();
            $t->string('cred_pass_2')->nullable();
            $t->string('cred_rut_2')->nullable()->index(); // ← RUT asociado al user_2

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
