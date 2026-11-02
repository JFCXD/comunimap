<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ComuniMap · Acceso comunitario</title>
    <link href="{{ secure_asset('ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ secure_asset('ample/css/style.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        :root { --cm-blue:#2563eb; --cm-navy:#112c50; --cm-ink:#203652; }
        html,body { min-height:100%; }
        body.cm-auth-body { background:linear-gradient(145deg,#edf4ff 0%,#f7f9fd 55%,#e8f2ee 100%)!important; color:var(--cm-ink); font-size:15px; }
        .cm-auth-shell { min-height:100vh; display:flex; justify-content:center; align-items:center; padding:28px 16px; }
        .cm-auth-panel { display:grid; grid-template-columns:minmax(270px,.85fr) minmax(360px,1fr); width:min(920px,100%); background:#fff; border-radius:22px; box-shadow:0 22px 65px rgba(19,46,88,.13); overflow:hidden; border:1px solid #e3ebf7; }
        .cm-auth-brand { background:linear-gradient(155deg,#10294b,#174579); color:#fff; padding:48px 38px; display:flex; flex-direction:column; justify-content:space-between; gap:48px; }
        .cm-auth-brand img { width:205px; max-width:100%; height:auto; max-height:90px; object-fit:contain; object-position:left center; }
        .cm-auth-brand h1 { color:#fff; font-size:29px; font-weight:750; letter-spacing:-.5px; line-height:1.2; margin-bottom:15px; }
        .cm-auth-brand p { color:#d2e2f6; line-height:1.7; margin:0; }
        .cm-auth-brand small { color:#b7cfe9; }
        .cm-auth-card { padding:40px 46px; align-self:center; min-width:0; }
        .cm-auth-title { font-size:27px; line-height:1.25; font-weight:750; color:#172f50; margin-bottom:7px; }
        .cm-auth-help { color:#64758d; margin-bottom:25px; line-height:1.5; }
        .cm-auth-card .form-label { font-weight:650; color:#344762; font-size:14px; margin-bottom:7px; }
        .cm-auth-card .form-control { min-height:46px; border:1px solid #d9e3f0; border-radius:10px; padding:10px 13px; font-size:15px; }
        .cm-auth-card .form-control:focus { border-color:#739bf1; box-shadow:0 0 0 3px rgba(37,99,235,.12); }
        .cm-auth-card .btn { min-height:47px; border-radius:10px; font-weight:700; }
        .cm-auth-card .btn-primary { background:var(--cm-blue); border-color:var(--cm-blue); }
        .cm-auth-card .btn-success { background:#16834a; border-color:#16834a; }
        .cm-auth-card a { color:#1d59ca; font-weight:650; }
        .cm-auth-note { background:#edf7f0; color:#286245; border:1px solid #d4e9db; padding:12px 14px; border-radius:10px; font-size:13px; line-height:1.5; }
        .cm-auth-switch { margin-top:24px; text-align:center; color:#63758e; }
        .cm-auth-recaptcha { max-width:100%; overflow-x:auto; }
        @media(max-width:760px) {
            .cm-auth-panel { grid-template-columns:1fr; width:min(520px,100%); border-radius:17px; }
            .cm-auth-brand { padding:22px 25px; gap:8px; }
            .cm-auth-brand img { width:155px; max-height:58px; }
            .cm-auth-brand h1 { display:none; }
            .cm-auth-brand p { font-size:13px; line-height:1.4; }
            .cm-auth-brand small { display:none; }
            .cm-auth-card { padding:28px 24px; }
            .cm-auth-shell { padding:16px 12px; align-items:flex-start; }
        }
        @media(max-width:380px) { .cm-auth-card { padding:24px 17px; } }
    </style>
</head>
<body class="cm-auth-body">
<main class="cm-auth-shell">
    <div class="cm-auth-panel">
        <aside class="cm-auth-brand" aria-label="ComuniMap">
            <div>
                <a href="/login" aria-label="ComuniMap">
                    <img src="{{ secure_asset('ample/plugins/images/comunimap-logo.png') }}" alt="ComuniMap">
                </a>
            </div>
            <div>
                <h1>Tu barrio, mejor comunicado.</h1>
                <p>Reporta problemas, consulta su seguimiento y participa en el cuidado de tu comunidad.</p>
            </div>
            <small>ComuniMap · Gestión de Alertas Comunitarias</small>
        </aside>
        <section class="cm-auth-card">
            @yield('contenido')
        </section>
    </div>
</main>
<script src="{{ secure_asset('ample/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ secure_asset('ample/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
