<?php

// app/Services/ClienteSyncService.php
namespace App\Services;

use App\Models\Sucursal;
use App\Models\Corporativo;
use App\Models\Cliente;
use Carbon\Carbon;

class ClienteSyncService
{
    public function sync(array $filas, string $codigoSucursal, ?string $corpCodigo, string $corpGiro): void
    {
        // 1) Sucursal
        $sucursal = Sucursal::firstOrCreate(['codigo' => (string) $codigoSucursal]);

        // 2) Si no nos pasaron codigo/giro del corporativo, los deducimos de la primera fila
        if (empty($corpCodigo) || empty($corpGiro)) {
            $first = $filas[0] ?? [];
            $corpCodigo = $corpCodigo ?: ($first['Codigo'] ?? null);
            $corpGiro   = $corpGiro   ?: ($first['Giro']    ?? null);
        }

        // Fallbacks seguros
        $corpGiro   = $corpGiro ?: 'SIN_GIRO';
        $corpCodigo = $corpCodigo ?: null;

        // 3) Corporativo (upsert por (codigo, giro))
        $corporativo = Corporativo::updateOrCreate(
            ['codigo' => $corpCodigo, 'giro' => $corpGiro],
            ['rut' => null] // se actualizará más abajo si llega en las filas
        );

        // 4) Enlazamos con la sucursal (pivot)
        $corporativo->sucursales()->syncWithoutDetaching([$sucursal->id]);

        // 5) Preparar upsert de clientes (clave natural: sucursal_id + correlativo_abonado)
        $now  = now();
        $rows = [];

        foreach ($filas as $f) {
            $nac = $this->toDate($f['Fecha_nacimiento'] ?? null);

            // si viene un RUT del corporativo en las filas y no lo teníamos, lo actualizamos
            if (!empty($f['Rut']) && empty($corporativo->rut)) {
                $corporativo->rut = $f['Rut'];
                $corporativo->save();
            }

            $rows[] = [
                'corporativo_id'       => $corporativo->id,
                'sucursal_id'          => $sucursal->id,
                'correlativo_abonado'  => (int)($f['Correlativo_abonado'] ?? 0),

                'plan'                 => $f['Plan'] ?? null,
                'rut'                  => $f['Rut'] ?? null,
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
                'giro'                 => $f['Giro'] ?? null,

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

        Cliente::upsert(
            $rows,
            ['sucursal_id','correlativo_abonado'],
            [
                'plan','rut','nombres','paterno','materno','nacionalidad','sexo','fecha_nacimiento',
                'telefono1','telefono2','email','email_comercial','telefono_comercial1','telefono_comercial2',
                'fax1','fax_comercial','direccion_comercial','empresa','giro',
                'banco','ctacte_banco','tipo_tarjeta','numero_tarjeta','tipo_cliente',
                'raw','updated_at','corporativo_id'
            ]
        );
    }

    private function toDate(?string $dmy): ?string
    {
        if (!$dmy) return null;
        $dmy = str_replace('-', '/', $dmy);
        [$d,$m,$y] = array_pad(explode('/', $dmy), 3, null);
        if (!$d || !$m || !$y) return null;
        try {
            return Carbon::createFromFormat('d/m/Y', sprintf('%02d/%02d/%04d',(int)$d,(int)$m,(int)$y))
                         ->format('Y-m-d');
        } catch (\Throwable) { return null; }
    }
}
