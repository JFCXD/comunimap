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
    </style>
</head>