<?php
session_start();
require_once 'config.php';
require_once 'permisos.php';
require_once 'log.php';  

// Restringir acceso solo administradores (rol=1)
verificarPermiso("usuarios.php", $_SESSION['rol']);

$mensaje = "";
$errores = [];

// CREAR USUARIO
if (isset($_POST['crear'])) {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $rol = (int)$_POST['rol'];

    if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
    if (empty($apellidos)) $errores[] = "Los apellidos son obligatorios.";
    if (empty($email)) $errores[] = "El email es obligatorio.";
    if (!preg_match("/@almacen\.com$/i", $email)) $errores[] = "El email debe ser del dominio @almacen.com";
    if (empty($password)) $errores[] = "Debe introducir una contraseña.";

    if (empty($errores)) {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario (nombre, apellidos, email, password, id_rol) 
                VALUES (?,?,?,?,?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $apellidos, $email, $passwordHash, $rol);

        if ($stmt->execute()) {
            $mensaje = "Usuario creado con éxito.";

            // LOG
            registrarLog($con, $_SESSION['id_usuario'], "CREAR", "usuario $email");

        } else {
            $errores[] = "Error al crear usuario: " . $stmt->error;
        }
    }
}

// BORRAR USUARIO
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];

    if ($id != $_SESSION['id_usuario']) { 
        $sql = "DELETE FROM usuario WHERE id_usuario=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id);

        $stmt->execute();
        $mensaje = "Usuario eliminado.";

        // LOG 
        registrarLog($con, $_SESSION['id_usuario'], "BORRAR", "usuario $id");

    } else {
        $errores[] = "No puedes borrar tu propio usuario.";
    }
}

// GUARDAR USUARIO EDITADO

if (isset($_POST['guardar'])) {

    $id = (int)$_POST['id_usuario'];
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $rol = (int)$_POST['rol'];

    if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
    if (empty($apellidos)) $errores[] = "Los apellidos son obligatorios.";
    if (empty($email)) $errores[] = "El email es obligatorio.";
    if (!preg_match("/@almacen\.com$/i", $email)) $errores[] = "El email debe ser del dominio @almacen.com";

    if (empty($errores)) {

        $sql = "UPDATE usuario 
                SET nombre=?, apellidos=?, email=?, id_rol=? 
                WHERE id_usuario=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sssii", $nombre, $apellidos, $email, $rol, $id);

        if ($stmt->execute()) {
            $mensaje = "Usuario modificado con éxito.";

            // ===== LOG =====
            registrarLog($con, $_SESSION['id_usuario'], "ACTUALIZAR", "usuario $id");

        } else {
            $errores[] = "Error al editar usuario: " . $stmt->error;
        }
    }
}

// EDITAR USUARIO (cargar datos en formulario)
$editando = null;

if (isset($_GET['editar'])) {
    $id = (int)$_GET['editar'];

    $stmt = $con->prepare("SELECT * FROM usuario WHERE id_usuario=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editando = $stmt->get_result()->fetch_assoc();
}

// Registrar en el log que se ha accedido a la lista de usuarios (acción: LEER)
registrarLog($con, $_SESSION['id_usuario'], "LEER", "lista usuarios");

// LISTAR USUARIOS 
$usuarios = $con->query("SELECT u.id_usuario, u.nombre, u.apellidos, u.email, r.nombre AS rol 
                         FROM usuario u 
                         JOIN rol r ON u.id_rol = r.id_rol");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gestión de usuarios</title>
    </head>
    <body>

<h1>Gestión de usuarios</h1>

<?php 
if ($mensaje) echo "<p style='color:green'>$mensaje</p>";
if (!empty($errores)) {
    echo "<ul style='color:red'>";
    foreach ($errores as $e) echo "<li>$e</li>";
    echo "</ul>";
}
?>

<h2>Crear nuevo usuario</h2>
<form method="POST">
    Nombre: <input type="text" name="nombre"><br>
    Apellidos: <input type="text" name="apellidos"><br>
    Email: <input type="text" name="email"><br>
    Contraseña: <input type="password" name="password"><br>
    Rol:
    <select name="rol">
        <option value="1">Administrador</option>
        <option value="2">Administrativo</option>
        <option value="3">Operario</option>
    </select>
    <br><br>
    <button type="submit" name="crear">Crear Usuario</button>
</form>

<?php if ($editando): ?>
<h2>Editar usuario (ID <?= $editando['id_usuario'] ?>)</h2>

<form method="POST">
    <input type="hidden" name="id_usuario" value="<?= $editando['id_usuario'] ?>">

    Nombre: <input type="text" name="nombre" value="<?= $editando['nombre'] ?>"><br>

    Apellidos: <input type="text" name="apellidos" value="<?= $editando['apellidos'] ?>"><br>

    Email: <input type="text" name="email" value="<?= $editando['email'] ?>"><br>

    Rol:
    <select name="rol">
        <option value="1" <?= $editando['id_rol']==1?"selected":"" ?>>Administrador</option>
        <option value="2" <?= $editando['id_rol']==2?"selected":"" ?>>Administrativo</option>
        <option value="3" <?= $editando['id_rol']==3?"selected":"" ?>>Operario</option>
    </select>
    <br><br>

    <button type="submit" name="guardar">Guardar Cambios</button>
</form>
<?php endif; ?>


<h2>Lista de usuarios</h2>
<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre completo</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($u = $usuarios->fetch_assoc()): ?>
        <tr>
            <td><?= $u['id_usuario'] ?></td>
            <td><?= $u['nombre'] . " " . $u['apellidos'] ?></td>
            <td><?= $u['email'] ?></td>
            <td><?= $u['rol'] ?></td>
            <td>
                <a href="usuarios.php?editar=<?= $u['id_usuario'] ?>">Editar</a> |
                <a href="usuarios.php?eliminar=<?= $u['id_usuario'] ?>" 
                   style="color:red" 
                   onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">Eliminar</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>
