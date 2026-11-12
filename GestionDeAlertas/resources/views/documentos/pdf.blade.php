<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
@page { margin: 2.05cm 2.1cm 1.95cm; }
body { font-family: "DejaVu Sans", sans-serif; color:#222; font-size:10.5px; line-height:1.60; }
.marca-agua { position:fixed; top:6.0cm; left:4.5cm; width:11.0cm; height:auto; z-index:-10; }
.marca-agua img { width:100%; height:auto; }
/* El fondo debe ser casi imperceptible y nunca desplazar el texto. */
.membrete { text-align:center; padding:0 0 8px; margin:0 0 19px; border-bottom:1px solid #262626; color:#163b77; }
.membrete-tabla { width:100%; border-collapse:collapse; table-layout:fixed; }
.membrete-logo { width:17%; text-align:center; vertical-align:middle; }
.membrete-logo img { width:77px; max-height:82px; }
.membrete-texto { width:66%; text-align:center; vertical-align:middle; }
.sublinea { margin-top:1px; font-size:6.6px; font-weight:bold; color:#163b77; }
.otb { font-family: "DejaVu Serif", serif; font-size:12px; font-weight:700; letter-spacing:0; color:#163b77; }
.ciudad { font-size:7px; font-weight:700; margin-top:1px; color:#163b77; }
.linea { font-size:6.6px; font-weight:700; color:#163b77; margin-top:1px; }
.fecha { text-align:right; margin:0 0 25px; font-style:italic; }
.destinatario { margin:0 0 16px; }
.ref { margin:12px 0 22px 36px; font-size:10.5px; font-weight:700; text-transform:uppercase; }
.saludo { margin:0 0 14px; }
.parrafo { margin:0 0 17px; text-align:justify; white-space:pre-line; overflow-wrap:break-word; }
.cierre { margin-top:13px; }
.firma { text-align:center; margin:55px auto 0; width:350px; page-break-inside:avoid; }
.raya { border-top:1px dotted #333; width:260px; margin:0 auto 6px; }
.cargo { font-size:9.5px; }
.sello { font-size:9px; margin-top:15px; color:#666; }
.pie { position:fixed; bottom:-1.20cm; width:100%; font-size:8px; text-align:center; border-top:1px solid #666; padding-top:5px; color:#555; }
.anexo { page-break-before:always; }
.anexo h2 { font-size:14px; border-bottom:1px solid #555; padding-bottom:7px; }
.foto { page-break-inside:avoid; text-align:center; margin:12px 0 25px; }
.foto img { max-width:95%; max-height:510px; }
.etiqueta { text-align:left; font-size:10px; font-weight:bold; margin-bottom:5px; }
</style>
</head>
<body>
@if(!empty($liston))
<div class="marca-agua" aria-hidden="true"><img src="{{ $liston }}" alt=""></div>
@endif
<div class="membrete">
    <table class="membrete-tabla">
        <tr>
            <td class="membrete-logo">
                @if(!empty($emblemas['izquierdo']))
                    <img src="{{ $emblemas['izquierdo'] }}" alt="Escudo oficial">
                @endif
            </td>
            <td class="membrete-texto">
                <div class="otb">O.T.B. UNIVERSITARIO ALTO</div>
                <div class="ciudad">RESOLUCIÓN PREFECTURAL N.º 201/96 DE FECHA 15/11/1996</div>
                <div class="linea">RESOLUCIÓN MUNICIPAL N.º 196/96 DE FECHA 01/11/1996</div>
                <div class="linea">REGISTRO D.S. 21060 — MUNICIPIO DE COCHABAMBA</div>
                <div class="sublinea">DISTRITO N.º 7 · ZONA UNIVERSITARIO ALTO · COCHABAMBA — BOLIVIA</div>
            </td>
            <td class="membrete-logo">
                @if(!empty($emblemas['derecho']))
                    <img src="{{ $emblemas['derecho'] }}" alt="Logotipo oficial">
                @endif
            </td>
        </tr>
    </table>
</div>
<div class="fecha">Cochabamba, {{ \Carbon\Carbon::parse($datos['fecha'])->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</div>
<div class="destinatario">
    Señor(a):<br>
    <strong>{{ $datos['destinatario'] }}</strong><br>
    @if(!empty($datos['cargo_destinatario'])){{ $datos['cargo_destinatario'] }}<br>@endif
    <strong>{{ $datos['institucion'] }}</strong><br>
    Presente.-
</div>
<div class="ref">REF.: {{ $datos['asunto'] }}</div>
<p class="saludo">De nuestra consideración:</p>
<div class="parrafo">{{ $datos['antecedentes'] }}</div>
<div class="parrafo">{{ $datos['solicitud'] }}</div>
<p class="cierre">Agradeciendo su atención, nos despedimos con las consideraciones más distinguidas.</p>
<p>Atentamente,</p>
<div class="firma">
    <div class="raya"></div>
    <strong>{{ $datos['remitente'] }}</strong><br>
    @if(!empty($datos['cargo_remitente']))<span class="cargo">{{ $datos['cargo_remitente'] }}</span><br>@endif
    <span class="cargo">OTB Barrio Universitario Alto</span>
    <div class="sello">Sello de la OTB</div>
</div>
<div class="pie">OTB BARRIO UNIVERSITARIO ALTO — COCHABAMBA, BOLIVIA</div>
@if(count($fotos) > 0)
<div class="anexo">
    <h2>ANEXOS FOTOGRÁFICOS</h2>
    <p>Evidencias de los reportes relacionados con la solicitud.</p>
    @foreach($fotos as $foto)
    <div class="foto">
        <div class="etiqueta">Reporte #{{ $foto['idReporte'] }} — {{ $foto['titulo'] }}</div>
        <img src="{{ $foto['imagen'] }}" alt="Evidencia fotográfica">
    </div>
    @endforeach
</div>
@endif
</body>
</html>
