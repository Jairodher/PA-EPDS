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
        require_once 'utilidad.php';

        // Redireccionar si no hay sesión
        if (!isset($_SESSION['usuario'])) {
            //header("Location: login.php");
        }

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $mensaje = "";
        $error = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_todo'])) {
            $conexion->begin_transaction();

            try {
                //Realizar operaciones (crear, modificar, borrar)
                foreach ($_SESSION['carrito'] as $car => $op) {
                    if ($op['tipo'] == 'crear') {

                        $sql = 'INSERT INTO producto (descripcion, num_pasillo, num_estanteria, cantidad) VALUES (?,?,?,?)';
                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param("siii", $op['descripcion'], $op['num_pasillo'], $op['num_estanteria'], $op['cantidad']);
                        $stmt->execute();

                        registrarLog($conexion, $_SESSION['usuario']['id'] ?? 1, 'CREAR', "Alta producto: " . $op['descripcion']);
                        $stmt->close();
                    } elseif ($op['tipo'] == 'modificar') {

                        $sql = "UPDATE producto SET descripcion=?, num_pasillo=?, num_estanteria=?, cantidad=? WHERE sku=?";
                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param("siiii", $op['descripcion'], $op['num_pasillo'], $op['num_estanteria'], $op['cantidad'], $op['sku']);
                        $stmt->execute();

                        registrarLog($conexion, $_SESSION['usuario']['id'] ?? 1, 'ACTUALIZAR', "Modificación producto SKU: " . $op['sku']);
                        $stmt->close();
                    } elseif ($op['tipo'] == 'borrar') {

                        $sql = "DELETE FROM producto WHERE sku=?";
                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param("i", $op['sku']);
                        $stmt->execute();

                        registrarLog($conexion, $_SESSION['usuario']['id'] ?? 1, 'BORRAR', "Borrado producto SKU: " . $op['sku']);
                        $stmt->close();
                    }
                }

                //confirmar cambios
                $conexion->commit();
                $_SESSION['carrito'] = [];
                $mensaje = "Operaciones terminadas con exito";
            } catch (Exception $ex) {
                $conexion->rollback();
                $error = "Error al procesar el carrito: " . $e->getMessage();
            }

            //Elimina una operacion del carrito
            if (isset($_GET['eliminar_indice'])) {
                $idx = $_GET['eliminar_indice'];
                if (isset($_SESSION['carrito'][$idx])) {
                    unset($_SESSION['carrito'][$idx]);
                    // Reindexar array
                    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
                }
                header("Location: carrito.php");
                exit;
            }
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

            <?php if ($mensaje): ?> <div class="alerta"><?php echo $mensaje; ?></div> <?php endif; ?>
            <?php if ($error): ?> <div class="error"><?php echo $error; ?></div> <?php endif; ?>

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
                                    <td><?php echo $idx + 1; ?></td>
                                    <td class="tipo-<?php echo $op['tipo']; ?>"><?php echo strtoupper($op['tipo']); ?></td>
                                    <td><?php echo $op['sku'] ? $op['sku'] : '-'; ?></td>
                                    <td>
                                        <?php
                                        if ($op['tipo'] != 'borrar') {
                                            echo "{$op['descripcion']} / Pas: {$op['num_pasillo']} / Est: {$op['num_estanteria']} / Cant: {$op['cantidad']}";
                                        } else {
                                            echo "Eliminación de registro";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="carrito.php?eliminar_indice=<?php echo $idx; ?>" style="color:red">Quitar del carrito</a>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>

                <!-- Formulario de Confirmación Final -->
                <section id="confirmacion">
                    <form method="POST" action="carrito.php" onsubmit="return confirmarProcesamiento()">
                        <!-- doble confirmación -->
                        <button type="submit" name="confirmar_todo" class="btn-confirmar">CONFIRMAR Y PROCESAR OPERACIONES</button>
                    </form>
                </section>
            <?php endif; ?>
        </main>
    </body>
</html>
<?php $conexion->close(); ?>
