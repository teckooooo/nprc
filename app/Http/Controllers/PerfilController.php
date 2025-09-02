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

        return view('mis-datos', compact('corp','cliente','sucursal'));
    }

    public function updateUsuario(Request $request)
    {
        $id   = Auth::guard('corporativos')->id();
        $corp = Corporativo::with('clientes')->findOrFail($id);
        $cliente = $corp->clientes->first();

        $request->validate([
            'telefono'  => ['nullable','string','max:40'],
            'email'     => ['required','email','max:190'],
            'password'  => ['nullable','min:8','confirmed'], // requiere password_confirmation
        ],[
            'email.required' => 'El correo es obligatorio.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // Actualiza email de acceso del corporativo
        $corp->email = $request->email;

        if ($request->filled('password')) {
            $corp->password = Hash::make($request->password);
        }
        $corp->save();

        // Sincroniza con el cliente “representante” si existe
        if ($cliente) {
            if ($request->filled('telefono')) $cliente->telefono1 = $request->telefono;
            $cliente->email = $request->email;
            $cliente->save();
        }

        return back()->with('ok','Datos de usuario actualizados correctamente.');
    }
}
