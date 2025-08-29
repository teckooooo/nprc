<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PagosController extends Controller
{
    /** GET /notificar-pagos */
    public function form()
    {
        // Siempre lista los clientes del corporativo logueado
        $corpId   = Auth::guard('corporativos')->id();
        $clientes = Cliente::with('sucursal:id,nombre,codigo')
            ->where('corporativo_id', $corpId)
            ->orderBy('correlativo_abonado')
            ->paginate(20);

        return view('notificar-pagos', [
            'clientes' => $clientes,
            'temp'     => null,        // sin archivo temporal aún
        ]);
    }

    /** POST /pagos/subir */
    public function subirNomina(Request $request)
    {
        // El input del <input type="file"> se llama "nomina"
        $request->validate([
            'nomina' => ['required', 'file', 'mimes:xlsx,csv,xls', 'max:10240'],
        ], [], ['nomina' => 'archivo de nómina']);

        Storage::disk('local')->makeDirectory('tmp/nominas');

        $uuid  = (string) Str::uuid();
        $ext   = strtolower($request->file('nomina')->getClientOriginalExtension());
        $token = "{$uuid}.{$ext}";

        // Guarda en storage/app/tmp/nominas/{uuid}.{ext}
        $request->file('nomina')->storeAs('tmp/nominas', $token, 'local');

        // Cargamos de nuevo los clientes (siempre visibles)
        $corpId   = Auth::guard('corporativos')->id();
        $clientes = Cliente::with('sucursal:id,nombre,codigo')
            ->where('corporativo_id', $corpId)
            ->orderBy('correlativo_abonado')
            ->paginate(20);

        return view('notificar-pagos', [
            'temp'     => $token,      // ahora sí hay archivo temporal
            'clientes' => $clientes,
        ]);
    }

    /** POST /pagos/guardar/{temp}/{clienteId} */
    public function guardarEnNas(string $temp, int $clienteId)
    {
        $corp = Auth::guard('corporativos')->user();
        if (!$corp) {
            return back()->with('error', 'Sesión no válida.');
        }

        $cliente = Cliente::where('id', $clienteId)
            ->where('corporativo_id', $corp->id)
            ->first();

        if (!$cliente) {
            return back()->with('error', 'Cliente no válido para tu cuenta.');
        }

        $localTmp = storage_path('app/tmp/nominas/'.$temp);
        if (!is_file($localTmp)) {
            return back()->with('error', 'El archivo temporal no existe o expiró.');
        }

        // Estructura: {codigoCorp}/{correlativo_abonado}{sucursal_id}
        $codigoCorp = trim((string)($corp->codigo ?: ('corp_'.$corp->id)));
        $subcarpeta = (string)($cliente->correlativo_abonado . $cliente->sucursal_id);
        $destDir    = trim($codigoCorp, " \\/") . '/' . trim($subcarpeta, " \\/");

        Storage::disk('nas')->makeDirectory($destDir); // idempotente

        $ext       = strtolower(pathinfo($localTmp, PATHINFO_EXTENSION));
        $finalName = 'nomina_' . date('Ymd_His') . '.' . $ext;

        $ok = Storage::disk('nas')->put($destDir.'/'.$finalName, file_get_contents($localTmp));
        if (!$ok) {
            return back()->with('error', 'No se pudo copiar el archivo al NAS.');
        }

        @unlink($localTmp); // opcional

        return back()->with('success', "Archivo guardado en NAS: \\{$codigoCorp}\\{$subcarpeta}\\{$finalName}");
    }

    /** GET /descargar-formato */
    public function descargarFormato()
    {
        $fullPath = base_path('app/templates/nomina_pagos_b2b.xlsx');
        if (!is_file($fullPath)) {
            abort(404, 'El formato no está disponible por el momento.');
        }

        return response()->download(
            $fullPath,
            'Formato_nomina_pagos_B2B.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}
