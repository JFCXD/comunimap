/**
 * ComuniMap - Sistema Digital de Gestión y Alerta Comunitaria
 * OTB Barrio Universitario Alto - Cochabamba, Bolivia
 * Controlador JavaScript Vanilla (Arquitectura SPA sin Topbar con Mapa Protagonista)
 * Todos los comentarios en español según los estándares del proyecto.
 */

// ============================================================================
// 1. Estado de la Aplicación y Datos Iniciales
// ============================================================================

// Base de datos de incidencias comunitarias georreferenciadas
let comReportsData = [
  {
    id: 1,
    code: 'REP-001',
    title: 'Baches profundos en calzada principal',
    category: 'Baches',
    categoryKey: 'baches',
    location: 'Av. Petrolera Km 4.2, carril de subida',
    lat: -17.4185,
    lng: -66.1395,
    status: 'pending', // pending, process, solved
    statusLabel: 'Pendiente',
    priority: 'Urgente',
    date: '24 Sep 2026',
    author: 'Carlos Mamani (Vecino)',
    desc: 'Baches de gran profundidad ocasionan daños vehiculares y peligro continuo de colisión en horario pico.'
  },
  {
    id: 2,
    code: 'REP-002',
    title: 'Foco quemado y luminaria parpadeante',
    category: 'Alumbrado',
    categoryKey: 'alumbrado',
    location: 'Pasaje Los Ceibos y Calle 3',
    lat: -17.4140,
    lng: -66.1430,
    status: 'process',
    statusLabel: 'En Proceso',
    priority: 'Media',
    date: '22 Sep 2026',
    author: 'Rosa Fernandez (Vecina)',
    desc: 'Pasaje en penumbras durante la noche. Se realizó la solicitud formal a Alumbrado Público municipal.'
  },
  {
    id: 3,
    code: 'REP-003',
    title: 'Acumulación de basura y residuos sólidos',
    category: 'Basura',
    categoryKey: 'basura',
    location: 'Plaza Principal OTB, esquina este',
    lat: -17.4110,
    lng: -66.1405,
    status: 'pending',
    statusLabel: 'Pendiente',
    priority: 'Alta',
    date: '25 Sep 2026',
    author: 'Javier Gutierrez (Dirigente)',
    desc: 'Contenedor comunal desbordado desde hace 3 días, genera focos de contaminación y olores fétidos.'
  },
  {
    id: 4,
    code: 'REP-004',
    title: 'Fuga constante de agua potable en acera',
    category: 'Agua',
    categoryKey: 'agua',
    location: 'Calle Los Álamos frente al Lote 14',
    lat: -17.4162,
    lng: -66.1465,
    status: 'process',
    statusLabel: 'En Proceso',
    priority: 'Alta',
    date: '23 Sep 2026',
    author: 'Elena Mendoza (Vecina)',
    desc: 'Rotura de matriz domiciliaria de SEMAPA con derrame de agua limpia sobre la calzada.'
  },
  {
    id: 5,
    code: 'REP-005',
    title: 'Poste de luz inclinado con cableado colgante',
    category: 'Seguridad',
    categoryKey: 'seguridad',
    location: 'Pasaje Estudiantil No. 240',
    lat: -17.4210,
    lng: -66.1360,
    status: 'pending',
    statusLabel: 'Pendiente',
    priority: 'Urgente',
    date: '25 Sep 2026',
    author: 'Fernando Corrales (Dirigente)',
    desc: 'Impacto de vehículo dejó poste inestable, representa peligro inminente de caída sobre transeúntes.'
  },
  {
    id: 6,
    code: 'REP-006',
    title: 'Reparación de rejilla de alcantarillado pluvial',
    category: 'Agua',
    categoryKey: 'agua',
    location: 'Av. Petrolera altura pasarela peatonal',
    lat: -17.4085,
    lng: -66.1450,
    status: 'solved',
    statusLabel: 'Solucionado',
    priority: 'Media',
    date: '18 Sep 2026',
    author: 'Cuadrilla SEMAPA / Obras Públicas',
    desc: 'Limpieza de sedimentos y reposición de rejilla metálica para prevenir inundaciones en época de lluvias.'
  },
  {
    id: 7,
    code: 'REP-007',
    title: 'Reposición de lámpara LED comunal',
    category: 'Alumbrado',
    categoryKey: 'alumbrado',
    location: 'Cancha Polifuncional Barrio Universitario',
    lat: -17.4150,
    lng: -66.1380,
    status: 'solved',
    statusLabel: 'Solucionado',
    priority: 'Alta',
    date: '15 Sep 2026',
    author: 'Comité de Deportes OTB',
    desc: 'Se instalaron 2 reflectores LED de 150W con apoyo de la dirigencia y aporte vecinal.'
  }
];

// Directorio de usuarios de la comunidad OTB
let communityUsersData = [
  {
    id: 1,
    name: 'Fernando Corrales Inturias',
    email: 'jcorrales@comunimap.bo',
    phone: '797-12345',
    role: 'Admin',
    roleLabel: 'Dirigente / Admin',
    location: 'Pasaje Los Ceibos Lote 4',
    status: 'Activo',
    avatar: 'assets/avatar.jpg'
  },
  {
    id: 2,
    name: 'Arleth Helen Calizaya Almendras',
    email: 'acalizaya@comunimap.bo',
    phone: '764-54321',
    role: 'Admin',
    roleLabel: 'Coordinadora Técnica',
    location: 'Av. Petrolera Km 4',
    status: 'Activo',
    avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&h=120&fit=crop&crop=faces'
  },
  {
    id: 3,
    name: 'Rosa Fernández Morales',
    email: 'rfernandez@gmail.com',
    phone: '722-98765',
    role: 'Vecino',
    roleLabel: 'Vecina Comunal',
    location: 'Calle 3 Lote 28',
    status: 'Activo',
    avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=120&h=120&fit=crop&crop=faces'
  },
  {
    id: 4,
    name: 'Javier Gutiérrez Valdivia',
    email: 'jgutierrez@gmail.com',
    phone: '714-33221',
    role: 'Admin',
    roleLabel: 'Presidente OTB',
    location: 'Calle Los Álamos Lote 2',
    status: 'Activo',
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=120&h=120&fit=crop&crop=faces'
  },
  {
    id: 5,
    name: 'Ing. Marcelo Ramos (SEMAPA)',
    email: 'mramos@semapa.bo',
    phone: '779-88112',
    role: 'Técnico',
    roleLabel: 'Cuadrilla Técnica',
    location: 'Distrito 8 Cochabamba',
    status: 'Activo',
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&h=120&fit=crop&crop=faces'
  }
];

