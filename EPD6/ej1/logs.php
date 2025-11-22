<?php
session_start();
require_once 'config.php';
require_once 'permisos.php';

// Solo administradores pueden ver el log
verificarPermiso("logs.php", $_SESSION['rol']);

// Consultar todos los logs
$sql = "SELECT l.id AS id_log, u.email AS usuario, l.tipo_accion, l.entidad, l.fecha
        FROM logs l
        LEFT JOIN usuario u ON l.id_usuario = u.id_usuario
        ORDER BY l.fecha DESC";

$logs = $con->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registro de acciones</title>
</head>
<body>

<h1>Registro de acciones (LOG)</h1>

<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>ID Log</th>
            <th>Usuario</th>
            <th>Acción</th>
            <th>Entidad</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($l = $logs->fetch_assoc()): ?>
        <tr>
            <td><?= $l['id_log'] ?></td>
            <td><?= $l['usuario'] ?></td>
            <td><?= $l['tipo_accion'] ?></td>
            <td><?= $l['entidad'] ?></td>
            <td><?= $l['fecha'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
