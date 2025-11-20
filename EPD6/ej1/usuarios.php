<?php
include "auth.php";
include "permisos.php";
verificarPermiso("usuarios.php", $_SESSION['rol']);
?>