// Usuario actualmente conectado
let currentUser = {
  name: 'Fernando Corrales',
  email: 'jcorrales@comunimap.bo',
  role: 'Dirigente OTB',
  avatar: 'assets/avatar.jpg'
};

// Instancias de mapas Leaflet
let comuniMapInstance = null;
let comuniMarkers = [];
let pickerMapInstance = null;
let pickerMarker = null;

// Filtro de categorías activo en el mapa de Inicio
let currentMapCatFilter = 'all';

// ============================================================================
// 2. Inicialización General al Cargar el DOM
// ============================================================================
document.addEventListener('DOMContentLoaded', () => {
  // Inicializar mapa protagonista de inicio
  initComuniMap();

  // Inicializar mapa selector de coordenadas en vista Alertas
  initPickerMap();

  // Actualizar contadores y tarjetas de métricas (alojadas en Estadísticas)
  updateMetricsCounters();

  // Renderizar listas y tablas iniciales
  renderHomeRecentIncidents();
  renderReportsTable();
  renderUsersTable();

  // Inicializar gráficos estadísticos
  initStatsCharts();

  // Inicializar menú móvil responsivo
  initMobileDrawer();

  // Redimensionar mapas y gráficos al cambiar tamaño de ventana
  window.addEventListener('resize', () => {
    if (comuniMapInstance) comuniMapInstance.invalidateSize();
    if (pickerMapInstance) pickerMapInstance.invalidateSize();
    drawStatsTrendChart();
    drawStatsCategoryChart();
  });
});

// ============================================================================
// 3. Sistema de Navegación Limpio por Vistas (SPA por Paneles)
// ============================================================================
window.switchView = function(viewName) {
  // 1. Ocultar todos los paneles de vista
  const panels = document.querySelectorAll('.view-panel');
  panels.forEach(panel => {
    panel.classList.remove('active');
  });

  // 2. Activar la vista solicitada
  const targetPanel = document.getElementById(`view-${viewName}`);
  if (targetPanel) {
    targetPanel.classList.add('active');
  }

  // 3. Actualizar estado activo en los enlaces del menú lateral
  const sidebarLinks = document.querySelectorAll('.sidebar-link');
  sidebarLinks.forEach(link => {
    const target = link.getAttribute('data-view');
    link.classList.toggle('active', target === viewName);
  });

  // 4. Acciones específicas al activar cada vista
  if (viewName === 'inicio') {
    setTimeout(() => {
      if (comuniMapInstance) comuniMapInstance.invalidateSize();
    }, 150);
  } else if (viewName === 'alertas') {
    setTimeout(() => {
      if (pickerMapInstance) pickerMapInstance.invalidateSize();
    }, 150);
  } else if (viewName === 'reportes') {
    renderReportsTable();
  } else if (viewName === 'usuarios') {
    renderUsersTable();
  } else if (viewName === 'estadisticas') {
    setTimeout(() => {
      drawStatsTrendChart();
      drawStatsCategoryChart();
    }, 100);
  }

  // Cerrar menú en dispositivos móviles si estaba abierto
  const sidebar = document.getElementById('sidebar');
  if (sidebar && window.innerWidth <= 768) {
    sidebar.classList.remove('open');
  }

  window.scrollTo({ top: 0, behavior: 'smooth' });
};

// ============================================================================
// 4. Cerrar Sesión y Perfil de Usuario
// ============================================================================
window.handleLogout = function() {
  if (currentUser) {
    currentUser = null;
    const nameLabel = document.getElementById('userNameLabel');
    const emailLabel = document.getElementById('userEmailLabel');
    const roleBadge = document.getElementById('userRoleBadge');
    const avatarImg = document.getElementById('userAvatarImg');

    if (nameLabel) nameLabel.textContent = 'INVITADO';
    if (emailLabel) emailLabel.textContent = 'Modo Consulta Vecinal';
    if (roleBadge) {
      roleBadge.textContent = 'Vecino';
      roleBadge.style.background = 'rgba(255,255,255,0.15)';
      roleBadge.style.color = '#B4C6D8';
    }
    if (avatarImg) {
      avatarImg.src = 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=120&h=120&fit=crop';
    }
    showToast('Sesión finalizada. Se ha activado el perfil de Invitado / Consulta.');
  } else {
    // Restaurar usuario administrativo
    currentUser = {
      name: 'Fernando Corrales',
      email: 'jcorrales@comunimap.bo',
      role: 'Dirigente OTB',
      avatar: 'assets/avatar.jpg'
    };
    updateSidebarProfile();
    showToast('Sesión de Dirigente OTB reactivada.');
  }

  switchView('inicio');
};

function updateSidebarProfile() {
  if (!currentUser) return;
  const nameLabel = document.getElementById('userNameLabel');
  const emailLabel = document.getElementById('userEmailLabel');
  const roleBadge = document.getElementById('userRoleBadge');
  const avatarImg = document.getElementById('userAvatarImg');

  if (nameLabel) nameLabel.textContent = currentUser.name.toUpperCase();
  if (emailLabel) emailLabel.textContent = currentUser.email;
  if (roleBadge) {
    roleBadge.textContent = currentUser.role;
    roleBadge.style.background = 'rgba(255, 165, 30, 0.18)';
    roleBadge.style.color = 'var(--orange-light)';
  }
  if (avatarImg && currentUser.avatar) {
    avatarImg.src = currentUser.avatar;
  }
}

