<aside class="left-sidebar" data-sidebarbg="skin6">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- User Profile-->
                <li class="sidebar-item pt-2">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link {{ request()->is('inicio') || request()->is('inicio/*') ? 'cm-selected' : '' }}" href="/inicio" aria-expanded="false">
                        <i class="fas fa-home" aria-hidden="true"></i>
                        <span class="hide-menu">Inicio</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link {{ request()->is('comunidad') || request()->is('comunidad/*') ? 'cm-selected' : '' }}" href="/comunidad" aria-expanded="false">
                        <i class="fas fa-users" aria-hidden="true"></i>
                        <span class="hide-menu">Comunidad</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link {{ request()->is('alertas') || request()->is('alertas/*') ? 'cm-selected' : '' }}" href="/alertas" aria-expanded="false">
                        <i class="fas fa-plus-circle" aria-hidden="true"></i>
                        <span class="hide-menu">Registrar alerta</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link {{ request()->is('reportes') || request()->is('reportes/*') ? 'cm-selected' : '' }}" href="/reportes" aria-expanded="false">
                        <i class=" fas fa-exclamation-triangle" aria-hidden="true"></i>
                        <span class="hide-menu">Reportes</span>
                    </a>
                </li>
                @if(session('rol') == 'Administrador')
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link {{ request()->is('usuarios') || request()->is('usuarios/*') ? 'cm-selected' : '' }}" href="/usuarios" aria-expanded="false">
                            <i class="fa fa-user" aria-hidden="true"></i>
                            <span class="hide-menu">Usuarios</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link {{ request()->is('estadisticas') || request()->is('estadisticas/*') ? 'cm-selected' : '' }}" href="/estadisticas"
                            aria-expanded="false">
                            <i class="fas fa-chart-pie" aria-hidden="true"></i>
                            <span class="hide-menu">Estadísticas</span>
                        </a>
                    </li>
                @endif
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="/logout" aria-expanded="false">
                        <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                        <span class="hide-menu">Cerrar sesión</span>
                    </a>
                </li>

            </ul>

        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>