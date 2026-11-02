<header class="topbar" data-navbarbg="skin5">
    <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <div class="navbar-header" data-logobg="skin6">
            <a class="navbar-brand" href="/inicio" aria-label="ComuniMap - Inicio">
                <img src="{{ secure_asset('ample/plugins/images/comunimap-logo.png') }}" alt="ComuniMap"
                    style="width:190px;height:56px;max-width:100%;object-fit:contain;">
            </a>
            <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"
                aria-label="Abrir o cerrar menú">
                <i class="ti-menu ti-close"></i>
            </a>
        </div>
        <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
            <ul class="navbar-nav ms-auto d-flex align-items-center">
                <li class="nav-item">
                    <div class="profile-pic" aria-label="Cuenta activa">
                        <i class="fas fa-user-circle" aria-hidden="true" style="font-size:28px;color:#2563eb;"></i>
                        <span class="font-medium">
                            {{ session('nombre') }}
                            <small style="display:block;font-size:11px;color:#667991;">
                                {{ session('rol') == 'Administrador' ? 'Administrador' : 'Vecino' }}
                            </small>
                        </span>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
