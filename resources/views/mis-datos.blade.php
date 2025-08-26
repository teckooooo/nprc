@extends('app')
@section('title','Mi Sucursal - Mis Datos')

@push('head')
  {{-- Icon fonts que usa esta página --}}
  <link href="{{ asset('lib/typicons.font/typicons.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/flag-icon-css/css/flag-icon.min.css') }}" rel="stylesheet">
  <style>
    .az-profile-menu .dropdown-menu .az-header-profile{ text-align:center;padding:16px 0 8px;border-bottom:1px solid #edf2f9;margin-bottom:8px}
    .az-profile-menu .dropdown-menu .az-header-profile h6{ margin:8px 0 2px}
    .az-profile-menu .dropdown-menu .az-header-profile span{ display:block;font-size:12px;color:#6c757d}
    .az-footer .container{display:flex;flex-direction:column;gap:6px}
    @media(min-width:576px){.az-footer .container{flex-direction:row;justify-content:space-between;align-items:center}}
    .datos-wrap{display:grid;grid-template-columns:1fr;gap:24px}
    @media(min-width:768px){.datos-wrap{grid-template-columns:1fr 1fr}}
    .datos-card h5{display:flex;align-items:center;gap:8px;margin-bottom:12px;font-weight:700;letter-spacing:.3px}
    .datos-box{border:2px solid #d3d7e0;border-radius:12px;padding:16px 18px}
    .datos-box ul{margin:0;padding-left:18px}.datos-box li{margin:8px 0}
    .icon-empresa{color:#3b3b3b;font-size:22px}.icon-usuario{color:#2c3e50;font-size:22px}
    .edit-inline{font-size:14px;color:#6c757d;margin-left:6px;vertical-align:middle}
  </style>
@endpush

@section('content')
  <div class="az-content az-content-dashboard">
    <div class="container">
      <div class="az-content-body">
        <div class="row row-sm mg-b-20">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <div class="datos-wrap">

                  <div class="datos-card">
                    <h5><i class="typcn typcn-clipboard icon-empresa"></i> DATOS EMPRESA</h5>
                    <div class="datos-box">
                      <ul>
                        <li>RUT</li>
                        <li>Dirección Tributaria</li>
                        <li>Comuna</li>
                        <li>Región</li>
                      </ul>
                    </div>
                  </div>

                  <div class="datos-card">
                    <h5><i class="typcn typcn-user-outline icon-usuario"></i> DATOS USUARIO</h5>
                    <div class="datos-box">
                      <ul>
                        <li>RUT <i class="typcn typcn-edit edit-inline" title="Editar"></i></li>
                        <li>Teléfono</li>
                        <li>Correo electrónico</li>
                        <li>Contraseña</li>
                      </ul>
                    </div>
                  </div>

                </div><!-- datos-wrap -->
              </div>
            </div>
          </div>
        </div>

      </div><!-- az-content-body -->
    </div><!-- container -->
  </div><!-- az-content -->
@endsection

@push('scripts')
  {{-- Vendors opcionales si los necesitas aquí --}}
  <script src="{{ asset('lib/ionicons/ionicons.js') }}"></script>
@endpush
