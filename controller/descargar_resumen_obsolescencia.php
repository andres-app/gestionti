<?php
require_once("../config/conexion.php");
require_once("../models/Activo.php");

$activo = new Activo();
$data = $activo->get_obsolescencia_garantia();

// Encabezados para Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=resumen_obsolescencia_" . date('Ymd_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Estilos opcionales para visualización
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th colspan='5' style='background:#f2f2f2'>Resumen de Obsolescencia y Garantia - " . date("d/m/Y H:i") . "</th></tr>";
echo "<tr>
        <th>Total Equipos</th>
        <th>Con Obsolescencia</th>
        <th>Fuera de Garantia</th>
        <th>Vigentes Tecnologicamente</th>
        <th>Con Garantia</th>
      </tr>";
echo "<tr>
        <td>{$data['total']}</td>
        <td>{$data['obsoletos']}</td>
        <td>{$data['fuera_garantia']}</td>
        <td>{$data['vigentes_tecnologicamente']}</td>
        <td>{$data['con_garantia']}</td>
      </tr>";
echo "</table>";
