<?php
include "auth.php";
include "permisos.php";
verificarPermiso("carrito.php", $_SESSION['rol']);
?>
