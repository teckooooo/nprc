<?php
// app/Models/Cliente.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    use HasFactory;

    /** Tabla y timestamps */
    protected $table = 'clientes';
    public $timestamps = false; // tu tabla no tiene created_at / updated_at

    /** Asignación masiva */
    protected $fillable = [
        'corporativo_id','sucursal_id','codigo_externo','correlativo_abonado',
        'rut','nombres','paterno','materno','email','telefono1','telefono2',
        'direccion_comercial','telefono_comercial1','telefono_comercial2',
        'fax_comercial','email_comercial','plan','nacionalidad','sexo',
        'fecha_nacimiento','giro','tipo_cliente','extra',
    ];

    /** Casts */
    protected $casts = [
        'extra'            => 'array',
        'fecha_nacimiento' => 'date', // acceso como Carbon
    ];

    /** Relaciones */
    public function corporativo(): BelongsTo
    {
        return $this->belongsTo(Corporativo::class, 'corporativo_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Sucursal::class, 'sucursal_id');
    }

    /** Accesor útil (opcional): nombre completo */
    public function getNombreCompletoAttribute(): string
    {
        return trim(collect([$this->nombres, $this->paterno, $this->materno])->filter()->implode(' '));
    }

    /** Scope útil para filtrar por corporativo */
    public function scopeDeCorporativo($query, int $corporativoId)
    {
        return $query->where('corporativo_id', $corporativoId);
    }
}
