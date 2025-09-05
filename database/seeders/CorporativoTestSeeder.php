<?php
// database/seeders/CorporativoTestSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;   // ← importa Hash
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
            ['nombre' => 'Prueba', 'ip' => '192.168.1.80']
        );

        // 2) Corporativo con dos pares de credenciales + RUT por par (passwords hasheadas)
        $corp = Corporativo::updateOrCreate(
            ['codigo' => 'TEST001'],
            [
                'giro'  => 'Corporativo de Prueba',
                'rut'   => '0020310129-5',
                'email' => 'contacto@prueba.com',
                'slug'  => 'corp-prueba',

                // Par #1
                'cred_user_1' => 'testuser1@test.com',
                'cred_pass_1' => Hash::make('123456'),  
                'cred_rut_1'  => '12.345.678-5',

                // Par #2
                'cred_user_2' => 'testuser2@test.com',
                'cred_pass_2' => Hash::make('abcdef'),   
                'cred_rut_2'  => '9.876.543-2',
            ]
        );

        // 3) Relación corporativo <-> sucursal
        $corp->sucursales()->syncWithoutDetaching([$sucursal->id]);

        // 4) Clientes de prueba (igual que tenías)
        Cliente::updateOrCreate(
            ['sucursal_id' => $sucursal->id, 'correlativo_abonado' => 1001],
            [
                'corporativo_id' => $corp->id, 'plan' => 'Plan Básico',
                'rut' => '22.222.222-2', 'nombres' => 'Juan', 'paterno' => 'Pérez',
                'materno' => 'González', 'email' => 'juan.perez@example.com',
            ]
        );

        Cliente::updateOrCreate(
            ['sucursal_id' => $sucursal->id, 'correlativo_abonado' => 1002],
            [
                'corporativo_id' => $corp->id, 'plan' => 'Plan Premium',
                'rut' => '33.333.333-3', 'nombres' => 'María', 'paterno' => 'López',
                'materno' => 'Ramírez', 'email' => 'maria.lopez@example.com',
            ]
        );
    }
}
