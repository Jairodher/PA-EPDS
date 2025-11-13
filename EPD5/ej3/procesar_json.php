<?php
function generarCampoEjercicio($ejercicio) {
    $safeName = preg_replace('/\s+/', '_', $ejercicio['nombre']);
    switch($ejercicio['tipo_campo']) {
        case 'repeticiones':
            return "<input type='number' name='ej_{$safeName}' min='{$ejercicio['min_valor']}' max='{$ejercicio['max_valor']}' placeholder='{$ejercicio['descripcion']}'>";
        case 'tiempo':
            return "<input type='number' name='ej_{$safeName}' min='{$ejercicio['min_valor']}' max='{$ejercicio['max_valor']}' placeholder='{$ejercicio['descripcion']} (min)'>";
        case 'peso':
            return "<input type='number' step='0.5' name='ej_{$safeName}' min='{$ejercicio['min_valor']}' max='{$ejercicio['max_valor']}' placeholder='{$ejercicio['descripcion']} (kg)'>";
        case 'series':
            return "<input type='number' name='ej_{$safeName}' min='{$ejercicio['min_valor']}' max='{$ejercicio['max_valor']}' placeholder='Número de series'>";
        default:
            return "<span style='color:red;'>Tipo de campo inválido</span>";
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit('Método no permitido.');

$entrenador = trim($_POST['entrenador'] ?? '');
$tipo_rutina = trim($_POST['tipo_rutina'] ?? '');

if ($entrenador === '' || $tipo_rutina === '') die('Faltan datos iniciales.');

if (!isset($_FILES['archivo_json'])) die('No se subió ningún archivo.');

if ($_FILES['archivo_json']['error'] !== UPLOAD_ERR_OK) {
    die('Error en subida: ' . $_FILES['archivo_json']['error']);
}

$raw = file_get_contents($_FILES['archivo_json']['tmp_name']);
$json = json_decode($raw, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error JSON: " . json_last_error_msg());
}

if (!isset($json['ejercicios']) || !is_array($json['ejercicios'])) {
    die("Estructura JSON incorrecta: falta 'ejercicios' o no es un arreglo.");
}

$validos = ['repeticiones','tiempo','peso','series'];
$erroresContenido = [];
foreach ($json['ejercicios'] as $i => $ej) {
    if (!isset($ej['nombre']) || trim($ej['nombre']) === '') $erroresContenido[] = "Ejercicio #".($i+1).": nombre vacío.";
    if (!isset($ej['categoria'])) $erroresContenido[] = "Ejercicio #".($i+1).": falta categoria.";
    if (!isset($ej['tipo_campo']) || !in_array($ej['tipo_campo'],$validos)) $erroresContenido[] = "Ejercicio #".($i+1).": tipo_campo inválido o faltante.";
    if (!isset($ej['min_valor']) || !is_numeric($ej['min_valor'])) $erroresContenido[] = "Ejercicio #".($i+1).": min_valor inválido o faltante.";
    if (!isset($ej['max_valor']) || !is_numeric($ej['max_valor'])) $erroresContenido[] = "Ejercicio #".($i+1).": max_valor inválido o faltante.";
    if (!isset($ej['descripcion'])) $erroresContenido[] = "Ejercicio #".($i+1).": falta descripcion.";
}

if (count($erroresContenido) > 0) {
    echo "<h3>Errores en el contenido JSON:</h3><ul>";
    foreach ($erroresContenido as $e) echo "<li>$e</li>";
    echo "</ul>";
    exit;
}

$filename = 'config_' . time() . '.json';
$dest = 'configuraciones/' . $filename;
if (!move_uploaded_file($_FILES['archivo_json']['tmp_name'], $dest)) {
    die('No se pudo guardar el archivo en configuraciones/.');
}

$porCat = [];
foreach ($json['ejercicios'] as $ej) {
    $porCat[$ej['categoria']][] = $ej;
}

echo "<h2>Crear rutina (Entrenador: " . htmlspecialchars($entrenador) . ")</h2>";
echo "<form action='generar_rutina.php' method='post'>";
echo "<input type='hidden' name='entrenador' value='" . htmlspecialchars($entrenador) ."'>";
echo "<input type='hidden' name='tipo_rutina' value='" . htmlspecialchars($tipo_rutina) ."'>";
echo "<input type='hidden' name='config_id' value='" . htmlspecialchars($filename) ."'>";

foreach ($porCat as $cat => $lista) {
    echo "<fieldset><legend>" . htmlspecialchars($cat) . "</legend>";
    foreach ($lista as $ej) {
        echo "<label>" . htmlspecialchars($ej['nombre']) . ":</label> ";
        echo generarCampoEjercicio($ej);
        echo "<br>";
    }
    echo "</fieldset><br>";
}

echo "<input type='submit' value='Generar rutina'>";
echo "</form>";