// ============================================================================
// 5. Vista 1: Mapa Protagonista de Inicio y Resumen Comunitario
// ============================================================================
function initComuniMap() {
  const mapElement = document.getElementById('comuniMapLeaflet');
  if (!mapElement) return;

  // Centro en Barrio Universitario Alto, Km 4 Av. Petrolera, Cochabamba
  const centerCoords = [-17.4155, -66.1415];

  comuniMapInstance = L.map('comuniMapLeaflet', {
    center: centerCoords,
    zoom: 15,
    zoomControl: true
  });

  // Capa libre de OpenStreetMap
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap | OTB Barrio Universitario Alto'
  }).addTo(comuniMapInstance);

  // Renderizar pines georreferenciados
  renderComuniMarkers();

  // Clic en el mapa para capturar coordenadas e ir a nueva alerta
  comuniMapInstance.on('click', (e) => {
    const lat = e.latlng.lat.toFixed(5);
    const lng = e.latlng.lng.toFixed(5);
    const coordInput = document.getElementById('alertCoordinates');
    if (coordInput) coordInput.value = `${lat}, ${lng}`;
    showToast(`Punto capturado: [${lat}, ${lng}]. Abriendo formulario de Alertas...`);
    switchView('alertas');
  });
}

function renderComuniMarkers() {
  if (!comuniMapInstance) return;

  // Limpiar marcadores existentes
  comuniMarkers.forEach(m => comuniMapInstance.removeLayer(m));
  comuniMarkers = [];

  const filtered = comReportsData.filter(r => {
    if (currentMapCatFilter === 'all') return true;
    return r.categoryKey === currentMapCatFilter;
  });

  filtered.forEach(rep => {
    let pinColor = '#E74C3C'; // Pendiente
    if (rep.status === 'process') pinColor = '#FFA51E'; // En Proceso
    if (rep.status === 'solved') pinColor = '#27AE60'; // Solucionado

    const customIcon = L.divIcon({
      className: 'custom-comunimap-marker',
      html: `
        <div style="
          width: 26px;
          height: 26px;
          background-color: ${pinColor};
          border: 2px solid #FFFFFF;
          border-radius: 50% 50% 50% 0;
          transform: rotate(-45deg);
          box-shadow: 0 3px 8px rgba(0,0,0,0.35);
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
        ">
          <div style="width: 8px; height: 8px; background-color: #FFFFFF; border-radius: 50%;"></div>
        </div>
      `,
      iconSize: [26, 26],
      iconAnchor: [13, 26],
      popupAnchor: [0, -26]
    });

    const marker = L.marker([rep.lat, rep.lng], { icon: customIcon }).addTo(comuniMapInstance);

    const popupHtml = `
      <div style="font-family: 'Open Sans', sans-serif; min-width: 200px; font-size: 11px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
          <span style="font-weight: 700; color: #103554; font-size: 10px; background: #E5EBF4; padding: 1px 6px; border-radius: 4px;">${rep.category}</span>
          <span style="font-weight: 700; font-size: 9px; color: ${pinColor};">${rep.statusLabel}</span>
        </div>
        <div style="font-weight: 700; color: #103554; font-size: 12px; margin-bottom: 4px;">${rep.title}</div>
        <div style="color: #555; margin-bottom: 6px;">${rep.location}</div>
        <div style="font-size: 10px; color: #777; margin-bottom: 8px;">Prioridad: <strong>${rep.priority}</strong> · ${rep.date}</div>
        <div style="display: flex; gap: 4px;">
          <button onclick="toggleIncidentStatus(${rep.id})" style="flex:1; background: #FFA51E; color:#FFF; border:none; padding:4px 8px; border-radius:3px; cursor:pointer; font-weight:700; font-size:10px;">
            Cambiar Estado
          </button>
          <button onclick="openPdfModal('individual', ${rep.id})" style="background: #103554; color:#FFF; border:none; padding:4px 8px; border-radius:3px; cursor:pointer; font-weight:700; font-size:10px;">
            PDF
          </button>
        </div>
      </div>
    `;

    marker.bindPopup(popupHtml);
    comuniMarkers.push(marker);
  });
}

window.filterMapCategory = function(catKey, element) {
  currentMapCatFilter = catKey;
  document.querySelectorAll('.filter-pill').forEach(btn => btn.classList.remove('active'));
  if (element) element.classList.add('active');
  renderComuniMarkers();
  showToast(`Filtrando mapa: ${catKey === 'all' ? 'Todas las categorías' : catKey.toUpperCase()}`);
};

function renderHomeRecentIncidents() {
  const container = document.getElementById('homeRecentIncidentsList');
  if (!container) return;

  const countLabel = document.getElementById('homeRecentCountLabel');
  if (countLabel) countLabel.textContent = `${comReportsData.length} registros`;

  let html = '';
  // Mostrar los primeros 5 más recientes
  comReportsData.slice(0, 5).forEach(rep => {
    let statusClass = 'status-red';
    if (rep.status === 'process') statusClass = 'status-amber';
    if (rep.status === 'solved') statusClass = 'status-green';

    html += `
      <div class="incident-summary-card ${statusClass}" onclick="openPdfModal('individual', ${rep.id})">
        <div class="incident-sum-top">
          <span class="incident-cat-tag">${rep.category}</span>
          <span class="status-badge ${rep.status}">${rep.statusLabel}</span>
        </div>
        <div class="incident-sum-title">${rep.title}</div>
        <div class="incident-sum-loc">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
          </svg>
          <span>${rep.location}</span>
        </div>
        <div style="font-size: 9px; color: var(--text-muted); margin-top: 4px; display: flex; justify-content: space-between;">
          <span>Prioridad: <strong>${rep.priority}</strong></span>
          <span>${rep.date}</span>
        </div>
      </div>
    `;
  });

  container.innerHTML = html;
}

// ============================================================================
// 6. Vista 2: Alertas (Registro de Problemas Comunitarios)
// ============================================================================
function initPickerMap() {
  const pickerEl = document.getElementById('pickerMapLeaflet');
  if (!pickerEl) return;

  const defaultCoords = [-17.4150, -66.1420];

  pickerMapInstance = L.map('pickerMapLeaflet', {
    center: defaultCoords,
    zoom: 16
  });

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap | OTB Barrio Universitario Alto'
  }).addTo(pickerMapInstance);

  // Marcador inicial
  pickerMarker = L.marker(defaultCoords, { draggable: true }).addTo(pickerMapInstance);

  // Evento al mover el marcador arrastrándolo
  pickerMarker.on('dragend', () => {
    const pos = pickerMarker.getLatLng();
    updatePickerCoords(pos.lat, pos.lng);
  });

  // Evento al hacer clic en cualquier lugar del mapa
  pickerMapInstance.on('click', (e) => {
    pickerMarker.setLatLng(e.latlng);
    updatePickerCoords(e.latlng.lat, e.latlng.lng);
  });
}

