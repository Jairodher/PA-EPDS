function comprobarFormato(campo, formato) {
    return formato.test(campo);
}

function comprobarNombre(valor) {
    return comprobarFormato(valor, /^[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,}$/);
}

function comprobarEmail(valor) {
    return comprobarFormato(valor, /^[^\s@]+@[^\s@]+\.[^\s@]+$/);
}

function comprobarTelefono(valor) {
    return comprobarFormato(valor, /^\d{9}$/);
}

function comprobarCampo(valor) {
    return valor !== "";
}

function actualizarEstadoFormulario() {
    let puntos = 0;
    
    //Valor de los campos
    const nombreVal = document.getElementById('nombre').value;
    const emailVal = document.getElementById('correo').value;
    const tlfVal = document.getElementById('tlf').value;
    const claseVal = document.getElementById('clases').value;
    
    const horarios = document.getElementsByName('horario');
    let horarioSeleccionado = false;
    for (let h of horarios) {
        if (h.checked) horarioSeleccionado = true;
    }
    
    //Compruebo sus valores
    if (comprobarNombre(nombreVal)) puntos += 20;
    if (comprobarEmail(emailVal)) puntos += 20;
    if (comprobarTelefono(tlfVal)) puntos += 20;
    if (comprobarCampo(claseVal)) puntos += 20;
    if (horarioSeleccionado) puntos += 20;

    //Actualizar la Barra y el Texto
    const barra = document.getElementById('barraProgreso');
    const textoPuntos = document.getElementById('puntuacion');
    const botonEnviar = document.getElementById('enviar');

    if (barra && textoPuntos) {
        barra.value = puntos;
        textoPuntos.innerText = puntos;
    }

    //Habilitar solo si llega a 100
    if (puntos === 100) {
        botonEnviar.disabled = false; //Habilitar el boton
        botonEnviar.style.cursor = "pointer";//Cambiar el cursor para indicar que se puede pulsar
    } else {
        botonEnviar.disabled = true;// Deshabilitar el botón
        botonEnviar.style.cursor = "not-allowed";
    }
}

document.addEventListener('DOMContentLoaded', function () {
    //Elementos input
    const nombre = document.getElementById('nombre');
    const email = document.getElementById('correo');
    const telefono = document.getElementById('tlf');
    const clase = document.getElementById('clases');
    const horarios = document.getElementsByName('horario');

    //EVENTOS
    //Input
    nombre.addEventListener('input', actualizarEstadoFormulario);
    email.addEventListener('input', actualizarEstadoFormulario);
    telefono.addEventListener('input', actualizarEstadoFormulario);

    //Change
    clase.addEventListener('change', actualizarEstadoFormulario);
    
    horarios.forEach(function(radio) {
        radio.addEventListener('change', actualizarEstadoFormulario);
    });
    
    //Validar el estado al cargar la pagina
    actualizarEstadoFormulario();
});
