<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Problema</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 30px;
        }
        .encabezado {
            border-bottom: 3px solid #343a40;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .logo {
            width: 55px;
            height: 55px;
            object-fit: contain;
            float: left;
            margin-right: 12px;
        }
        .titulo-sistema {
            font-size: 20px;
            font-weight: bold;
            margin-top: 5px;
        }
        .subtitulo {
            font-size: 11px;
            color: #666;
            margin-top: 4px;
        }
        .limpiar {
            clear: both;
        }
        .titulo-reporte {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }
        .reporte {
            border: 1px solid #d5d5d5;
            border-radius: 5px;
            padding: 15px;
        }
        .dato {
            margin-bottom: 9px;
        }
        .etiqueta {
            font-weight: bold;
        }
        .estado {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            background-color: #ffc107;
            color: #222;
            font-weight: bold;
        }
        .foto-titulo {
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 8px;
        }
        .foto {
            width: 300px;
            max-height: 220px;
            object-fit: contain;
            border: 1px solid #ccc;
        }
        .pie {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 9px;
            color: #777;
        }
    </style>
</head>
<body>
    @foreach($reportes as $reporte)
        <div class="encabezado">
            <div class="titulo-sistema">
                Barrio Universitario Alto
            </div>
            <div class="subtitulo">
                Sistema Digital para la Gestión y Alerta de Problemas Comunitarios
            </div>
            <div class="limpiar"></div>
        </div>
        <div class="titulo-reporte">
            REPORTE DE PROBLEMA COMUNITARIO
        </div>
        <div class="reporte">
            <div class="dato">
                <span class="etiqueta">N.º de Reporte:</span>
                {{ $reporte->idReporte }}
            </div>
            <div class="dato">
                <span class="etiqueta">Título:</span>
                {{ $reporte->titulo }}
            </div>
            <div class="dato">
                <span class="etiqueta">Categoría:</span>
                {{ $reporte->categoria }}
            </div>
            <div class="dato">
                <span class="etiqueta">Descripción:</span>
                {{ $reporte->descripcion }}
            </div>
            <div class="dato">
                <span class="etiqueta">Dirección:</span>
                {{ $reporte->direccion }}
            </div>
            <div class="dato">
                <span class="etiqueta">Ubicación:</span>
                {{ $reporte->ubicacion }}
            </div>
            <div class="dato">
                <span class="etiqueta">Estado:</span>
                <span class="estado">
                    {{ $reporte->estado }}
                </span>
            </div>
            <div class="dato">
                <span class="etiqueta">Reportado por:</span>
                {{ $reporte->usuario }}
            </div>
            @if($reporte->imagen)
                <div class="foto-titulo">
                    Evidencia fotográfica
                </div>
                <img
                    src="{{ public_path('storage/' . $reporte->imagen) }}"
                    class="foto"
                >
            @endif
        </div>
        <div class="pie">
            Reporte generado desde el Sistema Digital de Gestión de Alertas
        </div>
    @endforeach
</body>

</html>