function updatePickerCoords(lat, lng) {
  const latStr = lat.toFixed(5);
  const lngStr = lng.toFixed(5);

  const coordInput = document.getElementById('alertCoordinates');
  if (coordInput) coordInput.value = `${latStr}, ${lngStr}`;

  const coordsDisplay = document.getElementById('pickerCoordsDisplay');
  if (coordsDisplay) coordsDisplay.textContent = `Lat: ${latStr}, Lng: ${lngStr}`;

  showToast(`Ubicación fijada en mapa: [${latStr}, ${lngStr}]`);
}

window.centerPickerOnGPS = function() {
  // Simulación de geolocalización precisa en el Barrio Universitario Alto
  const userLat = -17.4172;
  const userLng = -66.1410;

  if (pickerMapInstance && pickerMarker) {
    pickerMapInstance.setView([userLat, userLng], 17);
    pickerMarker.setLatLng([userLat, userLng]);
    updatePickerCoords(userLat, userLng);
    showToast('GPS activado: Ubicación centrada en tu posición actual en la OTB.');
  }
};

window.handleAlertPhotoSelect = function(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    const label = document.getElementById('alertFileNameLabel');
    if (label) label.textContent = `Archivo: ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;

    const preview = document.getElementById('alertPhotoPreview');
    if (preview) {
      const reader = new FileReader();
      reader.onload = (e) => {
        preview.src = e.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(file);
    }
  }
};

window.resetAlertForm = function() {
  document.getElementById('alertRegisterForm')?.reset();
  const preview = document.getElementById('alertPhotoPreview');
  if (preview) preview.style.display = 'none';
  const label = document.getElementById('alertFileNameLabel');
  if (label) label.textContent = 'Formatos JPG, PNG (Cámara o galería)';
  showToast('Formulario de alerta restablecido');
};

window.handleAlertSubmit = function(e) {
  e.preventDefault();

  const category = document.getElementById('alertCategory')?.value || 'Baches';
  const title = document.getElementById('alertTitle')?.value || 'Problema Comunitario';
  const location = document.getElementById('alertLocation')?.value || 'Barrio Universitario Alto';
  const coords = document.getElementById('alertCoordinates')?.value || '-17.4150, -66.1420';
  const desc = document.getElementById('alertDesc')?.value || '';

  const coordParts = coords.split(',').map(s => parseFloat(s.trim()));
  const lat = !isNaN(coordParts[0]) ? coordParts[0] : -17.4150;
  const lng = !isNaN(coordParts[1]) ? coordParts[1] : -66.1420;

  let catKey = 'baches';
  if (category.includes('Alumbrado')) catKey = 'alumbrado';
  if (category.includes('Basura')) catKey = 'basura';
  if (category.includes('Agua')) catKey = 'agua';
  if (category.includes('Seguridad')) catKey = 'seguridad';

  // Cálculo automático de prioridad: a mayor cantidad de reportes similares, mayor prioridad
  const similarReportsCount = comReportsData.filter(r => r.categoryKey === catKey || r.category === category).length;
  let priority = 'Media';
  if (similarReportsCount >= 3) {
    priority = 'Urgente';
  } else if (similarReportsCount >= 1) {
    priority = 'Alta';
  } else {
    priority = 'Media';
  }

  const newCode = `REP-00${comReportsData.length + 1}`;
  const authorName = currentUser ? currentUser.name : 'Vecino Comunitario';

  const newReport = {
    id: comReportsData.length + 1,
    code: newCode,
    title,
    category,
    categoryKey: catKey,
    location,
    lat,
    lng,
    status: 'pending',
    statusLabel: 'Pendiente',
    priority,
    date: 'Hoy (25 Sep 2026)',
    author: authorName,
    desc
  };

  // Insertar al inicio de la lista
  comReportsData.unshift(newReport);

  // Actualizar componentes dependientes
  renderComuniMarkers();
  updateMetricsCounters();
  renderHomeRecentIncidents();
  renderReportsTable();
  resetAlertForm();

  showToast(`¡Alerta ${newCode} registrada exitosamente! Mostrando en la tabla de reportes...`);
  
  // Cambiar a la vista de reportes para que el usuario verifique la tabla
  switchView('reportes');
};

// ============================================================================
// 7. Vista 3: Reportes (Tabla Completa, Filtros y Acciones)
// ============================================================================
window.applyReportFilters = function() {
  renderReportsTable();
};

window.setReportStatusFilter = function(status) {
  const select = document.getElementById('filterStatusSelect');
  if (select) {
    select.value = status;
    applyReportFilters();
  }
};

function renderReportsTable() {
  const tbody = document.getElementById('reportsTableBody');
  if (!tbody) return;

  const searchText = (document.getElementById('tableSearchInput')?.value || '').toLowerCase();
  const catFilter = document.getElementById('filterCatSelect')?.value || 'all';
  const statusFilter = document.getElementById('filterStatusSelect')?.value || 'all';
  const priorityFilter = document.getElementById('filterPrioritySelect')?.value || 'all';

  const filtered = comReportsData.filter(rep => {
    // Filtro por texto
    if (searchText) {
      const matchTitle = rep.title.toLowerCase().includes(searchText);
      const matchLoc = rep.location.toLowerCase().includes(searchText);
      const matchCode = rep.code.toLowerCase().includes(searchText);
      if (!matchTitle && !matchLoc && !matchCode) return false;
    }

    // Filtro por categoría
    if (catFilter !== 'all' && rep.categoryKey !== catFilter) {
      return false;
    }

    // Filtro por estado
    if (statusFilter !== 'all' && rep.status !== statusFilter) {
      return false;
    }

    // Filtro por prioridad
    if (priorityFilter !== 'all' && rep.priority !== priorityFilter) {
      return false;
    }

    return true;
  });

  const countLabel = document.getElementById('reportsCountLabel');
  if (countLabel) {
    countLabel.textContent = `Mostrando ${filtered.length} de ${comReportsData.length} incidencias`;
  }

  if (filtered.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="8" style="text-align: center; padding: 24px; color: var(--text-secondary);">
          No se encontraron incidencias que coincidan con los filtros aplicados.
        </td>
      </tr>
    `;
    return;
  }

  let rowsHtml = '';
  filtered.forEach(rep => {
    let priorityClass = 'baja';
    if (rep.priority === 'Urgente') priorityClass = 'urgente';
    if (rep.priority === 'Alta') priorityClass = 'alta';
    if (rep.priority === 'Media') priorityClass = 'media';

    rowsHtml += `
      <tr>
        <td><strong>${rep.code}</strong></td>
        <td>
          <div style="font-weight: 700; color: var(--navy);">${rep.title}</div>
          <div style="font-size: 10px; color: var(--text-muted); max-width: 280px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
            ${rep.desc}
          </div>
        </td>
        <td>
          <span style="font-size: 10px; font-weight: 700; background: #E5EBF4; color: var(--navy); padding: 2px 6px; border-radius: 4px;">
            ${rep.category}
          </span>
        </td>
        <td>
          <div style="font-size: 11px;">${rep.location}</div>
          <div style="font-size: 9px; color: var(--text-muted);">Coord: [${rep.lat}, ${rep.lng}]</div>
        </td>
        <td>
          <span class="priority-tag ${priorityClass}">${rep.priority}</span>
        </td>
        <td>
          <span class="status-badge ${rep.status}" style="cursor: pointer;" onclick="toggleIncidentStatus(${rep.id})" title="Clic para cambiar de estado">
            ${rep.statusLabel}
          </span>
        </td>
        <td style="font-size: 11px; color: var(--text-secondary); white-space: nowrap;">
          ${rep.date}
        </td>
        <td>
          <div class="table-actions" style="justify-content: flex-end;">
            <button class="btn-action-icon" onclick="openPdfModal('individual', ${rep.id})" title="Ficha Técnica en PDF">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
              </svg>
            </button>
            <button class="btn-action-icon" onclick="toggleIncidentStatus(${rep.id})" title="Cambiar Estado">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 4 23 10 17 10"></polyline>
                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
              </svg>
            </button>
            <button class="btn-action-icon danger" onclick="deleteIncident(${rep.id})" title="Eliminar Reporte">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              </svg>
            </button>
          </div>
        </td>
      </tr>
    `;
  });

  tbody.innerHTML = rowsHtml;
}

