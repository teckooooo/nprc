<?php

namespace App\Http\Controllers;

use App\Services\ApiService;

class ClienteController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function sucursales()
    {
        $data = $this->api->get('?action=listSucursales');
        return response()->json($data);
    }

    public function corporativos($id)
    {
        $data = $this->api->get('?action=corpSucursal', [
            'sucursal' => $id,
        ]);
        return response()->json($data);
    }
}
