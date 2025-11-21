<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Productos</title>
    </head>
    <body>
        <?php
        session_start();
        require_once 'config.php';
        require_once 'utilidad.php';

        // Inicializar carrito si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $errores = [];
        $accion = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['envio'])) {

            //Si no se ha seleccionado la operacion, lanzamos un error
            if (!isset($_POST['accion']) || empty($_POST['accion'])) {
                $errores[] = "ERROR: Selecciona una operación a realizar";
            } else {
                $accion = $_POST['accion'];
            }

            // Si la operacion a realizar es borrar o modificar, buscamos el objeto por su SKU
            if ($accion === 'borrar' || $accion === 'modificar') {
                if (!isset($_POST['sku']) || empty($_POST['sku'])) {    //Si no existe, lanzamos error
                    $errores[] = "ERROR: Introduce el identificador del producto";
                }
            }
            //Si la operacion es crear
            if ($accion === 'crear') {
                if (!isset($_POST['descripcion']) || empty($_POST['descripcion'])) {
                    $errores[] = "ERROR: Describe el producto";
                }
                if (!isset($_POST['num_pasillo']) || empty($_POST['num_pasillo'])) {
                    $errores[] = "ERROR: Introduce el número del pasillo";
                }
                if (!isset($_POST['num_estanteria']) || empty($_POST['num_estanteria'])) {
                    $errores[] = "ERROR: Introduce la estantería";
                }
                if (!isset($_POST['cantidad']) || empty($_POST['cantidad'])) {
                    $errores[] = "ERROR: Introduce la cantidad del producto";
                }
            }

            //Si no hay errores
            if (empty($errores)) {
                $operacion = [
                    'tipo' => $accion,
                    'sku' => $_POST['sku'],
                    'descripcion' => $_POST['descripcion'],
                    'num_pasillo' => $_POST['num_pasillo'],
                    'num_estanteria' => $_POST['num_estanteria'],
                    'cantidad' => $_POST['cantidad'],
                ];

                $_SESSION['carrito'][] = $operacion;
                $mensaje = "Operación de '$accion' añadida al carrito correctamente.";
            }
        }

        // PAGINACIÓN

        $registros_por_pagina = 5;
        $pagina_actual = isset($_GET['pag']) ? (int) $_GET['pag'] : 1;
        if ($pagina_actual < 1)
            $pagina_actual = 1;
        $offset = ($pagina_actual - 1) * $registros_por_pagina;

        // BÚSQUEDA
        $busqueda = isset($_GET['q']) ? sanear($_GET['q']) : '';
        $condicion_busqueda = "";
        $params_tipo = "";
        $params_valor = [];

        if (!empty($busqueda)) {
            $condicion_busqueda = "WHERE descripcion LIKE ? OR sku LIKE ?";
            $like = "%$busqueda%";
            $params_tipo = "ss";
            $params_valor = [$like, $like];
        }

        // CONTAR REGISTROS
        $sql_total = "SELECT COUNT(*) as total FROM producto $condicion_busqueda";
        $stmt_total = $conexion->prepare($sql_total);

        if (!empty($params_valor)) {
            $stmt_total->bind_param($params_tipo, ...$params_valor);
        }

        $stmt_total->execute();
        $res_total = $stmt_total->get_result();
        $total_registros = $res_total->fetch_assoc()['total'];
        $total_paginas = max(1, ceil($total_registros / $registros_por_pagina));
        $stmt_total->close();

        // LISTAR PRODUCTOS
        $sql_productos = "SELECT * FROM producto $condicion_busqueda LIMIT ? OFFSET ?";
        $stmt = $conexion->prepare($sql_productos);

        if (!empty($busqueda)) {
            $params_tipo .= "ii";
            $params_valor[] = $registros_por_pagina;
            $params_valor[] = $offset;
            $stmt->bind_param($params_tipo, ...$params_valor);
        } else {
            $stmt->bind_param("ii", $registros_por_pagina, $offset);
        }

        $stmt->execute();
        $resultado_productos = $stmt->get_result();
        ?>

        <header>
            <h1>Operaciones con productos</h1>
        </header>
        <nav>
            <ul>
                <li><a href="#busqueda">Formulario de busqueda</a></li>
                <li><a href="#peticion">Formulario de peticion</a></li>
                <li><a href="#listado">listado de productos</a></li>
            </ul>
        </nav>
        <main>
            <?php
            if (!isset($_POST['envio']) || isset($errores)) {
                //Si hay errores, lanzarlos

                echo '<h2> Formulario de peteci&oacute;n </h>';

                if (isset($errores)) {
                    echo '<p style = "color:red">Errores cometidos: </p>';
                    echo '<ul style="color:red>';
                    foreach ($errores as $er)
                        echo "<li>$er</li>";
                    echo '</ul>';
                }
            }
            ?>
            <!--Formulario de busqueda-->
            <div id ="busqueda">
                <form method="GET" action="productos.php"> 
                    <label>Introduce el id del producto a buscar</label>
                    <input type="text" name="q" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="SKU">
                    <button type="submit">buscar</button>
                </form>
            </div>

            <!--Formulario de peticion-->
            <div id="peticion">
                <h2>Creacion de un nuevo producto</h2>
                <form method="POST" action="productos.php">
                    <label>SKU (Auto):</label>
                    <input type="text" name="sku" disabled size="5">

                    <label>Descripción:</label>
                    <input type="text" name="descripcion" required>

                    <label>Pasillo:</label>
                    <input type="number" name="num_pasillo" style="width:50px" required>

                    <label>Estantería:</label>
                    <input type="number" name="num_estanteria" style="width:50px" required>

                    <label>Cantidad:</label>
                    <input type="number" name="cantidad" style="width:60px" required>

                    <button type="submit" name="envio" value="1">Añadir ALTA al Carrito</button>
                </form>
            </div>

            <!--Listado de productos-->
            <div id="listado">
                <h2>Listado de productos</h2>
                <table>
                    <thead>
                    <th>SKU</th>
                    <th>DESCRIPCION</th>
                    <th>NUMERO DEL PASILLO</th>
                    <th>NUMERO DE ESTANTERIA</th>
                    <th>CANTIDAD</th>
                    </thead>
                </table>
                <tbody>
                    <?php while ($prod = $resultado_productos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $prod['sku']; ?></td>
                            <td><?php echo $prod['descripcion']; ?></td>
                            <td><?php echo $prod['num_pasillo']; ?></td>
                            <td><?php echo $prod['num_estanteria']; ?></td>
                            <td><?php echo $prod['cantidad']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </div>

            <!-- Paginación -->
            <div class="paginacion">
                <p>Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?></p>
                <?php if ($pagina_actual > 1): ?>
                    <a href="?pag=<?php echo $pagina_actual - 1; ?>&q=<?php echo $busqueda; ?>">Anterior</a>
                <?php endif; ?>
                <?php if ($pagina_actual < $total_paginas): ?>
                    <a href="?pag=<?php echo $pagina_actual + 1; ?>&q=<?php echo $busqueda; ?>">Siguiente</a>
                <?php endif; ?>
            </div>
        </main>
    </body>
</html>