window.toggleIncidentStatus = function(reportId) {
  const rep = comReportsData.find(r => r.id === reportId);
  if (!rep) return;

  if (rep.status === 'pending') {
    rep.status = 'process';
    rep.statusLabel = 'En Proceso';
    showToast(`${rep.code}: Cuadrilla municipal asignada (En Proceso)`);
  } else if (rep.status === 'process') {
    rep.status = 'solved';
    rep.statusLabel = 'Solucionado';
    showToast(`${rep.code}: Incidencia marcada como SOLUCIONADA ✓`);
  } else {
    rep.status = 'pending';
    rep.statusLabel = 'Pendiente';
    showToast(`${rep.code}: Incidencia reabierta como PENDIENTE`);
  }

  renderComuniMarkers();
  updateMetricsCounters();
  renderHomeRecentIncidents();
  renderReportsTable();
};

window.deleteIncident = function(reportId) {
  const rep = comReportsData.find(r => r.id === reportId);
  if (!rep) return;

  if (confirm(`¿Confirma eliminar el reporte ${rep.code} (${rep.title})?`)) {
    comReportsData = comReportsData.filter(r => r.id !== reportId);
    renderComuniMarkers();
    updateMetricsCounters();
    renderHomeRecentIncidents();
    renderReportsTable();
    showToast(`Reporte ${rep.code} eliminado correctamente.`);
  }
};

// ============================================================================
// 8. Vista 4: Usuarios (Administración de Dirigentes, Cuadrillas y Vecinos)
// ============================================================================
window.toggleNewUserForm = function() {
  const formCard = document.getElementById('newUserFormCard');
  if (formCard) {
    const isHidden = formCard.style.display === 'none';
    formCard.style.display = isHidden ? 'block' : 'none';
  }
};

window.applyUserFilters = function() {
  renderUsersTable();
};

function renderUsersTable() {
  const tbody = document.getElementById('usersTableBody');
  if (!tbody) return;

  const searchText = (document.getElementById('userSearchInput')?.value || '').toLowerCase();
  const roleFilter = document.getElementById('userRoleFilterSelect')?.value || 'all';

  const filtered = communityUsersData.filter(u => {
    if (searchText) {
      const matchName = u.name.toLowerCase().includes(searchText);
      const matchEmail = u.email.toLowerCase().includes(searchText);
      if (!matchName && !matchEmail) return false;
    }

    if (roleFilter !== 'all' && u.role !== roleFilter) {
      return false;
    }

    return true;
  });

  const totalLabel = document.getElementById('userCountTotal');
  if (totalLabel) totalLabel.textContent = communityUsersData.length + 147;

  let html = '';
  filtered.forEach(u => {
    let rolePillClass = 'vecino';
    if (u.role === 'Admin') rolePillClass = 'admin';
    if (u.role === 'Técnico') rolePillClass = 'tecnico';

    html += `
      <tr>
        <td>
          <div style="display: flex; align-items: center; gap: 8px;">
            <img src="${u.avatar}" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=120';">
            <div>
              <div style="font-weight: 700; color: var(--navy);">${u.name}</div>
            </div>
          </div>
        </td>
        <td>
          <div style="font-size: 11px;">${u.email}</div>
          <div style="font-size: 10px; color: var(--orange); font-weight: 600;">${u.phone}</div>
        </td>
        <td>
          <span class="user-role-pill ${rolePillClass}">${u.role}</span>
        </td>
        <td style="font-size: 11px;">
          ${u.location}
        </td>
        <td>
          <span style="font-size: 10px; font-weight: 700; color: ${u.status === 'Activo' ? '#27AE60' : '#E74C3C'};">
            ● ${u.status}
          </span>
        </td>
        <td>
          <div class="table-actions" style="justify-content: flex-end;">
            <button class="btn-action-icon" onclick="toggleUserStatus(${u.id})" title="Alternar Estado Activo/Inactivo">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
              </svg>
            </button>
            <button class="btn-action-icon danger" onclick="deleteCommunityUser(${u.id})" title="Eliminar Usuario">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              </svg>
            </button>
          </div>
        </td>
      </tr>
    `;
  });

  tbody.innerHTML = html;
}

