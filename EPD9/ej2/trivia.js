const preguntas = [
    ["Programación", "¿Cuál es el resultado de '10' + 1 en JS?", ["11", "101", "Error", "NaN"], 1],
    ["Programación", "¿Cómo declaramos la sección main en Java?", ["main", "def main", "int main(void)", "public static void main(String[] args)"], 3],
    ["Programación", "¿Cuál es el tipo de dato usado para los números decimales en C?", ["float", "int", "char", "struct"], 0],
    ["Matemáticas", "¿Cuál es la raíz cuadrada de 64?", ["4", "8", "16", "2"], 1],
    ["Matemáticas", "¿Cuál es la derivada de 2x?", ["2", "x", "4", "2x^2"], 0],
    ["Matemáticas", "¿Cuánto es 2^0?", ["2", "1", "0", "20"], 1],
    ["Historia", "¿En qué año inició la Guerra Civil Española?", ["1936", "1939", "1975", "1931"], 0],
    ["Historia", "¿En qué año llegó Cristobal Colón a América?", ["1482", "1492", "2025", "1490"], 1],
    ["Historia", "¿Quié fue el primer emperador romano?", ["Julio Cesar", "Augusto", "Paulus Motus", "Nerón"], 1],
    ["Ciencias", "¿Cuál es la forumlación química del agua?", ["HO", "H2O", "H2O2", "HO2"], 1],
    ["Ciencias", "¿Cuál es el planeta más grande?", ["La Tierra", "Marte", "Júpiter", "Neptuno"], 2],
    ["Ciencias", "¿Cúantos huesos tienen un humano adulto?", ["206", "300", "250", "215"], 0],
    ["Cultura General Universitaria", "¿Quién escribió 'El Quijote'?", ["Lope de Vega", "Góngora", "Quevedo", "Cervantes"], 3],
    ["Cultura General Universitaria", "¿Cuál es la capital de Estados Unidos?", ["Nueva York", "Ohio", "Michigan", "Washington DC"], 3]
    ["Cultura General Universitaria", "¿Quién pintó 'Las Meninas'?", ["Velazquez", "Modigliani", "Picasso", "Dalí"], 0]
];

let preguntasJuego = [];
let indice = 0;
let puntuacion = 0;
let cronoVisual = null;
let cronoFin = null;

window.onload = function() {
    document.getElementById("btnStart").onclick = iniciarJuego;
    document.getElementById("btnRestart").onclick = iniciarJuego;

    for (let i = 0; i < 4; i++) {
        let btn = document.getElementById("btn" + i);
        if (btn) {
            btn.onclick = function() { responderPregunta(i); };
        } else {
            console.error("ERROR: No encuentro el botón con id: btn" + i);
        }
    }
};

function iniciarJuego() {
    puntuacion = 0;
    indice = 0;
    preguntasJuego = [];
    
    const categorias = ["Programación", "Matemáticas", "Historia", "Ciencias", "Cultura General Universitaria"];
    for(let i = 0; i < categorias.length; i++){
        let categoriaActual = categorias[i];
        let preguntasDisponibles = preguntas.filter(function(pregunta){
            return pregunta[0] === categoriaActual;
        });
        if(preguntasDisponibles.length > 0){
            let preguntaAleatoria = Math.floor(Math.random() * preguntasDisponibles.length);
            preguntasJuego.push(preguntasDisponibles[preguntaAleatoria]);
        }
    }
    preguntasJuego.sort(function(){
        return Math.random() - 0.5;
    });
    
    document.getElementById("inicio").style.display = "none";
    document.getElementById("final").style.display = "none";
    document.getElementById("juego").style.display = "block";
    
    mostrarPregunta();
}

function mostrarPregunta() {
    if(indice >= preguntasJuego.length){
        finalizarJuego();
        return;
    }
    
    let datos = preguntasJuego[indice]; 

    document.getElementById("categoria").innerText = datos[0];
    document.getElementById("pregunta").innerText = datos[1];
    document.getElementById("puntos").innerText = puntuacion;
    document.getElementById("mensaje").innerText = "";

    document.getElementById("num-pregunta").innerText = (indice + 1) + "/" + preguntasJuego.length;
    document.getElementById("barra-progreso").value = indice + 1;
    document.getElementById("barra-progreso").max = preguntasJuego.length;

    for(let i = 0; i < 4; i++){
        let btn = document.getElementById("btn" + i);
        btn.innerText = datos[2][i];
        btn.disabled = false;
        btn.style.background = "";
    }
    
    iniciarTemporizador();
}

function iniciarTemporizador() {
    let tiempoRestante = 30;
    document.getElementById("tiempo").innerText = tiempoRestante;

    clearInterval(cronoVisual);
    clearTimeout(cronoFin);

    cronoVisual = setInterval(function() {
        tiempoRestante--;
        let spanTiempo = document.getElementById("tiempo");
        if(spanTiempo) spanTiempo.innerText = tiempoRestante;

        if (tiempoRestante <= 0) {
            clearInterval(cronoVisual);
        }
    }, 1000);

    cronoFin = setTimeout(function() {
        responderPregunta(-1);
    }, 30000);
}

function responderPregunta(opcionSeleccionada) {
    clearInterval(cronoVisual);
    clearTimeout(cronoFin);

    let indiceCorrecta = preguntasJuego[indice][3];
    let mensaje = document.getElementById("mensaje");
    let textoCorrecta = preguntasJuego[indice][2][indiceCorrecta];

    if(opcionSeleccionada === indiceCorrecta) {
        puntuacion += 10;
        mensaje.innerText = "¡CORRECTO! +10 puntos";
        mensaje.style.color = "green";
        document.getElementById("btn" + opcionSeleccionada).style.backgroundColor = "lightgreen";
    } else if(opcionSeleccionada === -1){
        mensaje.innerText = "¡TIEMPO AGOTADO! La respuesta era: " + textoCorrecta;
        mensaje.style.color = "orange";
    }
    else {
        puntuacion -= 2;
        mensaje.innerText = "Incorrecto. La respuesta era: " + textoCorrecta + ". -2 puntos";
        mensaje.style.color = "red";
        document.getElementById("btn" + opcionSeleccionada).style.backgroundColor = "salmon";
    }

    for(let i = 0; i < 4; i++){
        document.getElementById("btn" + i).disabled = true;
    }
    
    indice++;
    setTimeout(mostrarPregunta, 1000);
}

function finalizarJuego() {
    document.getElementById("inicio").style.display = "none";
    document.getElementById("juego").style.display = "none";
    document.getElementById("final").style.display = "block";
    
    document.getElementById("puntosFinal").innerText = puntuacion;
}
