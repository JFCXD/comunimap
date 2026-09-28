<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado General de Reportes</title>
    <style>
        body{
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color:#333;
        }
        h1{
            text-align:center;
            margin-bottom:0;
            color:#1f4e79;
        }
        h3{
            text-align:center;
            margin-top:5px;
            color:#555;
            font-weight:normal;
        }
        .fecha{
            text-align:right;
            margin-bottom:20px;
        }
        table{
            width:100%;
            border-collapse:collapse;
        }
        th{
            background:#1f4e79;
            color:white;
            padding:8px;
            border:1px solid #999;
        }
        td{
            border:1px solid #999;
            padding:7px;
            text-align:center;
        }
        tr:nth-child(even){
            background:#f5f5f5;
        }
        .pendiente{
            color:#c77d00;
            font-weight:bold;
        }
        .proceso{
            color:#0056b3;
            font-weight:bold;
        }
        .resuelto{
            color:green;
            font-weight:bold;
        }
        .footer{
            margin-top:30px;
            text-align:right;
            font-weight:bold;
        }
        hr{
            margin-top:15px;
            margin-bottom:20px;
        }
    </style>
</head>
<body>
<h1>BARRO UNIVERSITARIO ALTO</h1>
<h3>
Sistema Digital para la Gestión y Alerta de Problemas Comunitarios
</h3>
<hr>
<div class="fecha">
    Fecha:
    {{ date('d/m/Y H:i') }}
</div>
<h2 style="text-align:center;">
Listado General de Reportes
</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Categoría</th>
            <th>Estado</th>
            <th>Usuario</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reportes as $reporte)
        <tr>
            <td>{{ $reporte->idReporte }}</td>
            <td>{{ $reporte->titulo }}</td>
            <td>{{ $reporte->categoria }}</td>
            <td>
                @if($reporte->estado=="Pendiente")
                    <span class="pendiente">
                        Pendiente
                    </span>
                @elseif($reporte->estado=="En proceso")
                    <span class="proceso">
                        En proceso
                    </span>

                @else
                    <span class="resuelto">
                        {{ $reporte->estado }}
                    </span>
                @endif
            </td>
            <td>{{ $reporte->usuario }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="footer">
    Total de reportes registrados:
    {{ count($reportes) }}
</div>
</body>
</html>