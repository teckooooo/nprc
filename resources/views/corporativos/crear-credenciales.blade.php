@extends('app')
@section('title','Crear Credenciales de Corporativo')

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100">
  <div class="col-lg-6 col-md-8">
    <div class="card shadow-sm w-100">
      <div class="card-header">
        <h6 class="mb-0">Crear/Actualizar Credenciales para Corporativo</h6>
      </div>
      <div class="card-body">

        @if (session('ok'))
          <div class="alert alert-success">{{ session('ok') }}</div>
        @endif
        @if (session('error'))
          <div class="alert alert-warning">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="post" action="{{ route('corporativos.credenciales.store') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label">RUT Corporativo</label>
            <input type="text" name="rut_corporativo" class="form-control" value="{{ old('rut_corporativo') }}" required>
          </div>

          <hr class="my-4">

          <h6 class="mb-3">Primer Par de Credenciales (Obligatorio)</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Correo del usuario</label>
              <input type="email" name="user_email_1" class="form-control" value="{{ old('user_email_1') }}" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">RUT del usuario</label>
              <input type="text" name="user_rut_1" class="form-control" value="{{ old('user_rut_1') }}" required>
            </div>
            <div class="col-md-3">
  <label class="form-label">Contraseña</label>
  <input type="password" name="user_pass_1" class="form-control" required minlength="6" placeholder="Mínimo 6 caracteres">
  <small class="text-muted">Mínimo 6 caracteres.</small>
  @error('user_pass_1')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
          </div>


          <hr class="my-4">

          <div class="d-flex align-items-center mb-2">
            <h6 class="mb-0">Segundo Par de Credenciales (Opcional)</h6>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Correo del usuario</label>
              <input type="email" name="user_email_2" class="form-control" value="{{ old('user_email_2') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">RUT del usuario</label>
              <input type="text" name="user_rut_2" class="form-control" value="{{ old('user_rut_2') }}">
            </div>
<div class="col-md-3">
  <label class="form-label">Contraseña</label>
  <input type="password" name="user_pass_2" class="form-control" minlength="6" placeholder="Mínimo 6 caracteres">
  <small class="text-muted">Mínimo 6 caracteres (si la ingresas).</small>
  @error('user_pass_2')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

          </div>

          <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary" type="submit">
              <i class="fas fa-save me-1"></i> Guardar credenciales
            </button>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Volver</a>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
@endsection
