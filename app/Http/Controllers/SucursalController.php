<?php
namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function store(Request $request)
    {
        // Aquí validas los datos antes de guardar
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|unique:sucursales,codigo',
            'ip'     => 'nullable|ip',  // 👈 validación de IP
        ]);

        Sucursal::create($validated);

        return redirect()->back()->with('success', 'Sucursal creada correctamente.');
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|unique:sucursales,codigo,' . $sucursal->id,
            'ip'     => 'nullable|ip',
        ]);

        $sucursal->update($validated);

        return redirect()->back()->with('success', 'Sucursal actualizada correctamente.');
    }
}
