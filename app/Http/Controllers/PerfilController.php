<?php
// app/Http/Controllers/PerfilController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Corporativo;

class PerfilController extends Controller
{
    public function misDatos()
    {
        $id = Auth::guard('corporativos')->id();
        if (!$id) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        // Trae clientes (para dirección/telefono/email) y sucursales (para comuna/region)
        $corp = Corporativo::with([
                'clientes' => function ($q) {
                    $q->select('id','corporativo_id','rut','telefono1','email','direccion_comercial');
                },
                'sucursales' => function ($q) {
                    // selecciona campos usados
                    $q->select('sucursales.id','nombre','comuna','region');
                },
        ])->findOrFail($id);

        $cliente  = $corp->clientes->first();      // representante
        $sucursal = $corp->sucursales->first();    // sucursal principal (ajusta criterio si quieres)

        return view('mis-datos', compact('corp','cliente','sucursal'));
    }
}
