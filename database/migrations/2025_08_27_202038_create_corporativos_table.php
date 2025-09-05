<?php
// database/migrations/2025_08_27_202038_create_corporativos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ... arriba igual
Schema::create('corporativos', function (Blueprint $t) {
    $t->id();

    $t->string('codigo')->nullable()->index();
    $t->string('giro')->nullable()->index();

    // <-- cambia a unique en vez de index
    $t->string('rut')->nullable();
    $t->unique('rut', 'corp_rut_unique');

    $t->string('email')->nullable()->index();
    $t->string('slug', 191)->nullable()->unique();

    $t->string('cred_user_1')->nullable();
    $t->string('cred_pass_1')->nullable();
    $t->string('cred_rut_1')->nullable()->index();

    $t->string('cred_user_2')->nullable();
    $t->string('cred_pass_2')->nullable();
    $t->string('cred_rut_2')->nullable()->index();

    $t->timestamps();

    // ya estaba y está bien aquí
    $t->unique(['codigo','giro'], 'corp_codigo_giro_unique');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('corporativos');
    }
};
