<?php
// app/Models/Sucursal.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = ['codigo','nombre','ip','direccion_tributaria','comuna','region','telefono','email'];

    public function corporativos()
    {
        return $this->belongsToMany(Corporativo::class, 'corporativo_sucursal')
                    ->withTimestamps(); // quítalo si tu pivot no tiene timestamps
    }
}