window.handleAdminCreateUser = function(e) {
  e.preventDefault();
  const name = document.getElementById('newUserName')?.value;
  const email = document.getElementById('newUserEmail')?.value;
  const role = document.getElementById('newUserRole')?.value || 'Vecino';
  const phone = document.getElementById('newUserPhone')?.value || '70000000';
  const location = document.getElementById('newUserLocation')?.value || 'Barrio Universitario Alto';

  const newUser = {
    id: communityUsersData.length + 1,
    name,
    email,
    phone,
    role,
    roleLabel: role === 'Admin' ? 'Dirigente OTB' : role === 'Técnico' ? 'Cuadrilla Técnica' : 'Vecino Comunitario',
    location,
    status: 'Activo',
    avatar: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=120'
  };

  communityUsersData.push(newUser);
  renderUsersTable();
  toggleNewUserForm();
  document.getElementById('adminUserForm')?.reset();
  showToast(`Usuario ${name} registrado con rol: ${role}`);
};

window.toggleUserStatus = function(userId) {
  const u = communityUsersData.find(x => x.id === userId);
  if (!u) return;

  u.status = (u.status === 'Activo') ? 'Inactivo' : 'Activo';
  renderUsersTable();
  showToast(`Estado de ${u.name} cambiado a: ${u.status}`);
};

window.deleteCommunityUser = function(userId) {
  const u = communityUsersData.find(x => x.id === userId);
  if (!u) return;

  if (confirm(`¿Desea eliminar la cuenta de ${u.name}?`)) {
    communityUsersData = communityUsersData.filter(x => x.id !== userId);
    renderUsersTable();
    showToast(`Cuenta de ${u.name} eliminada.`);
  }
};

// ============================================================================
// 9. Vista 5: Estadísticas (Métricas Trasladadas de Inicio y Gráficos Canvas)
// ============================================================================
let statsTrendCanvas, statsTrendCtx;
let statsCategoryCanvas, statsCategoryCtx;

function initStatsCharts() {
  statsTrendCanvas = document.getElementById('statsTrendCanvas');
  if (statsTrendCanvas) statsTrendCtx = statsTrendCanvas.getContext('2d');

  statsCategoryCanvas = document.getElementById('statsCategoryCanvas');
  if (statsCategoryCanvas) statsCategoryCtx = statsCategoryCanvas.getContext('2d');

  drawStatsTrendChart();
  drawStatsCategoryChart();
}

function drawStatsTrendChart() {
  if (!statsTrendCanvas || !statsTrendCtx) return;

  const dpr = window.devicePixelRatio || 1;
  const rect = statsTrendCanvas.getBoundingClientRect();
  if (rect.width === 0 || rect.height === 0) return;

  statsTrendCanvas.width = rect.width * dpr;
  statsTrendCanvas.height = rect.height * dpr;
  statsTrendCtx.scale(dpr, dpr);

  const w = rect.width;
  const h = rect.height;
  statsTrendCtx.clearRect(0, 0, w, h);

  // Eje base y cuadrícula sutil
  statsTrendCtx.strokeStyle = '#E5E9EF';
  statsTrendCtx.lineWidth = 1;
  for (let i = 1; i <= 4; i++) {
    const y = (h / 5) * i;
    statsTrendCtx.beginPath();
    statsTrendCtx.moveTo(40, y);
    statsTrendCtx.lineTo(w - 10, y);
    statsTrendCtx.stroke();
  }

  // Meses en el eje X
  const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep'];
  statsTrendCtx.fillStyle = '#7B8794';
  statsTrendCtx.font = '10px "Open Sans", sans-serif';
  statsTrendCtx.textAlign = 'center';

  const stepX = (w - 60) / (months.length - 1);
  months.forEach((m, idx) => {
    const x = 45 + idx * stepX;
    statsTrendCtx.fillText(m, x, h - 6);
  });

  // Curva 1: Alumbrado Público (Naranja)
  const orangeValues = [12, 18, 15, 22, 28, 35, 30, 24, 28];
  drawChartLine(statsTrendCtx, orangeValues, 45, stepX, h - 25, 40, '#FFA51E', 'rgba(255, 165, 30, 0.15)');

  // Curva 2: Baches y Calzadas (Azul marino)
  const navyValues = [25, 32, 28, 20, 15, 12, 18, 22, 24];
  drawChartLine(statsTrendCtx, navyValues, 45, stepX, h - 25, 40, '#103554', 'rgba(16, 53, 84, 0.12)');
}

function drawChartLine(ctx, values, startX, stepX, zeroY, scaleY, strokeColor, fillColor) {
  const points = values.map((val, idx) => {
    return {
      x: startX + idx * stepX,
      y: zeroY - (val / 40) * (zeroY - 20)
    };
  });

  // Área sombreada
  ctx.beginPath();
  ctx.moveTo(points[0].x, points[0].y);
  for (let i = 0; i < points.length - 1; i++) {
    const p0 = points[i];
    const p1 = points[i + 1];
    const cpX = (p0.x + p1.x) / 2;
    ctx.bezierCurveTo(cpX, p0.y, cpX, p1.y, p1.x, p1.y);
  }
  ctx.lineTo(points[points.length - 1].x, zeroY);
  ctx.lineTo(points[0].x, zeroY);
  ctx.closePath();
  ctx.fillStyle = fillColor;
  ctx.fill();

  // Línea trazada
  ctx.beginPath();
  ctx.moveTo(points[0].x, points[0].y);
  for (let i = 0; i < points.length - 1; i++) {
    const p0 = points[i];
    const p1 = points[i + 1];
    const cpX = (p0.x + p1.x) / 2;
    ctx.bezierCurveTo(cpX, p0.y, cpX, p1.y, p1.x, p1.y);
  }
  ctx.strokeStyle = strokeColor;
  ctx.lineWidth = 2.5;
  ctx.stroke();

  // Puntos circulares
  points.forEach(pt => {
    ctx.beginPath();
    ctx.arc(pt.x, pt.y, 3, 0, Math.PI * 2);
    ctx.fillStyle = '#FFFFFF';
    ctx.fill();
    ctx.strokeStyle = strokeColor;
    ctx.lineWidth = 2;
    ctx.stroke();
  });
}

