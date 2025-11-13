<html>
    <body>
        <?php

        function generarIDSocio() {
            $numero = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            return "SOC" . $numero;
        }

        function generarActividad() {
            $actividades = array(
                'Spinning', 'Yoga', 'Pilates', 'Zumba', 'CrossFit',
                'Aeróbicos', 'Aqua Fitness', 'BodyCombat', 'TRX',
                'Funcional', 'GAP', 'Stretching'
            );
            return $actividades[array_rand($actividades)];
        }

        function generarFechaReserva() {
            $dias = rand(1, 30);
            $timestamp = time() + ($dias * 24 * 60 * 60);
            return date('d/m/Y', $timestamp);
        }

        function generarHorarioGimnasio() {
            $horarios = array(
                '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
                '16:00', '17:00', '18:00', '19:00', '20:00', '21:00'
            );
            return $horarios[array_rand($horarios)];
        }

        function obtenerDuracionActividad($actividad) {
            $duraciones = array(
                'Spinning' => 45, 'Yoga' => 60, 'Pilates' => 50,
                'Zumba' => 45, 'CrossFit' => 60, 'Aeróbicos' => 45,
                'Aqua Fitness' => 45, 'BodyCombat' => 45, 'TRX' => 30,
                'Funcional' => 45, 'GAP' => 30, 'Stretching' => 30
            );
            return isset($duraciones[$actividad]) ? $duraciones[$actividad] : 45;
        }

        function generarSala($actividad) {
            $salas = array(
                'Spinning' => 'Sala Cycling',
                'Yoga' => 'Sala Zen',
                'Pilates' => 'Sala Pilates',
                'Aqua Fitness' => 'Piscina',
                'CrossFit' => 'Box CrossFit'
            );
            return isset($salas[$actividad]) ? $salas[$actividad] : 'Sala Polivalente';
        }

        function generarInstructor() {
            $instructores = array(
                'Ana López', 'Miguel Torres', 'Carmen Ruiz', 'David García',
                'Laura Martín', 'Javier Moreno', 'Elena Sánchez', 'Carlos Vega',
                'María González', 'Pablo Jiménez'
            );
            return $instructores[array_rand($instructores)];
        }

        $resultado_generacion = null;
        $numArchivos_val = isset($_POST['numArchivos']) ? htmlspecialchars($_POST['numArchivos']) : '';
        $numReservas_val = isset($_POST['numReservas']) ? htmlspecialchars($_POST['numReservas']) : '';
        $sede_val = isset($_POST['sede']) ? htmlspecialchars($_POST['sede']) : '';

        if (isset($_POST['envio'])) {
            $numArchivos = filter_input(INPUT_POST, 'numArchivos', FILTER_SANITIZE_NUMBER_INT);
            $numReservas = filter_input(INPUT_POST, 'numReservas', FILTER_SANITIZE_NUMBER_INT);
            $sede = filter_input(INPUT_POST, 'sede', FILTER_SANITIZE_STRING);
            $sede = trim(strtoupper($sede));

            $archivosOptions = array(
                'options' => array(
                    'min_range' => 1,
                    'max_range' => 7
                )
            );
            if (filter_var($numArchivos, FILTER_VALIDATE_INT, $archivosOptions) === false) {
                $errores[] = 'El numero de archivos debe estar entre 1 y 7';
            }
            $reservasOptions = array(
                'options' => array(
                    'min_range' => 15,
                    'max_range' => 40
                )
            );
            if (filter_var($numReservas, FILTER_VALIDATE_INT, $reservasOptions) === false) {
                $errores[] = 'El numero de reservas debe estar entre 15 y 40';
            }
            $sedeOptions = array(
                'GYM', 'FIT', 'SPA'
            );
            if (strlen($sede) != 3 || !in_array($sede, $sedeOptions)) {
                $errores[] = 'La sede debe ser GYM, FIT o SPA y tener 3 caracteres';
            }

            if (!isset($errores)) { //Si no hay errores en el formulario:
                echo '<h1> Datos recibidos </h1>';
                echo 'N&uacute;mero de archivos de reservas: ' . $numArchivos . '<br />';
                echo 'N&uacute;mero de reservas por archivo: ' . $numReservas . '<br />';
                echo 'Sede del gimnasio: ' . $sede . '<br />';
                echo 'Env&iacute;o: ' . $_POST['envio'] . '<br />';

                //Crear directorio si no existe
                $directorio = $_SERVER['DOCUMENT_ROOT'] . '/reservas_generadas';
                if (!is_dir($directorio)) {
                    if (!mkdir($directorio, 0755, true)) {
                        $errores[] = "ERROR: No se pudo crear el directorio 'reservas_generadas'.";
                    }
                }

                if (empty($errores)) {
                    for ($j = 1; $j <= $numArchivos; $j++) {

                        $contenido_fichero = "";
                        $reservas_unicas = [];

                        for ($i = 1; $i <= $numReservas; $i++) {
                            $exito = false;
                            do {
                                $id_socio = generarIDSocio();
                                $actividad = generarActividad();
                                $fecha_reserva = generarFechaReserva();
                                $franja_horaria = generarHorarioGimnasio();
                                $duracion_actividad = obtenerDuracionActividad($actividad);
                                $sala_actividad = generarSala($actividad);
                                $instructor = generarInstructor();

                                $clave_reserva = $id_socio . '#' . $fecha_reserva . '#' . $franja_horaria;

                                if (!isset($reservas_unicas[$clave_reserva])) {
                                    $reservas_unicas[$clave_reserva] = true;
                                    $exito = true;
                                }
                            } while (!$exito);
                            
                            $linea_array = [
                                $id_socio,
                                $actividad,
                                $fecha_reserva,
                                $franja_horaria,
                                $duracion_actividad,
                                $sala_actividad,
                                $instructor
                            ];

                            $contenido_fichero .= implode("#", $linea_array) . PHP_EOL;
                        }

                        //Escribir el fichero
                        $num_fichero = str_pad($j, 3, '0', STR_PAD_LEFT);
                        $fecha = date("Ymd_His");
                        $nombre_fichero = "reservas_{$sede}_{$num_fichero}_{$fecha}.txt";
                        $ruta = $directorio . '/' . $nombre_fichero;

                        // Abrir el archivo
                        $f = fopen($ruta, 'w');
                        if (!$f) {
                            $errores[] = "ERROR: no se pudo abrir el fichero $nombre_fichero";
                            continue;
                        }

                        if (flock($f, LOCK_EX)) {
                            if (fwrite($f, $contenido_fichero) === false) {
                                $errores[] = "ERROR: no se pudo escribir en el fichero $nombre_fichero";
                            } else {
                                $mensajes_exito[] = "Archivo $nombre_fichero guardado con éxito!";
                            }
                            flock($f, LOCK_UN);
                        } else {
                            $errores[] = "ERROR: no se pudo bloquear el fichero $nombre_fichero";
                        }

                        fclose($f);
                    }
                }
                if (!empty($errores)) {
                    echo "<pre>Se produjeron errores:\n" . implode("\n", $errores) . "</pre>";
                }
                if (!empty($mensajes_exito)) {
                    echo "<pre>Archivos generados correctamente:\n" . implode("\n", $mensajes_exito) . "</pre>";
                }
            }
        }

        if (!isset($_POST['envio']) || isset($errores)) {
            echo '<h1> Realizar reservas de gimnasio </h1>';
            if (isset($errores)) {
                echo '<p style="color:red">Errores cometidos: </p>';
                echo '<ul style="color:red">';
                foreach ($errores as $e) {
                    echo "<li>$e</li>";
                }
                echo '</ul>';
            }
        }
        ?>

        <form method="post">
            <label for="numArchivos">N&uacute;mero de archivos de reserva: </label>
            <input name="numArchivos" type="number" value="<?php echo $numArchivos_val; ?>"/><br />
            <label for="numReservas">N&uacute;mero de reservas por archivo: </label>
            <input name="numReservas" type="number" value="<?php echo $numReservas_val; ?>"/><br/>
            <label for="sede">Sede del gimnasio: </label>
            <input name="sede" type="text" value="<?php echo $sede_val; ?>"><br/>
            <button name="envio" type="submit" value="enviado">Enviar</button>
        </form>
    </body>
</html>
