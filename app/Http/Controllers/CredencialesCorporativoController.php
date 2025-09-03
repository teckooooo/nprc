<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCredencialesCorporativoRequest;
use App\Models\Corporativo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CredencialesCorporativoController extends Controller
{
    /** Normaliza RUT (quita puntos y guión, mayúscula K) */
    protected function rutClean(?string $rut): ?string
    {
        if ($rut === null) return null;
        $r = preg_replace('/[\.\-]/', '', trim($rut));
        return $r === '' ? null : strtoupper($r);
    }

    /** GET: formulario */
    public function create(Request $request)
    {
        return view('corporativos.crear-credenciales');
    }

    /** POST: guarda/actualiza credenciales del corporativo encontrado por RUT */
    public function store(StoreCredencialesCorporativoRequest $request)
    {
    // 1) Normaliza el rut ingresado
    $rutCorpClean = $this->rutClean($request->input('rut_corporativo')); // quita . y -
    $rutCorpKey   = ltrim($rutCorpClean, '0'); // ✅ quita ceros a la izquierda

    // 2) Busca el corporativo normalizando la columna y también quitando ceros a la izquierda
    $corporativo = Corporativo::query()
        ->whereRaw("
            TRIM(LEADING '0' FROM REPLACE(REPLACE(UPPER(rut),'.',''),'-','')) = ?
        ", [$rutCorpKey])
        ->first();

    if (!$corporativo) {
        return back()->withInput()
            ->with('error', "El RUT de corporativo ingresado no existe en la base de datos.");
    }

        // Evita sobreescribir si ya hay datos (puedes cambiar esta política)
        // Si quieres permitir sobreescritura, elimina estos checks.
        if ($corporativo->cred_user_1 || $corporativo->cred_pass_1 || $corporativo->cred_rut_1) {
            // seguimos, pero solo llenamos los campos vacíos
        }

// Guardar credencial 1 (obligatoria)
$corporativo->cred_user_1 = $request->input('user_email_1');
$corporativo->cred_pass_1 = Hash::make($request->input('user_pass_1'));
$corporativo->cred_rut_1  = $this->rutFormat($request->input('user_rut_1')); // ← aquí

// Si viene credencial 2 (opcional)
if ($request->filled(['user_email_2','user_pass_2','user_rut_2'])) {
    $corporativo->cred_user_2 = $request->input('user_email_2');
    $corporativo->cred_pass_2 = Hash::make($request->input('user_pass_2'));
    $corporativo->cred_rut_2  = $this->rutFormat($request->input('user_rut_2')); // ← aquí
}


        $corporativo->save();

        return redirect()
            ->route('corporativos.credenciales.create')
            ->with('ok', '¡Credenciales registradas/actualizadas correctamente para el corporativo '.$corporativo->codigo.'!');
    }

    /** Devuelve RUT formateado: 12.345.678-5 (o null si viene vacío) */
protected function rutFormat(?string $rut): ?string
{
    if ($rut === null) return null;

    // limpiar: quitar puntos/guión y espacios
    $r = preg_replace('/[\.\-\s]/', '', strtoupper(trim($rut)));
    if ($r === '' || strlen($r) < 2) return null;

    $dv   = substr($r, -1);         // dígito verificador (incluye K)
    $body = substr($r, 0, -1);      // parte numérica

    // agrupar miles con punto
    $bodyGrouped = strrev(implode('.', str_split(strrev($body), 3)));

    return "{$bodyGrouped}-{$dv}";
}

}
