<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title','NPRC B2B')</title>

  <link rel="stylesheet" href="{{ asset('css/azia.css') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link href="{{ asset('lib/typicons.font/typicons.css') }}" rel="stylesheet">

<style>
  /* Menú principal */
  .az-header-menu .nav { gap: 18px; }

  .az-header-menu .nav-link{
    display:flex; align-items:center; gap:8px;
    font-weight:700; letter-spacing:.3px;
    color:#0b1535; padding:10px 4px; position:relative;
  }
  .az-header-menu .nav-link i{ font-size:18px; }

  /* Hover / foco */
  .az-header-menu .nav-link:hover { color:#3366ff; }

  /* Activa en azul + subrayado */
  .az-header-menu .nav-link.active,
  .az-header-menu .nav-link.show{
    color:#3366ff;
  }
  .az-header-menu .nav-link.active::after,
  .az-header-menu .nav-link.show::after{
    content:""; position:absolute; left:0; right:0; bottom:-8px;
    height:3px; background:#3366ff; border-radius:2px;
  }
</style>

  @stack('head')
</head>
<body>
@if (!Route::is('login'))
  {{-- NAV principal --}}
<header class="az-header shadow-sm mb-3">
  <div class="container d-flex justify-content-between align-items-center">

    {{-- Logo + hamburguesa --}}
    <div class="az-header-left">
      <img style="width:120px" src="{{ asset('img/logomi.png') }}" alt="logo">
      <a href="#" id="azMenuShow" class="az-header-menu-icon d-lg-none"><span></span></a>
    </div>

    {{-- Menú principal --}}
    <div class="az-header-menu">
      <ul class="nav">
        <li class="nav-item">
          <a href="{{ url('/mis-datos') }}" class="nav-link {{ request()->is('mis-datos') ? 'active show' : '' }}">
            <i class="typcn typcn-user-outline"></i> MIS DATOS
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/contratos') }}" class="nav-link {{ request()->is('contratos') ? 'active show' : '' }}">
            <i class="typcn typcn-document-text"></i> CONTRATOS
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/historial-pagos') }}" class="nav-link {{ request()->is('historial-pagos') ? 'active show' : '' }}">
            <i class="typcn typcn-time"></i> HISTORIAL PAGOS
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/notificar-pagos') }}" class="nav-link {{ request()->is('notificar-pagos') ? 'active show' : '' }}">
            <i class="typcn typcn-bell"></i> NOTIFICAR PAGOS
          </a>
        </li>
        {{--
        <li class="nav-item">
          <a href="{{ url('/soporte') }}" class="nav-link {{ request()->is('soporte') ? 'active show' : '' }}">
            <i class="typcn typcn-headphones"></i> SOPORTE
          </a>
        </li>
        --}}
      </ul>
    </div>

    {{-- Perfil (dropdown) --}}
    <div class="az-header-right">
      <div class="dropdown az-profile-menu">
        <a href="#" class="az-img-user dropdown-toggle"
           data-bs-toggle="dropdown" data-toggle="dropdown"
           aria-expanded="false" aria-haspopup="true" role="button">
          <img src="{{ asset('img/faces/face1.jpg') }}" alt="Foto Perfil">
        </a>

        <div class="dropdown-menu dropdown-menu-right">
          <div class="az-header-profile">
            <div class="az-img-user"><img src="{{ asset('img/faces/face1.jpg') }}" alt=""></div>
            <h6>{{ Auth::user()->name ?? 'Nombre Usuario' }}</h6>
            <span>RUT: {{ Auth::user()->rut ?? '11.111.111-1' }}</span>
          </div>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item">
              <i class="typcn typcn-power-outline"></i> Cerrar Sesión
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>
</header>


@push('scripts')
  {{-- Fallback por si el dropdown de Bootstrap no está activo --}}
  <script>
    (function () {
      var trigger = document.querySelector('.az-profile-menu .az-img-user');
      var menu    = document.querySelector('.az-profile-menu .dropdown-menu');
      var wrap    = document.querySelector('.az-profile-menu');

      if (!trigger || !menu) return;
      
      var hasBootstrap = (typeof bootstrap !== 'undefined') || (typeof $ !== 'undefined' && typeof $.fn !== 'undefined' && $.fn.dropdown);

      if (!hasBootstrap) {
        trigger.addEventListener('click', function (e) {
          e.preventDefault();
          wrap.classList.toggle('show');
          menu.classList.toggle('show');
        });

        document.addEventListener('click', function (e) {
          if (!e.target.closest('.az-profile-menu')) {
            wrap.classList.remove('show');
            menu.classList.remove('show');
          }
        });
      }
    })();
  </script>
@endpush

@endif

  <main class="container py-3">
    @yield('content')
  </main>

@if (!Route::is('login'))
  <footer class="border-top py-3 mt-4">
    <div class="container small text-muted">
      © {{ date('Y') }} NPRC
    </div>
  </footer>
@endif
  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/ui.js') }}"></script>
  @stack('scripts')
</body>
</html>
