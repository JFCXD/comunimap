<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Estadísticas de ComuniMap</title>
<style>
@page { margin: 28px 34px; }
body { font-family: DejaVu Sans, sans-serif; color:#1d304a; font-size:10px; }
.header { background:#17375e; color:#fff; padding:17px 19px; }
.header h1 { font-size:20px; margin:0 0 6px; color:#fff; }
.header p { margin:0; font-size:10px; color:#dbeafe; }
.meta { padding:13px 0; color:#475569; line-height:1.7; }
h2 { font-size:13px; margin:18px 0 9px; color:#17375e; border-bottom:1px solid #dbe5f0; padding-bottom:6px; }
.summary { width:100%; border-spacing:7px; border-collapse:separate; margin-left:-7px; }
.summary td { background:#f1f5f9; padding:12px 10px; width:20%; border:1px solid #e2e8f0; }
.summary .number { font-size:20px; font-weight:bold; color:#17375e; display:block; margin-top:6px; }
table.data { width:100%; border-collapse:collapse; }
table.data th { background:#eaf1fa; color:#17375e; text-align:left; padding:8px; }
table.data td { padding:7px 8px; border-bottom:1px solid #e2e8f0; }
table.data tr:nth-child(even) td { background:#f8fafc; }
.right { text-align:right; }
.rate { background:#e8f3ff; padding:12px; margin:12px 0; border-left:4px solid #2563eb; }
.rate strong { font-size:18px; color:#17375e; }
.bar { background:#e2e8f0; height:8px; width:100%; }
.fill { background:#2563eb; height:8px; }
.note { font-size:9px; color:#64748b; margin-top:14px; line-height:1.6; }
.footer { margin-top:22px; border-top:1px solid #dbe5f0; padding-top:9px; font-size:9px; color:#64748b; }
</style>
</head>
<body>
<div class="header">
    <h1>ComuniMap | Informe estadístico</h1>
    <p>Gestión y seguimiento de incidencias comunitarias</p>
</div>
<div class="meta">
    <strong>Generado:</strong> {{ $fechaGeneracion }}<br>
    <strong>Período:</strong> {{ $desde ?: 'Sin fecha inicial' }} — {{ $hasta ?: 'Sin fecha final' }}<br>
    <strong>Categoría:</strong> {{ $categoriaSeleccionada ? (optional($categoriasDisponibles->firstWhere('idCategoria', (int)$categoriaSeleccionada))->nombre ?: 'Seleccionada') : 'Todas' }}
    &nbsp; | &nbsp; <strong>Estado:</strong> {{ $estadoSeleccionado ?: 'Todos' }}
</div>
<table class="summary"><tr>
    <td>Reportes<span class="number">{{ $totalReportes }}</span></td>
    <td>Pendientes<span class="number">{{ $pendientes }}</span></td>
    <td>En proceso<span class="number">{{ $enProceso }}</span></td>
    <td>Solucionados<span class="number">{{ $solucionados }}</span></td>
    <td>Usuarios*<span class="number">{{ $totalUsuarios }}</span></td>
</tr></table>
<div class="rate">Porcentaje de resolución de reportes filtrados: <strong>{{ number_format($porcentajeResolucion, 1) }}%</strong></div>
<h2>Distribución por estado</h2>
<table class="data">
<thead><tr><th>Estado</th><th class="right">Reportes</th><th class="right">Porcentaje</th></tr></thead>
<tbody>
@foreach([['Pendiente',$pendientes],['En Proceso',$enProceso],['Solucionado',$solucionados],['Otros estados',$otrosEstados]] as $fila)
<tr><td>{{ $fila[0] }}</td><td class="right">{{ $fila[1] }}</td><td class="right">{{ $totalReportes ? number_format($fila[1]*100/$totalReportes, 1) : '0.0' }}%</td></tr>
@endforeach
</tbody></table>
<h2>Reportes por categoría</h2>
<table class="data"><thead><tr><th>Categoría</th><th class="right">Cantidad</th><th style="width:38%">Proporción del total</th></tr></thead><tbody>
@forelse($categorias as $categoria)
<tr><td>{{ $categoria->categoria }}</td><td class="right">{{ $categoria->total }}</td><td><div class="bar"><div class="fill" style="width:{{ $totalReportes ? min(100, round($categoria->total*100/$totalReportes, 1)) : 0 }}%"></div></div></td></tr>
@empty
<tr><td colspan="3">No hay categorías disponibles.</td></tr>
@endforelse
</tbody></table>
<h2>Evolución de reportes — mes actual (prueba)</h2>
<table class="data"><thead><tr><th>Día</th><th class="right">Cantidad de reportes</th></tr></thead><tbody>
@forelse($evolucion as $dia)
<tr><td>{{ $dia['periodo'] }}</td><td class="right">{{ $dia['total'] }}</td></tr>
@empty
<tr><td colspan="2">Sin datos para este período.</td></tr>
@endforelse
</tbody></table>
<div class="note">* El total de usuarios corresponde a todos los registrados en el sistema, sin aplicar filtros. Los demás indicadores respetan los filtros seleccionados. La evolución diaria del mes actual es una visualización temporal de prueba.</div>
<div class="footer">ComuniMap · Informe generado automáticamente con los datos registrados en el sistema.</div>
</body>
</html>