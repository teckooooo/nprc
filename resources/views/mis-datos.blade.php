{{-- resources/views/mis-datos.blade.php --}}
@extends('app')
@section('title','Mi Sucursal - Mis Datos')

@push('head')
  <link href="{{ asset('lib/typicons.font/typicons.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/flag-icon-css/css/flag-icon.min.css') }}" rel="stylesheet">
  <style>
    .datos-wrap{display:grid;grid-template-columns:1fr;gap:24px}
    @media(min-width:768px){.datos-wrap{grid-template-columns:1fr 1fr}}
    .datos-card h5{display:flex;align-items:center;gap:8px;margin-bottom:12px;font-weight:700}
    .datos-box{border:2px solid #d3d7e0;border-radius:12px;padding:16px 18px}
    .datos-box ul{margin:0;padding-left:18px}.datos-box li{margin:8px 0}
    .icon-empresa{color:#3b3b3b;font-size:22px}.icon-usuario{color:#2c3e50;font-size:22px}
    .muted{color:#6c757d}
  </style>
@endpush

@section('content')
  <div class="az-content az-content-dashboard">
    <div class="container">
      <div class="az-content-body">
        <div class="row row-sm mg-b-20">
          <div class="col-lg-12">
            <div class="card"><div class="card-body">

              <div class="datos-wrap">

{{-- DATOS EMPRESA --}}
<li><strong>RUT:</strong> <span class="muted">{{ $corp->rut ?: '—' }}</span></li>
<li><strong>Dirección Tributaria:</strong> <span class="muted">{{ optional($cliente)->direccion_comercial ?: '—' }}</span></li>
<li><strong>Comuna:</strong> <span class="muted">{{ optional($sucursal)->comuna ?: '—' }}</span></li>
<li><strong>Región:</strong> <span class="muted">{{ optional($sucursal)->region ?: '—' }}</span></li>

{{-- DATOS USUARIO (representante) --}}
<li><strong>RUT:</strong> <span class="muted">{{ optional($cliente)->rut ?: ($corp->rut ?: '—') }}</span></li>
<li><strong>Teléfono:</strong> <span class="muted">{{ optional($cliente)->telefono1 ?: '—' }}</span></li>
<li><strong>Correo electrónico:</strong>
  <span class="muted">
    {{ optional($cliente)->email ?: ($corp->email ?: ($corp->cred_user_1 ?: ($corp->cred_user_2 ?: '—'))) }}
  </span>
</li>
<li>
  <strong>Contraseña:</strong>
  <span class="muted">{{ optional($cliente)->password ?? ($corp->cred_pass_1 ?? $corp->cred_pass_2 ?? '—') }}</span>
</li>


              </div>

            </div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('lib/ionicons/ionicons.js') }}"></script>
@endpush
