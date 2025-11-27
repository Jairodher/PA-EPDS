// Lectura de las puntuaciones
var numScores = parseInt(prompt("¿Cuántas puntuaciones desea introducir?"));

if (numScores <= 0 || isNaN(numScores)) {
    document.write("ERROR: Número de puntuaciones inválido.");
} else {
    document.write("<h1>Estadisticas y puntuaciones</h1>");
    var puntuaciones = [];
    var excelentes = [];
    var buenos = [];
    var regulares = [];
    var deficientes = [];
    var puntuacion;
    var sumatorio = 0;

    document.write("<h2>Puntuaciones</h2>")
    for (var i = 0; i < numScores; i++) {
        var input = prompt("Introduzca la puntuación " + (i + 1) + ":");
        puntuacion = parseFloat(input);

        if (isNaN(puntuacion)) {
            document.write("ERROR: Entrada no válida, se contará como 0.<br>");
            puntuacion = 0;
        }

        switch (true) {
            case (puntuacion > 8):
                document.write("Puntuación " + (i + 1) + ": " + puntuacion + " : Nivel: Excelente");
                excelentes.push(puntuacion);
                break;
            case (puntuacion > 6 && puntuacion <= 8):
                document.write("Puntuación " + (i + 1) + ": " + puntuacion + " : Nivel: Bueno");
                buenos.push(puntuacion);
                break;
            case (puntuacion > 4 && puntuacion <= 6):
                document.write("Puntuación " + (i + 1) + ": " + puntuacion + " : Nivel : Regular");
                regulares.push(puntuacion);
                break;
            case (puntuacion <= 4):
                document.write("Puntuación " + (i + 1) + ": " + puntuacion + " : Nivel : Deficiente");
                deficientes.push(puntuacion);
                break;
            default:
                document.write("Puntuación " + (i + 1) + ": " + puntuacion + " : Nivel : Inválida");
                break;
        }

        puntuaciones.push(puntuacion);
        sumatorio += puntuacion;
        document.write("<br>");
    }

    // Cálculo de estadísticas
    var min = Math.min(...puntuaciones);
    var max = Math.max(...puntuaciones);
    var media = sumatorio / numScores;

    document.write("<h2>Estadisticas</h2>");
    document.write("Puntuación Máxima: " + max + "<br>");
    document.write("Puntuación Mínima: " + min + "<br>");
    document.write("Puntuación Media: " + media.toFixed(2) + "<br>");

    //Distribucion de niveles
    var porcentaje = (puntuaciones, num) => (puntuaciones.length / num) * 100;

    document.write("<h2>Distribucion de niveles</h2>");
    document.write("- Excelente: " + excelentes.length + " socios" + "(" + porcentaje(excelentes, numScores).toFixed(2) + "%)<br>");
    document.write("- Bueno: " + buenos.length + " socios" + "(" + porcentaje(buenos, numScores).toFixed(2) + "%)<br>");
    document.write("- Regular: " + regulares.length + " socios" + "(" + porcentaje(regulares, numScores).toFixed(2) + "%)<br>");
    document.write("- Deficiente: " + deficientes.length + " socios" + "(" + porcentaje(deficientes, numScores).toFixed(2) + "%)<br>");
    
    document.write("<br>");
    
    //Ordenacion del vector
    document.write("Puntuaciones ordenadas de menor a mayor: " + puntuaciones.sort() + "<br>");
    document.write("Puntuaciones ordenadas de mayor a menor: " + puntuaciones.reverse());
}


