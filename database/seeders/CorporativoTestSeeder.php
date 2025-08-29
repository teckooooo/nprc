<?php
// database/seeders/CorporativoTestSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Corporativo;
use App\Models\Sucursal;
use App\Models\Cliente;

class CorporativoTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Sucursal 0
        $sucursal = Sucursal::firstOrCreate(
            ['codigo' => '0'],
            ['nombre' => 'Prueba', 'ip' => '192.168.31.85']
        );

        // 2) Corporativo con dos pares de credenciales
        $corp = Corporativo::updateOrCreate(
            ['codigo' => 'TEST001'],
            [
                'giro'  => 'Corporativo de Prueba',
                'rut'   => '11.111.111-1',
                'email' => 'contacto@prueba.com',
                'slug'  => 'corp-prueba',

                // Primer par de credenciales
                'cred_user_1' => 'testuser1@test.com',
                'cred_pass_1' => '123456', 

                // Segundo par de credenciales
                'cred_user_2' => 'testuser2@test.com',
                'cred_pass_2' => 'abcdef', 
            ]
        );

        // 3) Relación corporativo <-> sucursal
        $corp->sucursales()->syncWithoutDetaching([$sucursal->id]);

        // 4) Dos clientes de prueba
        Cliente::updateOrCreate(
            [
                'sucursal_id' => $sucursal->id,
                'correlativo_abonado' => 1001,
            ],
            [
                'corporativo_id' => $corp->id,
                'plan' => 'Plan Básico',
                'rut' => '22.222.222-2',
                'nombres' => 'Juan',
                'paterno' => 'Pérez',
                'materno' => 'González',
                'email' => 'juan.perez@example.com',
            ]
        );

        Cliente::updateOrCreate(
            [
                'sucursal_id' => $sucursal->id,
                'correlativo_abonado' => 1002,
            ],
            [
                'corporativo_id' => $corp->id,
                'plan' => 'Plan Premium',
                'rut' => '33.333.333-3',
                'nombres' => 'María',
                'paterno' => 'López',
                'materno' => 'Ramírez',
                'email' => 'maria.lopez@example.com',
            ]
        );
    }
}
