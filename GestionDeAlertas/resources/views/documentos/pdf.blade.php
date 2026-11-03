<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
@page { margin: 2.6cm 2.3cm; }
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color:#202b36; line-height:1.65; }
.encabezado { text-align:center; border-bottom:2px solid #243c5a; padding-bottom:12px; margin-bottom:26px; }
.encabezado strong { font-size:16px; color:#183451; }
.fecha { text-align:right; margin-bottom:30px; }
.asunto { margin:25px 0 24px; }
.parrafo { white-space:pre-line; text-align:justify; margin-bottom:23px; overflow-wrap:break-word; }
.firma { margin-top:64px; text-align:center; }
.firma-linea { width:240px; border-top:1px solid #222; margin:0 auto 9px; }
.anexo { page-break-before:always; }
.anexo h2 { font-size:15px; color:#183451; }
.foto { page-break-inside:avoid; margin-bottom:25px; }
.foto img { max-width:100%; max-height:560px; }
.nota { font-size:9px; color:#4a5568; }
</style>
</head>
<body>
<div class="encabezado">
    <strong>COMUNICACIÓN FORMAL</strong>
    <div>Solicitud comunitaria de atención</div>
</div>

<div class="fecha">Cochabamba, {{ \Carbon\Carbon::parse($datos['fecha'])->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</div>

<div>
    <strong>Señor(a): {{ $datos['destinatario'] }}</strong><br>
    @if(!empty($datos['cargo_destinatario'])){{ $datos['cargo_destinatario'] }}<br>@endif
    <strong>{{ $datos['institucion'] }}</strong><br>
    Presente.-
</div>

<div class="asunto"><strong>REF.: {{ $datos['asunto'] }}</strong></div>

<p>De nuestra consideración:</p>
<div class="parrafo">{{ $datos['antecedentes'] }}</div>
<div class="parrafo">{{ $datos['solicitud'] }}</div>

<p>Sin otro particular, agradecemos su atención y quedamos a la espera de una respuesta.</p>
<p>Atentamente,</p>

<div class="firma">
    <div class="firma-linea"></div>
    <strong>{{ $datos['remitente'] }}</strong>
    @if(!empty($datos['cargo_remitente']))<div>{{ $datos['cargo_remitente'] }}</div>@endif
</div>

@if(count($fotos) > 0)
    <div class="anexo">
        <h2>Anexos fotográficos</h2>
        <p class="nota">Evidencia disponible correspondiente a los reportes seleccionados.</p>
        @foreach($fotos as $foto)
            <div class="foto">
                <strong>Reporte #{{ $foto['idReporte'] }} — {{ $foto['titulo'] }}</strong><br>
                <img src="{{ $foto['imagen'] }}" alt="Evidencia fotográfica del reporte">
            </div>
        @endforeach
    </div>
@endif
</body>
</html>
