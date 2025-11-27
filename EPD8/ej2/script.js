function horariosPadel() {
    const TARIFA_HORA = 30;
    const HORA_APERTURA = 8;
    const HORA_CIERRE = 22;
    const DURACION_MIN = 60;
    const DURACION_MAX = 180;

    let salida = "";

    let horaInicio = prompt("Introduzca la hora de inicio de la reserva");
    if (horaInicio === null) {
        return;
    }

    salida += "Hora de inicio: " + horaInicio + "\n";

    const minInicio = procesarHora(horaInicio);
    if (minInicio === null) {
        salida += "ERROR: HORARIO INCORRECTO";
        alert(salida);
        return;
    }

    let horaFin = prompt("Introduzca la hora de finalizacion de la reserva");
    if (horaFin === null) {
        return;
    }

    salida += "Hora de fin: " + horaFin + "\n";

    const minFin = procesarHora(horaFin);

    if (minFin === null) {
        salida += "ERROR: HORARIO INCORRECTO";
        alert(salida);
        return;
    }

    const minApertura = HORA_APERTURA * 60;
    const minCierre = HORA_CIERRE * 60;

    if (minInicio < minApertura || minFin > minCierre) {
        salida += "ERROR: Las pistas están disponibles de " + HORA_APERTURA + ":00 a " + HORA_CIERRE + ":00.";
        alert(salida);
        return;
    }

    if (minFin <= minInicio) {
        salida += ("ERROR: La hora de fin debe ser posterior a la de inicio");
        alert(salida);
        return;
    }

    let duracion = minFin - minInicio;

    if (duracion < DURACION_MIN) {
        salida += "ERROR: La duracion minima de la reserva es de 1 hora.";
        alert(salida);
        return;
    }

    if (duracion > DURACION_MAX) {
        salida += "ERROR: La duración máxima permitida es de 3 horas.";
        alert(salida);
        return;
    }

    let horasDuracion = Math.floor(duracion / 60);
    let minutosDuracion = duracion % 60;

    let mensaje = horasDuracion + " hora";
    if (horasDuracion > 1 || horasDuracion < 1) {
        mensaje += "s";
    }
    if (minutosDuracion > 0) {
        mensaje += " y " + minutosDuracion + " minutos";
    }

    let precio = (duracion / 60) * TARIFA_HORA;

    salida += "Duracion de la sesion: " + mensaje + "\n";
    salida += "Tarifa: " + precio.toFixed(2) + "€ (" + TARIFA_HORA + "€/hora)";

    alert(salida);
}

function procesarHora(hora) {
    const expReg = /^([0-1][0-9]|2[0-3]):([0-5][0-9])$/;
    if (!expReg.test(hora)) {
        return null;
    }

    const partes = hora.split(':');
    const horas = parseInt(partes[0], 10);
    const minutos = parseInt(partes[1], 10);

    return (horas * 60) + minutos;
}
