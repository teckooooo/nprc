<?php
/// database/migrations/2025_08_27_202038_create_corporativos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('corporativos', function (Blueprint $t) {
            $t->id();

            $t->string('codigo')->nullable()->index();
            $t->string('giro')->nullable()->index();
            $t->string('rut')->nullable()->index();

            // si vas a usar slug, defínelo aquí de una:
            $t->string('slug')->nullable()->unique(); // o ->index() si lo usarás solo para búsquedas

            $t->string('cred_user_1')->nullable();
            $t->string('cred_pass_1')->nullable();
            $t->string('cred_user_2')->nullable();
            $t->string('cred_pass_2')->nullable();

            $t->timestamps();

            $t->unique(['codigo', 'giro'], 'corp_codigo_giro_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporativos');
    }
};
