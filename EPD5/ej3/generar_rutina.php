<?php
function validarEjercicio($valor, $ejercicio) {
    if ($ejercicio['tipo_campo'] === 'peso') {
        $valor = filter_var($valor, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $opciones = ['options' => ['min_range' => $ejercicio['min_valor'], 'max_range' => $ejercicio['max_valor']]];
        return filter_var($valor, FILTER_VALIDATE_FLOAT, $opciones) !== false ? $valor : false;
    } else {
        $valor = filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
        $opciones = ['options' => ['min_range' => (int)$ejercicio['min_valor'], 'max_range' => (int)$ejercicio['max_valor']]];
        return filter_var($valor, FILTER_VALIDATE_INT, $opciones) !== false ? $valor : false;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit('Método no permitido.');

$entrenador = trim($_POST['entrenador'] ?? '');
$tipo_rutina = trim($_POST['tipo_rutina'] ?? '');
$config_id = basename($_POST['config_id'] ?? '');

if ($entrenador === '' || $tipo_rutina === '' || $config_id === '') die('Faltan datos.');

$path = 'configuraciones/' . $config_id;
if (!file_exists($path)) die('Archivo de configuración no encontrado.');

$config = json_decode(file_get_contents($path), true);
if (!isset($config['ejercicios'])) die('Config inválida.');

$errores = [];
$resultados = [];
$total_ejer = 0;
$tiempo_estimado = 0;

foreach ($config['ejercicios'] as $ej) {
    $safeName = preg_replace('/\s+/', '_', $ej['nombre']);
    $campo = $_POST['ej_' . $safeName] ?? '';

    if ($campo === '') {
        $errores[] = "{$ej['nombre']}: Campo obligatorio vacío.";
        continue;
    }

    $val = validarEjercicio($campo, $ej);
    if ($val === false) {
        $errores[] = "{$ej['nombre']}: Valor '{$campo}' fuera de rango ({$ej['min_valor']} - {$ej['max_valor']}).";
    } else {
        if ($ej['tipo_campo'] === 'tiempo') {
            $suf = "minutos";
            $tiempo_estimado += (float)$val;
        } elseif ($ej['tipo_campo'] === 'peso') {
            $suf = "kg";
        } else {
            $suf = ($ej['tipo_campo'] === 'repeticiones') ? "repeticiones" : "";
        }
        $resultados[$ej['categoria']][] = "{$ej['nombre']}: {$val} {$suf}";
        $total_ejer++;
    }
}

$timestamp = time();
$nombre_salida = "rutina_" . preg_replace('/\s+/', '', $entrenador) . "_{$timestamp}.txt";
$ruta = 'rutinas_guardadas/' . $nombre_salida;
$f = fopen($ruta, 'w');

if (count($errores) > 0) {
    fwrite($f, "Errores en los valores introducidos:\n");
    foreach ($errores as $e) fwrite($f, "- $e\n");
} else {
    fwrite($f, "RUTINA PERSONALIZADA - NIVEL: $tipo_rutina\n");
    fwrite($f, "Entrenador: $entrenador\n");
    fwrite($f, "Fecha de creación: " . date("d/m/Y H:i") . "\n\n");
    fwrite($f, "### EJERCICIOS POR CATEGORÍA ###\n");
    foreach ($resultados as $cat => $list) {
        fwrite($f, strtoupper($cat) . ":\n");
        foreach ($list as $l) fwrite($f, "- $l\n");
    }
    fwrite($f, "\n### RESUMEN ###\n");
    fwrite($f, "Total de ejercicios: $total_ejer\n");
    fwrite($f, "Tiempo estimado: $tiempo_estimado minutos\n");
    fwrite($f, "Dificultad: $tipo_rutina\n");
}

fclose($f);

echo "<h3>Archivo generado:</h3>";
echo "<a href='{$ruta}' target='_blank'>{$nombre_salida}</a>";
echo "<pre>" . htmlspecialchars(file_get_contents($ruta)) . "</pre>";
