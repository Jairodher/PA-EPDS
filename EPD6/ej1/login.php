<?php
session_start();
require_once 'config.php';  

if (isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$errores = [];

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errores[] = "Debe completar todos los campos.";
    } else {

        // Escapar email
        $emailSql = mysqli_real_escape_string($con, $email);

        // Buscar usuario
        $sql = "SELECT id_usuario, nombre, email, password, id_rol 
                FROM usuario 
                WHERE email='$emailSql'";

        $result = mysqli_query($con, $sql);

        if ($result && mysqli_num_rows($result) === 1) {

            $row = mysqli_fetch_assoc($result);

            // Verificar contraseña
            if (password_verify($password, $row['password'])) {

                // Crear sesión segura regeneraando id de sesión
                session_regenerate_id(true);

                // Crear sesión
                $_SESSION['id_usuario'] = $row['id_usuario'];
                $_SESSION['nombre'] = $row['nombre'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['rol'] = $row['id_rol']; // admin, operario, etc.

                // Crear cookie con nombre de usuario (1 día)
                $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
                setcookie('almacen_user', $row['nombre'], time() + 86400, '/', $_SERVER['HTTP_HOST'], $secure, true);

                header("Location: index.php");
                exit();

            } else {
                $errores[] = "Contraseña incorrecta.";
            }
        } else {
            $errores[] = "El email no existe.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<body>

<h1>Login</h1>

<?php
if (!empty($errores)) {
    echo '<div style="color:red"><ul>';
    foreach ($errores as $e) echo "<li>" . htmlspecialchars($e, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</li>";
    echo '</ul></div>';
}
?>

<form method="POST" action="login.php">

    Email: <input type="text" name="email" value="<?= isset($email) ? htmlspecialchars($email, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '' ?>"><br><br>

    Contraseña: <input type="password" name="password"><br><br>

    <input type="submit" name="login" value="Iniciar sesión">
</form>

</body>
</html>
