<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Corporativo;

class PerfilController extends Controller
{
public function misDatos()
{
    $id = Auth::guard('corporativos')->id();
    if (!$id) return redirect()->route('login')->with('error','Debes iniciar sesión');

    $corp = Corporativo::with([
        'clientes'   => fn($q) => $q->select('id','corporativo_id','rut','telefono1','email','direccion_comercial'),
        'sucursales' => fn($q) => $q->select('sucursales.id','nombre','comuna','region'),
    ])->findOrFail($id);

    $cliente  = $corp->clientes->first();
    $sucursal = $corp->sucursales->first();

    // --- detectar con qué usuario entró
    $authUserEmail = optional(Auth::guard('corporativos')->user())->email;
    // Si tu login usa el email del corporativo o una de las credenciales:
    $slot = null;
    if ($corp->cred_user_1 && strcasecmp($authUserEmail, $corp->cred_user_1) === 0) {
        $slot = 1;
    } elseif ($corp->cred_user_2 && strcasecmp($authUserEmail, $corp->cred_user_2) === 0) {
        $slot = 2;
    } else {
        // Fallback: si no se puede inferir, elige el que exista (prioriza 1)
        $slot = $corp->cred_user_2 ? 2 : 1;
    }

    $rutLogin   = $slot === 2 ? ($corp->cred_rut_2 ?: $corp->rut) : ($corp->cred_rut_1 ?: $corp->rut);
    $emailLogin = $slot === 2 ? ($corp->cred_user_2 ?: $corp->email) : ($corp->cred_user_1 ?: $corp->email);

    return view('mis-datos', compact('corp','cliente','sucursal','rutLogin','emailLogin','slot'));
}


    public function updateUsuario(Request $request)
{
    $id   = Auth::guard('corporativos')->id();
    $corp = Corporativo::with('clientes')->findOrFail($id);
    $cliente = $corp->clientes->first();

    // ✅ valida también el RUT (opcionalmente puedes dejarlo nullable)
    $request->validate([
        'rut'       => ['required','regex:/^[0-9.\-kK]+$/','max:20'],
        'telefono'  => ['nullable','string','max:40'],
        'email'     => ['required','email','max:190'],
        'password'  => ['nullable','min:8','confirmed'],
    ],[
        'rut.required' => 'El RUT es obligatorio.',
        'rut.regex'    => 'Formato de RUT no válido.',
        'email.required' => 'El correo es obligatorio.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
    ]);

    // ✅ detectar con qué usuario se logeó (mismo criterio que en misDatos)
    $authEmail = optional(Auth::guard('corporativos')->user())->email;
    $slot = null;
    if ($corp->cred_user_1 && strcasecmp($authEmail, $corp->cred_user_1) === 0) {
        $slot = 1;
    } elseif ($corp->cred_user_2 && strcasecmp($authEmail, $corp->cred_user_2) === 0) {
        $slot = 2;
    }

    // ✅ actualizar RUT en el campo correspondiente
    if ($request->filled('rut')) {
        $rutNuevo = strtoupper(trim($request->rut));             // opcional: normalización
        if ($slot === 1) {
            $corp->cred_rut_1 = $rutNuevo;
        } elseif ($slot === 2) {
            $corp->cred_rut_2 = $rutNuevo;
        } else {
            $corp->rut = $rutNuevo; // si entró con el email base del corporativo
        }
    }

    // email / password como ya tenías
    $corp->email = $request->email;
    if ($request->filled('password')) {
        $corp->password = Hash::make($request->password);
    }
    $corp->save();

    if ($cliente) {
        if ($request->filled('telefono')) $cliente->telefono1 = $request->telefono;
        $cliente->email = $request->email;
        $cliente->save();
    }

    return back()->with('ok','Datos de usuario actualizados correctamente.');
}

}
