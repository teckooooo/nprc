<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorporativoCredential extends Model
{
    // tabla: corporativo_credentials (por convención)
    protected $fillable = [
        'corporativo_id',
        'user_a', 'password_a',
        'user_b', 'password_b',
        'is_active',
        'meta', // si quieres guardar extra en json
    ];

    protected $casts = [
        'is_active' => 'bool',
        'meta'      => 'array',
    ];

    public function corporativo(): BelongsTo
    {
        return $this->belongsTo(Corporativo::class);
    }
}
