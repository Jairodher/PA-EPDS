<?php
function procesarRegistrosActividades($cadenaEntrada) {
    // --- 1. Separar líneas ---
    $lineas = explode("\n", trim($cadenaEntrada));

    $socios = []; // array para agrupar por socio
    $actividadesGlobales = [];
    $instructoresGlobales = [];
    $totalMinutosGlobal = 0;
    $totalSesionesGlobal = 0;
    $todasLasFechas = [];

    // --- 2. Procesar cada línea ---
    foreach ($lineas as $linea) {
        $partes = explode("@@", trim($linea));
        if (count($partes) < 6) continue;

        list($id, $actividad, $duracion, $fecha, $instructor, $pista) = $partes;

        // Normalizar formatos
        $fechaObj = date_create_from_format("d/m/Y", $fecha);
        $fechaISO = $fechaObj ? date_format($fechaObj, "Y-m-d") : null;
        $duracion = intval($duracion);

        // Guardar fechas globales
        if ($fechaISO) $todasLasFechas[] = $fechaISO;

        // Inicializar socio si no existe
        if (!isset($socios[$id])) {
            $socios[$id] = [
                "total_minutos" => 0,
                "actividades" => [],
                "instructores" => [],
                "fechas" => [],
                "num_actividades" => 0
            ];
        }

        // Acumular datos del socio
        $socios[$id]["total_minutos"] += $duracion;
        $socios[$id]["num_actividades"] += 1;
        $socios[$id]["actividades"][$actividad] = ($socios[$id]["actividades"][$actividad] ?? 0) + 1;
        $socios[$id]["instructores"][$instructor] = ($socios[$id]["instructores"][$instructor] ?? 0) + 1;
        $socios[$id]["fechas"][] = $fechaISO;

        // Acumular globales
        $actividadesGlobales[$actividad] = ($actividadesGlobales[$actividad] ?? 0) + 1;
        $instructoresGlobales[$instructor] = ($instructoresGlobales[$instructor] ?? 0) + 1;
        $totalMinutosGlobal += $duracion;
        $totalSesionesGlobal++;
    }

    // --- 3. Calcular estadísticas por socio ---
    $tablaHTML = "<table border='1' cellspacing='0' cellpadding='5'>
        <tr style='background-color:#ddd;'>
            <th>ID Socio</th>
            <th>Total Minutos</th>
            <th>Nº Actividades</th>
            <th>Actividad Favorita</th>
            <th>Período (Primera - Última)</th>
            <th>Días Activos</th>
            <th>Instructor Principal</th>
        </tr>";

    $socioMasActivo = ["id" => null, "minutos" => 0];

    foreach ($socios as $id => &$data) {
        sort($data["fechas"]);
        $primera = reset($data["fechas"]);
        $ultima = end($data["fechas"]);

        // Calcular días entre fechas
        $f1 = date_create($primera);
        $f2 = date_create($ultima);
        $diff = date_diff($f1, $f2);
        $dias = intval(date_interval_format($diff, "%a"));

        // Actividad favorita
        arsort($data["actividades"]);
        $actividadFavorita = array_key_first($data["actividades"]);

        // Instructor principal
        arsort($data["instructores"]);
        $instructorPrincipal = array_key_first($data["instructores"]);

        // Actualizar socio más activo
        if ($data["total_minutos"] > $socioMasActivo["minutos"]) {
            $socioMasActivo = ["id" => $id, "minutos" => $data["total_minutos"]];
        }

        // Construir fila HTML
        $tablaHTML .= "<tr>
            <td>{$id}</td>
            <td>{$data["total_minutos"]}</td>
            <td>{$data["num_actividades"]}</td>
            <td>{$actividadFavorita}</td>
            <td>" . date("d/m/Y", strtotime($primera)) . " - " . date("d/m/Y", strtotime($ultima)) . "</td>
            <td>{$dias}</td>
            <td>{$instructorPrincipal}</td>
        </tr>";
    }

    $tablaHTML .= "</table>";

    // --- 4. Estadísticas globales ---
    arsort($actividadesGlobales);
    arsort($instructoresGlobales);

    $actividadPopular = array_key_first($actividadesGlobales);
    $instructorPopular = array_key_first($instructoresGlobales);
    $promedio = round($totalMinutosGlobal / $totalSesionesGlobal, 2);

    sort($todasLasFechas);
    $fechaMin = reset($todasLasFechas);
    $fechaMax = end($todasLasFechas);

    // --- 5. Generar resumen final ---
    $resumenHTML = "
        <h3>Estadísticas Globales</h3>
        <ul>
            <li><strong>Socio más activo:</strong> {$socioMasActivo["id"]} ({$socioMasActivo["minutos"]} minutos)</li>
            <li><strong>Actividad más popular:</strong> {$actividadPopular} ({$actividadesGlobales[$actividadPopular]} sesiones)</li>
            <li><strong>Instructor más solicitado:</strong> {$instructorPopular} ({$instructoresGlobales[$instructorPopular]} sesiones)</li>
            <li><strong>Promedio por sesión:</strong> {$promedio} minutos</li>
        </ul>
        <h3>Características del conjunto de datos</h3>
        <ul>
            <li><strong>Total de registros:</strong> {$totalSesionesGlobal}</li>
            <li><strong>Total de socios:</strong> " . count($socios) . "</li>
            <li><strong>Rango de fechas:</strong> " . date("d/m/Y", strtotime($fechaMin)) . " - " . date("d/m/Y", strtotime($fechaMax)) . "</li>
        </ul>
    ";

    // --- 6. Salida final ---
    return "<h2>Resumen por socio</h2>" . $tablaHTML . $resumenHTML;
}