function drawStatsCategoryChart() {
  if (!statsCategoryCanvas || !statsCategoryCtx) return;

  const dpr = window.devicePixelRatio || 1;
  const rect = statsCategoryCanvas.getBoundingClientRect();
  if (rect.width === 0 || rect.height === 0) return;

  statsCategoryCanvas.width = rect.width * dpr;
  statsCategoryCanvas.height = rect.height * dpr;
  statsCategoryCtx.scale(dpr, dpr);

  const w = rect.width;
  const h = rect.height;
  statsCategoryCtx.clearRect(0, 0, w, h);

  const categories = [
    { label: 'Alumbrado', count: 48, pct: '35%', color: '#FFA51E' },
    { label: 'Baches', count: 38, pct: '28%', color: '#103554' },
    { label: 'Basura', count: 30, pct: '22%', color: '#2A4E70' },
    { label: 'Agua Potable', count: 16, pct: '11%', color: '#27AE60' },
    { label: 'Seguridad', count: 10, pct: '4%', color: '#E74C3C' }
  ];

  const maxVal = 50;
  const barWidth = 36;
  const gap = (w - (barWidth * categories.length)) / (categories.length + 1);

  categories.forEach((cat, i) => {
    const x = gap + i * (barWidth + gap);
    const barHeight = (cat.count / maxVal) * (h - 50);
    const y = (h - 26) - barHeight;

    // Barra
    statsCategoryCtx.fillStyle = cat.color;
    statsCategoryCtx.beginPath();
    statsCategoryCtx.roundRect(x, y, barWidth, barHeight, [3, 3, 0, 0]);
    statsCategoryCtx.fill();

    // Valor arriba
    statsCategoryCtx.fillStyle = '#103554';
    statsCategoryCtx.font = 'bold 10px "Open Sans", sans-serif';
    statsCategoryCtx.textAlign = 'center';
    statsCategoryCtx.fillText(cat.pct, x + barWidth / 2, y - 5);

    // Etiqueta abajo
    statsCategoryCtx.fillStyle = '#7B8794';
    statsCategoryCtx.font = '9px "Open Sans", sans-serif';
    statsCategoryCtx.fillText(cat.label, x + barWidth / 2, h - 8);
  });
}

// ============================================================================
// 10. Actualización de Contadores y Métricas Dinámicas (Alertas y Reportes)
// ============================================================================
function updateMetricsCounters() {
  const pendingIncidents = comReportsData.filter(r => r.status === 'pending');
  const processIncidents = comReportsData.filter(r => r.status === 'process');
  const solvedIncidents = comReportsData.filter(r => r.status === 'solved');

  // Conteo real de alertas e incidencias pendientes en el sistema
  const realPendingCount = pendingIncidents.length;

  // Actualizar badges funcionales en las vistas de Alertas y Reportes
  const elAlertasPending = document.getElementById('alertasPendingCount');
  if (elAlertasPending) elAlertasPending.textContent = realPendingCount;

  const elReportsPending = document.getElementById('reportsPendingCount');
  if (elReportsPending) elReportsPending.textContent = realPendingCount;

  const elReportsToolbarPending = document.getElementById('reportsToolbarPendingCount');
  if (elReportsToolbarPending) elReportsToolbarPending.textContent = realPendingCount;

  // Métricas para la vista de Estadísticas
  const total = comReportsData.length + 135;
  const process = processIncidents.length + 32;
  const solved = solvedIncidents.length + 96;

  const elTotal = document.getElementById('statTotalReports');
  const elProcess = document.getElementById('statProcessReports');
  const elSolved = document.getElementById('statSolvedReports');
  const elPending = document.getElementById('statPendingAlerts');
  const elBadgeAlerts = document.getElementById('badgeActiveAlerts');

  if (elTotal) elTotal.textContent = total;
  if (elProcess) elProcess.textContent = process;
  if (elSolved) elSolved.textContent = solved;
  if (elPending) elPending.textContent = realPendingCount;
  if (elBadgeAlerts) elBadgeAlerts.textContent = realPendingCount;
}

