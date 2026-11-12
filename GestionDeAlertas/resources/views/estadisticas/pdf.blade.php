<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>ComuniMap - Resumen estadístico comunitario</title>
<style>
@page{margin:24px 28px 27px}
body{font-family:DejaVu Sans,sans-serif;color:#263449;font-size:9px;margin:0}
h1{font-size:18px;color:#153e6e;margin:0 0 6px;text-align:center}
h2{font-size:12px;color:#0b4e89;margin:0 0 8px}
p{margin:4px 0}
.header{text-align:center;margin-bottom:13px}
.subtitle{font-size:9px;color:#58677a;line-height:1.5}
.meta{background:#f0f6fc;padding:9px 11px;margin:9px 0 12px;border-left:4px solid #0b4e89;line-height:1.6}
.panel{border:1px solid #d5dfe9;padding:12px 13px;margin-bottom:12px;page-break-inside:avoid}
.panel-title{font-size:12px;font-weight:bold;text-align:center;color:#0b4e89;margin:0 0 9px}
.panel-title.cyan{color:#04a7d9}
table{width:100%;border-collapse:collapse}
th{background:#0b4e89;color:white;font-size:9px;padding:7px 6px;text-align:center;border:1px solid #c7d4e2}
td{border:1px solid #d4dce6;padding:6px 7px;text-align:center}
td.label{text-align:left;font-weight:bold}
tr:nth-child(even) td{background:#f6f9fc}
.total td{background:#0b4e89!important;color:#fff;font-weight:bold}
.cyan-table th{background:#08b1df}
.metrics td{border:1px solid #d4dce6;padding:10px 4px;width:20%;background:#edf4fb}
.metrics .num{font-size:17px;font-weight:bold;color:#0b4e89;display:block;margin-top:5px}
.metrics .lbl{font-size:8px}
.rate{margin:9px 0;padding:9px 12px;background:#e7f4fd;border-left:4px solid #08b1df}
.rate strong{font-size:16px;color:#0b4e89}
.columns{width:100%;border-collapse:collapse}
.columns td{border:0;vertical-align:top;padding:0}
.columns .left{width:49%;padding-right:12px}
.columns .right{width:51%;padding-left:5px}
.chart-title{font-size:10px;color:#0b4e89;font-weight:bold;margin:8px 0}
.barrow{width:100%;margin:5px 0;border-collapse:collapse}
.barrow td{border:0;background:transparent!important;padding:3px 2px;font-size:8px}
.barrow .name{text-align:left;width:30%}
.barrow .count{text-align:right;width:10%;font-weight:bold}
.track{background:#e4edf5;height:11px;width:100%}
.fill{background:#0b4e89;height:11px}
.fill.cyan{background:#0bb4df}
.chartgrid{border-bottom:1px solid #aab8c9;height:102px;white-space:nowrap;text-align:center}
.chartgrid table{height:100%;border-collapse:collapse}
.chartgrid td{vertical-align:bottom;border:0;background:transparent!important;padding:0 3px;text-align:center}
.vertical{background:#0bb4df;width:65%;margin:0 auto;min-height:1px}
.daylabel{font-size:7px;color:#51647b;margin-top:4px}
.dayvalue{font-size:7px;color:#153e6e}
.note{color:#64748b;font-size:8px;line-height:1.5;margin-top:8px}
.footer{font-size:8px;color:#64748b;text-align:center;border-top:1px solid #d4dce6;padding-top:8px;margin-top:11px}
</style>
</head>
<body>
<div class="header">
<h1>ComuniMap - Resumen estadístico comunitario</h1>
<p class="subtitle">Informe de gestión y seguimiento de incidencias de la comunidad.<br>Datos obtenidos de los reportes registrados en el sistema.</p>
</div>
<div class="meta">
<strong>Fecha de generación:</strong> {{ $fechaGeneracion }} &nbsp;&nbsp;
<strong>Período:</strong> {{ $desde ?: 'Sin fecha inicial' }} al {{ $hasta ?: 'Sin fecha final' }}<br>
<strong>Categoría:</strong> {{ $categoriaSeleccionada ? (optional($categoriasDisponibles->firstWhere('idCategoria', (int)$categoriaSeleccionada))->nombre ?: 'Seleccionada') : 'Todas' }}
&nbsp;&nbsp; <strong>Estado:</strong> {{ $estadoSeleccionado ?: 'Todos' }}
</div>
<div class="panel">
<div class="panel-title">Resumen general de incidencias</div>
<table class="metrics"><tr>
<td><span class="lbl">Reportes</span><span class="num">{{ $totalReportes }}</span></td>
<td><span class="lbl">Pendientes</span><span class="num">{{ $pendientes }}</span></td>
<td><span class="lbl">En proceso</span><span class="num">{{ $enProceso }}</span></td>
<td><span class="lbl">Solucionados</span><span class="num">{{ $solucionados }}</span></td>
<td><span class="lbl">Usuarios*</span><span class="num">{{ $totalUsuarios }}</span></td>
</tr></table>
<div class="rate">Porcentaje de resolución: <strong>{{ number_format($porcentajeResolucion,1) }}%</strong></div>
</div>
<div class="panel">
<div class="panel-title">Estado de los reportes</div>
<table><thead><tr><th>Estado</th><th>Cantidad</th><th>Porcentaje</th></tr></thead><tbody>
@foreach([['Pendiente',$pendientes],['En Proceso',$enProceso],['Solucionado',$solucionados],['Otros estados',$otrosEstados]] as $fila)
<tr><td class="label">{{ $fila[0] }}</td><td>{{ $fila[1] }}</td><td>{{ $totalReportes ? number_format($fila[1]*100/$totalReportes,1) : '0.0' }}%</td></tr>
@endforeach
<tr class="total"><td class="label">TOTAL</td><td>{{ $totalReportes }}</td><td>100%</td></tr>
</tbody></table>
</div>
<div class="panel">
<div class="panel-title cyan">Evolución diaria de reportes (mes actual - prueba)</div>
<p class="subtitle">Cantidad de incidencias registradas por día, respetando los filtros seleccionados.</p>
@php
$maximoDia = max(1, (int) collect($evolucion)->max('total'));
$diasConDatos = collect($evolucion)->filter(fn($d) => (int)$d['total'] > 0)->values();
@endphp
@if($diasConDatos->isEmpty())
<p>No se registraron reportes durante el período mostrado.</p>
@else
<div class="chartgrid"><table><tr>
@foreach($evolucion as $dia)
<td><div class="dayvalue">{{ $dia['total'] ?: '' }}</div><div class="vertical" style="height:{{ $dia['total'] ? max(3, round($dia['total']*75/$maximoDia)) : 1 }}px;{{ $dia['total'] ? '' : 'background:#e4edf5;' }}"></div></td>
@endforeach
</tr></table></div>
<table style="margin-top:3px"><tr>
@foreach($evolucion as $indice => $dia)
<td style="border:0;padding:1px 0;font-size:6px;background:transparent">{{ $indice % 3 === 0 ? substr($dia['periodo'],0,2) : '' }}</td>
@endforeach
</tr></table>
<p class="note">Total en el gráfico: {{ collect($evolucion)->sum('total') }} reportes. Eje horizontal: día del mes.</p>
@endif
</div>
<div class="panel">
<div class="panel-title">Incidencias por categoría</div>
<table class="columns"><tr>
<td class="left">
<table><thead><tr><th>Categoría</th><th>Reportes</th><th>%</th></tr></thead><tbody>
@forelse($categorias as $categoria)
<tr><td class="label">{{ $categoria->categoria }}</td><td>{{ $categoria->total }}</td><td>{{ $totalReportes ? number_format($categoria->total*100/$totalReportes,1) : '0.0' }}%</td></tr>
@empty
<tr><td colspan="3">Sin categorías disponibles.</td></tr>
@endforelse
<tr class="total"><td class="label">TOTAL</td><td>{{ $totalReportes }}</td><td>100%</td></tr>
</tbody></table>
</td>
<td class="right">
<div class="chart-title">Comparación por categoría</div>
@foreach($categorias as $categoria)
<div class="barrow"><table><tr><td class="name">{{ $categoria->categoria }}</td><td style="width:60%"><div class="track"><div class="fill {{ $loop->even ? 'cyan' : '' }}" style="width:{{ $totalReportes ? min(100, round($categoria->total*100/$totalReportes,1)) : 0 }}%"></div></div></td><td class="count">{{ $categoria->total }}</td></tr></table></div>
@endforeach
</td>
</tr></table>
</div>
<p class="note">* El total de usuarios corresponde a todos los usuarios registrados y no se ve afectado por los filtros. Los porcentajes se calculan a partir de los reportes seleccionados. El gráfico diario corresponde temporalmente al mes actual para realizar pruebas.</p>
<div class="footer">ComuniMap | Informe estadístico comunitario | {{ $fechaGeneracion }}</div>
</body>
</html>