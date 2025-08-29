<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\ClienteSyncService;

class SyncCorporativos extends Command
{
    protected $signature = 'sync:corporativos
                            {--all : Sincroniza todas las sucursales}
                            {--sucursal= : ID de sucursal (10,20,60...)}';

    protected $description = 'Sincroniza clientes corporativos desde el gateway remoto (api_mia.php).';

    public function handle(ClienteSyncService $svc): int
    {
        $cfg = config('services.corp_gateway');

        $url    = rtrim($cfg['url']    ?? '', '/');
        $action =       $cfg['action'] ?? 'corpSucursal';
        $tout   = (int)($cfg['timeout']?? 25);
        $verify = (bool)($cfg['verify'] ?? true);
        $token  =       $cfg['token']  ?? null;

        if ($url === '') {
            $this->error('Falta CORP_GATEWAY_URL en .env / services.corp_gateway.url');
            return self::FAILURE;
        }

        // 1) Resolver sucursales
        if ($this->option('all')) {
            $this->info('Obteniendo sucursales desde la API remota…');

            $req = Http::withOptions(['verify' => $verify])
                       ->timeout($tout)->retry(2, 250);
            if ($token) $req = $req->withToken($token);

            $resp = $req->get($url, ['action' => 'listSucursales']);
            if (!$resp->ok()) {
                $this->error('Fallo listSucursales: HTTP '.$resp->status());
                $this->line(substr($resp->body(), 0, 600));
                return self::FAILURE;
            }

            $json = $resp->json();
            if (isset($json['sucursales']) && is_array($json['sucursales'])) {
                $sucs = array_keys($json['sucursales']);
            } elseif (isset($json['results']) && is_array($json['results'])) {
                $sucs = array_keys($json['results']);
            } else {
                $this->error('Respuesta inesperada de listSucursales.');
                $this->line(substr(json_encode($json), 0, 600));
                return self::FAILURE;
            }

            if (empty($sucs)) {
                $this->error('No se recibieron sucursales.');
                return self::FAILURE;
            }
        } else {
            $sid = trim((string)$this->option('sucursal'));
            if ($sid === '') {
                $this->error('Usa --all o --sucursal=ID (ej. 10, 20, 60).');
                return self::FAILURE;
            }
            $sucs = [$sid];
        }

        // 2) Iterar y sincronizar
        $total = 0;

        foreach ($sucs as $sid) {
            $this->line("• Sucursal {$sid} …");

            $req = Http::withOptions(['verify' => $verify])
                       ->timeout($tout)->retry(2, 300);
            if ($token) $req = $req->withToken($token);

            $resp = $req->get($url, [
                'action'   => $action,
                'sucursal' => $sid,
            ]);

            if (!$resp->ok()) {
                $this->warn("  → HTTP {$resp->status()} en sucursal {$sid}");
                continue;
            }

            $json = $resp->json();

            // Normaliza filas según posibles formas del payload
            $filas = [];
            if (isset($json['results'][$sid]['data']['rows']) && is_array($json['results'][$sid]['data']['rows'])) {
                $filas = $json['results'][$sid]['data']['rows'];
            } elseif (isset($json['data']['rows']) && is_array($json['data']['rows'])) {
                $filas = $json['data']['rows'];
            } elseif (isset($json['rows']) && is_array($json['rows'])) {
                $filas = $json['rows'];
            }

            if (!$filas) {
                $this->warn("  → sin filas para sucursal {$sid}");
                continue;
            }

            try {
                // Extrae un “giro” de la primera fila como razón social por defecto
                $corpRazon = (string)($filas[0]['Giro'] ?? "CORP {$sid}");
                $corpCodigo = null; // si luego tienes un código de corporativo, pásalo aquí

                // 🔧 Llamada correcta con 4 argumentos
                $svc->sync($filas, (string)$sid, $corpCodigo, $corpRazon);

                $this->info('  → OK: '.count($filas).' filas');
                $total += count($filas);
            } catch (\Throwable $e) {
                $this->error("  → Error guardando sucursal {$sid}: ".$e->getMessage());
            }
        }

        $this->info("Listo. Filas procesadas: {$total}");
        return self::SUCCESS;
    }
}
