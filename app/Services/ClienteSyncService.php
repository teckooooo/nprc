<?php

namespace App\Services;

use App\Models\Sucursal;
use App\Models\Corporativo;
use App\Models\Cliente;
use Carbon\Carbon;

class ClienteSyncService
{
    /**
     * @param array  $filas           Filas devueltas por la API (tu SELECT actual)
     * @param string $codigoSucursal  p.ej. "10"
     */
    public function sync(array $filas, string $codigoSucursal): void
    {
        // 1) Sucursal (asegura existencia)
        $sucursal = Sucursal::firstOrCreate(['codigo' => (string) $codigoSucursal]);

        $now  = now();
        $rows = [];

        foreach ($filas as $f) {
            // ---- 1.1: Normaliza claves relevantes de la fila
            $rut    = $this->norm($f['Rut']    ?? null);
            $giro   = $this->norm($f['Giro']   ?? null);
            $codigo = $this->norm($f['Codigo'] ?? null);  // viene en tu JSON como "Codigo"

            // ---- 1.2: Resolver corporativo SIN duplicar
            // Regla: si hay RUT, dedup por RUT; si no hay RUT, dedup por (codigo,giro) normalizados.
            if ($rut) {
                $corporativo = Corporativo::firstOrCreate(
                    ['rut' => $rut],
                    [
                        'codigo' => $codigo ?: null,
                        'giro'   => $giro   ?: null,
                    ]
                );
            } elseif ($codigo || $giro) {
                // Ojo: si ambos vinieran null, no hay firma estable -> se omite la fila
                $corporativo = Corporativo::firstOrCreate(
                    ['codigo' => $codigo ?: null, 'giro' => $giro ?: null],
                    ['rut' => null]
                );
            } else {
                // Sin RUT y sin (codigo|giro) no podemos deduplicar de forma segura
                continue;
            }

            // ---- 1.3: Vincular corporativo con sucursal (pivot)
            // Evita duplicados en la tabla corporativo_sucursal
            $corporativo->sucursales()->syncWithoutDetaching([$sucursal->id]);

            // ---- 1.4: Mapear cliente (la persona/contrato de la sucursal)
            $nac = $this->toDate($f['Fecha_nacimiento'] ?? null);

            $rows[] = [
                'corporativo_id'       => $corporativo->id,
                'sucursal_id'          => $sucursal->id,
                'correlativo_abonado'  => (int)($f['Correlativo_abonado'] ?? 0),
                'plan'                 => $f['Plan'] ?? null,

                'rut'                  => $rut,
                'nombres'              => $f['Nombres'] ?? null,
                'paterno'              => $f['Paterno'] ?? null,
                'materno'              => $f['Materno'] ?? null,
                'nacionalidad'         => $f['Nacionalidad'] ?? null,
                'sexo'                 => $f['Sexo'] ?? null,
                'fecha_nacimiento'     => $nac,

                'telefono1'            => $f['Telefono1'] ?? null,
                'telefono2'            => $f['Telefono2'] ?? null,
                'email'                => $f['Email1'] ?? null,
                'email_comercial'      => $f['Email_comercial'] ?? null,
                'telefono_comercial1'  => $f['Telefono_comercial1'] ?? null,
                'telefono_comercial2'  => $f['Telefono_comercial2'] ?? null,
                'fax1'                 => $f['Fax1'] ?? null,
                'fax_comercial'        => $f['Fax_comercial'] ?? null,

                'direccion_comercial'  => $f['Direccion_comercial'] ?? null,
                'empresa'              => $f['Empresa'] ?? null,
                'giro'                 => $giro, // se guarda como lo mandó la fila

                'banco'                => $f['Banco'] ?? null,
                'ctacte_banco'         => $f['CtaCte_Banco'] ?? null,
                'tipo_tarjeta'         => $f['Tipo_tarjeta'] ?? null,
                'numero_tarjeta'       => $f['Numero_tarjeta'] ?? null,
                'tipo_cliente'         => $f['Tipo_cliente'] ?? null,

                'raw'                  => json_encode($f, JSON_UNESCAPED_UNICODE),
                'created_at'           => $now,
                'updated_at'           => $now,
            ];
        }

        // 2) Upsert idempotente por (sucursal_id, correlativo_abonado)
        if (!empty($rows)) {
            Cliente::upsert(
                $rows,
                ['sucursal_id', 'correlativo_abonado'], // clave natural en tu dominio
                [
                    'plan','rut','nombres','paterno','materno','nacionalidad','sexo','fecha_nacimiento',
                    'telefono1','telefono2','email','email_comercial','telefono_comercial1','telefono_comercial2',
                    'fax1','fax_comercial','direccion_comercial','empresa','giro',
                    'banco','ctacte_banco','tipo_tarjeta','numero_tarjeta','tipo_cliente',
                    'raw','updated_at','corporativo_id',
                ]
            );
        }
    }

    /** dd/mm/yyyy o dd-mm-yyyy -> Y-m-d */
    private function toDate(?string $dmy): ?string
    {
        if (!$dmy) return null;
        $dmy = str_replace('-', '/', $dmy);
        [$d,$m,$y] = array_pad(explode('/', $dmy), 3, null);
        if (!$d || !$m || !$y) return null;

        try {
            return Carbon::createFromFormat('d/m/Y', sprintf('%02d/%02d/%04d',(int)$d,(int)$m,(int)$y))
                ->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Normaliza: trim y null si queda vacío */
    private function norm($v): ?string
    {
        if ($v === null) return null;
        $v = trim((string)$v);
        return $v === '' ? null : $v;
    }
}
