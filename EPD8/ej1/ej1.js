// Lectura de las puntuaciones
var numScores = parseInt(prompt("¿Cuántas puntuaciones desea introducir?"));

if (numScores <= 0 || isNaN(numScores)) {
    document.write("ERROR: Número de puntuaciones inválido.");
} else {
    document.write("<h1>Estadisticas y puntuaciones</h1>");
    var scores = [];
    var puntuacion;
    var sumatorio = 0;

    document.write("<h2>Puntuaciones</h2>")
    for (var i = 0; i < numScores; i++) {
        var input = prompt("Introduzca la puntuación " + (i + 1) + ":");
        puntuacion = parseFloat(input);

        if (isNaN(puntuacion)) {
            document.write("ERROR:Entrada no válida, se contará como 0.<br>");
            puntuacion = 0;
        }

        switch (true) {
            case (puntuacion > 8):
                document.write("Puntuación "+ (i+1) + ": "+ puntuacion + " : Nivel: Excelente");
                break;
            case (puntuacion > 6 && puntuacion <= 8):
                document.write("Puntuación "+ (i+1) + ": " + puntuacion + " : Nivel: Bueno");
                break;
            case (puntuacion > 4 && puntuacion <= 6):
                document.write("Puntuación " + (i+1) + ": "+ puntuacion + " : Nivel : Regular");
                break;
            case (puntuacion <= 4):
                document.write("Puntuación "+ (i+1) + ": " + puntuacion + " : Nivel : Deficiente");
                break;
            default:
                document.write("Puntuación "+ (i+1) + ": " + puntuacion + " : Nivel : Inválida");
                break;
        }

        scores.push(puntuacion);
        sumatorio += puntuacion;
        document.write("<br>");
    }

    // Cálculo de estadísticas
    var min = Math.min(...scores);
    var max = Math.max(...scores);
    var media = sumatorio / numScores;

    document.write("<h2>Estadisticas</h2>");
    document.write("Puntuación Máxima: " + max + "<br>");
    document.write("Puntuación Mínima: " + min + "<br>" );
    document.write("Puntuación Media: " + media.toFixed(2) + "<br>");
}


