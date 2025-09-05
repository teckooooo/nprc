<?php

namespace App\Services;

use App\Models\Corporativo;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CorporativoSyncService
{
    public function syncSucursal($sucursal): array
    {
        $created = 0; $completedRut = 0;

        $rows = $this->tryLocalIp($sucursal);
        if (empty($rows)) $rows = $this->tryGateway($sucursal);
        if (empty($rows)) return ['created'=>0,'completed_rut'=>0];

        // correlativos ya existentes en esta sucursal
        $existentes = Cliente::where('sucursal_id', $sucursal->id)
            ->pluck('correlativo_abonado')
            ->all();
        $ya = array_flip($existentes);

        $loteClientes = [];
        $now = now();

        foreach ($rows as $row) {
            $codigo = $this->norm($row['Codigo'] ?? null);
            $giro   = $this->norm($row['Giro']   ?? null) ?? 'SIN_GIRO';
            $rut    = $this->norm($row['Rut']    ?? null);

            // Resolver/crear corporativo solo si no existe
            if ($rut) {
                $corp = Corporativo::firstOrCreate(
                    ['rut' => $rut],
                    ['codigo' => $codigo, 'giro' => $giro]
                );
                // Si existía sin RUT y ahora llegó RUT, lo completamos una vez
                if (!$corp->wasRecentlyCreated && empty($corp->rut)) {
                    $corp->rut = $rut;
                    $corp->save();
                    $completedRut++;
                }
            } else {
                $corp = Corporativo::firstOrCreate(
                    ['codigo' => $codigo, 'giro' => $giro],
                    ['rut' => null]
                );
            }

            $corr = (int)($row['Correlativo_abonado'] ?? 0);
            if (isset($ya[$corr])) {
                // cliente ya estaba, no lo traemos de nuevo
                continue;
            }

            $loteClientes[] = [
                'corporativo_id'      => $corp->id,
                'sucursal_id'         => $sucursal->id,
                'correlativo_abonado' => $corr,
                'plan'                => $row['Plan'] ?? null,
                'rut'                 => $rut,
                'nombres'             => $row['Nombres'] ?? null,
                'email'               => $row['Email1'] ?? null,
                'telefono1'           => $row['Telefono1'] ?? null,
                'raw'                 => json_encode($row, JSON_UNESCAPED_UNICODE),
                'created_at'          => $now,
                'updated_at'          => $now,
            ];
        }

        if (!empty($loteClientes)) {
            DB::table('clientes')->insertOrIgnore($loteClientes);
            $created = count($loteClientes);
        }

        return ['created'=>$created, 'completed_rut'=>$completedRut];
    }

    /**
     * Primer intento: IP local expuesto por la sucursal.
     * Devuelve array de filas o [] si falla/no hay datos.
     */
    protected function tryLocalIp($sucursal): array
    {
        if (empty($sucursal->ip)) {
            return [];
        }

        try {
            $url = "http://{$sucursal->ip}/OficinaVirtual/API_NPRC/corporativos.php";

            $resp = Http::timeout(config('corporativos.timeout'))
                        ->withOptions(['verify' => config('corporativos.verify_ssl')]) // por si luego es https con cert
                        ->get($url);

            if (!$resp->ok()) {
                Log::warning("Local IP API failed ({$sucursal->codigo}): HTTP {$resp->status()}");
                return [];
            }

            $json = $resp->json();
            $rows = $json['data']['rows'] ?? null;

            return is_array($rows) ? $rows : [];
        } catch (\Throwable $e) {
            Log::warning("Local IP API exception ({$sucursal->codigo}): {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Segundo intento: gateway público
     * Devuelve array de filas o [] si falla/no hay datos.
     */
    protected function tryGateway($sucursal): array
    {
        try {
            $params = [
                'action'   => config('corporativos.gateway_action'),
                // usa tu campo de sucursal que coincide con el gateway (p.ej. 'codigo')
                'sucursal' => $sucursal->codigo,
            ];
            if (config('corporativos.token')) {
                $params['token'] = config('corporativos.token');
            }

            $resp = Http::timeout(config('corporativos.timeout'))
                        ->withOptions(['verify' => config('corporativos.verify_ssl')])
                        ->get(config('corporativos.gateway_url'), $params);

            if (!$resp->ok()) {
                Log::error("Gateway API failed ({$sucursal->codigo}): HTTP {$resp->status()}");
                return [];
            }

            $json = $resp->json();

            // Estructura: results -> "{codigo}" -> data -> rows
            $key  = (string) $sucursal->codigo;
            $rows = $json['results'][$key]['data']['rows'] ?? null;

            // fallback a data->rows si el gateway devolviera un formato plano
            if (!$rows && isset($json['data']['rows'])) {
                $rows = $json['data']['rows'];
            }

            return is_array($rows) ? $rows : [];
        } catch (\Throwable $e) {
            Log::error("Gateway API exception ({$sucursal->codigo}): {$e->getMessage()}");
            return [];
        }
    }

    private function norm($v): ?string
    {
        if ($v === null) return null;
        $v = trim((string)$v);
        return $v === '' ? null : $v;
    }
}
