@extends('app')
@section('title','Mi Sucursal - Soporte')

@push('head')
  <link href="{{ asset('lib/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/typicons.font/typicons.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/flag-icon-css/css/flag-icon.min.css') }}" rel="stylesheet">
  <style>
    .az-profile-menu .dropdown-menu .az-header-profile{ text-align:center;padding:16px 0 8px;border-bottom:1px solid #edf2f9;margin-bottom:8px }
    .az-profile-menu .dropdown-menu .az-header-profile span{ display:block;font-size:12px;color:#6c757d }
    .az-footer .container{display:flex;flex-direction:column;gap:6px}
    @media(min-width:576px){.az-footer .container{flex-direction:row;justify-content:space-between;align-items:center}}

    /* layout soporte */
    .sup-wrap{display:grid;grid-template-columns:1fr;gap:24px;max-width:900px;margin:0 auto}
    @media(min-width:768px){.sup-wrap{grid-template-columns:1fr 1fr}}
    .sup-box{border:2px solid #d3d7e0;border-radius:12px;padding:16px 18px}
    .sup-box h6{font-weight:700;margin-bottom:10px}
    .sup-box ul{margin:0;padding-left:18px}
    .sup-cta{display:flex;justify-content:center;margin-top:16px}
  </style>
@endpush

@section('content')
  <div class="az-content az-content-dashboard">
    <div class="container">
      <div class="az-content-body">
        <div class="card">
          <div class="card-header">
            <h6 class="card-title">Soporte</h6>
            <p class="card-text">Crea un ticket para reportar incidencias o solicitar ayuda del equipo de soporte.</p>
          </div>

          <div class="card-body">
            <div class="sup-wrap">
              <div class="sup-box">
                <h6>Ticket info</h6>
                <ul>
                  <li>ID contrato</li>
                  <li>Asunto</li>
                  <li>Descripción</li>
                </ul>
              </div>
              <div class="sup-box">
                <h6>Additional info</h6>
                <ul>
                  <li>Prioridad</li>
                  <li>Servicio afectado</li>
                  <li>Tipo de problema</li>
                  <li>Contacto</li>
                </ul>
              </div>
            </div>

            <div class="sup-cta">
              <a href="#" class="btn btn-outline-dark">CREAR TICKET</a>
            </div>
          </div>
        </div>

      </div><!-- az-content-body -->
    </div><!-- container -->
  </div><!-- az-content -->
@endsection
