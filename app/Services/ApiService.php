<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiService
{
    protected Client $http;
    protected string $indexPath;

    public function __construct()
    {
        $this->indexPath = '/' . ltrim(env('REMOTE_API_INDEX_PATH', '/api_mia.php'), '/');

        $this->http = new Client([
            'base_uri' => rtrim(env('REMOTE_API_BASE_URL'), '/'),
            'timeout'  => (int) env('REMOTE_API_TIMEOUT', 15),
            'headers'  => [
                'Accept'     => 'application/json',
                'User-Agent' => 'NPRC/1.0',
            ],
            'verify' => $this->sslVerifyOption(),
        ]);
    }

    protected function sslVerifyOption()
    {
        $v = env('REMOTE_API_VERIFY', true);
        if (is_string($v)) {
            $lv = strtolower(trim($v));
            if ($lv === 'false' || $lv === '0') return false;
            if (preg_match('/[\/\\\\]/', $v) && file_exists($v)) return $v; // ruta a cacert
        }
        return filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
    }

    public function get(string $pathOrQuery, array $query = [])
    {
        // Si viene sólo query (?action=...), prepend el archivo índice
        if ($pathOrQuery !== '' && $pathOrQuery[0] === '?') {
            $uri = $this->indexPath . $pathOrQuery;
        } else {
            $uri = $pathOrQuery;
            if ($uri && $uri[0] !== '/' && strpos($uri, '?') === false) {
                $uri = '/' . $uri;
            }
        }

        try {
            $res = $this->http->get($uri, ['query' => $query]);
            $body = (string) $res->getBody();
            return json_decode($body, true) ?? ['raw' => $body];
        } catch (GuzzleException $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
