<?php

namespace App\Services;

use App\Models\Corporativo;
use App\Models\Cliente;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CorporativoSyncService
{
    public function syncSucursal($sucursal): array
    {
        $created = 0; $updated = 0;

        $rows = $this->tryLocalIp($sucursal);
        if (empty($rows)) $rows = $this->tryGateway($sucursal);
        if (empty($rows)) return ['created'=>0,'updated'=>0];

        foreach ($rows as $row) {
            $codigo = $row['Codigo'] ?? null;
            $giro   = $row['Giro']   ?? 'SIN_GIRO';
            $rut    = $row['Rut']    ?? null;

            // Corporativo por (codigo, giro); actualiza rut si llega
            $corp = Corporativo::updateOrCreate(
                ['codigo' => $codigo, 'giro' => $giro],
                ['rut'    => $rut]
            );

            $cliente = Cliente::updateOrCreate(
                [
                    'corporativo_id' => $corp->id,
                    'sucursal_id'    => $sucursal->id,
                    'correlativo_abonado' => (int)($row['Correlativo_abonado'] ?? 0),
                ],
                [
                    'plan'     => $row['Plan'] ?? null,
                    'rut'      => $row['Rut'] ?? null,
                    'nombres'  => $row['Nombres'] ?? null,
                    'email'    => $row['Email1'] ?? null,
                    'telefono1'=> $row['Telefono1'] ?? null,
                    'raw'      => json_encode($row, JSON_UNESCAPED_UNICODE),
                ]
            );

            $cliente->wasRecentlyCreated ? $created++ : ($cliente->wasChanged() && $updated++);
        }

        return ['created'=>$created,'updated'=>$updated];
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
}
