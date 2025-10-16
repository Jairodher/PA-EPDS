<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Project/PHP/PHPProject.php to edit this template
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Horarios Asignados</title>
    </head>
    <body>
        <h1>Horarios Asignados Aleatoriamente a los Empleados</h1>
        <?php
        $empleados = array(
            'Agustin Andrades' => 'Entrenador Funcional',
            'Andres Sanchez' => 'Entrenador de Futbol',
            'Pedro Gomez' => 'Entrenador Personal',
            'Julia Estevez' => 'Monitora de Zumba',
            'Laura Traverso' => 'Nutricionista',
            'Lucia Asencio' => 'Fisioterapeuta'
        );
        
        $turnos = array(
            'Mañana' => '07:30-14:30',
            'Tarde' => '14-30-23:30',
            'Noche' => '23:30-07:30'
        );
        
        $empleadosDeHoy = rand(3, count($empleados));
        echo "Hoy tienen que trabajar $empleadosDeHoy empleados.<br>";
        
        $nombres = array_keys($empleados);
        $empleadosQueTrabajaran = array_rand($nombres, $empleadosDeHoy);
        $turnoAsignado = array(
            'Mañana' => [],
            'Tarde' => [],
            'Noche' => []
        );
        foreach($empleadosQueTrabajaran as $i){
            $nombre = $nombres[$i];
            $especialidad = $empleados[$nombre];
            $turno = array_rand($turnos);
            $asignaciones[$turno][] = array(
                'nombre' => $nombre,
                'especialidad' => $especialidad
            );
        }
        
        echo "<table border='1'>";
        echo "<tr><th>Turno</th><th>Horario</th><th>Empleados Asignados</th></tr>";
        foreach($turnos as $nombreTurno => $horario){
            echo "<tr>";
            echo "<td>$nombreTurno</td>";
            echo "<td>$horario</td>";
            echo "<td>";
            if(empty($asignaciones[$nombreTurno])){
                echo "No hay empleados asignados";
            }else{
                foreach($asignaciones[$nombreTurno] as $empleado){
                    echo "{$empleado['nombre']} ({$empleado['especialidad']})<br>";
                }
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        ?>
    </body>
</html>
