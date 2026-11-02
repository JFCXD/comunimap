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

    <style>
        :root { --cm-navy:#10284c; --cm-blue:#2563eb; --cm-green:#16834a; --cm-bg:#f2f6fc; }
        body { background:var(--cm-bg)!important; color:#1c2d45; }
        .topbar { box-shadow:0 2px 14px rgba(22,46,86,.08); }
        .topbar .navbar { background:#fff!important; min-height:72px; }
        .topbar .navbar-header { background:var(--cm-navy)!important; min-height:72px; }
        .topbar .navbar-brand { padding:8px 14px; display:flex; align-items:center; justify-content:center; }
        .topbar .profile-pic { color:#23334d!important; display:flex; align-items:center; gap:10px; padding:9px 18px; }
        .topbar .profile-pic span { color:#23334d!important; }
        .topbar .navbar-toggler,.topbar .nav-toggler { color:var(--cm-blue)!important; }
        .left-sidebar { background:var(--cm-navy)!important; box-shadow:4px 0 20px rgba(9,35,70,.07); }
        .left-sidebar .scroll-sidebar,.left-sidebar .sidebar-nav { background:transparent!important; }
        #sidebarnav { padding:16px 11px!important; }
        #sidebarnav .sidebar-item { margin-bottom:5px; }
        #sidebarnav .sidebar-link { border-radius:10px; padding:12px 14px!important; color:#dbeafe!important; display:flex; align-items:center; gap:12px; }
        #sidebarnav .sidebar-link i { color:#a7c9ff!important; font-size:16px; width:19px; text-align:center; }
        #sidebarnav .sidebar-link:hover,#sidebarnav .sidebar-link.cm-active { background:var(--cm-blue)!important; color:white!important; }
        #sidebarnav .sidebar-link:hover i,#sidebarnav .sidebar-link.cm-active i { color:white!important; }
        #sidebarnav .hide-menu { font-size:14px; font-weight:600; letter-spacing:.1px; }
        .page-wrapper { background:var(--cm-bg)!important; min-height:calc(100vh - 72px); }
        .page-breadcrumb { background:transparent!important; padding:24px 28px 12px!important; }
        .page-breadcrumb .page-title { color:#142c50; font-weight:750; font-size:24px; text-transform:none; }
        .container-fluid { padding:10px 26px 28px!important; }
        .card { border:1px solid #e5ecf6!important; border-radius:16px!important; box-shadow:0 5px 20px rgba(28,51,90,.055)!important; }
        .card-body { padding:22px; }
        .btn { border-radius:9px; }
        .form-control,.form-select { border-radius:9px; }
        .footer { background:transparent!important; color:#64748b!important; }
        .cm-kicker { display:block; color:#64748b; font-size:14px; margin-top:4px; }
        .cm-action-card { display:flex; align-items:center; gap:13px; text-decoration:none!important; color:#1d3555!important; }
        .cm-action-icon { width:48px; height:48px; border-radius:13px; display:flex; align-items:center; justify-content:center; flex-shrink:0; background:#e7efff; color:#2563eb; font-size:20px; }
        .cm-action-card strong { display:block; font-size:15px; }
        .cm-action-card small { color:#667991; font-size:12px; }
        .cm-map-heading { display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:8px; margin-bottom:16px; }
        .cm-map-legend { display:flex; flex-wrap:wrap; gap:9px; font-size:12px; color:#536680; }
        .cm-dot { width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:4px; }
        @media(max-width:767px) {
            .topbar .navbar-header { min-height:62px; }
            .topbar .navbar-brand img { max-width:155px!important; height:48px!important; }
            .page-breadcrumb { padding:20px 15px 8px!important; }
            .container-fluid { padding:8px 12px 22px!important; }
            .card-body { padding:16px; }
            .page-breadcrumb .page-title { font-size:21px; }
        }
    </style>
</head>