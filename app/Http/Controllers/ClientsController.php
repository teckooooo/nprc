<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ClientsController extends Controller
{
    public function getClientByRut(Request $request)
    {
        // Obtener los parámetros de la URL
        $sucursal = $request->input('sucursal');  // Ej: Sucursal 10
        $rut = $request->input('rut');            // Ej: RUT del cliente

        // Verificar si los parámetros están presentes
        if (!$sucursal || !$rut) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }

        // Construir la URL para la API remota
        $url = env('REMOTE_API_BASE_URL') . "/listen.php?getCliByRut=true&sucursal={$sucursal}&rut={$rut}";

        try {
            // Realizar la solicitud GET a la API remota sin verificar el SSL (para evitar errores de certificados)
            $response = Http::withoutVerifying()->get($url);

            // Verificar si la solicitud fue exitosa
            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $response->json(),
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se pudo obtener los datos del cliente desde la API',
                ], 500);
            }
        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver un error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al conectarse con la API: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Método para obtener los últimos 5 clientes de una sucursal (simulando con un endpoint que devuelva clientes)
    public function getLastFiveClients(Request $request)
    {
        $sucursal = $request->input('sucursal');  // Ej: Sucursal 10

        // Verificar si el parámetro de sucursal está presente
        if (!$sucursal) {
            return response()->json(['error' => 'Falta el parámetro de sucursal'], 400);
        }

        // URL para obtener la lista de clientes de la sucursal
        $url = env('REMOTE_API_BASE_URL') . "/listen.php?getClientsBySucursal=true&sucursal={$sucursal}";

        try {
            // Realizar la solicitud GET a la API remota sin verificar el SSL
            $response = Http::withoutVerifying()->get($url);

            // Verificar si la solicitud fue exitosa
            if ($response->successful()) {
                $clientes = $response->json();

                // Ordenar los clientes por fecha de registro (suponiendo que la API remota devuelve 'fecha_registro')
                usort($clientes, function ($a, $b) {
                    return strtotime($b['fecha_registro']) - strtotime($a['fecha_registro']);
                });

                // Obtener los últimos 5 clientes
                $lastFiveClients = array_slice($clientes, 0, 5);

                return response()->json([
                    'status' => 'success',
                    'data' => $lastFiveClients,
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se pudo obtener la lista de clientes desde la API',
                ], 500);
            }
        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver un error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al conectarse con la API: ' . $e->getMessage(),
            ], 500);
        }
    }
}
