{{-- resources/views/contratos/show.blade.php --}}
@extends('app')
@section('title','Detalle contrato')

@section('content')
<div class="container">
  <div class="card">
    <div class="card-body">
      <h5 class="mb-3">Cliente: {{ $cliente->correlativo_abonado }}</h5>
      <ul class="mb-0">
        <li><strong>Sucursal:</strong> {{ optional($cliente->sucursal)->nombre ?? '—' }}</li>
        <li><strong>RUT:</strong> {{ $cliente->rut ?? '—' }}</li>
      </ul>
    </div>
  </div>
</div>
@endsection
