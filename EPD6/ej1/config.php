<?php

    $con = mysqli_connect("localhost", "root", "", "EPD06");
    if(!$con){
        // Registrar el error en logs del servidor, no mostrar información sensible
        error_log('DB connection error: ' . mysqli_connect_error());
        die('No se pudo conectar a la base de datos.');
    }

    // Forzar charset utf8mb4 para evitar problemas XSS / codificación
    mysqli_set_charset($con, 'utf8mb4');

    // Mantener ambas variables ($con y $conexion) para compatibilidad mínima con el código existente
    $conexion = $con;
?>
