var numeroSecreto;

$(document).ready(function() {
    iniciarJuego();
});

function iniciarJuego() {
    reiniciarJuego();

    $("#enviarSuposicion").on("click", function() {
        verificarSuposicion();
    });

    $('#reiniciarJuego').on('click', function(){
        reiniciarJuego();
    });

    $('#suposicion').on('blur', function(){
        comprobarEntrada();
    });

    $('#suposicion').on('input', function(){
        comprobarEntrada();
    });
}

function verificarSuposicion(){
    if(!comprobarEntrada()){
        mostrarResultado("Introduce un número entre 1 y 100", "fallo");
        return;
    }

    var suposicion = parseInt($("#suposicion").val());

    if (suposicion === numeroSecreto) {
        mostrarResultado("¡Número adivinado!!!", "exito");
        desactivarBotonEnviar();
    } else if (suposicion < numeroSecreto) {
        mostrarResultado("Un poco más alto", "pista");
    } else {
        mostrarResultado("Un poco más bajo", "pista");
    }
}

function mostrarResultado(mensaje, claseResultado){
    var $resultado = $("#resultado"); 

    $resultado.hide();
    $resultado.removeClass().addClass(claseResultado);
    $resultado.text(mensaje);
    $resultado.fadeIn(500); 
}

function generarNumeroAdivinar() {
    return Math.floor(Math.random() * 100) + 1;
}

function comprobarEntrada() {
    var valor = $("#suposicion").val();
    
    if (valor === "" || isNaN(valor) || valor < 1 || valor > 100) {
        $("#enviarSuposicion").attr("disabled", true);
        return false;
    } else {
        $("#enviarSuposicion").attr("disabled", false);
        return true;
    }
}

function desactivarBotonEnviar() {
    $("#enviarSuposicion").attr("disabled", true);
}

function reiniciarJuego() {
    numeroSecreto = generarNumeroAdivinar();
    $("#suposicion").val(""); 
    $("#enviarSuposicion").attr("disabled", false); 
    $("#resultado").empty().hide(); 
    
    console.log("Nuevo juego. Secreto: " + numeroSecreto);
}
