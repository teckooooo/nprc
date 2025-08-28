<?php
// app/Services/ApiCorporativos.php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiCorporativos
{
    public function fetch(array $params = []): array
    {
        $res = Http::withHeaders(config('corporativos.headers'))
            ->timeout((int)config('corporativos.timeout'))
            ->get(config('corporativos.api_url'), $params);

        if (!$res->ok()) {
            throw new \RuntimeException('Error API corporativos: '.$res->status());
        }

        $json = $res->json();
        // esperamos estructura: { ok: true, data: { rows: [...] } } ó similar
        return data_get($json, 'data.rows', []);
    }
}
