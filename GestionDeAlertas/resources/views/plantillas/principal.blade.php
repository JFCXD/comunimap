<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sistema de Gestión de Alertas Comunitarias</title>

    <!-- Bootstrap -->
    <link
        href="{{ asset('ample/bootstrap/dist/css/bootstrap.min.css') }}"
        rel="stylesheet"
    >

    <!-- Estilos Ample Admin -->
    <link
        href="{{ asset('ample/css/style.min.css') }}"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

<div class="container">

    <div class="row justify-content-center align-items-center vh-100">

        <div class="col-lg-5 col-md-7 col-sm-10">

            <div class="card shadow">

                <div class="card-body p-5">

                    @yield('contenido')

                </div>

            </div>

        </div>

    </div>

</div>

<script
    src="{{ asset('ample/plugins/bower_components/jquery/dist/jquery.min.js') }}">
</script>

<script
    src="{{ asset('ample/bootstrap/dist/js/bootstrap.bundle.min.js') }}">
</script>

</body>

</html>
