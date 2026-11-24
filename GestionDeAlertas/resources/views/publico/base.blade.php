<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('descripcion', 'Información pública de ComuniMap, plataforma de gestión comunitaria.')">
    <meta name="robots" content="index,follow">
    <title>@yield('titulo') | ComuniMap</title>
    <link rel="canonical" href="{{ url()->current() }}">
    <style>
        :root {color-scheme:light;--navy:#122b4b;--blue:#2563eb;--muted:#53657e;--bg:#f3f6fb}
        *{box-sizing:border-box}
        body{margin:0;background:var(--bg);font-family:system-ui,-apple-system,"Segoe UI",Arial,sans-serif;color:#23374e;line-height:1.7}
        a{color:#1858c5;text-underline-offset:3px}
        a:focus-visible{outline:3px solid #3b82f6;outline-offset:3px}
        header{background:var(--navy);color:white;padding:20px max(22px,calc((100% - 980px)/2))}
        header .brand{font-weight:800;font-size:24px;letter-spacing:-.5px;color:white;text-decoration:none}
        header small{display:block;color:#c8daf5;line-height:1.4}
        nav{background:white;border-bottom:1px solid #dfe6f2}
        nav .links{max-width:980px;margin:auto;padding:12px 22px;display:flex;flex-wrap:wrap;gap:18px;font-size:14px}
        main{max-width:980px;padding:38px 22px 60px;margin:auto}
        article{background:white;border:1px solid #e4ebf5;border-radius:16px;padding:clamp(20px,4vw,46px);box-shadow:0 6px 24px #122b4b0b}
        h1{color:var(--navy);font-size:clamp(27px,4vw,36px);line-height:1.25;margin:0 0 12px}
        h2{color:var(--navy);font-size:20px;margin:28px 0 8px}
        p{margin:0 0 15px}
        ul{padding-left:22px;margin:8px 0 18px}
        li{padding-bottom:5px}
        .intro{font-size:17px;color:#49617e}
        .note{background:#eef4ff;border-left:4px solid var(--blue);padding:14px 17px;border-radius:5px;margin:18px 0}
        footer{border-top:1px solid #dce4f0;text-align:center;padding:22px;font-size:13px;color:var(--muted)}
        @media(max-width:520px){main{padding:20px 12px 36px}header{padding:16px}nav .links{padding:10px 16px}}
    </style>
</head>
<body>
    <header>
        <a class="brand" href="{{ route('publico.informacion') }}">ComuniMap</a>
        <small>Gestión comunitaria de la O.T.B. Universitario Alto · Cochabamba, Bolivia</small>
    </header>
    <nav aria-label="Navegación pública">
        <div class="links">
            <a href="{{ route('publico.informacion') }}">Información del proyecto</a>
            <a href="{{ route('publico.privacidad') }}">Política de privacidad</a>
            <a href="{{ url('/login') }}">Iniciar sesión</a>
        </div>
    </nav>
    <main id="contenido"><article>@yield('contenido')</article></main>
    <footer>&copy; {{ date('Y') }} ComuniMap · Contacto: <a href="mailto:fercho657555@gmail.com">fercho657555@gmail.com</a></footer>
</body>
</html>
