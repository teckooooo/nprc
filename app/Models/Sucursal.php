<?php
// app/Models/Sucursal.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    protected $fillable = ['codigo','nombre','ip'];

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }
}
