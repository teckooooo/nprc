<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    // 👇 Fuerza el nombre de la tabla
    protected $table = 'sucursales';

    protected $fillable = ['nombre','codigo'. 'ip'];

    public function corporativos() {
        return $this->belongsToMany(Corporativo::class)->withTimestamps();
    }

    public function clientes() {
        return $this->hasMany(Cliente::class);
    }
}
