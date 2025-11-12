<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Gesti&oacute;n de Reservas</title>
        <link rel="stylesheet" href="estilo.css">
    </head>
    <body>
        <div class="container">
            <h1>Gesti&oacute;n de Reservas</h1>

            <?php

            function compararFechas($a, $b) {
                if ($a['fecha'] != $b['fecha']) {
                    return strcmp($a['fecha'], $b['fecha']);
                }

                return strcmp($a['instructor'], $b['instructor']);
            }

            // Preparamos las variables
            $actividades = array("Spinning", "Yoga", "Natación", "Tenis", "Pilates", "Aeróbicos");
            $instructores = array("Ana López", "Miguel Torres", "Carmen Ruiz", "David Garcia", "Laura Martin");
            $today = date('Y-m-d');
            $errores = array();

            // Comprobamos que se ha pulsado el boton 'Generar planning' 
            if (isset($_POST['generar_planning'])) {
                // Hacemos las validaciones para cada campo del formulario
                $num_reservas = intval(isset($_POST['num_reservas']) ? $_POST['num_reservas'] : 0);

                for ($i = 0; $i < $num_reservas; $i++) {
                    $fecha = trim($_POST['fecha'][$i]);
                    if (empty($fecha)) {
                        $errores['fecha'][$i] = 'La fecha es obligatoria.';
                    } else {
                        $partes_fecha = explode('-', $fecha);
                        if (count($partes_fecha) != 3 || !checkdate(intval($partes_fecha[1]), intval($partes_fecha[2]), intval($partes_fecha[0]))) {
                            $errores['fecha'][$i] = 'Fecha no válida.';
                        } elseif (strtotime($fecha) < strtotime($today)) {
                            $errores['fecha'][$i] = 'La fecha no puede ser anterior a hoy.';
                        }
                    }
                    if (empty(trim($_POST['actividad'][$i]))) {
                        $errores['actividad'][$i] = 'Seleccione una actividad.';
                    }
                    if (empty(trim($_POST['instructor'][$i]))) {
                        $errores['instructor'][$i] = 'Seleccione un instructor.';
                    }
                    $duracion = trim($_POST['duracion'][$i]);
                    if ($duracion < 30 || $duracion > 180) {
                        $errores['duracion'][$i] = 'Duración entre 30 y 180 min.';
                    }
                    $participantes = trim($_POST['participantes'][$i]);
                    if ($participantes < 1 || $participantes > 25) {
                        $errores['participantes'][$i] = 'Participantes entre 1 y 25.';
                    }
                    if (strlen(trim($_POST['observaciones'][$i])) > 300) {
                        $errores['observaciones'][$i] = 'Máximo 300 caracteres.';
                    }
                }
            }
            // Dependiendo de la validación, se mostrará una de estas opciones
            // Opción 1: No hay errores y el formulario se envía con exito
            if (isset($_POST['generar_planning']) && empty($errores)) {
                $reservas = array();

                //Ordenamos los datos en un array de reservas
                for ($i = 0; $i < $num_reservas; $i++) {
                    $reserva = array(
                        'fecha' => $_POST['fecha'][$i],
                        'actividad' => $_POST['actividad'][$i],
                        'instructor' => $_POST['instructor'][$i],
                        'duracion' => $_POST['duracion'][$i],
                        'participantes' => $_POST['participantes'][$i],
                        'observaciones' => ucwords(strtolower(trim($_POST['observaciones'][$i])))
                    );
                    $reservas[] = $reserva;
                }

                //Ordeno el array segun la fecha, y si son iguales las fechas, por instructor.
                usort($reservas, 'compararFechas');

                // Cálculo de la fila TOTALES:
                $total_duracion = array_sum(array_column($reservas, 'duracion'));
                $total_participantes = array_sum(array_column($reservas, 'participantes'));

                $instructores_unicos = count(array_unique(array_column($reservas, 'instructor')));
                $actividades_unicas = count(array_unique(array_column($reservas, 'actividad')));
                //Calculo del rowspam:

                $copia = $reservas; //Copio el array para no modificar el original
                $n = count($copia);
                $i = 0;

                while ($i < $n) {
                    //Calculo para la fecha
                    $fecha_actual = $copia[$i]['fecha'];
                    $fecha_span = 1;
                    for ($k = $i + 1; $k < $n; $k++) {
                        if ($copia[$k]['fecha'] == $fecha_actual) {
                            $fecha_span++;
                            $copia[$k]['mostrar_fecha'] = false;
                        } else {
                            break;
                        }
                    }
                    $copia[$i]['fecha_span'] = $fecha_span;
                    $copia[$i]['mostrar_fecha'] = true;

                    //Calculo para los instructores
                    $j = $i;
                    while ($j < $i + $fecha_span) {
                        $instructor_actual = $copia[$j]['instructor'];
                        $instructor_span = 1;
                        for ($k = $j + 1; $k < $i + $fecha_span; $k++) {
                            if ($copia[$k]['instructor'] == $instructor_actual) {
                                $instructor_span++;
                                $copia[$k]['mostrar_instructor'] = false;
                            } else {
                                break;
                            }
                        }
                        $copia[$j]['instructor_span'] = $instructor_span;
                        $copia[$j]['mostrar_instructor'] = true;

                        $j += $instructor_span;
                    }
                    $i += $fecha_span;
                }
                ?>
                <h2>Planing de actividades</h2>
                <table class="tabla-resumen">
                    <thead>
                        <tr>
                            <td colspan="5"><strong>Fecha del informe</strong></td>
                            <td><strong><?php echo date('d-m-Y'); ?></strong></td>
                        </tr>
                        <tr>
                            <th>Fecha</th>
                            <th>Instructor</th>
                            <th>Actividad</th>
                            <th>Duración (min)</th>
                            <th>Participantes</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        //Imprimir los datos en la tabla
                        foreach ($copia as $res):
                            ?>
                            <tr>
                                <?php if ($res['mostrar_fecha']): ?>
                                    <td rowspan="<?php echo $res['fecha_span']; ?>">
                                        <?php echo date('d/m/Y', strtotime($res['fecha'])); ?>
                                    </td>
                                <?php endif; ?>

                                <?php if ($res['mostrar_instructor']): ?>
                                    <td rowspan="<?php echo $res['instructor_span']; ?>">
                                        <?php echo htmlspecialchars($res['instructor']); ?>
                                    </td>
                                <?php endif; ?>

                                <td><?php echo htmlspecialchars($res['actividad']); ?></td>
                                <td><?php echo $res['duracion']; ?></td>
                                <td><?php echo $res['participantes']; ?></td>
                                <td><?php echo htmlspecialchars($res['observaciones']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fila-total">
                            <td colspan="2"><strong>TOTALES</strong></td>
                            <td><?php echo $actividades_unicas; ?> (Actividades)</td>
                            <td><?php echo $total_duracion; ?></td>
                            <td><?php echo $total_participantes; ?></td>
                            <td><?php echo $instructores_unicos; ?> (Instructores)</td>
                        </tr>
                    </tfoot>

                    <br><br>
                    <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="btn">Rellenar nuevo formulario</a>
                </table>
                <?php
                // Opción 2: Mostramos el formulario principal (el de hacer la reserva)
            } elseif ((isset($_GET['num_reservas']) && intval($_GET['num_reservas']) > 0) || (isset($_POST['generar_planning']) && !empty($errores))) {
                // Si hay errores, los mostramos
                if (!empty($errores)) {
                    echo '<p style="color:red">Errores cometidos:</p><ul style="color:red">';
                    foreach ($errores as $campo_errores) {
                        if (is_array($campo_errores)) {
                            foreach ($campo_errores as $e) {
                                echo "<li>$e</li>";
                            }
                        }
                    }
                    echo '</ul>';
                }
                // Determinamos el número de reservas
                $num_reservas_form = 0;
                if (isset($_GET['num_reservas'])) {
                    $num_reservas_form = intval($_GET['num_reservas']);
                } else {
                    $num_reservas_form = intval(isset($_POST['num_reservas']) ? $_POST['num_reservas'] : 0);
                }

                // Cerramos el bloque de php para escribir el html del formulario
                ?>
                <form method="POST" action=""> <input type="hidden" name="num_reservas" value="<?php echo $num_reservas_form; ?>">
                    <!-- Utilizamos un for para generar el número especificado de formularios -->
                    <?php for ($i = 0; $i < $num_reservas_form; $i++) { ?>
                        <fieldset>
                            <legend>Reserva <?php echo $i + 1; ?></legend>
                            <div class="form-grid">
                                <?php
                                $val_fecha = isset($_POST['fecha'][$i]) ? $_POST['fecha'][$i] : '';
                                $val_actividad = isset($_POST['actividad'][$i]) ? $_POST['actividad'][$i] : '';
                                $val_instructor = isset($_POST['instructor'][$i]) ? $_POST['instructor'][$i] : '';
                                $val_duracion = isset($_POST['duracion'][$i]) ? $_POST['duracion'][$i] : '';
                                $val_participantes = isset($_POST['participantes'][$i]) ? $_POST['participantes'][$i] : '';
                                $val_obs = isset($_POST['observaciones'][$i]) ? $_POST['observaciones'][$i] : '';
                                ?>

                                <div>
                                    <label>Fecha de la actividad:</label>
                                    <input type="date" name="fecha[]" min="<?php echo $today; ?>" 
                                           class="<?php echo isset($errores['fecha'][$i]) ? 'campo-invalido' : ''; ?>" 
                                           value="<?php echo $val_fecha; ?>">
                                           <?php
                                           if (isset($errores['fecha'][$i])) {
                                               echo '<span class="error-message">' . $errores['fecha'][$i] . '</span>';
                                           }
                                           ?>
                                </div>

                                <div>
                                    <label>Actividad:</label>
                                    <select name="actividad[]" class="<?php echo isset($errores['actividad'][$i]) ? 'campo-invalido' : ''; ?>">
                                        <option value="">Sin valor</option>
                                        <?php foreach ($actividades as $act) { ?>
                                            <option value="<?php echo $act; ?>" <?php echo ($val_actividad == $act) ? 'selected' : ''; ?>><?php echo $act; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php
                                    if (isset($errores['actividad'][$i])) {
                                        echo '<span class="error-message">' . $errores['actividad'][$i] . '</span>';
                                    }
                                    ?>
                                </div>

                                <div>
                                    <label>Instructor:</label>
                                    <select name="instructor[]" class="<?php echo isset($errores['instructor'][$i]) ? 'campo-invalido' : ''; ?>">
                                        <option value=""></option>
                                        <?php foreach ($instructores as $inst) { ?>
                                            <option value="<?php echo $inst; ?>" <?php echo ($val_instructor == $inst) ? 'selected' : ''; ?>><?php echo $inst; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php
                                    if (isset($errores['instructor'][$i])) {
                                        echo '<span class="error-message">' . $errores['instructor'][$i] . '</span>';
                                    }
                                    ?>
                                </div>

                                <div>
                                    <label>Duración estimada:</label>
                                    <input type="number" name="duracion[]" min="30" max="180" 
                                           class="<?php echo isset($errores['duracion'][$i]) ? 'campo-invalido' : ''; ?>" 
                                           value="<?php echo $val_duracion; ?>">
                                           <?php
                                           if (isset($errores['duracion'][$i])) {
                                               echo '<span class="error-message">' . $errores['duracion'][$i] . '</span>';
                                           }
                                           ?>
                                </div>
                                <div class="full-width">
                                    <label>Observaciones:</label>
                                    <textarea name="observaciones[]" maxlength="300" class="<?php echo isset($errores['observaciones'][$i]) ? 'campo-invalido' : ''; ?>"><?php echo $val_obs; ?></textarea>
                                    <?php
                                    if (isset($errores['observaciones'][$i])) {
                                        echo '<span class="error-message">' . $errores['observaciones'][$i] . '</span>';
                                    }
                                    ?>
                                </div>
                                <div>
                                    <label>N&uacute;mero de Participantes:</label>
                                    <input type="number" name="participantes[]" min="1" max="25" 
                                           class="<?php echo isset($errores['participantes'][$i]) ? 'campo-invalido' : ''; ?>" 
                                           value="<?php echo $val_participantes; ?>">
                                           <?php
                                           if (isset($errores['participantes'][$i])) {
                                               echo '<span class="error-message">' . $errores['participantes'][$i] . '</span>';
                                           }
                                           ?>
                                </div>


                            </div>
                        </fieldset>
                    <?php } // Final del for   ?>
                    <button type="submit" name="generar_planning" class="btn" style="width:100%; padding:10px;">Generar planning</button>
                </form>
                <?php
                // Creamos otro bloque de php para cerrar el elseif
                // Opción 3: Mostramos el formulario inicial (cuantas reservas se van a hacer)
            } else {
                if (isset($_GET['num_reservas'])) {
                    echo '<p style="color:red">Debe introducir un número positivo.</p>';
                }
                // Cerramos php para escribir el html del formulario inicial
                ?>
                <form method="GET" action="">
                    <fieldset>
                        <legend>Inicio de Planificación</legend>
                        <label>Número de reservas a procesar:</label>
                        <input type="number" name="num_reservas" min="1" value="1" required style="width:auto;">
                        <button type="submit" class="btn">Generar Formulario</button>
                    </fieldset>
                </form>
                <?php
                // Volvemos a abrir php para cerrar el else
            }
            ?>

        </div> </body>
</html>
