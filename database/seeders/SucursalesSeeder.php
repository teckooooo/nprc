<?php
// database/seeders/SucursalesSeeder.php
use App\Models\Sucursal;
use Illuminate\Database\Seeder;

class SucursalesSeeder extends Seeder {
    public function run(): void {
        $data = [
            ['codigo'=>'0',  'nombre'=>'prueba',          'ip'=>'192.168.31.85'],
            ['codigo'=>'1',  'nombre'=>'prueba ovalle',   'ip'=>'192.168.31.86'],
            ['codigo'=>'10', 'nombre'=>'ovalle',          'ip'=>'192.168.1.80'],
            ['codigo'=>'20', 'nombre'=>'vicuna',          'ip'=>'192.168.21.80'],
            ['codigo'=>'30', 'nombre'=>'monte patria',    'ip'=>'192.168.31.80'],
            ['codigo'=>'40', 'nombre'=>'combarbala',      'ip'=>'192.168.41.80'],
            ['codigo'=>'50', 'nombre'=>'salamanca',       'ip'=>'192.168.51.80'],
            ['codigo'=>'60', 'nombre'=>'(sin nombre)',    'ip'=>'192.168.61.80'],
            ['codigo'=>'80', 'nombre'=>'Punta Arenas',    'ip'=>'192.168.31.86'],
        ];
        foreach ($data as $s) { Sucursal::updateOrCreate(['codigo'=>$s['codigo']], $s); }
    }
}
