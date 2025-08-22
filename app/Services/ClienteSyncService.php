<?php

namespace App\Services;

use App\Models\Sucursal;
use App\Models\Cliente;
use Illuminate\Support\Carbon;

class ClienteSyncService
{
    public static function syncCorporativos(array $filas, string $codigoSucursal): void
    {
        $suc = Sucursal::firstOrCreate(['codigo' => $codigoSucursal], [
            'nombre' => 'Sucursal '.$codigoSucursal,
        ]);

        $now  = now();
        $rows = [];

        foreach ($filas as $f) {
            $rows[] = [
                'sucursal_id'         => $suc->id,
                'correlativo_abonado' => (int)($f['Correlativo_abonado'] ?? $f['correlativo'] ?? 0),
                'rut'                 => $f['rut'] ?? null,
                'nombre'              => $f['nombre'] ?? ($f['Nombres'] ?? null),
                'paterno'             => $f['paterno'] ?? ($f['Paterno'] ?? null),
                'materno'             => $f['materno'] ?? ($f['Materno'] ?? null),
                'plan'                => $f['Plan'] ?? null,
                'corporativo'         => true,
                'extra'               => $f,          // payload crudo por si luego agregas campos
                'activo'              => true,        // si usas marcado activo/inactivo
                'last_seen_at'        => $now,
                'updated_at'          => $now,
                'created_at'          => $now,
            ];
        }

        // Inserta nuevos o actualiza existentes por (sucursal_id, correlativo_abonado)
        Cliente::upsert(
            $rows,
            ['sucursal_id', 'correlativo_abonado'],
            ['rut','nombre','paterno','materno','plan','corporativo','extra','activo','last_seen_at','updated_at']
        );
    }
}
