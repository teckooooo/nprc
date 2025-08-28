<?php

// app/Models/Corporativo.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Corporativo extends Model
{
    protected $fillable = ['nombre','rut','email','telefono','extra'];
    protected $casts = ['extra' => 'array'];

    public function sucursales() {
        return $this->belongsToMany(Sucursal::class)->withTimestamps();
    }
    public function clientes() {
        return $this->hasMany(Cliente::class);
    }
    public function credentials() {
        return $this->hasMany(CorporativoCredential::class);
    }
}
