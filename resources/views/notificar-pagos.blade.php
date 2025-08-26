@extends('app')
@section('title','Mi Sucursal - Notificar Pagos')

@push('head')
  {{-- Icon fonts que usa esta página --}}
  <link href="{{ asset('lib/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/typicons.font/typicons.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/flag-icon-css/css/flag-icon.min.css') }}" rel="stylesheet">

  <style>
    /* comunes */
    .az-profile-menu .dropdown-menu .az-header-profile{ text-align:center;padding:16px 0 8px;border-bottom:1px solid #edf2f9;margin-bottom:8px }
    .az-profile-menu .dropdown-menu .az-header-profile span{ display:block;font-size:12px;color:#6c757d }
    .az-footer .container{display:flex;flex-direction:column;gap:6px}
    @media(min-width:576px){.az-footer .container{flex-direction:row;justify-content:space-between;align-items:center}}

    /* bloque de notificación (mock) */
    .np-wrap{max-width:720px;margin:0 auto}
    .np-tip{font-style:italic;color:#6c757d}
    .np-format{display:flex;align-items:center;justify-content:space-between;border:2px solid #d3d7e0;border-radius:12px;padding:16px 18px;margin:16px 0}
    .np-upload{display:flex;align-items:center;gap:10px;justify-content:center;border:2px solid #d3d7e0;border-radius:12px;padding:14px 16px;font-weight:700;cursor:pointer}
    .np-upload input{display:none}
    .np-small{font-size:.95rem}
  </style>
@endpush

@section('content')
  <div class="az-content az-content-dashboard">
    <div class="container">
      <div class="az-content-body">

        {{-- Tarjeta principal --}}
        <div class="card">
          <div class="card-header">
            <h6 class="card-title">Notificar Pagos</h6>
            <p class="card-text">Selecciona el archivo de nómina en formato CSV o Excel y súbelo para procesar todos los pagos de forma segura.</p>
          </div>

          <div class="card-body np-wrap">
            <div class="np-format">
              <div><strong>Formato de archivo<br>nómina de pagos</strong></div>
              <a href="#" class="btn btn-outline-secondary">DESCARGAR</a>
            </div>

            <p class="np-tip"><strong>Tip:</strong> Verifica que tu archivo cumpla con el formato requerido para evitar retrasos.</p>

            <label class="np-upload">
              <input type="file" id="archivoNomina" accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
              <span>CARGAR NÓMINA DE PAGOS</span>
              <i class="far fa-paperclip"></i>
            </label>
            <div id="np-file-name" class="tx-gray-700 mg-t-10"></div>
          </div>
        </div>

      </div><!-- az-content-body -->
    </div><!-- container -->
  </div><!-- az-content -->
@endsection

@push('scripts')
  <script>
    (function(){
      var input = document.getElementById('archivoNomina');
      if(input){
        input.addEventListener('change', function(){
          var el = document.getElementById('np-file-name');
          el.textContent = (this.files && this.files.length)
            ? ('Archivo seleccionado: ' + this.files[0].name)
            : '';
        });
      }
    })();
  </script>
@endpush
