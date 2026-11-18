<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
@page { margin: 2.05cm 2.1cm 1.95cm; }
body { font-family: "DejaVu Sans", sans-serif; color:#222; font-size:10.5px; line-height:1.60; }
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
.parrafo.formateado { white-space:normal; }
.parrafo.formateado div { margin-bottom:5px; }
.parrafo.formateado ul,.parrafo.formateado ol { margin:4px 0 12px; padding-left:22px; }
.parrafo.formateado li { margin:2px 0; }
.membrete-texto div div { margin:0; }
.pie div,.firma div div { margin:0; }
.membrete-texto ul,.destinatario ul,.firma ul { padding-left:18px; }
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
<div class="membrete">
    <table class="membrete-tabla">
        <tr>
            <td class="membrete-logo">
                @if(!empty($emblemas['izquierdo']))
                    <img src="{{ $emblemas['izquierdo'] }}" alt="Escudo oficial">
                @endif
            </td>
            <td class="membrete-texto">
                <div class="otb">@if(!empty($formatoCarta['membrete_titulo'])){!! $formatoCarta['membrete_titulo'] !!}@else{{ $datos['membrete_titulo'] ?? 'O.T.B. UNIVERSITARIO ALTO' }}@endif</div>
                <div class="ciudad">@if(!empty($formatoCarta['membrete_prefectural'])){!! $formatoCarta['membrete_prefectural'] !!}@else{{ $datos['membrete_prefectural'] ?? 'RESOLUCIÓN PREFECTURAL N.º 201/96 DE FECHA 15/11/1996' }}@endif</div>
                <div class="linea">@if(!empty($formatoCarta['membrete_municipal'])){!! $formatoCarta['membrete_municipal'] !!}@else{{ $datos['membrete_municipal'] ?? 'RESOLUCIÓN MUNICIPAL N.º 196/96 DE FECHA 01/11/1996' }}@endif</div>
                <div class="linea">@if(!empty($formatoCarta['membrete_registro'])){!! $formatoCarta['membrete_registro'] !!}@else{{ $datos['membrete_registro'] ?? 'REGISTRO D.S. 21060 — MUNICIPIO DE COCHABAMBA' }}@endif</div>
                <div class="sublinea">@if(!empty($formatoCarta['membrete_zona'])){!! $formatoCarta['membrete_zona'] !!}@else{{ $datos['membrete_zona'] ?? 'DISTRITO N.º 7 · ZONA UNIVERSITARIO ALTO · COCHABAMBA — BOLIVIA' }}@endif</div>
            </td>
            <td class="membrete-logo">
                @if(!empty($emblemas['derecho']))
                    <img src="{{ $emblemas['derecho'] }}" alt="Logotipo oficial">
                @endif
            </td>
        </tr>
    </table>
</div>
<div class="fecha">{{ trim((string) ($datos['lugar_emision'] ?? '')) ?: 'Cochabamba' }}, {{ \Carbon\Carbon::parse($datos['fecha'])->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</div>
<div class="destinatario">
    @if(!empty($formatoCarta['tratamiento'])){!! $formatoCarta['tratamiento'] !!}@else{{ $datos['tratamiento'] ?? 'Señor(a):' }}@endif<br>
    <strong>{{ $datos['destinatario'] }}</strong><br>
    @if(!empty($datos['cargo_destinatario'])){{ $datos['cargo_destinatario'] }}<br>@endif
    <strong>{{ $datos['institucion'] }}</strong><br>
    @if(!empty($formatoCarta['presente'])){!! $formatoCarta['presente'] !!}@else{{ $datos['presente'] ?? 'Presente.-' }}@endif
</div>
<div class="ref">@if(!empty($formatoCarta['referencia_etiqueta'])){!! $formatoCarta['referencia_etiqueta'] !!}@else{{ $datos['referencia_etiqueta'] ?? 'REF.:' }}@endif {{ $datos['asunto'] }}</div>
<div class="saludo">@if(!empty($formatoCarta['saludo'])){!! $formatoCarta['saludo'] !!}@else{{ $datos['saludo'] ?? 'De nuestra consideración:' }}@endif</div>
@if(!empty($formatoCarta['antecedentes']))
<div class="parrafo formateado">{!! $formatoCarta['antecedentes'] !!}</div>
@else
<div class="parrafo">{{ $datos['antecedentes'] }}</div>
@endif
@if(!empty($formatoCarta['solicitud']))
<div class="parrafo formateado">{!! $formatoCarta['solicitud'] !!}</div>
@else
<div class="parrafo">{{ $datos['solicitud'] }}</div>
@endif
<div class="cierre">@if(!empty($formatoCarta['despedida'])){!! $formatoCarta['despedida'] !!}@else{{ $datos['despedida'] ?? 'Agradeciendo su atención, nos despedimos con las consideraciones más distinguidas.' }}@endif</div>
<div class="saludo">@if(!empty($formatoCarta['atentamente'])){!! $formatoCarta['atentamente'] !!}@else{{ $datos['atentamente'] ?? 'Atentamente,' }}@endif</div>
<div class="firma">
    <div class="raya"></div>
    <strong>{{ $datos['remitente'] }}</strong><br>
    @if(!empty($datos['cargo_remitente']))<span class="cargo">{{ $datos['cargo_remitente'] }}</span><br>@endif
    <span class="cargo">@if(!empty($formatoCarta['firma_otb'])){!! $formatoCarta['firma_otb'] !!}@else{{ $datos['firma_otb'] ?? 'OTB Barrio Universitario Alto' }}@endif</span>
    <div class="sello">@if(!empty($formatoCarta['sello_etiqueta'])){!! $formatoCarta['sello_etiqueta'] !!}@else{{ $datos['sello_etiqueta'] ?? 'Sello de la OTB' }}@endif</div>
</div>
<div class="pie">@if(!empty($formatoCarta['pie_carta'])){!! $formatoCarta['pie_carta'] !!}@else{{ $datos['pie_carta'] ?? 'OTB BARRIO UNIVERSITARIO ALTO — COCHABAMBA, BOLIVIA' }}@endif</div>
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
