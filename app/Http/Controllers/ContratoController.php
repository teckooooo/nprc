<?php
namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContratoController extends Controller
{
    public function index()
    {
        $corpId = Auth::guard('corporativos')->id();

        $clientes = Cliente::with(['sucursal:id,nombre,codigo'])
            ->where('corporativo_id', $corpId)
            ->orderBy('correlativo_abonado')
            ->paginate(15);

        return view('contratos.index', compact('clientes'));
    }

    // app/Http/Controllers/ContratoController.php
public function verViaApi(\App\Models\Cliente $cliente)
{
    $cliente->loadMissing('sucursal:id,codigo');

    if (!$cliente->sucursal || !$cliente->sucursal->codigo) {
        return back()->with('error','El cliente no tiene sucursal válida.');
    }

    $apiUrl = config('services.nprc_api.base'); // viene de services.php (Opción B)
    if (!$apiUrl) {
        return back()->with('error','No se ha configurado NPRC_API_URL / CORP_GATEWAY_URL.');
    }

    $sucursal = $cliente->sucursal->codigo;      // '0','10','20',...
    $numero   = $cliente->correlativo_abonado;

    $url = rtrim($apiUrl,'/').'?action=contratoFTTH'
         .'&sucursal='.rawurlencode($sucursal)
         .'&cliente='.rawurlencode($numero);

    // modo debug opcional
    if (request()->boolean('debug')) {
        return response()->json(['url'=>$url], 200);
    }

    try {
        $resp = \Illuminate\Support\Facades\Http::withOptions([
                    'verify'  => false,
                    'timeout' => 30,
                ])->get($url);

        if (!$resp->ok()) {
            // Devuelve parte del body para diagnosticar (máx 600 chars)
            $snippet = mb_substr($resp->body(), 0, 600);
            return back()->with('error', 'La API devolvió HTTP '.$resp->status().' al consultar el contrato. '.$snippet);
        }

        $contentType = $resp->header('Content-Type','text/html; charset=UTF-8');

        return new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($resp) {
            echo $resp->body();
        }, 200, ['Content-Type' => $contentType]);

    } catch (\Throwable $e) {
        return back()->with('error','Error al contactar la API: '.$e->getMessage());
    }
}

}
