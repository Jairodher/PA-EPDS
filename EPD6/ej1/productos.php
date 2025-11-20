<?php
include "auth.php";
include "permisos.php";
verificarPermiso("productos.php", $_SESSION['rol']);
?>
