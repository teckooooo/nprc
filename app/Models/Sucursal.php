<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
     protected $table = 'sucursales';
    protected $fillable = ['codigo','nombre','ip'];

    // al final de la clase
public function corporativos()
{
    return $this->belongsToMany(Corporativo::class, 'corporativo_sucursal')->withTimestamps();
}

}
