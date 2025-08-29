<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class PagosController extends Controller
{
    public function descargarFormato()
    {
        // Ruta absoluta a tu archivo fijo en /app/templates/
        $fullPath = base_path('app/templates/nomina_pagos_b2b.xlsx');

        // Verifica que exista
        if (!file_exists($fullPath)) {
            abort(404, 'El formato no está disponible por el momento.');
        }

        // Nombre amigable
        $downloadName = 'Formato_nomina_pagos_B2B.xlsx';

        return response()->download(
            $fullPath,
            $downloadName,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}
