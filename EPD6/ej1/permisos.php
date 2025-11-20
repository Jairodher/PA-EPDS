<?php

$permisos = [
    "usuarios.php" => [1],         
    "productos.php" => [1, 2],     
    "carrito.php"   => [2, 3],     
];


function verificarPermiso($pagina, $rol) {
    global $permisos;

    if (!isset($permisos[$pagina])) return;

    if (!in_array($rol, $permisos[$pagina])) {
        echo "<h1 style='color:red'>çNo tienes permiso para acceder a esta sección.</h1>";
        exit();
    }
}
?>