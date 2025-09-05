<?php

namespace App\Services;

use App\Models\Sucursal;
use App\Models\Corporativo;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClienteSyncService
{
    /**
     * @return array{
     *   clientes: array{created:int, updated:int, skipped:int},
     *   corporativos: array{created:int, completed_rut:int}
     * }
     */
    public function sync(array $filas, string $codigoSucursal): array
    {
        $sucursal = Sucursal::firstOrCreate(['codigo' => (string) $codigoSucursal]);

        // correlativos ya existentes en esta sucursal
        $existentes = Cliente::where('sucursal_id', $sucursal->id)
            ->pluck('correlativo_abonado')
            ->all();
        $ya = array_flip($existentes); // set O(1)

        $now  = now();
        $rows = [];

        $cliCreated = 0;
        $cliUpdated = 0;   // hoy en “solo nuevos” queda 0 (lo dejo por si activas update luego)
        $cliSkipped = 0;

        $corpCreated = 0;
        $corpCompletedRut = 0;

        foreach ($filas as $f) {
            $rut    = $this->norm($f['Rut']    ?? null);
            $giro   = $this->norm($f['Giro']   ?? null);
            $codigo = $this->norm($f['Codigo'] ?? null);
            $corr   = (int)($f['Correlativo_abonado'] ?? 0);

            // Resolver corporativo sin duplicar (y contar)
            if ($rut) {
                $corp = Corporativo::firstOrCreate(
                    ['rut' => $rut],
                    ['codigo' => $codigo, 'giro' => $giro]
                );
                if ($corp->wasRecentlyCreated) $corpCreated++;
                // si existía sin RUT y ahora llegó RUT, complétalo y cuenta
                if (!$corp->wasRecentlyCreated && empty($corp->getOriginal('rut'))) {
                    $corp->rut = $rut;
                    $corp->save();
                    $corpCompletedRut++;
                }
            } elseif ($codigo || $giro) {
                $corp = Corporativo::firstOrCreate(
                    ['codigo' => $codigo, 'giro' => $giro],
                    ['rut' => null]
                );
                if ($corp->wasRecentlyCreated) $corpCreated++;
            } else {
                // sin firma, no podemos crear algo consistente
                $cliSkipped++;
                continue;
            }

            // pivot N..N por si lo usas
            $corp->sucursales()->syncWithoutDetaching([$sucursal->id]);

            // si el correlativo ya existe en la sucursal, saltar (modo “solo nuevos”)
            if (isset($ya[$corr])) {
                $cliSkipped++;
                continue;
            }

            $nac = $this->toDate($f['Fecha_nacimiento'] ?? null);

            $rows[] = [
                'corporativo_id'       => $corp->id,
                'sucursal_id'          => $sucursal->id,
                'correlativo_abonado'  => $corr,
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
                'giro'                 => $giro,

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

        if (!empty($rows)) {
            // Solo nuevos: si chocan con unique, se ignoran silenciosamente
            DB::table('clientes')->insertOrIgnore($rows);
            $cliCreated += count($rows);
        }

        return [
            'clientes' => [
                'created' => $cliCreated,
                'updated' => $cliUpdated,
                'skipped' => $cliSkipped,
            ],
            'corporativos' => [
                'created' => $corpCreated,
                'completed_rut' => $corpCompletedRut,
            ],
        ];
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
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function norm($v): ?string
    {
        if ($v === null) return null;
        $v = trim((string)$v);
        return $v === '' ? null : $v;
    }
}
