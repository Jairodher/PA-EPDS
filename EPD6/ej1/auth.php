<?php
session_start();

// Si no existe sesión, redirigir a login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Renovar cookie 'almacen_user' (1 día) en cada acceso
if (!empty($_SESSION['nombre'])) {
    // Usamos setcookie con parámetros compatibles
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    setcookie('almacen_user', $_SESSION['nombre'], time() + 86400, '/', $_SERVER['HTTP_HOST'], $secure, true);
}
