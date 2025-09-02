@extends('app')

@section('content')
<div class="container-fluid"> {{-- ancho completo --}}
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h6 class="mb-0">Historial de Facturación</h6>
    </div>

    <div class="card-body p-2"> {{-- menos padding --}}
      @isset($error)
        <div class="alert alert-warning mb-3">{{ $error }}</div>
      @endisset

      <div class="table-responsive" style="max-height:75vh; overflow-y:auto;">
        <table class="table table-sm table-striped table-bordered w-100">
          <thead class="table-light">
            <tr>
              <th>Clte</th>
              <th>RUT</th>
              <th>Cliente</th>
              <th>Dirección Comercial</th>
              <th>Dir. Contrato</th>
              <th>Fono</th>
              <th>Giro</th>
              <th class="text-end">Mes</th>
              <th class="text-end">Año</th>
              <th class="text-end">FTTH</th>
              <th class="text-end">TV</th>
              <th class="text-end">Internet</th>
              <th class="text-end">Premium</th>
              <th class="text-end">Cargos</th>
              <th class="text-end">Desc.</th>
              <th class="text-end">Total</th>
            </tr>
          </thead>

          <tbody>
          @php $rows = is_iterable($rows ?? null) ? $rows : []; @endphp
          @forelse($rows as $r)
            @php
              $clte   = $r['Correlativo_abonado'] ?? $r['correlativo_abonado'] ?? $r['cliente'] ?? '—';
              $rutR   = $r['Rut'] ?? $r['rut'] ?? '—';
              $nom    = trim(($r['Nombres'] ?? '').' '.($r['Paterno'] ?? '').' '.($r['Materno'] ?? ''));
              $dirCom = $r['Direccion_comercial'] ?? '';
              $dirCt  = trim(($r['Calle'] ?? '').' '.($r['Numero_calle'] ?? '').' '.($r['Depto_casa'] ?? '').' '.($r['Villa_poblacion'] ?? ''));
              $fono   = $r['Telefono1'] ?? '';
              $giro   = $r['Giro'] ?? '';
              $mesR   = (int)($r['mes'] ?? 0);
              $anioR  = (int)($r['anio'] ?? 0);
              $tv       = (int)($r['cable'] ?? 0);
              $ftth     = 0;
              $internet = (int)($r['internet'] ?? 0);
              $premium  = (int)($r['premium'] ?? 0);
              $cargos   = (int)($r['cargo_adicional'] ?? 0);
              $desc     = (int)($r['descuento'] ?? 0);
              $total    = (int)($r['valor_230'] ?? 0);
              $fmt = fn($n) => '$'.number_format((int)$n,0,',','.');
            @endphp
            <tr>
              <td>{{ $clte }}</td>
              <td>{{ $rutR }}</td>
              <td>{{ $nom ?: '—' }}</td>
              <td>{{ $dirCom ?: '—' }}</td>
              <td>{{ $dirCt ?: '—' }}</td>
              <td>{{ $fono ?: '—' }}</td>
              <td>{{ $giro ?: '—' }}</td>
              <td class="text-end">{{ $mesR ?: '—' }}</td>
              <td class="text-end">{{ $anioR ?: '—' }}</td>
              <td class="text-end">{{ $fmt($ftth) }}</td>
              <td class="text-end">{{ $fmt($tv) }}</td>
              <td class="text-end">{{ $fmt($internet) }}</td>
              <td class="text-end">{{ $fmt($premium) }}</td>
              <td class="text-end">{{ $fmt($cargos) }}</td>
              <td class="text-end">{{ $fmt($desc) }}</td>
              <td class="text-end">{{ $fmt($total) }}</td>
            </tr>
          @empty
            <tr><td colspan="16" class="text-center text-muted">Sin movimientos para mostrar.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
