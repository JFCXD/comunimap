<header class="topbar" data-navbarbg="skin5">
    <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <div class="navbar-header" data-logobg="skin6">
            <a class="navbar-brand" href="/inicio" aria-label="ComuniMap, volver a Inicio">
                <img src="{{ secure_asset('ample/plugins/images/comunimap-logo.png') }}" alt="ComuniMap"
                    style="width:190px;height:60px;max-width:100%;object-fit:contain;">
            </a>
            <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"
                aria-label="Abrir menú">
                <i class="ti-menu ti-close"></i>
            </a>
        </div>
        <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
            <ul class="navbar-nav ms-auto d-flex align-items-center">
                <li class="nav-item">
                    <div class="cm-account">
                        <i class="fas fa-user-circle cm-account-icon" aria-hidden="true"></i>
                        <span>
                            <span class="cm-account-name">{{ session('nombre') }}</span>
                            <span class="cm-account-role d-block">{{ session('rol') == 'Administrador' ? 'Administrador' : 'Vecino' }}</span>
                        </span>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
