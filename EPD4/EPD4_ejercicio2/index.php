<?php
$resultado = "";
$errores = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $texto = trim($_POST["datos"] ?? "");
    if ($texto !== "") {
        $lineas = explode("\n", $texto);
        $productos = [];

        foreach ($lineas as $num => $linea) {
            $linea = trim($linea);
            if ($linea === "") continue;
            if (!preg_match('/^[^#]+#\d+#\d+#\d+$/', $linea)) {
                $errores[] = "Línea " . ($num + 1) . ": " . htmlspecialchars($linea);
                continue;
            }
            list($producto, $pasillo, $estanteria, $cantidad) = explode("#", $linea);
            $cantidad = (int)$cantidad;

            if (!isset($productos[$producto])) {
                $productos[$producto] = [
                    "total" => 0,
                    "ubicaciones" => []
                ];
            }

            $productos[$producto]["total"] += $cantidad;
            $productos[$producto]["ubicaciones"][] = [
                "pasillo" => $pasillo,
                "estanteria" => $estanteria,
                "cantidad" => $cantidad
            ];
        }
        if ($errores) {
            $resultado .= "<h3>La información propuesta no está bien formateada.</h3>";
            $resultado .= "<p>Hay errores en las siguientes líneas:</p><ul>";
            foreach ($errores as $e) {
                $resultado .= "<li>$e</li>";
            }
            $resultado .= "</ul><p>Revise la información.</p>";
        }else{
            foreach ($productos as $nombre => $info) {
                $resultado .= "<h3>$nombre</h3>";
                $resultado .= "<p>Total: {$info['total']} unidades</p>";

                $por_pasillo = [];
                foreach($info["ubicaciones"] as $u) {
                    $clave = $u["pasillo"];
                    $por_pasillo[$clave]["cantidad"] = ($por_pasillo[$clave]["cantidad"] ?? 0) + $u["cantidad"];
                    $por_pasillo[$clave]["estanterias"][] = $u["estanteria"];
                }

                foreach($por_pasillo as $pasillo => $data) {
                    $ests = implode(", ", array_unique($data["estanterias"]));
                    $resultado .= "<p>{$data['cantidad']} unidades en el pasillo $pasillo, estanterías $ests.</p>";
                }
            }
        }
    }else{
        $resultado = "<p>Por favor, introduce los datos en el formulario.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>EPD4 - Ejercicio 2</title>
</head>
<body style="font-family: Arial; margin: 40px;">
    <h2>Gestión de Productos Ejercicio 2 Grupo 10</h2>

    <form method="post">
        <label for="datos">Introduce los datos de productos:</label><br>
        <textarea name="datos" id="datos" rows="10" cols="70" placeholder="Producto#Pasillo#Estantería#Cantidad"><?php
            if (!empty($_POST["datos"])) echo htmlspecialchars($_POST["datos"]);
        ?></textarea><br><br>

        <input type="submit" value="Procesar">
    </form>

    <hr>
    <div>
        <?= $resultado ?>
    </div>
</body>
</html>
