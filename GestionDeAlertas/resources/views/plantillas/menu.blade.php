<aside class="left-sidebar" data-sidebarbg="skin6">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- User Profile-->
                <li class="sidebar-item pt-2">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link {{ request()->is('inicio') || request()->is('inicio/*') ? 'cm-active' : '' }}" href="/inicio" aria-expanded="false">
                        <i class="fas fa-home" aria-hidden="true"></i>
                        <span class="hide-menu">Inicio</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link {{ request()->is('alertas') || request()->is('alertas/*') ? 'cm-active' : '' }}" href="/alertas" aria-expanded="false">
                        <i class="fas fa-plus-circle" aria-hidden="true"></i>
                        <span class="hide-menu">Registrar alerta</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link {{ request()->is('reportes') || request()->is('reportes/*') ? 'cm-active' : '' }}" href="/reportes" aria-expanded="false">
                        <i class=" fas fa-exclamation-triangle" aria-hidden="true"></i>
                        <span class="hide-menu">Reportes</span>
                    </a>
                </li>
                @if(session('rol') == 'Administrador')
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link {{ request()->is('usuarios') || request()->is('usuarios/*') ? 'cm-active' : '' }}" href="/usuarios" aria-expanded="false">
                            <i class="fa fa-user" aria-hidden="true"></i>
                            <span class="hide-menu">Usuarios</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link {{ request()->is('estadisticas') || request()->is('estadisticas/*') ? 'cm-active' : '' }}" href="/estadisticas"
                            aria-expanded="false">
                            <i class="fas fa-chart-pie" aria-hidden="true"></i>
                            <span class="hide-menu">Estadísticas</span>
                        </a>
                    </li>
                @endif
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link {{ request()->is('logout') || request()->is('logout/*') ? 'cm-active' : '' }}" href="/logout" aria-expanded="false">
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