// ============================================================================
// 11. Generador de Documentos Oficiales en PDF (Imprimible)
// ============================================================================
window.openPdfModal = function(type, reportId) {
  const modal = document.getElementById('pdfPreviewModal');
  const content = document.getElementById('pdfPrintableContent');
  const title = document.getElementById('pdfSheetTitle');
  if (!modal || !content) return;

  const currentDate = new Date().toLocaleDateString('es-BO', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  });

  if (type === 'general') {
    if (title) title.textContent = 'Informe Técnico Municipal Consolidado (Formato PDF)';
    content.innerHTML = `
      <div class="pdf-letterhead">
        <div>
          <div class="pdf-inst-name">INSTITUTO TÉCNICO NACIONAL DE COMERCIO "FEDERICO ÁLVAREZ PLATA"</div>
          <div style="font-size: 10px; color: #4A5568;">CARRERA DE SISTEMAS INFORMÁTICOS · PROYECTO DE GRADO</div>
          <div class="pdf-otb-title">SISTEMA COMUNIMAP — OTB BARRIO UNIVERSITARIO ALTO</div>
          <div style="font-size: 9px; color: #718096;">Km 4 Av. Petrolera · Distrito 8 · Cochabamba, Bolivia</div>
        </div>
        <div style="text-align: right;">
          <div style="font-weight: 700; color: var(--navy); font-size: 12px;">INFORME N° 09/2026</div>
          <div style="font-size: 9px; color: #718096;">Fecha de emisión: ${currentDate}</div>
          <div style="font-size: 9px; color: #27AE60; font-weight: 600;">Estado: Oficial / Trámite</div>
        </div>
      </div>

      <div style="margin-bottom: 12px;">
        <h3 style="font-size: 13px; color: var(--navy); margin-bottom: 4px;">CONSOLIDADO DE INCIDENCIAS COMUNITARIAS GEORREFERENCIADAS</h3>
        <p style="font-size: 10px; color: #4A5568;">
          El presente informe detalla las problemáticas vecinales registradas y georreferenciadas a través de la plataforma <strong>ComuniMap</strong> en la OTB Barrio Universitario Alto, con el fin de remitir requerimientos a las cuadrillas de SEMAPA, Alumbrado Público y Obras Públicas de la Comuna Alejo Calatayud.
        </p>
      </div>

      <table class="pdf-table-print">
        <thead>
          <tr>
            <th>Cód.</th>
            <th>Tipo / Problema</th>
            <th>Ubicación / Sector</th>
            <th>Coordenadas</th>
            <th>Prioridad</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          ${comReportsData.map(r => `
            <tr>
              <td><strong>${r.code}</strong></td>
              <td>${r.title}</td>
              <td>${r.location}</td>
              <td style="font-size: 9px;">[${r.lat}, ${r.lng}]</td>
              <td><span style="font-weight:700; color:${r.priority === 'Urgente' ? '#E74C3C' : '#2C3E50'};">${r.priority}</span></td>
              <td>${r.statusLabel}</td>
            </tr>
          `).join('')}
        </tbody>
      </table>

      <div class="pdf-sign-row">
        <div class="pdf-sign-box">
          <strong>Fernando Corrales Inturias</strong><br>
          Dirigente / Gestor Comunitario OTB<br>
          C.I. 5241890 Cbba
        </div>
        <div class="pdf-sign-box">
          <strong>Arleth Helen Calizaya Almendras</strong><br>
          Desarrolladora y Coordinadora Técnica<br>
          ComuniMap Cochabamba
        </div>
        <div class="pdf-sign-box">
          <strong>Mesa Directiva OTB</strong><br>
          Barrio Universitario Alto<br>
          Sello y Visto Bueno
        </div>
      </div>
    `;
  } else {
    // Ficha Técnica Individual
    const rep = comReportsData.find(r => r.id === reportId) || comReportsData[0];
    if (title) title.textContent = `Ficha Técnica Individual: ${rep.code}`;
    content.innerHTML = `
      <div class="pdf-letterhead">
        <div>
          <div class="pdf-inst-name">SISTEMA DIGITAL COMUNIMAP — FICHA OFICIAL DE REPORTE</div>
          <div class="pdf-otb-title">OTB BARRIO UNIVERSITARIO ALTO · COCHABAMBA</div>
          <div style="font-size: 9px; color: #718096;">Inspección y Seguimiento de Obras Comunales</div>
        </div>
        <div style="text-align: right;">
          <div style="font-weight: 700; color: #E74C3C; font-size: 13px;">${rep.code}</div>
          <div style="font-size: 9px; color: #718096;">Fecha: ${rep.date}</div>
        </div>
      </div>

      <div style="background: #F7FAFC; border: 1px solid #E2E8F0; padding: 12px; border-radius: 4px; margin-bottom: 14px;">
        <div style="font-size: 14px; font-weight: 700; color: var(--navy);">${rep.title}</div>
        <div style="font-size: 11px; color: #4A5568; margin-top: 2px;">Categoría: <strong>${rep.category}</strong> · Prioridad: <strong style="color:#E74C3C;">${rep.priority}</strong></div>
      </div>

      <table class="pdf-table-print">
        <tr>
          <th style="width: 25%;">Ubicación / Sector:</th>
          <td>${rep.location}</td>
        </tr>
        <tr>
          <th>Georreferencia GPS:</th>
          <td>Latitud: ${rep.lat}, Longitud: ${rep.lng} (Datum WGS84)</td>
        </tr>
        <tr>
          <th>Estado Actual:</th>
          <td><strong>${rep.statusLabel.toUpperCase()}</strong></td>
        </tr>
        <tr>
          <th>Vecino / Responsable:</th>
          <td>${rep.author}</td>
        </tr>
        <tr>
          <th>Descripción Técnica:</th>
          <td>${rep.desc}</td>
        </tr>
      </table>

      <div style="margin-top: 14px; padding: 10px; border: 1px dashed #CBD5E0; border-radius: 4px; font-size: 10px; color: #4A5568;">
        <strong>Dictamen para Cuadrilla Municipal:</strong> La presente ficha constituye constancia oficial de notificación vecinal. Se solicita a la unidad municipal correspondiente programar la cuadrilla técnica a la brevedad conforme al nivel de prioridad asignado.
      </div>

      <div class="pdf-sign-row" style="margin-top: 40px;">
        <div class="pdf-sign-box">
          Firma Vecino Solicitante<br>
          ${rep.author}
        </div>
        <div class="pdf-sign-box">
          Firma y Sello Dirigencia OTB<br>
          Barrio Universitario Alto
        </div>
      </div>
    `;
  }

  modal.classList.add('active');
};

window.closePdfModal = function() {
  const modal = document.getElementById('pdfPreviewModal');
  if (modal) modal.classList.remove('active');
};

window.printOfficialPdf = function() {
  window.print();
};

// ============================================================================
// 12. Utilidades: Menú Móvil y Notificaciones Toast
// ============================================================================
window.toggleMobileSidebar = function() {
  const sidebar = document.getElementById('sidebar');
  if (sidebar) sidebar.classList.toggle('open');
};

function initMobileDrawer() {
  const btn = document.getElementById('mobileMenuBtn');
  const sidebar = document.getElementById('sidebar');

  if (btn && sidebar) {
    document.addEventListener('click', (e) => {
      if (window.innerWidth <= 768 && sidebar.classList.contains('open')) {
        if (!sidebar.contains(e.target) && !btn.contains(e.target)) {
          sidebar.classList.remove('open');
        }
      }
    });
  }
}

function showToast(message) {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = 'toast';
  toast.innerHTML = `
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#FFA51E" stroke-width="2.5">
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
      <polyline points="22 4 12 14.01 9 11.01"></polyline>
    </svg>
    <span>${message}</span>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(10px)';
    toast.style.transition = 'all 0.25s ease';
    setTimeout(() => toast.remove(), 250);
  }, 3200);
}
