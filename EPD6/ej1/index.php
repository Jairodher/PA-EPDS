<?php
include "auth.php";
?>

<h1>Bienvenido, <?php echo $_SESSION['nombre']; ?></h1>

<ul>
    <li><a href="index.php">Inicio</a></li>

    <!-- Administrador -->
    <?php if ($_SESSION['rol'] == 1): ?>
        <li><a href="usuarios.php">Gestión de usuarios</a></li>
    <?php endif; ?>

    <!-- Administrador y Administrativo -->
    <?php if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2): ?>
        <li><a href="productos.php">Productos</a></li>
    <?php endif; ?>

    <!-- Administrativo y Operario -->
    <?php if ($_SESSION['rol'] == 2 || $_SESSION['rol'] == 3): ?>
        <li><a href="carrito.php">Carrito</a></li>
    <?php endif; ?>

    <li><a href="logout.php">Cerrar sesión</a></li>
</ul>
