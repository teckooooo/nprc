<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Corporativo extends Model
{
    protected $fillable = [
        'codigo', 'giro', 'rut',
        'cred_user_1','cred_pass_1','cred_user_2','cred_pass_2',
    ];

    // al final de la clase
public function sucursales()
{
    return $this->belongsToMany(Sucursal::class, 'corporativo_sucursal')->withTimestamps();
}

}
