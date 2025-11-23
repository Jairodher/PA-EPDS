<?php
session_start();
session_unset();
session_destroy();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
// Borrar cookie personalizada del nombre de usuario
setcookie('almacen_user', '', time() - 3600, '/');

header("Location: login.php");
exit();
?>
