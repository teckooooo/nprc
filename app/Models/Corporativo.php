<?php
// app/Models/Corporativo.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Corporativo extends Authenticatable
{
    use Notifiable;

    protected $table = 'corporativos';

    protected $fillable = [
        'codigo','giro','rut','email','slug',
        'cred_user_1','cred_pass_1','cred_user_2','cred_pass_2',
    ];

    protected $hidden = ['cred_pass_1','cred_pass_2','remember_token'];

    // Auth tomará por defecto este campo como contraseña
    public function getAuthPassword(): string
    {
        return (string) $this->cred_pass_1;
    }

    /** Relaciones */
    /** @return BelongsToMany<Sucursal> */
    public function sucursales(): BelongsToMany
    {
        // pivot: corporativo_sucursal (corporativo_id, sucursal_id)
        return $this->belongsToMany(\App\Models\Sucursal::class, 'corporativo_sucursal');
        // ->withTimestamps(); // descomenta solo si tu pivot tiene timestamps
    }

    /** @return HasMany<Cliente> */
    public function clientes(): HasMany
    {
        return $this->hasMany(\App\Models\Cliente::class, 'corporativo_id');
    }
}
