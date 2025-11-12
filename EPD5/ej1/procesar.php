<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errores = [];

    // --- Validar email ---
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) $errores[] = "Email no válido.";

    // --- Validar nombre ---
    $nombre = trim($_POST['nombre']);
    if (!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,100}$/", $nombre))
        $errores[] = "Nombre inválido: solo letras y espacios (máx. 100).";

    // --- Validar archivo ---
    if ($_FILES['archivo']['error'] != 0) {
        $errores[] = "Error al subir el archivo.";
    } else {
        $archivoTmp = $_FILES['archivo']['tmp_name'];
        $nombreArchivo = $_FILES['archivo']['name'];
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $mime = mime_content_type($archivoTmp);

        if ($extension != 'txt' || $mime != 'text/plain')
            $errores[] = "El archivo debe ser .txt y de texto plano.";

        $lineas = file($archivoTmp, FILE_IGNORE_NEW_LINES);
        $numLineas = count($lineas);

        if ($numLineas > 50)
            $errores[] = "El archivo tiene más de 50 líneas.";

        $regex = "/^[A-ZÁÉÍÓÚÑ]+#[0-2][0-9]:[0-5][0-9]#[0-2][0-9]:[0-5][0-9]#.{1,200}$/";

        foreach ($lineas as $i => $linea) {
            if (trim($linea) == "")
                $errores[] = "Línea ".($i+1).": vacía no permitida.";
            elseif (strlen($linea) > 200)
                $errores[] = "Línea ".($i+1).": más de 200 caracteres.";
            elseif (!preg_match($regex, $linea))
                $errores[] = "Línea ".($i+1).": formato incorrecto ($linea).";
            else {
                // Validar horas
                $partes = explode("#", $linea);
                if (isset($partes[1], $partes[2])) {
                    if ($partes[1] >= $partes[2])
                        $errores[] = "Línea ".($i+1).": hora fin anterior o igual al inicio.";
                }
            }
        }
    }

    // --- Mostrar resultado ---
    if (empty($errores)) {
        if (!file_exists("horarios_validados")) mkdir("horarios_validados");

        $nuevoNombre = "horarios_"
            . str_replace(" ", "", $nombre)
            . "_".time().".txt";

        move_uploaded_file($archivoTmp, "horarios_validados/".$nuevoNombre);

        echo "<h3>Archivo procesado correctamente</h3>";
        echo "<p>Instructor: $nombre<br>Enviado por: $email<br>";
        echo "Guardado en: horarios_validados/$nuevoNombre</p>";
    } else {
        echo "<h3>Errores encontrados:</h3><ul>";
        foreach ($errores as $e) echo "<li>$e</li>";
        echo "</ul>";
    }
}
?>
