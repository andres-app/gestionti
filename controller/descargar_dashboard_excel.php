<?php
require_once("../config/conexion.php");
require_once("../models/Activo.php");

$activo = new Activo();
$total = $activo->get_total_activos();
$estados = $activo->get_activos_por_estado();
$tipos = $activo->get_activos_por_tipo();
$ubicaciones = $activo->get_activos_por_ubicacion();
$condiciones = $activo->get_activos_por_condicion();

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=dashboard_activos.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<table border="1">
    <tr><th colspan="2">REPORTE DE DASHBOARD - ACTIVOS</th></tr>
    <tr><td><strong>Total de Activos</strong></td><td><?= $total ?></td></tr>
</table>
<br>

<table border="1">
    <tr><th colspan="2">Distribución por Estado</th></tr>
    <tr><th>Estado</th><th>Total</th></tr>
    <?php foreach ($estados as $e): ?>
        <tr><td><?= $e["estado_nombre"] ?></td><td><?= $e["total"] ?></td></tr>
    <?php endforeach; ?>
</table>
<br>

<table border="1">
    <tr><th colspan="2">Distribución por Tipo</th></tr>
    <tr><th>Tipo</th><th>Total</th></tr>
    <?php foreach ($tipos as $t): ?>
        <tr><td><?= $t["tipo"] ?></td><td><?= $t["total"] ?></td></tr>
    <?php endforeach; ?>
</table>
<br>

<table border="1">
    <tr><th colspan="2">Distribución por Ubicación</th></tr>
    <tr><th>Ubicación</th><th>Total</th></tr>
    <?php foreach ($ubicaciones as $u): ?>
        <tr><td><?= $u["ubicacion"] ?></td><td><?= $u["total"] ?></td></tr>
    <?php endforeach; ?>
</table>
<br>

<table border="1">
    <tr><th colspan="2">Distribución por Condición</th></tr>
    <tr><th>Condición</th><th>Total</th></tr>
    <?php foreach ($condiciones as $c): ?>
        <tr><td><?= $c["condicion"] ?></td><td><?= $c["total"] ?></td></tr>
    <?php endforeach; ?>
</table>
