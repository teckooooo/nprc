@extends('app')
@section('title','Mi Sucursal - Login')

@push('head')
  <link href="{{ asset('lib/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/typicons.font/typicons.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/azia.css') }}">
@endpush

@section('content')
  <div class="az-signin-wrapper">
    <div class="az-card-signin">
      <img style="width:65%" src="{{ asset('img/logomi.png') }}" alt="logo">
      <div class="az-signin-header">
        <h2>Bienvenido</h2>
        <h4>Ingrese sus datos</h4>

        {{-- mensajes de error --}}
        @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
          </div>
        @endif

<form method="POST" action="{{ route('login.post') }}">
  @csrf
  <div class="form-group">
    <label>Usuario</label>
    <input type="text" class="form-control" name="usuario"
           placeholder="Usuario corporativo" value="{{ old('usuario') }}"
           required autofocus>
  </div>
  <div class="form-group">
    <label>Contraseña</label>
    <input type="password" class="form-control" name="password" required>
  </div>
  <button type="submit" class="btn btn-az-primary btn-block">Ingresar</button>
</form>



      </div>

    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('lib/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('lib/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('lib/ionicons/ionicons.js') }}"></script>
  <script src="{{ asset('js/azia.js') }}"></script>
@endpush
