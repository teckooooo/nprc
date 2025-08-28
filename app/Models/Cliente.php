<?php
// app/Models/Cliente.php
// app/Models/Cliente.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'corporativo_id','sucursal_id','codigo_externo','correlativo_abonado',
        'rut','nombres','paterno','materno','email','telefono1','telefono2',
        'direccion_comercial','telefono_comercial1','telefono_comercial2',
        'fax_comercial','email_comercial','plan','nacionalidad','sexo',
        'fecha_nacimiento','giro','tipo_cliente','extra'
    ];
    protected $casts = ['extra' => 'array','fecha_nacimiento' => 'date'];

    public function corporativo(){ return $this->belongsTo(Corporativo::class); }
    public function sucursal(){ return $this->belongsTo(Sucursal::class); }
}
