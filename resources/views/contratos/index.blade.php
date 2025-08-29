@extends('app')
@section('title','Mi Sucursal - Contratos')

@push('head')
  <link href="{{ asset('lib/typicons.font/typicons.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/azia.css') }}">
@endpush

@section('content')
<div class="az-content az-content-dashboard"><div class="container"><div class="az-content-body">

  {{-- Mensajes --}}
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="card">
    <div class="card-header">
      <h6 class="card-title">Contratos asociados a tu cuenta</h6>
      <p class="card-text">Revisa el estado y detalles de tus contratos.</p>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th style="width:140px">N° Cliente</th>
              <th>Sucursal</th>
              <th>RUT</th>
              <th style="width:140px">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($clientes as $c)
              <tr>
                <td>{{ $c->correlativo_abonado }}</td>
                <td>{{ optional($c->sucursal)->nombre ?? '—' }}</td>
                <td>{{ $c->rut ?? '—' }}</td>
                <td>
                  {{-- IMPORTANTE: pasar el ID real del cliente --}}
                  <a href="{{ route('contratos.ver', $c->id) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">Ver</a>

                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted">No hay clientes asociados.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-end">
        {{ $clientes->links() }}
      </div>
    </div>
  </div>

</div></div></div>
@endsection
