@extends('app')
@section('title','Mi Sucursal - Historial de Pagos')

@push('head')
  <link href="{{ asset('lib/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/typicons.font/typicons.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/flag-icon-css/css/flag-icon.min.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/azia.css') }}">
  <style>
    .az-profile-menu .dropdown-menu .az-header-profile{ text-align:center;padding:16px 0 8px;border-bottom:1px solid #edf2f9;margin-bottom:8px}
    .az-profile-menu .dropdown-menu .az-header-profile span{ display:block;font-size:12px;color:#6c757d}
    .az-footer .container{display:flex;flex-direction:column;gap:6px}
    @media(min-width:576px){.az-footer .container{flex-direction:row;justify-content:space-between;align-items:center}}
  </style>
@endpush

@section('content')
  <div class="az-content az-content-dashboard"><div class="container"><div class="az-content-body">
    <div class="card">
      <div class="card-header"><h6 class="card-title">Historial de Pagos</h6><p class="card-text">Comprobantes y estados de pago asociados a tus contratos.</p></div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>ID Pago</th><th>Fecha</th><th>Método</th><th>Monto</th><th>Estado</th><th>Comprobante</th></tr></thead>
            <tbody>
              <tr><td>PG-1023</td><td>05-07-2025</td><td>Transferencia</td><td>$120.000</td><td><span class="badge badge-success">Aprobado</span></td><td><a href="#" class="btn btn-sm btn-outline-secondary">Descargar</a></td></tr>
              <tr><td>PG-1022</td><td>05-06-2025</td><td>Tarjeta</td><td>$120.000</td><td><span class="badge badge-warning">Pendiente</span></td><td><a href="#" class="btn btn-sm btn-outline-secondary">Descargar</a></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div></div></div>
@endsection

@push('scripts')
  <script src="{{ asset('lib/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('lib/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('lib/ionicons/ionicons.js') }}"></script>
  <script src="{{ asset('js/azia.js') }}"></script>
@endpush
