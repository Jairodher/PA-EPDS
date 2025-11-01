<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Solicitud de Reembolso - Instalaciones Deportivas</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>

        <?php
        //Opciones y variables
        $opciones_incidencia = [
            "Sin valor" => "--- Seleccione una Opción ---",
            "Cancelación de clase" => "Cancelación de clase",
            "Equipamiento defectuoso" => "Equipamiento defectuoso",
            "Instructor ausente" => "Instructor ausente",
            "Sobreventa de plazas" => "Sobreventa de plazas",
            "Otros" => "Otros"
        ];

        $campos = [
            'nombre_socio', 'numero_membresia', 'fecha_actividad', 'hora_inicio',
            'tipo_incidencia', 'importe_reembolsar', 'dias_resolucion', 'descripcion_incidencia'
        ];

        $datos = [];
        $errores = [];
        $mostrar_resumen = false;

        //Inicializacion de datos 
        foreach ($campos as $campo) {
            $datos[$campo] = '';
        }

        //Procesamiento del formulario con POST
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            
            foreach ($campos as $campo) {
                $datos[$campo] = isset($_POST[$campo]) ? trim($_POST[$campo]) : '';
            }

            //Validacion de los campos vacios
            foreach ($campos as $campo) {
                if (empty($datos[$campo]) && $campo !== 'tipo_incidencia') {
                    $errores[$campo] = "Este campo es obligatorio.";
                }
                if ($campo === 'tipo_incidencia' && $datos[$campo] === 'Sin valor') {
                    $errores[$campo] = "Debe seleccionar una opción válida.";
                }
            }
            
            if (empty($errores)) {
                if (strlen($datos['nombre_socio']) > 150) {
                    $errores['nombre_socio'] = "El nombre no puede exceder los 150 caracteres.";
                }
                
                if (!preg_match('/^SOC\d{6}$/i', $datos['numero_membresia'])) {
                    $errores['numero_membresia'] = "Debe seguir el formato SOC123456 (6 dígitos).";
                }
                
                $fecha_valida = date_create_from_format('Y-m-d', $datos['fecha_actividad']);
                if ($fecha_valida && $fecha_valida->format('Y-m-d') === $datos['fecha_actividad']) {
                    $hoy = new DateTime();
                    $hace_30_dias = (new DateTime())->modify('-30 days');

                    if ($fecha_valida > $hoy) {
                        $errores['fecha_actividad'] = "La fecha no puede ser posterior al día actual.";
                    } elseif ($fecha_valida < $hace_30_dias) {
                        $errores['fecha_actividad'] = "La fecha no puede ser anterior a 30 días.";
                    }
                } else {
                    $errores['fecha_actividad'] = "Formato de fecha inválido (YYYY-MM-DD).";
                }

                if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $datos['hora_inicio'])) {
                    $errores['hora_inicio'] = "Formato de hora incorrecto. Use HH:MM (24h).";
                }

                $validas = ["Cancelación de clase", "Equipamiento defectuoso", "Instructor ausente", "Sobreventa de plazas", "Otros"];
                if (!in_array($datos['tipo_incidencia'], $validas)) {
                    $errores['tipo_incidencia'] = "Opción de incidencia no válida.";
                }

                if (!preg_match('/^\d+(\.\d{1,2})?$/', $datos['importe_reembolsar']) || !is_numeric($datos['importe_reembolsar'])) {
                    $errores['importe_reembolsar'] = "Formato inválido. Use hasta 2 decimales (ej: 25.50).";
                } else {
                    $importe = floatval($datos['importe_reembolsar']);
                    if ($importe < 5.00 || $importe > 200.00) {
                        $errores['importe_reembolsar'] = "El importe debe estar entre 5.00€ y 200.00€.";
                    }
                    $datos['importe_reembolsar'] = number_format($importe, 2, '.', '');
                }

                if (!preg_match('/^\d+(\.\d)?$/', $datos['dias_resolucion']) || !is_numeric($datos['dias_resolucion'])) {
                    $errores['dias_resolucion'] = "Formato inválido. Máximo 1 decimal (ej: 2.0).";
                } else {
                    $dias = floatval($datos['dias_resolucion']);
                    if ($dias < 0.5 || $dias > 10.0) {
                        $errores['dias_resolucion'] = "Debe estar entre 0.5 y 10.0 días.";
                    }
                    $datos['dias_resolucion'] = number_format($dias, 1, '.', '');
                }

                if (empty($datos['descripcion_incidencia'])) {
                    $errores['descripcion_incidencia'] = "La descripción no puede estar vacía.";
                } elseif (strlen($datos['descripcion_incidencia']) > 400) {
                    $errores['descripcion_incidencia'] = "La descripción no puede exceder los 400 caracteres.";
                }
                
                if (empty($errores)) {
                    $datos['descripcion_incidencia_formato'] = ucwords(strtolower($datos['descripcion_incidencia']));
                    $mostrar_resumen = true;
                }
            }
        }

        //Funcion para calcular la fecha de la resolucion
        function calcular_fecha_resolucion($dias) {
            $fecha = new DateTime();
            $fecha->modify('+' . ceil($dias) . ' weekdays');
            $fecha->setTime(9, 0); // resolución a primera hora
            return $fecha;
        }
        ?>

        <div class="gestion-reembolsos">
            <h2>Gestión de Solicitudes de Reembolso</h2>

            <?php if ($mostrar_resumen): ?>
                <div class="resumen">
                    <p><strong>Socio:</strong> <?= htmlspecialchars($datos['nombre_socio']); ?> (<?= htmlspecialchars($datos['numero_membresia']); ?>)</p>
                    <p><strong>Actividad afectada:</strong> <?= htmlspecialchars($datos['fecha_actividad']); ?> a las <?= htmlspecialchars($datos['hora_inicio']); ?></p>
                    <p><strong>Tipo de incidencia:</strong> <?= htmlspecialchars($datos['tipo_incidencia']); ?></p>
                    <p><strong>Importe del reembolso:</strong> <?= htmlspecialchars($datos['importe_reembolsar']); ?> €</p>

                    <?php $fecha_resolucion = calcular_fecha_resolucion(floatval($datos['dias_resolucion'])); ?>
                    <p><strong>Fecha límite de resolución:</strong> <?= $fecha_resolucion->format('d/m/Y'); ?> - 9:00<br>
                        (Se resolverá a primera hora del día límite)</p>

                    <p><strong>Descripción:</strong> <?= htmlspecialchars($datos['descripcion_incidencia_formato']); ?></p>
                </div>

            <?php else: ?>
                <?php if (!empty($errores)): ?>
                    <div class="error-box">
                        <strong>Se han encontrado errores.</strong> Por favor, corrige los campos marcados.
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="nombre_socio">Nombre del socio:</label>
                    <input type="text" id="nombre_socio" name="nombre_socio" maxlength="150"
                        class="<?= isset($errores['nombre_socio']) ? 'campo-invalido' : ''; ?>"
                        value="<?= htmlspecialchars($datos['nombre_socio']); ?>">
                    <?php if (isset($errores['nombre_socio'])): ?><p class="error-msg"><?= $errores['nombre_socio']; ?></p><?php endif; ?>

                    <label for="numero_membresia">Número de membresía (Ej: SOC123456):</label>
                    <input type="text" id="numero_membresia" name="numero_membresia"
                        class="<?= isset($errores['numero_membresia']) ? 'campo-invalido' : ''; ?>"
                        value="<?= htmlspecialchars($datos['numero_membresia']); ?>">
                    <?php if (isset($errores['numero_membresia'])): ?><p class="error-msg"><?= $errores['numero_membresia']; ?></p><?php endif; ?>

                    <label for="fecha_actividad">Fecha de la actividad afectada (YYYY-MM-DD):</label>
                    <input type="date" id="fecha_actividad" name="fecha_actividad"
                        class="<?= isset($errores['fecha_actividad']) ? 'campo-invalido' : ''; ?>"
                        value="<?= htmlspecialchars($datos['fecha_actividad']); ?>">
                    <?php if (isset($errores['fecha_actividad'])): ?><p class="error-msg"><?= $errores['fecha_actividad']; ?></p><?php endif; ?>

                    <label for="hora_inicio">Hora de inicio de la actividad (HH:MM):</label>
                    <input type="text" id="hora_inicio" name="hora_inicio" placeholder="Ej: 10:30"
                        class="<?= isset($errores['hora_inicio']) ? 'campo-invalido' : ''; ?>"
                        value="<?= htmlspecialchars($datos['hora_inicio']); ?>">
                    <?php if (isset($errores['hora_inicio'])): ?><p class="error-msg"><?= $errores['hora_inicio']; ?></p><?php endif; ?>

                    <label for="tipo_incidencia">Tipo de incidencia:</label>
                    <select id="tipo_incidencia" name="tipo_incidencia"
                            class="<?= isset($errores['tipo_incidencia']) ? 'campo-invalido' : ''; ?>">
                                <?php foreach ($opciones_incidencia as $valor => $etiqueta): ?>
                            <option value="<?= htmlspecialchars($valor); ?>" <?= $datos['tipo_incidencia'] === $valor ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($etiqueta); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['tipo_incidencia'])): ?><p class="error-msg"><?= $errores['tipo_incidencia']; ?></p><?php endif; ?>

                    <label for="importe_reembolsar">Importe a reembolsar (€ 5.00 - 200.00):</label>
                    <input type="text" id="importe_reembolsar" name="importe_reembolsar" placeholder="Ej: 25.50"
                        class="<?= isset($errores['importe_reembolsar']) ? 'campo-invalido' : ''; ?>"
                        value="<?= htmlspecialchars($datos['importe_reembolsar']); ?>">
                    <?php if (isset($errores['importe_reembolsar'])): ?><p class="error-msg"><?= $errores['importe_reembolsar']; ?></p><?php endif; ?>

                    <label for="dias_resolucion">Días laborables para resolución (0.5 - 10.0):</label>
                    <input type="text" id="dias_resolucion" name="dias_resolucion" placeholder="Ej: 2.0"
                        class="<?= isset($errores['dias_resolucion']) ? 'campo-invalido' : ''; ?>"
                        value="<?= htmlspecialchars($datos['dias_resolucion']); ?>">
                    <?php if (isset($errores['dias_resolucion'])): ?><p class="error-msg"><?= $errores['dias_resolucion']; ?></p><?php endif; ?>

                    <label for="descripcion_incidencia">Descripción de la incidencia:</label>
                    <textarea id="descripcion_incidencia" name="descripcion_incidencia" rows="5" maxlength="400"
                            class="<?= isset($errores['descripcion_incidencia']) ? 'campo-invalido' : ''; ?>"><?= htmlspecialchars($datos['descripcion_incidencia']); ?></textarea>
                    <?php if (isset($errores['descripcion_incidencia'])): ?><p class="error-msg"><?= $errores['descripcion_incidencia']; ?></p><?php endif; ?>

                    <button type="submit" class="boton-submit">Enviar Solicitud</button>
                </form>
            <?php endif; ?>
        </div>

    </body>
</html>

