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
    $corpId   = Auth::guard('corporativos')->id();
    $clientes = Cliente::with('sucursal:id,nombre,codigo')
        ->where('corporativo_id', $corpId)
        ->orderBy('correlativo_abonado')
        ->paginate(20);

    return view('notificar-pagos', [
        'clientes' => $clientes,
        'temp'     => session('temp'), // ← lee el token flasheado si viene de la subida
    ]);
}

public function subirNomina(Request $request)
{
    $request->validate([
        'nomina' => ['required','file','mimes:xlsx,csv,xls','max:10240'],
    ], [], ['nomina' => 'archivo de nómina']);

    Storage::disk('local')->makeDirectory('tmp/nominas');

    $uuid  = (string) Str::uuid();
    $ext   = strtolower($request->file('nomina')->getClientOriginalExtension());
    $token = "{$uuid}.{$ext}";

    // Guarda en storage/app/tmp/nominas/{uuid}.{ext}
    $request->file('nomina')->storeAs('tmp/nominas', $token, 'local');

    // ✅ Redirige a la vista con mensajes en sesión
    return redirect()
        ->route('pagos.form')
        ->with('success', 'Archivo cargado correctamente.')
        ->with('temp', $token)
        ->with('file_name', $request->file('nomina')->getClientOriginalName());
}


    /** POST /pagos/guardar/{temp}/{clienteId} */
/** POST /pagos/guardar/{temp}/{clienteId} */
/** POST /pagos/guardar/{temp}/{clienteId} */
public function guardarEnNas(string $temp, int $clienteId)
{
    $corp = Auth::guard('corporativos')->user();
    if (!$corp) {
        return back()->with('error', 'Sesión no válida.');
    }

    // Trae el cliente con su sucursal
    $cliente = Cliente::with('sucursal:id,nombre')
        ->where('id', $clienteId)
        ->where('corporativo_id', $corp->id)
        ->first();

    if (!$cliente) {
        return back()->with('error', 'Cliente no válido para tu cuenta.');
    }

    $localTmp = storage_path('app/tmp/nominas/'.$temp);
    if (!is_file($localTmp)) {
        return back()->with('error', 'El archivo temporal no existe o expiró.');
    }

    // --- Ruta destino en NAS: /<Sucursal>/<N°Cliente> ---
    $sucursalNombre = $cliente->sucursal?->nombre ?: 'SinSucursal';
    // Permite letras, números, espacio, guion y guion_bajo (limpia otros)
    $sucursalSafe = preg_replace('/[^A-Za-z0-9 \-_]/u', '-', trim($sucursalNombre));
    $sucursalSafe = preg_replace('/\s+/', ' ', $sucursalSafe);

    // Ajusta este campo si tu “N° de cliente” es otro
    $numeroCliente = (string) ($cliente->correlativo_abonado ?? $cliente->id);

    // Estructura final: <Sucursal>/<N°Cliente>
    $destDir = trim($sucursalSafe, " \\/") . '/'
             . trim($numeroCliente, " \\/");

    // Crea directorios si no existen
    Storage::disk('nas')->makeDirectory($destDir);

    // Nombre final del archivo
    $ext       = strtolower(pathinfo($localTmp, PATHINFO_EXTENSION)) ?: 'xlsx';
    $finalName = 'nomina_' . date('Ymd_His') . '.' . $ext;

    // Copia al NAS
    $ok = Storage::disk('nas')->put($destDir.'/'.$finalName, file_get_contents($localTmp));
    if (!$ok) {
        return back()->with('error', 'No se pudo copiar el archivo al NAS.');
    }

    @unlink($localTmp);

    // ✅ Mensaje genérico (sin ruta) y nombre del archivo opcional
return back()
    ->with('success', 'Nómina guardada correctamente.');
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
