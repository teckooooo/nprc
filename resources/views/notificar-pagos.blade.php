{{-- resources/views/notificar-pagos.blade.php --}}
@extends('app')
@section('title','Mi Sucursal - Notificar Pagos')

@push('head')
  <style>
    .np-wrap{max-width:980px;margin:0 auto}
    .np-format{display:flex;align-items:center;justify-content:space-between;border:2px solid #d3d7e0;border-radius:12px;padding:16px 18px;margin:16px 0}
  </style>
@endpush

@section('content')
<div class="az-content az-content-dashboard">
  <div class="container">
    <div class="az-content-body">

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif
      @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
      @if (session('error'))  <div class="alert alert-danger">{{ session('error') }}</div>@endif

      <div class="card np-wrap">
        <div class="card-header">
          <h6 class="card-title mb-1">Notificar Pagos</h6>
          <p class="card-text mb-0">Primero sube tu nómina. Luego selecciona el/los clientes y confirma.</p>
        </div>

        <div class="card-body">

          {{-- Descargar formato --}}
          <div class="np-format">
            <div><strong>Formato de archivo<br>nómina de pagos</strong></div>
            <a href="{{ route('pagos.descargarFormato') }}" class="btn btn-outline-success">Descargar Formato</a>
          </div>

          {{-- FORM A: subir nómina (NO se envía al elegir archivo) --}}
          <form id="formSubir" action="{{ route('pagos.procesarNomina') }}" method="POST" enctype="multipart/form-data" class="border rounded p-3 mb-3">
            @csrf
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="fw-bold mb-1">Archivo de nómina (CSV / Excel)</div>
                <div id="np-file-name" class="text-muted small">
                  {{ $temp ? 'Archivo cargado correctamente.' : 'Ningún archivo seleccionado' }}
                </div>
              </div>
              <div class="d-flex gap-2">
                <label class="mb-0">
                  <input type="file" name="nomina" id="archivoNomina"
                    accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                    style="display:none">
                  <span class="btn btn-outline-primary fw-bold">Seleccionar archivo</span>
                </label>
                <button type="submit" class="btn btn-primary">Cargar nómina</button>
              </div>
            </div>
          </form>

          {{-- FORM B: confirmar guardado (NO depende del input file) --}}
          <form id="formConfirmar" action="#" method="POST"
                onsubmit="return false;"> {{-- aquí aún no hacemos el guardado masivo --}}
            @csrf
            {{-- ejemplo de tabla; pinta tus $clientes como lo tenías --}}
            @if(isset($clientes) && count($clientes))
              <div class="table-responsive">
                <table class="table table-striped align-middle">
                  <thead>
                    <tr>
                      <th style="width:46px"><input type="checkbox" id="chkAll"></th>
                      <th>N° Cliente</th><th>RUT</th><th>Sucursal</th><th>Nombre</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($clientes as $c)
                      <tr>
                        <td><input type="checkbox" class="chkRow" name="clientes[]" value="{{ $c->id }}"></td>
                        <td>{{ $c->correlativo_abonado }}</td>
                        <td>{{ $c->rut }}</td>
                        <td>{{ optional($c->sucursal)->nombre ?? '—' }}</td>
                        <td>{{ trim(($c->nombres ?? '').' '.($c->paterno ?? '').' '.($c->materno ?? '')) }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              {{-- Paginación si aplica --}}
              @if(method_exists($clientes,'links'))
                <div class="d-flex justify-content-end">{{ $clientes->links() }}</div>
              @endif
            @else
              <div class="alert alert-info mb-0">Aún no hay clientes para mostrar. Carga una nómina primero.</div>
            @endif

            <div class="d-flex justify-content-between align-items-center mt-3">
              <div class="text-muted small"><span id="selCount">0</span> seleccionados</div>
              {{-- ESTE BOTÓN YA NO ENVÍA EL FORM A --}}
              <button type="button" class="btn btn-primary" id="btnConfirmar" {{ $temp ? '' : 'disabled' }}>
                Confirmar y guardar en NAS
              </button>
            </div>
          </form>

          {{-- guardamos el token temporal para el paso de confirmación --}}
          @if($temp)
            <input type="hidden" id="tempToken" value="{{ $temp }}">
          @endif

        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
  // Mostrar nombre del archivo seleccionado (sin enviar el form)
  const inFile = document.getElementById('archivoNomina');
  const nameEl = document.getElementById('np-file-name');
  if (inFile) {
    inFile.addEventListener('change', function(){
      nameEl.textContent = (this.files && this.files.length) ? this.files[0].name : 'Ningún archivo seleccionado';
    });
  }

  // Contador de seleccionados
  function updateCount(){
    const n = document.querySelectorAll('.chkRow:checked').length;
    document.getElementById('selCount').textContent = n;
  }
  document.getElementById('chkAll')?.addEventListener('change', e => {
    document.querySelectorAll('.chkRow').forEach(chk => chk.checked = e.target.checked);
    updateCount();
  });
  document.querySelectorAll('.chkRow').forEach(chk => chk.addEventListener('change', updateCount));

  // Confirmar: ejemplo simple (por ahora solo valida selección)
  document.getElementById('btnConfirmar')?.addEventListener('click', () => {
    const ids = Array.from(document.querySelectorAll('.chkRow:checked')).map(x => x.value);
    if (!ids.length) { alert('Selecciona al menos un cliente.'); return; }
    const temp = document.getElementById('tempToken')?.value;
    if (!temp) { alert('Primero debes cargar la nómina.'); return; }

    // Aquí puedes redirigir a tu endpoint de guardado (uno por uno o masivo).
    // Ejemplo simple (primer seleccionado):
    const first = ids[0];
    window.location.href = `/pagos/guardar/${encodeURIComponent(temp)}/${encodeURIComponent(first)}`;
  });
})();
</script>
@endpush
