<?php
// app/Models/Corporativo.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Corporativo extends Authenticatable
{
    use Notifiable;

    protected $table = 'corporativos';

    protected $fillable = [
        'nombre','rut','email','codigo','giro','slug',
        'cred_user_1','cred_pass_1','cred_user_2','cred_pass_2',
    ];

    // oculta los hashes
    protected $hidden = ['cred_pass_1','cred_pass_2','remember_token'];

    // si vas a guardar las contraseñas con Hash::make()
    protected $casts = [
        'cred_pass_1' => 'hashed',
        'cred_pass_2' => 'hashed',
    ];

    /**
     * Por defecto Laravel busca un atributo "password".
     * Indicamos qué campo devolver como contraseña para Auth.
     * Si quieres probar primero con cred_pass_1:
     */
    public function getAuthPassword()
    {
        return $this->cred_pass_1;
    }
}
