<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ComuniMap - Sistema Digital de Gestión y Alerta Comunitaria | OTB Barrio Universitario Alto</title>
    <meta name="description"
        content="Sistema digital para la gestión y alerta de problemas comunitarios mediante un mapa interactivo georreferenciado para la OTB Barrio Universitario Alto, Cochabamba.">
    <meta property="og:title" content="ComuniMap - Gestión Comunitaria">
    <meta property="og:description"
        content="Gestión y visualización georreferenciada de problemas comunitarios en la OTB Barrio Universitario Alto.">
    <meta property="og:type" content="website">

    <!-- Tipografía Open Sans según el sistema de diseño Classic Navy & Amber -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Hoja de estilos de Leaflet para los mapas georreferenciados -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">

    <!-- Estilos del Sistema de Diseño Classic Navy & Amber (SPA por Vistas) -->
    <link rel="stylesheet" href="{{ asset('plantilla1/css/styles.css') }}">
</head>

<body>
    <!-- Botón flotante para alternar menú en pantallas móviles (reemplaza el menú de la cabecera) -->
    <button class="mobile-toggle-btn" id="mobileMenuBtn" aria-label="Abrir menú lateral"
        onclick="toggleMobileSidebar()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    </button>

    <div class="dashboard-root">

        <!-- ======================================================================
        Barra Lateral de Navegación: Azul Marino (#103554)
        Incluye Logo Institucional de ComuniMap y Menú Simplificado
    ====================================================================== -->
        <aside class="sidebar" id="sidebar">

            <!-- Bloque de Marca Estilizado ComuniMap (Classic Navy) -->
            <div class="brand-area" id="sidebarBrandBlock">
                <div class="brand-icon-box">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"></path>
                        <circle cx="12" cy="9" r="2.5"></circle>
                    </svg>
                </div>
                <div class="brand-text">
                    <h2>ComuniMap</h2>
                    <p>OTB Univ. Alto</p>
                </div>
            </div>

            <!-- Tarjeta del perfil de usuario activo -->
            <div class="profile-block" id="sidebarProfileBlock">
                <div class="avatar-wrapper" title="Perfil de Usuario">
                    <img src="{{ asset('plantilla1/assets/avatar.jpg') }}" alt="Fernando Corrales" class="avatar-img"
                        id="userAvatarImg"
                        onerror="this.src='https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&h=120&fit=crop&crop=faces';">
                </div>
                <h2 class="user-name" id="userNameLabel">FERNANDO CORRALES</h2>
                <span class="user-email-text" id="userEmailLabel">jcorrales@comunimap.bo</span>
                <span class="user-role-badge" id="userRoleBadge">Dirigente OTB</span>
            </div>

            <!-- Menú de Navegación Simplificado y Reordenado -->
            <nav class="sidebar-menu">

                <!-- 1. Inicio (Mapa Protagonista) -->
                <button class="sidebar-link active" data-view="inicio" onclick="switchView('inicio')">
                    <svg viewBox="0 0 24 24">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path>
                    </svg>
                    <span>1. INICIO</span>
                </button>

                <!-- 2. Alertas (Registro de Problemas) -->
                <button class="sidebar-link" data-view="alertas" onclick="switchView('alertas')">
                    <svg viewBox="0 0 24 24">
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>
                    </svg>
                    <span>2. ALERTAS</span>
                </button>

                <!-- 3. Reportes (Tabla y Descarga PDF) -->
                <button class="sidebar-link" data-view="reportes" onclick="switchView('reportes')">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z">
                        </path>
                    </svg>
                    <span>3. REPORTES</span>
                </button>

                <!-- 4. Usuarios (Directorio Vecinal) -->
                <button class="sidebar-link" data-view="usuarios" onclick="switchView('usuarios')">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z">
                        </path>
                    </svg>
                    <span>4. USUARIOS</span>
                </button>

                <!-- 5. Estadísticas (Indicadores, Gráficos y Métricas) -->
                <button class="sidebar-link" data-view="estadisticas" onclick="switchView('estadisticas')">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z">
                        </path>
                    </svg>
                    <span>5. ESTADÍSTICAS</span>
                </button>

            </nav>

            <!-- Cerrar Sesión -->
            <div class="sidebar-bottom">
                <button class="btn-logout" id="btnLogout" onclick="handleLogout()"
                    title="Cerrar sesión del usuario actual">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" />
                    </svg>
                    <span>CERRAR SESIÓN</span>
                </button>
            </div>

        </aside>

        <!-- ======================================================================
        Área de Contenido Principal (Espacio limpio desde el borde superior)
        ====================================================================== -->
        <main class="main-content">

            <!-- Contenedor de Vistas SPA -->
            <div class="view-container">

                <!-- ==================================================================
            VISTA 1: INICIO (Mapa Georreferenciado Protagonista y Resumen)
            ================================================================== -->
                <section class="view-panel active" id="view-inicio">

                    <!-- Encabezado Limpio de Inicio -->
                    <div class="view-header">
                        <div class="view-title-group">
                            <h2>1. Mapa Comunitario Georreferenciado</h2>
                            <p>OTB Barrio Universitario Alto (Km 4 Av. Petrolera) — Cochabamba, Bolivia</p>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button class="btn-pill-orange" onclick="switchView('alertas')">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <span>+ Registrar Alerta</span>
                            </button>
                            <button class="btn-pill-navy" onclick="openPdfModal('general')">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                <span>Informe PDF</span>
                            </button>
                        </div>
                    </div>

                    <!-- Cuadrícula de Inicio: Mapa georreferenciado protagonista + Incidencias críticas -->
                    <div class="home-layout-grid">

                        <!-- Tarjeta del Mapa Comunitario -->
                        <div class="dash-card" style="margin-bottom: 0;">
                            <div class="map-card-header">
                                <div class="map-card-title-group">
                                    <span class="card-title-clean">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                                            <line x1="8" y1="2" x2="8" y2="18"></line>
                                            <line x1="16" y1="6" x2="16" y2="22"></line>
                                        </svg>
                                        Explorador de Problemas Georreferenciados en Tiempo Real
                                    </span>
                                    <span class="card-subtitle-clean">Visualización de luminarias, baches, fugas y
                                        puntos críticos</span>
                                </div>

                                <!-- Filtros rápidos por categoría -->
                                <div class="map-filter-pills">
                                    <button class="filter-pill active"
                                        onclick="filterMapCategory('all', this)">Todos</button>
                                    <button class="filter-pill"
                                        onclick="filterMapCategory('alumbrado', this)">Alumbrado</button>
                                    <button class="filter-pill"
                                        onclick="filterMapCategory('baches', this)">Baches</button>
                                    <button class="filter-pill"
                                        onclick="filterMapCategory('basura', this)">Basura</button>
                                    <button class="filter-pill" onclick="filterMapCategory('agua', this)">Agua
                                        Potable</button>
                                    <button class="filter-pill"
                                        onclick="filterMapCategory('seguridad', this)">Seguridad</button>
                                </div>
                            </div>

                            <!-- Contenedor del Mapa Leaflet -->
                            <div class="map-container-wrap">
                                <div id="comuniMapLeaflet"></div>
                            </div>

                            <!-- Barra de leyenda informativa -->
                            <div class="map-legend-bar">
                                <div class="map-legend-status-items">
                                    <div class="legend-indicator">
                                        <span class="dot-status red"></span>
                                        <span><strong>Pendiente</strong></span>
                                    </div>
                                    <div class="legend-indicator">
                                        <span class="dot-status amber"></span>
                                        <span><strong>En Proceso</strong></span>
                                    </div>
                                    <div class="legend-indicator">
                                        <span class="dot-status green"></span>
                                        <span><strong>Solucionado</strong></span>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn-pill-orange" onclick="switchView('alertas')"
                                        style="padding: 4px 12px; font-size: 10px;">
                                        + Marcar Punto en este Sector
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Columna lateral de Inicio: Resumen de Incidencias Recientes -->
                        <div class="dash-card" style="margin-bottom: 0;">
                            <div class="card-header-clean">
                                <span class="card-title-clean">Incidencias Recientes</span>
                                <span class="card-subtitle-clean" id="homeRecentCountLabel">Últimos reportes</span>
                            </div>

                            <div class="home-summary-list" id="homeRecentIncidentsList">
                                <!-- Se poblará dinámicamente con JavaScript -->
                            </div>

                            <div style="margin-top: 14px; text-align: center;">
                                <button class="btn-pill-navy" onclick="switchView('reportes')"
                                    style="width: 100%; justify-content: center;">
                                    Ver Directorio Completo (Tabla)
                                </button>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- ==================================================================
            VISTA 2: ALERTAS (Registro de Problemas Comunitarios)
            ================================================================== -->
                <section class="view-panel" id="view-alertas">
                    <div class="view-header">
                        <div class="view-title-group">
                            <div class="header-title-flex">
                                <h2>2. Registro y Emisión de Alertas Comunitarias</h2>
                                <span class="view-badge-alert" id="alertasViewPendingBadge"
                                    title="Alertas pendientes activas en la OTB">
                                    <span class="view-badge-dot"></span>
                                    <span id="alertasPendingCount">3</span> Alertas Pendientes
                                </span>
                            </div>
                            <p>Georreferencie baches, luminarias rotas, fugas de agua o problemas de basura para su
                                atención municipal</p>
                        </div>
                        <div>
                            <button class="btn-pill-navy" onclick="switchView('reportes')">
                                Ver Directorio de Reportes
                            </button>
                        </div>
                    </div>

                    <div class="alerts-layout-grid">

                        <!-- Columna Izquierda: Formulario de Alerta -->
                        <div class="dash-card">
                            <div class="card-header-clean">
                                <span class="card-title-clean">Formulario de Registro de Incidencia</span>
                                <span class="card-subtitle-clean">Todos los campos son necesarios</span>
                            </div>

                            <form id="alertRegisterForm" onsubmit="handleAlertSubmit(event)">
                                <div class="form-group">
                                    <label class="form-label" for="alertCategory">Tipo de Problema Comunitario:</label>
                                    <select id="alertCategory" class="form-select" required>
                                        <option value="Baches">Baches y deterioro de calzadas</option>
                                        <option value="Alumbrado" selected>Luminarias quemadas / falta de alumbrado
                                        </option>
                                        <option value="Basura">Acumulación de basura y residuos sólidos</option>
                                        <option value="Agua">Fugas de agua potable / alcantarillado</option>
                                        <option value="Seguridad">Seguridad ciudadana y riesgo de postes</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="alertTitle">Título breve o resumen del
                                        problema:</label>
                                    <input type="text" id="alertTitle" class="form-input"
                                        placeholder="Ej: Luminaria parpadeante en pasaje oscuro" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="alertLocation">Calle / Pasaje / Referencia:</label>
                                    <input type="text" id="alertLocation" class="form-input"
                                        placeholder="Ej: Pasaje Los Ceibos y Calle 3" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="alertCoordinates">Coordenadas Georreferenciadas
                                        (Latitud, Longitud):</label>
                                    <input type="text" id="alertCoordinates" class="form-input"
                                        value="-17.4150, -66.1420" required>
                                    <span style="font-size: 10px; color: var(--text-muted);">Haz clic en el mapa de la
                                        derecha para fijar la ubicación exacta con un pin.</span>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="alertDesc">Descripción detallada de la
                                        situación:</label>
                                    <textarea id="alertDesc" class="form-textarea" rows="3"
                                        placeholder="Indique la gravedad, tiempo que lleva el problema, afectación a vecinos y detalles para la cuadrilla técnica..."
                                        required></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Evidencia Fotográfica (Opcional):</label>
                                    <div class="dropzone-box"
                                        onclick="document.getElementById('alertFileInput').click()">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--navy)"
                                            stroke-width="1.8" style="margin-bottom: 4px;">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                        <div style="font-weight: 700; color: var(--navy); font-size: 11px;">Adjuntar
                                            fotografía de evidencia del problema</div>
                                        <div style="font-size: 9px; color: var(--text-muted);" id="alertFileNameLabel">
                                            Formatos JPG, PNG (Cámara o galería)</div>
                                        <input type="file" id="alertFileInput" accept="image/*" style="display:none;"
                                            onchange="handleAlertPhotoSelect(this)">
                                    </div>
                                    <img id="alertPhotoPreview" class="preview-thumb" style="display:none;"
                                        alt="Evidencia seleccionada">
                                </div>

                                <div style="display: flex; gap: 10px; margin-top: 18px;">
                                    <button type="submit" class="btn-pill-orange"
                                        style="flex: 1; justify-content: center; padding: 10px;">
                                        Publicar y Georreferenciar Alerta
                                    </button>
                                    <button type="button" class="btn-pill-secondary" onclick="resetAlertForm()">
                                        Limpiar
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Columna Derecha: Selector en Mapa Interactivo -->
                        <div class="dash-card">
                            <div class="card-header-clean">
                                <span class="card-title-clean">Ubicación en el Mapa</span>
                                <button class="filter-pill" onclick="centerPickerOnGPS()">
                                    Usar Mi Ubicación Actual
                                </button>
                            </div>

                            <div class="picker-hint-box">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <span><strong>Instrucciones:</strong> Haz clic en cualquier calle del mapa para mover el
                                    marcador rojo y capturar las coordenadas automáticamente.</span>
                            </div>

                            <div class="picker-map-wrap">
                                <div id="pickerMapLeaflet"></div>
                            </div>

                            <div
                                style="margin-top: 12px; display: flex; justify-content: space-between; font-size: 11px; color: var(--text-secondary);">
                                <span>Sector: <strong>OTB Barrio Universitario Alto</strong></span>
                                <span id="pickerCoordsDisplay">Lat: -17.4150, Lng: -66.1420</span>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- ==================================================================
            VISTA 3: REPORTES (Tabla de Incidencias + Gestión y Generación de PDF)
            ================================================================== -->
                <section class="view-panel" id="view-reportes">
                    <div class="view-header">
                        <div class="view-title-group">
                            <div class="header-title-flex">
                                <h2>3. Directorio Oficial de Reportes e Incidencias</h2>
                                <span class="view-badge-alert alert-orange-tint" id="reportsViewPendingBadge"
                                    title="Incidencias pendientes de atención técnica">
                                    <span class="view-badge-dot"></span>
                                    <span id="reportsPendingCount">3</span> Reportes Sin Atender
                                </span>
                            </div>
                            <p>Consolidado para fiscalización vecinal y generación de informes técnicos municipales en
                                PDF</p>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button class="btn-pill-orange" onclick="switchView('alertas')">
                                + Nueva Alerta
                            </button>
                            <button class="btn-pill-navy" onclick="openPdfModal('general')">
                                Generar Informe General PDF
                            </button>
                        </div>
                    </div>

                    <!-- Barra de herramientas de búsqueda y filtros -->
                    <div class="table-toolbar">
                        <div class="table-filters">
                            <input type="text" class="search-input" id="tableSearchInput"
                                placeholder="Buscar por código, calle..." style="width: 220px;"
                                oninput="applyReportFilters()">

                            <select class="table-filter-select" id="filterCatSelect" onchange="applyReportFilters()">
                                <option value="all">Todas las Categorías</option>
                                <option value="baches">Baches y Calzadas</option>
                                <option value="alumbrado">Alumbrado Público</option>
                                <option value="basura">Acumulación de Basura</option>
                                <option value="agua">Agua Potable y SEMAPA</option>
                                <option value="seguridad">Seguridad / Riesgo</option>
                            </select>

                            <select class="table-filter-select" id="filterStatusSelect" onchange="applyReportFilters()">
                                <option value="all">Todos los Estados</option>
                                <option value="pending">Pendiente</option>
                                <option value="process">En Proceso</option>
                                <option value="solved">Solucionado</option>
                            </select>

                            <select class="table-filter-select" id="filterPrioritySelect"
                                onchange="applyReportFilters()">
                                <option value="all">Todas las Prioridades</option>
                                <option value="Urgente">Urgente</option>
                                <option value="Alta">Alta</option>
                                <option value="Media">Media</option>
                                <option value="Baja">Baja</option>
                            </select>
                        </div>

                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="table-badge-counter" id="reportsToolbarPendingBadge"
                                title="Incidencias pendientes">
                                <span class="badge-dot-mini"></span>
                                <span id="reportsToolbarPendingCount">3</span> Pendientes
                            </span>
                            <span style="font-size: 11px; color: var(--text-secondary);" id="reportsCountLabel">
                                Mostrando 7 incidencias
                            </span>
                        </div>
                    </div>

                    <!-- Tabla de Incidencias -->
                    <div class="reports-table-wrap">
                        <table class="clean-table" id="reportsMainTable">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Incidencia / Problema</th>
                                    <th>Categoría</th>
                                    <th>Ubicación / Sector</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th style="text-align: right;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="reportsTableBody">
                                <!-- Se poblará dinámicamente con JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- ==================================================================
            VISTA 4: USUARIOS (Administración de Dirigentes, Cuadrillas y Vecinos)
            ================================================================== -->
                <section class="view-panel" id="view-usuarios">
                    <div class="view-header">
                        <div class="view-title-group">
                            <h2>4. Administración de Usuarios y Directorio Vecinal</h2>
                            <p>Control de acceso, roles comunitarios y fiscalización de cuentas registradas en la OTB
                            </p>
                        </div>
                        <div>
                            <button class="btn-pill-orange" onclick="toggleNewUserForm()">
                                + Registrar Nuevo Usuario
                            </button>
                        </div>
                    </div>

                    <!-- Métricas de Usuarios -->
                    <div class="users-metrics-row">
                        <div class="stat-card">
                            <div class="stat-card-title">Total Vecinos Registrados</div>
                            <div class="stat-card-value tabular-nums" id="userCountTotal">152</div>
                            <div class="stat-card-footer">Cuentas activas en la OTB</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-title">Mesa Directiva / Admins</div>
                            <div class="stat-card-value tabular-nums">3</div>
                            <div class="stat-card-footer">Dirigentes autorizados</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-title">Cuadrillas Técnicas</div>
                            <div class="stat-card-value tabular-nums">4</div>
                            <div class="stat-card-footer">SEMAPA y Alumbrado</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-title">Vecinos Activos este mes</div>
                            <div class="stat-card-value tabular-nums">118</div>
                            <div class="stat-card-footer">Reportando o consultando</div>
                        </div>
                    </div>

                    <!-- Formulario colapsable para agregar usuario -->
                    <div class="dash-card" id="newUserFormCard"
                        style="display: none; background: #F8FAFD; border: 1px dashed var(--navy-light);">
                        <div class="card-header-clean">
                            <span class="card-title-clean">Registrar Vecino o Funcionario</span>
                            <button class="btn-action-icon" onclick="toggleNewUserForm()">✕</button>
                        </div>
                        <form id="adminUserForm" onsubmit="handleAdminCreateUser(event)">
                            <div class="form-row-2">
                                <div class="form-group">
                                    <label class="form-label" for="newUserName">Nombre Completo:</label>
                                    <input type="text" id="newUserName" class="form-input"
                                        placeholder="Ej: Gonzalo Murillo" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="newUserEmail">Correo Electrónico:</label>
                                    <input type="email" id="newUserEmail" class="form-input"
                                        placeholder="gmurillo@gmail.com" required>
                                </div>
                            </div>
                            <div class="form-row-2">
                                <div class="form-group">
                                    <label class="form-label" for="newUserRole">Rol en el Sistema:</label>
                                    <select id="newUserRole" class="form-select">
                                        <option value="Vecino">Vecino Comunitario</option>
                                        <option value="Admin">Dirigente / Administrador</option>
                                        <option value="Técnico">Técnico / Cuadrilla Municipal</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="newUserPhone">Celular / WhatsApp:</label>
                                    <input type="tel" id="newUserPhone" class="form-input" placeholder="Ej: 76543210"
                                        required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="newUserLocation">Dirección / Lote en la OTB:</label>
                                <input type="text" id="newUserLocation" class="form-input"
                                    placeholder="Ej: Calle 4 Lote 18" required>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <button type="submit" class="btn-pill-orange">Guardar Usuario</button>
                                <button type="button" class="btn-pill-secondary"
                                    onclick="toggleNewUserForm()">Cancelar</button>
                            </div>
                        </form>
                    </div>

                    <!-- Tabla de Usuarios -->
                    <div class="dash-card">
                        <div class="table-toolbar">
                            <input type="text" class="search-input" id="userSearchInput"
                                placeholder="Buscar por nombre, correo..." style="width: 240px;"
                                oninput="applyUserFilters()">

                            <select class="table-filter-select" id="userRoleFilterSelect" onchange="applyUserFilters()">
                                <option value="all">Todos los Roles</option>
                                <option value="Admin">Dirigentes (Admin)</option>
                                <option value="Técnico">Técnicos</option>
                                <option value="Vecino">Vecinos</option>
                            </select>
                        </div>

                        <div class="reports-table-wrap">
                            <table class="clean-table" id="usersMainTable">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Contacto</th>
                                        <th>Rol</th>
                                        <th>Ubicación en la OTB</th>
                                        <th>Estado</th>
                                        <th style="text-align: right;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <!-- Se poblará dinámicamente con JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- ==================================================================
            VISTA 5: ESTADÍSTICAS (Métricas Trasladadas de Inicio + Gráficos)
            ================================================================== -->
                <section class="view-panel" id="view-estadisticas">
                    <div class="view-header">
                        <div class="view-title-group">
                            <h2>5. Análisis y Estadísticas Comunitarias</h2>
                            <p>Métricas de desempeño, tiempos de respuesta municipal y distribución de incidencias</p>
                        </div>
                        <div>
                            <button class="btn-pill-navy" onclick="openPdfModal('general')">
                                Exportar Informe Estadístico en PDF
                            </button>
                        </div>
                    </div>

                    <!-- 4 Tarjetas de Métricas Trasladadas desde Inicio -->
                    <div class="metric-row">
                        <div class="stat-card navy-highlight" onclick="switchView('reportes')"
                            title="Ver todas las incidencias" style="cursor: pointer;">
                            <div class="stat-card-header">
                                <span class="stat-card-title">Total Reportes</span>
                                <div class="icon-badge-circle">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="stat-card-value tabular-nums" id="statTotalReports">142</div>
                            <div class="stat-card-footer">
                                <span>Historial acumulado en la OTB</span>
                            </div>
                        </div>

                        <div class="stat-card" onclick="switchView('reportes'); setReportStatusFilter('process');"
                            title="Casos con cuadrilla asignada" style="cursor: pointer;">
                            <div class="stat-card-header">
                                <span class="stat-card-title">En Proceso</span>
                                <div class="stat-card-icon"
                                    style="color: var(--orange); background: var(--orange-pale);">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="stat-card-value tabular-nums" id="statProcessReports">34</div>
                            <div class="stat-card-footer" style="color: var(--orange); font-weight: 600;">
                                <span>● 24% del total en atención</span>
                            </div>
                        </div>

                        <div class="stat-card" onclick="switchView('reportes'); setReportStatusFilter('solved');"
                            title="Casos solucionados" style="cursor: pointer;">
                            <div class="stat-card-header">
                                <span class="stat-card-title">Solucionados</span>
                                <div class="stat-card-icon"
                                    style="color: #27AE60; background: var(--status-solved-bg);">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="stat-card-value tabular-nums" id="statSolvedReports">98</div>
                            <div class="stat-card-footer" style="color: #27AE60; font-weight: 600;">
                                <span>▲ 69% tasa de resolución exitosa</span>
                            </div>
                        </div>

                        <div class="stat-card" onclick="switchView('reportes'); setReportStatusFilter('pending');"
                            title="Requieren intervención prioritaria" style="cursor: pointer;">
                            <div class="stat-card-header">
                                <span class="stat-card-title">Alertas Activas</span>
                                <div class="stat-card-icon"
                                    style="color: var(--status-pending); background: var(--status-pending-bg);">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="stat-card-value tabular-nums" id="statPendingAlerts">10</div>
                            <div class="stat-card-footer" style="color: var(--status-pending); font-weight: 600;">
                                <span>Atención prioritaria pendiente</span>
                            </div>
                        </div>
                    </div>

                    <!-- Métricas de Desempeño Comunitario -->
                    <div class="metric-row">
                        <div class="stat-card">
                            <div class="stat-card-title">Tiempo Promedio Atención</div>
                            <div class="stat-card-value tabular-nums" style="color: var(--navy);">3.2 <span
                                    style="font-size: 14px;">días</span></div>
                            <div class="stat-card-footer">Desde alerta hasta cuadrilla</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-title">Tasa de Resolución</div>
                            <div class="stat-card-value tabular-nums" style="color: #27AE60;">69%</div>
                            <div class="stat-card-footer">98 de 142 casos solucionados</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-title">Incidencias este mes</div>
                            <div class="stat-card-value tabular-nums" style="color: var(--orange);">28</div>
                            <div class="stat-card-footer">Septiembre 2026</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-title">Eficacia Municipal</div>
                            <div class="stat-card-value tabular-nums" style="color: var(--navy);">91%</div>
                            <div class="stat-card-footer">Cumplimiento en inspección</div>
                        </div>
                    </div>

                    <!-- Gráficos de Análisis -->
                    <div class="stats-charts-grid">

                        <!-- Gráfico 1: Tendencia Mensual (Canvas) -->
                        <div class="dash-card">
                            <div class="card-header-clean">
                                <span class="card-title-clean">Tendencia Mensual de Reportes (2026)</span>
                                <span class="card-subtitle-clean">Enero a Septiembre</span>
                            </div>
                            <div style="display: flex; gap: 14px; font-size: 11px; margin-bottom: 8px;">
                                <span style="display: flex; align-items: center; gap: 4px;">
                                    <span
                                        style="width: 10px; height: 10px; background-color: var(--orange); border-radius: 2px;"></span>
                                    Alumbrado Público
                                </span>
                                <span style="display: flex; align-items: center; gap: 4px;">
                                    <span
                                        style="width: 10px; height: 10px; background-color: var(--navy); border-radius: 2px;"></span>
                                    Baches y Calzadas
                                </span>
                            </div>
                            <div class="chart-card-wrap">
                                <canvas id="statsTrendCanvas" class="stats-canvas"></canvas>
                            </div>
                        </div>

                        <!-- Gráfico 2: Distribución por Categoría (Canvas de Barras) -->
                        <div class="dash-card">
                            <div class="card-header-clean">
                                <span class="card-title-clean">Incidencias por Categoría</span>
                                <span class="card-subtitle-clean">Distribución porcentual acumulada</span>
                            </div>
                            <div class="chart-card-wrap">
                                <canvas id="statsCategoryCanvas" class="stats-canvas"></canvas>
                            </div>
                        </div>

                    </div>

                    <!-- Tabla de Sectores Críticos -->
                    <div class="dash-card" style="margin-top: 18px;">
                        <div class="card-header-clean">
                            <span class="card-title-clean">Sectores y Calles con Mayor Reincidencia</span>
                            <span class="card-subtitle-clean">Zonas prioritarias para el POA de la OTB</span>
                        </div>
                        <table class="ranking-list-table">
                            <tbody>
                                <tr>
                                    <td style="font-weight: 700; width: 30%;">Av. Petrolera Km 4 al Km 4.4</td>
                                    <td style="width: 20%; color: #E74C3C; font-weight: 600;">38 reportes (Baches e
                                        Iluminación)</td>
                                    <td style="width: 50%;">
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill"
                                                style="width: 85%; background-color: #E74C3C;"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700;">Pasaje Los Ceibos y Calle 3</td>
                                    <td style="color: var(--orange); font-weight: 600;">26 reportes (Luminarias)</td>
                                    <td style="width: 50%;">
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill"
                                                style="width: 60%; background-color: var(--orange);"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700;">Plaza Principal OTB (Sector Este)</td>
                                    <td style="color: var(--navy); font-weight: 600;">18 reportes (Contenedores Basura)
                                    </td>
                                    <td style="width: 50%;">
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill"
                                                style="width: 42%; background-color: var(--navy);"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700;">Pasaje Estudiantil No. 240</td>
                                    <td style="color: #27AE60; font-weight: 600;">12 reportes (Atendidos)</td>
                                    <td style="width: 50%;">
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill"
                                                style="width: 25%; background-color: #27AE60;"></div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </section>

            </div>
        </main>
    </div>

    <!-- ======================================================================
    Previsualizador y Generador Oficial de Documentos PDF (General o Individual)
    ====================================================================== -->
    <div class="pdf-preview-container" id="pdfPreviewModal">
        <div class="pdf-modal-sheet">
            <div class="pdf-sheet-header">
                <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 13px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <span id="pdfSheetTitle">Generador de Documento PDF Oficial</span>
                </div>
                <button class="btn-action-icon" onclick="closePdfModal()"
                    style="color:#FFF; background:rgba(255,255,255,0.15); border:none;">✕</button>
            </div>

            <div class="pdf-sheet-content" id="pdfPrintableContent">
                <!-- El contenido se inyecta dinámicamente con plantilla de membrete oficial -->
            </div>

            <div class="pdf-modal-actions"
                style="padding: 12px 24px; background: #F8FAFD; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px;">
                <button class="btn-pill-secondary" onclick="closePdfModal()">Cerrar</button>
                <button class="btn-pill-orange" onclick="printOfficialPdf()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Imprimir / Descargar PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Contenedor para Alertas y Mensajes Toast -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Librería JavaScript de Leaflet para el soporte cartográfico -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Controlador JavaScript Vanilla de ComuniMap -->
    <script src="{{ asset('plantilla1/js/app.js') }}"></script>
</body>

</html>