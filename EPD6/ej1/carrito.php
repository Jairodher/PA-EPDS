<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>CarritoDeCompra</title>
        <script>
            function confirmarProcesamiento() {
                return confirm("¿Estás seguro de que deseas aplicar todos estos cambios a la base de datos permanentemente?");
            }
        </script>
    </head>
    <body>
        <?php
        session_start();
        require_once 'config.php';
        require_once 'permisos.php';
        require_once 'log.php';

        // Redireccionar si no hay sesión
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: login.php");
            exit();
        }

        // Renovar cookie
        if (!empty($_SESSION['nombre'])) {
            $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            setcookie('almacen_user', $_SESSION['nombre'], time() + 86400, '/', $_SERVER['HTTP_HOST'], $secure, true);
        }

        // Inicializar carrito si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // CSRF token mínimo: si no existe, generarlo
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $mensaje = "";
        $error = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_todo'])) {

            // VALIDAR CSRF
            $token = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', (string)$token)) {
                $error = "Token CSRF inválido. Operación cancelada.";
            } else {
                // Usamos $conexion
                $conexion->begin_transaction();

                try {
                    //Realizar operaciones (crear, modificar, borrar)
                    foreach ($_SESSION['carrito'] as $car => $op) {
                        if ($op['tipo'] == 'crear') {

                            $sql = 'INSERT INTO producto (descripcion, num_pasillo, num_estanteria, cantidad) VALUES (?,?,?,?)';
                            $stmt = $conexion->prepare($sql);
                            $stmt->bind_param("siii", $op['descripcion'], $op['num_pasillo'], $op['num_estanteria'], $op['cantidad']);
                            $stmt->execute();

                            // Registrar log usando id de sesión
                            registrarLog($conexion, $_SESSION['id_usuario'] ?? 1, 'CREAR', "Alta producto: " . $op['descripcion']);
                            $stmt->close();
                        } elseif ($op['tipo'] == 'modificar') {

                            $sql = "UPDATE producto SET descripcion=?, num_pasillo=?, num_estanteria=?, cantidad=? WHERE sku=?";
                            $stmt = $conexion->prepare($sql);
                            $stmt->bind_param("siiii", $op['descripcion'], $op['num_pasillo'], $op['num_estanteria'], $op['cantidad'], $op['sku']);
                            $stmt->execute();

                            registrarLog($conexion, $_SESSION['id_usuario'] ?? 1, 'ACTUALIZAR', "Modificación producto SKU: " . $op['sku']);
                            $stmt->close();
                        } elseif ($op['tipo'] == 'borrar') {

                            $sql = "DELETE FROM producto WHERE sku=?";
                            $stmt = $conexion->prepare($sql);
                            $stmt->bind_param("i", $op['sku']);
                            $stmt->execute();

                            registrarLog($conexion, $_SESSION['id_usuario'] ?? 1, 'BORRAR', "Borrado producto SKU: " . $op['sku']);
                            $stmt->close();
                        }
                    }

                    //confirmar cambios
                    $conexion->commit();
                    $_SESSION['carrito'] = [];
                    $mensaje = "Operaciones terminadas con exito";
                } catch (Exception $ex) {
                    $conexion->rollback();
                    $error = "Error al procesar el carrito: " . $ex->getMessage();
                }
            }
        }

        //Elimina una operacion del carrito
        if (isset($_GET['eliminar_indice'])) {
            $idx = (int)$_GET['eliminar_indice'];
            if (isset($_SESSION['carrito'][$idx])) {
                unset($_SESSION['carrito'][$idx]);
                $_SESSION['carrito'] = array_values($_SESSION['carrito']);
            }
            header("Location: carrito.php");
            exit;
        }
        ?>
        <nav>
            <ul>
                <li><a href="productos.php">Productos</a></li>
                <li><a href="#operaciones">Operaciones pendientes</a></li>
                <li><a href="#confirmacion">Confirmacion de cambios</a></li>
            </ul>
        </nav>
        <main>
            <h1>Carrito de operaciones pendientes</h1>

            <?php if ($mensaje): ?> <div class="alerta"><?php echo htmlspecialchars($mensaje, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></div> <?php endif; ?>
            <?php if ($error): ?> <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></div> <?php endif; ?>

            <section id="operaciones">
                <?php if (empty($_SESSION['carrito'])): ?>
                    <p>No hay ninguna operacion pendiente</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Tipo Acción</th>
                                <th>SKU Afectado</th>
                                <th>Detalles (Descripción / Pasillo / Estantería / Cantidad)</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['carrito'] as $idx => $op): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars((string)($idx + 1), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>
                                    <td class="tipo-<?php echo htmlspecialchars($op['tipo'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"><?php echo htmlspecialchars(strtoupper($op['tipo']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>
                                    <td><?php echo isset($op['sku']) ? htmlspecialchars((string)$op['sku'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '-'; ?></td>
                                    <td>
                                        <?php
                                        if ($op['tipo'] != 'borrar') {
                                            echo htmlspecialchars("{$op['descripcion']} / Pas: {$op['num_pasillo']} / Est: {$op['num_estanteria']} / Cant: {$op['cantidad']}", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                        } else {
                                            echo "Eliminación de registro";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="carrito.php?eliminar_indice=<?php echo (int)$idx; ?>" style="color:red">Quitar del carrito</a>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>

                <section id="confirmacion">
                    <form method="POST" action="carrito.php" onsubmit="return confirmarProcesamiento()">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                        <button type="submit" name="confirmar_todo" class="btn-confirmar">CONFIRMAR Y PROCESAR OPERACIONES</button>
                    </form>
                </section>
            <?php endif; ?>
        </main>
    </body>
</html>
<?php $conexion->close(); ?>