// --- DEMO DE PRUEBA ---
$cadenaEntrada = <<<EOD
SOC001@@Spinning@@45@@15/01/2025@@Ana López@@Sala1
SOC001@@Natación@@30@@16/01/2025@@Miguel Torres@@Piscina1
SOC002@@Spinning@@45@@15/01/2025@@Ana López@@Sala1
SOC001@@Yoga@@60@@17/01/2025@@Carmen Ruiz@@Sala2
SOC003@@Natación@@30@@16/01/2025@@Miguel Torres@@Piscina1
SOC002@@Tenis@@90@@18/01/2025@@David García@@Pista1
SOC001@@Spinning@@45@@19/01/2025@@Ana López@@Sala1
SOC004@@Pilates@@50@@15/01/2025@@Laura Martín@@Sala3
SOC003@@Spinning@@45@@17/01/2025@@Ana López@@Sala1
SOC005@@Natación@@40@@16/01/2025@@Miguel Torres@@Piscina1
SOC004@@Yoga@@60@@18/01/2025@@Carmen Ruiz@@Sala2
SOC002@@Natación@@35@@20/01/2025@@Miguel Torres@@Piscina1
SOC006@@Tenis@@120@@17/01/2025@@David García@@Pista1
SOC001@@Pilates@@50@@21/01/2025@@Laura Martín@@Sala3
SOC007@@Spinning@@45@@18/01/2025@@Ana López@@Sala1
SOC003@@Yoga@@60@@20/01/2025@@Carmen Ruiz@@Sala2
SOC005@@Tenis@@90@@19/01/2025@@David García@@Pista1
SOC008@@Natación@@25@@17/01/2025@@Miguel Torres@@Piscina1
SOC006@@Spinning@@45@@21/01/2025@@Ana López@@Sala1
SOC004@@Natación@@40@@22/01/2025@@Miguel Torres@@Piscina1
SOC007@@Pilates@@55@@20/01/2025@@Laura Martín@@Sala3
SOC002@@Yoga@@60@@22/01/2025@@Carmen Ruiz@@Sala2
SOC009@@Spinning@@45@@19/01/2025@@Ana López@@Sala1
SOC008@@Tenis@@75@@21/01/2025@@David García@@Pista1
SOC005@@Pilates@@50@@23/01/2025@@Laura Martín@@Sala3
SOC010@@Natación@@30@@20/01/2025@@Miguel Torres@@Piscina1
SOC003@@Spinning@@45@@24/01/2025@@Ana López@@Sala1
SOC006@@Yoga@@60@@23/01/2025@@Carmen Ruiz@@Sala2
SOC009@@Natación@@35@@22/01/2025@@Miguel Torres@@Piscina1
SOC001@@Tenis@@105@@25/01/2025@@David García@@Pista1
SOC007@@Spinning@@45@@24/01/2025@@Ana López@@Sala1
SOC010@@Pilates@@50@@22/01/2025@@Laura Martín@@Sala3
SOC004@@Spinning@@45@@25/01/2025@@Ana López@@Sala1
SOC008@@Yoga@@60@@25/01/2025@@Carmen Ruiz@@Sala2
SOC002@@Spinning@@45@@26/01/2025@@Ana López@@Sala1
SOC005@@Natación@@40@@26/01/2025@@Miguel Torres@@Piscina1
SOC011@@Tenis@@80@@23/01/2025@@David García@@Pista1
SOC006@@Pilates@@55@@26/01/2025@@Laura Martín@@Sala3
SOC012@@Spinning@@45@@24/01/2025@@Ana López@@Sala1
SOC003@@Natación@@30@@27/01/2025@@Miguel Torres@@Piscina1
SOC009@@Yoga@@60@@26/01/2025@@Carmen Ruiz@@Sala2
SOC011@@Spinning@@45@@27/01/2025@@Ana López@@Sala1
SOC007@@Natación@@40@@28/01/2025@@Miguel Torres@@Piscina1
SOC010@@Tenis@@95@@27/01/2025@@David García@@Pista1
SOC012@@Pilates@@50@@28/01/2025@@Laura Martín@@Sala3
SOC001@@Spinning@@45@@29/01/2025@@Ana López@@Sala1
SOC008@@Natación@@35@@28/01/2025@@Miguel Torres@@Piscina1
SOC004@@Yoga@@60@@29/01/2025@@Carmen Ruiz@@Sala2
SOC005@@Spinning@@45@@30/01/2025@@Ana López@@Sala1
SOC011@@Pilates@@55@@30/01/2025@@Laura Martín@@Sala3
SOC006@@Natación@@40@@30/01/2025@@Miguel Torres@@Piscina1
EOD;

echo procesarRegistrosActividades($cadenaEntrada);
?>
