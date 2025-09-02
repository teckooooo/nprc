<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HistorialPagosController extends Controller
{
    public function index(Request $request)
    {
        $corp = Auth::guard('corporativos')->user();
        if (!$corp) abort(401);

        // 1) RUT + sucursal
        $rut            = $corp->rut ?? null;
        $sucursalCodigo = optional($corp->sucursal)->codigo;

        if (!$rut || !$sucursalCodigo) {
            $c = Cliente::with('sucursal:id,codigo')
                ->where('corporativo_id', $corp->id)
                ->first();
            if ($c) {
                $rut            = $rut            ?: ($c->rut ?? null);
                $sucursalCodigo = $sucursalCodigo ?: optional($c->sucursal)->codigo;
            }
        }

        $mes  = (int) $request->query('mes', 0);
        $anio = (int) $request->query('anio', 0);

        $rows  = [];
        $error = null;
        $debug = null;

        if ($rut && $sucursalCodigo) {
            // 2) Base de API desde config
            $base = trim((string) config('services.nprc_api.base', ''));
            if ($base === '') {
                return view('historial-pagos', [
                    'rows'           => [],
                    'error'          => 'No hay URL de API configurada (services.nprc_api.base).',
                    'sucursalCodigo' => $sucursalCodigo,
                    'rut'            => $rut,
                    'mes'            => $mes ?: null,
                    'anio'           => $anio ?: null,
                    'debug'          => null,
                ]);
            }

            // Asegurar que apunte a api_mia.php
            $legacyUrl = $this->ensureApiMiaUrl($base);

            // Query para nominaFacturacion (la que te funciona hoy)
            $q = [
                'action'   => 'nominaFacturacion',
                'sucursal' => $sucursalCodigo,
                'rut'      => $rut,
            ];
            if ($mes  >= 1 && $mes <= 12) $q['mes']  = $mes;
            if ($anio >= 2000)            $q['anio'] = $anio;

            // 3) HTTP client
            $http = Http::withOptions([
                'connect_timeout' => 6,
                'verify'          => false, // igual que venías usando
            ])->timeout(25)->retry(1, 1500);

            // 4) Llamada
            $resp = null; $payload = null;
            try { $resp = $http->get($legacyUrl, $q); } catch (\Throwable $e) {}

            if ($resp) {
                $raw  = (string) $resp->body();
                $ct   = (string) $resp->header('content-type');
                $st   = (int) $resp->status();

                $debug = [
                    'url'          => $legacyUrl,
                    'query'        => $q,
                    'status'       => $st,
                    'content_type' => $ct,
                    'body_snippet' => Str::limit($raw, 2000),
                ];
                Log::debug('HistorialPagos.nominaFacturacion', $debug);

                // 5) Parseo seguro de JSON
                $payload = $this->safeJson($resp);
                if (!is_array($payload)) $payload = json_decode($raw, true);

                // 6) Extraer filas con tolerancia a formas
                $rows = $this->extractRows($payload);

                if (!is_array($rows)) {
                    $rows  = [];
                    $error = 'La API respondió JSON pero sin arreglo de filas reconocible.';
                }
            } else {
                $error = 'No fue posible contactar la API.';
            }
        } else {
            $error = 'No se pudo determinar RUT y/o código de sucursal del usuario.';
        }

        return view('historial-pagos', [
            'rows'           => is_array($rows) ? $rows : [],
            'error'          => $error,
            'sucursalCodigo' => $sucursalCodigo,
            'rut'            => $rut,
            'mes'            => $mes ?: null,
            'anio'           => $anio ?: null,
            'debug'          => $debug,
        ]);
    }

    /* ================= Helpers ================= */

    private function ensureApiMiaUrl(string $base): string
    {
        // si ya termina en .php, reemplaza por api_mia.php
        if (preg_match('~\.php$~i', $base)) {
            if (!preg_match('~api_mia\.php$~i', $base)) {
                return preg_replace('~[^/]+\.php$~i', 'api_mia.php', $base);
            }
            return $base;
        }
        // si es carpeta o dominio, agrega api_mia.php
        return rtrim($base, '/') . '/api_mia.php';
    }

    private function safeJson($resp): array|null
    {
        try { return $resp->json(); } catch (\Throwable $e) { return null; }
    }

    /**
     * Acepta distintos formatos y devuelve array de filas o null.
     */
    private function extractRows($payload): ?array
    {
        if (!is_array($payload)) return null;

        // 1) { ok:true, data:[…] }
        if (isset($payload['data']) && is_array($payload['data']) && $this->looksLikeRow($payload['data'])) {
            return $payload['data'];
        }

        // 2) { results: { "60": { data:{ data:[…] } } } }
        if (isset($payload['results']) && is_array($payload['results'])) {
            foreach ($payload['results'] as $suc) {
                if (is_array($suc) && isset($suc['data'])) {
                    $d = $suc['data'];
                    // puede venir como { ok:true, count:2, data:[…] }
                    if (isset($d['data']) && is_array($d['data']) && $this->looksLikeRow($d['data'])) {
                        return $d['data'];
                    }
                    // o directamente un array de filas
                    if (is_array($d) && $this->looksLikeRow($d)) {
                        return $d;
                    }
                }
            }
        }

        // 3) array plano de filas
        if ($this->looksLikeRow($payload)) {
            return $payload;
        }

        return null;
    }

    /**
     * Heurística mínima para decidir si es "arreglo de filas".
     */
    private function looksLikeRow($arr): bool
    {
        if (!is_array($arr)) return false;
        if ($arr === []) return true;
        // mirar primer elemento
        $first = reset($arr);
        return is_array($first);
    }
}
