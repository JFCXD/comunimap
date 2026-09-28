<aside class="left-sidebar" data-sidebarbg="skin6">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- User Profile-->
                <li class="sidebar-item pt-2">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="/inicio" aria-expanded="false">
                        <i class="fas fa-eject" aria-hidden="true"></i>
                        <span class="hide-menu">INICIO</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="/alertas" aria-expanded="false">
                        <i class="fas fa-exclamation" aria-hidden="true"></i>
                        <span class="hide-menu">ALERTAS</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="/reportes" aria-expanded="false">
                        <i class=" fas fa-exclamation-triangle" aria-hidden="true"></i>
                        <span class="hide-menu">REPORTES</span>
                    </a>
                </li>
                @if(session('rol') == 'Administrador')
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="/usuarios" aria-expanded="false">
                            <i class="fa fa-user" aria-hidden="true"></i>
                            <span class="hide-menu">USUARIOS</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="/estadisticas"
                            aria-expanded="false">
                            <i class="fas fa-chart-pie" aria-hidden="true"></i>
                            <span class="hide-menu">ESTADISTICAS</span>
                        </a>
                    </li>
                @endif
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="/logout" aria-expanded="false">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                        <span class="hide-menu">CERRAR SESIÓN</span>
                    </a>
                </li>

            </ul>

        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>