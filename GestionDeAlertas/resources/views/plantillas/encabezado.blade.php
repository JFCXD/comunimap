<!DOCTYPE html>
<html dir="ltr" lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords"
        content="wrappixel, admin dashboard, html css dashboard, web dashboard, bootstrap 5 admin, bootstrap 5, css3 dashboard, bootstrap 5 dashboard, Ample lite admin bootstrap 5 dashboard, frontend, responsive bootstrap 5 admin template, Ample admin lite dashboard bootstrap 5 dashboard template">
    <meta name="description"
        content="Ample Admin Lite is powerful and clean admin dashboard template, inpired from Bootstrap Framework">
    <meta name="robots" content="noindex,nofollow">
    <title> Gestión Alerta de Problemas Comunitarios</title>
    <link rel="canonical" href="#/templates/ample-admin-lite/" />
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ secure_asset('ample/plugins/images/favicon.png') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ secure_asset('ample/plugins/bower_components/chartist/dist/chartist.min.css') }}">

    <link rel="stylesheet"
        href="{{ secure_asset('ample/plugins/bower_components/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ secure_asset('ample/css/style.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <!-- Ajustes propios de ComuniMap: se aplican después del CSS original Ample. -->
    <style>
      :root { --cm-navy:#122b4b; --cm-blue:#2563eb; --cm-green:#16834a; --cm-bg:#f3f6fb; }
      body { background:var(--cm-bg); color:#243651; }
      #main-wrapper[data-layout="vertical"] .left-sidebar[data-sidebarbg="skin6"],
      #main-wrapper[data-layout="vertical"] .left-sidebar[data-sidebarbg="skin6"] .sidebar-nav ul {
        background:var(--cm-navy)!important;
      }
      #main-wrapper[data-layout="vertical"] .left-sidebar[data-sidebarbg="skin6"] .sidebar-nav ul .sidebar-item .sidebar-link {
        color:#e6eefb!important; border-radius:10px; margin:2px 10px; padding:12px 13px;
        font-weight:600; font-size:14px;
      }
      #main-wrapper[data-layout="vertical"] .left-sidebar[data-sidebarbg="skin6"] .sidebar-nav ul .sidebar-item .sidebar-link i {
        color:#bdcdec!important; font-size:16px; width:21px; text-align:center;
      }
      #main-wrapper[data-layout="vertical"] .left-sidebar[data-sidebarbg="skin6"] .sidebar-nav ul .sidebar-item .sidebar-link:hover,
      #main-wrapper[data-layout="vertical"] .left-sidebar[data-sidebarbg="skin6"] .sidebar-nav ul .sidebar-item .sidebar-link.cm-selected {
        background:#2563eb!important; color:white!important;
      }
      #main-wrapper[data-layout="vertical"] .left-sidebar[data-sidebarbg="skin6"] .sidebar-nav ul .sidebar-item .sidebar-link:hover i,
      #main-wrapper[data-layout="vertical"] .left-sidebar[data-sidebarbg="skin6"] .sidebar-nav ul .sidebar-item .sidebar-link.cm-selected i {
        color:white!important;
      }
      #main-wrapper[data-layout="vertical"] .topbar .navbar-collapse[data-navbarbg="skin5"],
      #main-wrapper[data-layout="vertical"] .topbar[data-navbarbg="skin5"] {
        background:#ffffff!important;
      }
      #main-wrapper[data-layout="vertical"] .topbar .top-navbar .navbar-header[data-logobg="skin6"] {
        background:var(--cm-navy)!important;
      }
      .topbar { box-shadow:0 2px 16px rgba(13,42,78,.09); }
      .topbar .cm-account { display:flex; align-items:center; gap:11px; color:#233852; padding:10px 20px; }
      .topbar .cm-account-icon { color:#2563eb; font-size:27px; }
      .topbar .cm-account-name { font-weight:700; font-size:14px; line-height:1.2; }
      .topbar .cm-account-role { color:#728197; font-size:12px; }
      .topbar .nav-toggler { color:#fff!important; }
      .page-wrapper { background:var(--cm-bg)!important; }
      .page-breadcrumb { background:transparent!important; padding-top:25px!important; padding-bottom:14px!important; }
      .page-breadcrumb .page-title { font-size:25px; color:#183453; font-weight:750; }
      .card { border:1px solid #e4ebf5!important; border-radius:16px!important; box-shadow:0 5px 20px rgba(18,47,85,.06)!important; }
      .card-body { padding:22px; }
      .cm-subtitle { color:#65778e; font-size:14px; margin-top:7px; }
      .cm-quicklink { display:flex; align-items:center; gap:14px; color:#213a5b!important; text-decoration:none!important; }
      .cm-quicklink-icon { width:48px; height:48px; flex-shrink:0; border-radius:13px; background:#eaf1ff; color:var(--cm-blue); display:flex; align-items:center; justify-content:center; font-size:20px; }
      .cm-quicklink strong { display:block; font-size:15px; margin-bottom:3px; }
      .cm-quicklink small { display:block; font-size:12px; color:#6b7f96; }
      .cm-legend { display:flex; flex-wrap:wrap; gap:12px; color:#65778e; font-size:12px; }
      .cm-legend-dot { display:inline-block; width:9px; height:9px; margin-right:5px; border-radius:50%; }
      @media (max-width:767px) {
        .page-breadcrumb { padding:18px 15px 10px!important; }
        .page-breadcrumb .page-title { font-size:21px; }
        .container-fluid { padding-left:12px!important; padding-right:12px!important; }
        .card-body { padding:16px; }
        .topbar .cm-account { padding:8px 12px; }
      }

      /* Distribución compacta, pensada para pantallas de escritorio y móvil */
      .page-breadcrumb { padding-top:14px!important; padding-bottom:8px!important; }
      .page-breadcrumb .page-title { margin-bottom:2px; font-size:23px; }
      .container-fluid { padding-top:8px!important; }
      .cm-page-heading { border-left:4px solid var(--cm-blue); padding-left:14px; }
      .cm-page-heading .cm-subtitle { margin-top:4px; }
      .cm-overview-map { height:clamp(350px, 54vh, 530px); min-height:350px; border-radius:12px; overflow:hidden; }
      .cm-summary-list { display:grid; gap:12px; }
      .cm-summary-item { display:flex; align-items:center; justify-content:space-between; border-radius:12px; padding:13px 15px; background:#f3f7ff; }
      .cm-summary-item strong { font-size:24px; line-height:1.1; color:#17395e; }
      .cm-summary-item span { font-size:13px; color:#486078; }
      .cm-summary-item.cm-pending { background:#fff0ef; }
      .cm-summary-item.cm-working { background:#fff8e8; }
      .cm-summary-item.cm-solved { background:#eaf8f0; }
      .cm-summary-item.cm-pending strong { color:#c53939; }
      .cm-summary-item.cm-working strong { color:#976800; }
      .cm-summary-item.cm-solved strong { color:#17734b; }
      .cm-status-bar { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:15px; }
      .cm-status-pill { background:#f5f8fd; border:1px solid #e4ebf5; border-radius:12px; padding:10px 14px; display:flex; gap:10px; align-items:center; flex:1 1 135px; }
      .cm-status-pill strong { font-size:20px; color:#163355; }
      .cm-status-pill small { color:#697b92; font-size:12px; }
      .cm-report-table { min-width:680px; }
      .cm-report-table thead th { background:#f4f7fb; color:#3d526e; font-size:12px; border:0; padding:14px 12px; }
      .cm-report-table tbody td { padding:13px 12px; border-color:#edf1f7; vertical-align:middle; }
      .cm-report-table .badge { border-radius:20px; font-size:12px; padding:6px 10px; }
      .cm-footer.footer { position:relative!important; left:auto!important; right:auto!important; bottom:auto!important; width:100%; padding:15px 28px!important; background:transparent!important; color:#64748b!important; font-size:12px; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:8px; border-top:1px solid #e2eaf5; }
      .cm-footer strong { color:#2563eb; }
      @media (max-width:991px) { .cm-overview-map { height:430px; } }
      @media (max-width:767px) {
        .cm-overview-map { height:390px; min-height:300px; }
        .cm-footer.footer { padding:14px!important; }
        .page-breadcrumb { padding-top:15px!important; }
      }

      /* Escritorio: menú lateral inmóvil mientras se desplaza el contenido.
         Se conserva el espacio superior del logo y el menú móvil existente. */
      @media (min-width:768px) {
        #main-wrapper[data-layout="vertical"] .left-sidebar {
          position:fixed!important;
          top:0!important;
          bottom:0!important;
          height:100vh!important;
          height:100dvh!important;
          overflow-y:auto!important;
          overscroll-behavior:contain;
        }
        #main-wrapper[data-layout="vertical"] .left-sidebar .scroll-sidebar {
          height:auto!important;
          max-height:none!important;
          overflow:visible!important;
        }
      }

      /* Teléfonos: menú deslizable sin reducir el ancho de Inicio ni del mapa. */
      @media (max-width:767px) {
        #main-wrapper[data-layout="vertical"] { overflow-x:hidden; }
        #main-wrapper[data-layout="vertical"] .page-wrapper {
          margin-left:0!important; width:100%!important; min-width:0;
        }
        #main-wrapper[data-layout="vertical"] .left-sidebar {
          position:fixed!important; top:0!important; bottom:0!important;
          left:-280px!important; width:min(260px,85vw)!important;
          max-width:85vw; height:100dvh!important; z-index:1100!important;
          transition:left .23s ease!important; overflow-y:auto;
        }
        #main-wrapper[data-layout="vertical"].show-sidebar .left-sidebar {
          left:0!important;
        }
        #main-wrapper[data-layout="vertical"] .topbar .top-navbar .navbar-header {
          width:100%!important; min-width:0; display:flex; align-items:center;
          justify-content:space-between; padding:0 12px;
        }
        #main-wrapper[data-layout="vertical"] .topbar .navbar-brand {
          width:auto!important; margin:0; padding:5px 0;
        }
        #main-wrapper[data-layout="vertical"] .topbar .navbar-brand img {
          width:160px!important; height:48px!important; max-width:65vw!important;
        }
        #main-wrapper[data-layout="vertical"] .topbar .nav-toggler {
          display:inline-flex!important; align-items:center; justify-content:center;
          color:white!important; width:42px; height:42px; font-size:19px;
        }
        #main-wrapper[data-layout="vertical"].show-sidebar .page-wrapper:before {
          content:""; position:fixed; inset:0; z-index:1050;
          background:rgba(10,27,51,.42);
        }
        #main-wrapper[data-layout="vertical"] .card,
        #main-wrapper[data-layout="vertical"] .container-fluid { min-width:0; }
      }
    </style>
</head>