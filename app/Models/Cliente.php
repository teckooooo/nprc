<?php
// app/Models/Cliente.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    protected $fillable = [
        'sucursal_id', 'correlativo_abonado', 'rut', 'nombre', 'paterno', 'materno',
        'plan', 'corporativo', 'extra'
    ];

    protected $casts = [
        'corporativo' => 'bool',
        'extra'       => 'array',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    // Conveniente: UID legible tipo "10-12345"
    public function getUidAttribute(): string
    {
        return optional($this->sucursal)->codigo . '-' . $this->correlativo_abonado;
    }
}
