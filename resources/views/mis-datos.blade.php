@extends('app')
@section('title','Mi Sucursal - Mis Datos')

{{-- resources/views/mis-datos.blade.php (fragmento) --}}
{{-- ... cabecera/estilos que ya tenías ... --}}
@push('head')
<link href="{{ asset('lib/typicons.font/typicons.css') }}" rel="stylesheet">
<link href="{{ asset('lib/flag-icon-css/css/flag-icon.min.css') }}" rel="stylesheet">
<style>
  .datos-wrap{display:grid;grid-template-columns:1fr;gap:24px}
  @media(min-width:768px){.datos-wrap{grid-template-columns:1fr 1fr}}
  .datos-card h5{display:flex;align-items:center;gap:8px;margin-bottom:12px;font-weight:700}
  .datos-box{border:2px solid #d3d7e0;border-radius:12px;padding:16px 18px;position:relative}
  .datos-box ul{margin:0;padding-left:18px}.datos-box li{margin:8px 0}
  .icon-empresa{color:#3b3b3b;font-size:22px}.icon-usuario{color:#2c3e50;font-size:22px}
  .muted{color:#6c757d}
  .edit-btn{position:absolute;top:10px;right:12px;border:0;background:transparent;cursor:pointer}
  .edit-btn .typcn{font-size:20px;color:#6b7280}
  .edit-btn:hover .typcn{color:#111827}
  .d-none{display:none !important}
</style>
@endpush

@section('content')
<div class="az-content az-content-dashboard">
  <div class="container">
    <div class="az-content-body">

      @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
      @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif

      <div class="row row-sm mg-b-20">
        <div class="col-lg-12">
          <div class="card"><div class="card-body">

            <div class="datos-wrap">

              {{-- DATOS EMPRESA (solo lectura) --}}
              <div class="datos-card">
                <h5><i class="typcn typcn-business-card icon-empresa"></i> Datos Empresa</h5>
                <div class="datos-box">
                  <ul>
                    <li><strong>RUT:</strong> <span class="muted">{{ $corp->rut ?: '—' }}</span></li>
                    <li><strong>Dirección Tributaria:</strong> <span class="muted">{{ optional($cliente)->direccion_comercial ?: '—' }}</span></li>
                    <li><strong>Comuna:</strong> <span class="muted">{{ optional($sucursal)->comuna ?: '—' }}</span></li>
                    <li><strong>Región:</strong> <span class="muted">{{ optional($sucursal)->region ?: '—' }}</span></li>
                  </ul>
                </div>
              </div>

              {{-- DATOS USUARIO: lectura + edición al click del lápiz --}}
              <div class="datos-card" id="box-usuario">
                <h5><i class="typcn typcn-user icon-usuario"></i> Datos Usuario</h5>
                <div class="datos-box">
                  {{-- botón lápiz --}}
                  <button type="button" class="edit-btn" id="btn-editar-usuario" aria-label="Editar">
                    <i class="typcn typcn-pencil"></i>
                  </button>

                  {{-- MODO LECTURA --}}
                  <div id="usuario-view">
                    <ul>
                      <li><strong>RUT:</strong>
  <span class="muted">{{ $rutLogin ?: '—' }}</span>
</li>

                      <li><strong>Teléfono:</strong>
                        <span class="muted">{{ optional($cliente)->telefono1 ?: '—' }}</span>
                      </li>
<li><strong>Correo electrónico:</strong>
  <span class="muted">{{ $emailLogin ?: '—' }}</span>
</li>

                      <li><strong>Contraseña:</strong> <span class="muted">********</span></li>
                    </ul>
                  </div>

                  {{-- MODO EDICIÓN (oculto por defecto) --}}
                  <div id="usuario-edit" class="d-none">
                    <form method="POST" action="{{ route('perfil.usuario.update') }}" novalidate>
                      @csrf @method('PUT')

<div class="mb-3">
  <label class="form-label">RUT</label>
  <input
    type="text"
    name="rut"
    class="form-control"
    value="{{ old('rut', $rutLogin) }}"
    placeholder="Ej: 12.345.678-5">
</div>


                      <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control"
                               value="{{ old('telefono', optional($cliente)->telefono1) }}"
                               placeholder="Ej: 9 8765 4321">
                      </div>

{{-- AGREGA este bloque dentro del <form> (modo edición) --}}
<div class="mb-3">
  <label class="form-label">Correo electrónico</label>
  <input
    type="email"
    name="email"
    class="form-control"
    required
    value="{{ old('email', $emailLogin) }}"
    placeholder="correo@dominio.cl">
</div>
                      <div class="mb-3">
                        <label class="form-label">Nueva contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repite la nueva contraseña">
                      </div>

                      <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        <button type="button" class="btn btn-outline-secondary" id="btn-cancelar-usuario">Cancelar</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

            </div><!-- /.datos-wrap -->

          </div></div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('lib/ionicons/ionicons.js') }}"></script>
<script>
  (function(){
    const btnEdit = document.getElementById('btn-editar-usuario');
    const btnCancel = document.getElementById('btn-cancelar-usuario');
    const view = document.getElementById('usuario-view');
    const edit = document.getElementById('usuario-edit');

    function toEdit(){ view.classList.add('d-none'); edit.classList.remove('d-none'); }
    function toView(){ edit.classList.add('d-none'); view.classList.remove('d-none'); }

    btnEdit?.addEventListener('click', toEdit);
    btnCancel?.addEventListener('click', toView);

    // Si viene con errores de validación, abre automáticamente modo edición

  })();
</script>
@endpush
