<?php
// app/Actions/Sync/SyncCorporativosFromApi.php
namespace App\Actions\Sync;

use App\Models\Cliente;
use App\Models\Corporativo;
use App\Models\Sucursal;
use App\Services\ApiCorporativos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncCorporativosFromApi
{
    public function __construct(private ApiCorporativos $api) {}

    /**
     * Sincroniza clientes de un corporativo en una sucursal.
     *
     * @param  int         $sucursalId       Sucursal local
     * @param  int         $corporativoId    Corporativo local
     * @param  array       $apiParams        Parámetros que exige tu API (ej: ['sucursal' => '001'])
     * @return array       ['created' => x, 'updated' => y]
     */
    public function handle(int $sucursalId, int $corporativoId, array $apiParams = []): array
    {
        $sucursal = Sucursal::findOrFail($sucursalId);
        $corporativo = Corporativo::findOrFail($corporativoId);

        // Asegura el vínculo corporativo-sucursal
        $corporativo->sucursales()->syncWithoutDetaching([$sucursal->id]);

        $rows = $this->api->fetch($apiParams);

        $created = 0; $updated = 0;

        DB::transaction(function () use ($rows, $sucursal, $corporativo, &$created, &$updated) {

            foreach ($rows as $row) {
                // Normalizaciones básicas (adapta a tu realidad)
                $rut      = $this->normalizeRut(data_get($row, 'Rut'));
                $codigo   = (string) data_get($row, 'Codigo');
                $extra    = collect($row)->except([])->toArray();

                // Clave para upsert: preferimos rut; si no, código externo
                $finder = [
                    'corporativo_id' => $corporativo->id,
                    'sucursal_id'    => $sucursal->id,
                ] + ($rut ? ['rut' => $rut] : ['codigo_externo' => $codigo]);

                $payload = [
                    'codigo_externo'       => $codigo ?: null,
                    'correlativo_abonado'  => (string) data_get($row, 'Correlativo_abonado'),
                    'nombres'              => trim((string) data_get($row, 'Nombres')),
                    'paterno'              => trim((string) data_get($row, 'Paterno')),
                    'materno'              => trim((string) data_get($row, 'Materno')),
                    'email'                => (string) data_get($row, 'Email'),
                    'telefono1'            => (string) data_get($row, 'Telefono1'),
                    'telefono2'            => (string) data_get($row, 'Telefono2'),
                    'direccion_comercial'  => (string) data_get($row, 'Direccion_comercial'),
                    'telefono_comercial1'  => (string) data_get($row, 'Telefono_comercial1'),
                    'telefono_comercial2'  => (string) data_get($row, 'Telefono_comercial2'),
                    'fax_comercial'        => (string) data_get($row, 'Fax_comercial'),
                    'email_comercial'      => (string) data_get($row, 'Email_comercial'),
                    'plan'                 => (string) data_get($row, 'Plan'),
                    'nacionalidad'         => (string) data_get($row, 'Nacionalidad'),
                    'sexo'                 => (string) data_get($row, 'Sexo'),
                    'fecha_nacimiento'     => $this->parseDate((string) data_get($row, 'Fecha_nacimiento')),
                    'giro'                 => (string) data_get($row, 'Giro'),
                    'tipo_cliente'         => (string) data_get($row, 'Tipo_cliente'),
                    'extra'                => $extra,
                ];

                // upsert manual (find -> updateOrCreate) para mantener contadores
                $exists = Cliente::where($finder)->first();
                if ($exists) {
                    $exists->fill($payload);
                    $exists->save();
                    $updated++;
                } else {
                    Cliente::create($finder + $payload);
                    $created++;
                }
            }
        });

        return compact('created','updated');
    }

    private function normalizeRut(?string $rut): ?string
    {
        if (!$rut) return null;
        $rut = Str::upper(preg_replace('/[^0-9Kk]/', '', $rut));
        // Formatea 12345678K -> 12.345.678-K (opcional)
        if (strlen($rut) < 2) return $rut;
        $dv  = substr($rut, -1);
        $num = substr($rut, 0, -1);
        $num = number_format((int)$num, 0, '', '.');
        return "{$num}-{$dv}";
    }

    private function parseDate(?string $d): ?string
    {
        if (!$d) return null;
        // Acomoda a tu formato (ej: 1990-05-21)
        try {
            return \Carbon\Carbon::parse($d)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
