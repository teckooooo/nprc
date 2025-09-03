<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use Illuminate\Database\Seeder;

class SucursalesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['codigo' => '0',  'nombre' => 'prueba',         'ip' => '192.168.31.85', 'comuna' => 'Ovalle',        'region' => 'Región de Coquimbo'],
            ['codigo' => '1',  'nombre' => 'prueba ovalle',  'ip' => '192.168.31.86', 'comuna' => 'Ovalle',        'region' => 'Región de Coquimbo'],
            ['codigo' => '10', 'nombre' => 'ovalle',         'ip' => '192.168.1.80',  'comuna' => 'Ovalle',        'region' => 'Región de Coquimbo'],
            ['codigo' => '20', 'nombre' => 'vicuna',         'ip' => '192.168.21.80', 'comuna' => 'Vicuña',        'region' => 'Región de Coquimbo'],
            ['codigo' => '30', 'nombre' => 'monte patria',   'ip' => '192.168.31.80', 'comuna' => 'Monte Patria',  'region' => 'Región de Coquimbo'],
            ['codigo' => '40', 'nombre' => 'combarbala',     'ip' => '192.168.41.80', 'comuna' => 'Combarbalá',    'region' => 'Región de Coquimbo'],
            ['codigo' => '50', 'nombre' => 'salamanca',      'ip' => '192.168.51.80', 'comuna' => 'Salamanca',     'region' => 'Región de Coquimbo'],
            ['codigo' => '60', 'nombre' => 'illapel',        'ip' => '192.168.61.80', 'comuna' => 'Illapel',       'region' => 'Región de Coquimbo'],
            ['codigo' => '80', 'nombre' => 'Punta Arenas',   'ip' => '192.168.31.86', 'comuna' => 'Punta Arenas',  'region' => 'Región de Magallanes y de la Antártica Chilena'],
        ];

        foreach ($data as $s) {
            Sucursal::updateOrCreate(['codigo' => $s['codigo']], $s);
        }
    }
}
