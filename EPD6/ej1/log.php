<?php
function registrarLog($con, $id_usuario, $accion, $entidad) {
    $sql = "INSERT INTO logs (id_usuario, tipo_accion, entidad, fecha)
            VALUES (?, ?, ?, NOW())";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("iss", $id_usuario, $accion, $entidad);
    $stmt->execute();
}
