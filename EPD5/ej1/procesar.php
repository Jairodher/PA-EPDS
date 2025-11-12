<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errores = [];
    $horariosProcesados = 0; 


    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) $errores[] = "Email no válido.";


    $nombre = trim($_POST['nombre']);
    if (!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,100}$/", $nombre))
        $errores[] = "Nombre inválido: solo letras y espacios (máx. 100).";


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
            $esValida = true; // Flag para rastrear la validez de la línea
            
            if (trim($linea) == "") {
                $errores[] = "Línea ".($i+1).": vacía no permitida.";
                $esValida = false;
            } elseif (strlen($linea) > 200) {
                $errores[] = "Línea ".($i+1).": más de 200 caracteres.";
                $esValida = false;
            } elseif (!preg_match($regex, $linea)) {
                $errores[] = "Línea ".($i+1).": formato incorrecto ($linea).";
                $esValida = false;
            } else {
                
                $partes = explode("#", $linea);
                if (isset($partes[1], $partes[2])) {
                    if ($partes[1] >= $partes[2]) {
                        $errores[] = "Línea ".($i+1).": hora fin anterior o igual al inicio.";
                        $esValida = false;
                    }
                }
            }
            
            
            if ($esValida) {
                $horariosProcesados++;
            }
        }
    }

    if (empty($errores)) {
        if (!file_exists("horarios_validados")) mkdir("horarios_validados");

       
        $nombreSinEspacios = str_replace(" ", "", $nombre);
        
  
        $timestamp = time(); 
        
        $nuevoNombre = "horarios_"
            . $nombreSinEspacios
            . "_".$timestamp.".txt";

        move_uploaded_file($archivoTmp, "horarios_validados/".$nuevoNombre);

        echo "<h3>Archivo procesado correctamente</h3>";
        echo "<p>Instructor: $nombre<br>";
        echo "Horarios procesados: $horariosProcesados<br>"; // Línea agregada
        echo "Enviado por: $email<br>";
        echo "Guardado en: horarios_validados/$nuevoNombre</p>";
        
    } else {
        echo "<h3>Errores encontrados:</h3><ul>";
        foreach ($errores as $e) echo "<li>$e</li>";
        echo "</ul>";
    }
}
?>