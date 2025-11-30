function leerQR() {
    let url = prompt("Introduce la URL contenida en el QR:");

    if (!url) {
        alert("No se introdujo ninguna URL");
        return;
    }

    analizarQR(url);
}

function analizarQR(url) {
    const actividadesPermitidas = [
        "spinning", "yoga", "pilates", "zumba",
        "crossfit", "aqua", "bodycombat", "trx"
    ];

    let datos = {
        actividad: { valor: "", valido: false, mensaje: "" },
        duracion: { valor: "", valido: false, mensaje: "" },
        id: { valor: "", valido: false, mensaje: "" },
        coste: 0
    };

    try {
        let u = new URL(url);
        datos.actividad.valor = u.searchParams.get("actividad");
        datos.duracion.valor = parseInt(u.searchParams.get("duracion"));
        datos.id.valor = parseInt(u.searchParams.get("id_participante"));
    } catch (e) {
        alert("La URL no es válida.");
        return;
    }

    
    if (actividadesPermitidas.includes(datos.actividad.valor)) {
        datos.actividad.valido = true;
        datos.actividad.mensaje = "Correcta";
    } else {
        datos.actividad.mensaje = "Incorrecto - Actividad no reconocida";
    }

    
    if (!isNaN(datos.duracion.valor) && datos.duracion.valor >= 30 && datos.duracion.valor <= 120) {
        datos.duracion.valido = true;
        datos.duracion.mensaje = "Correcta";
    } else {
        datos.duracion.mensaje = "Incorrecto - Duración fuera de rango";
    }


    if (!isNaN(datos.id.valor) && datos.id.valor >= 1 && datos.id.valor <= 50) {
        datos.id.valido = true;
        datos.id.mensaje = "Correcta";
    } else {
        datos.id.mensaje = "Incorrecto - Identificador no válido";
    }

    let reservaValida = datos.actividad.valido && datos.duracion.valido && datos.id.valido;

    
    if (reservaValida) {
        let coste = 1.50; 

        if (datos.duracion.valor > 30) coste += 0.50;

        
        if (datos.id.valor <= 15) coste *= 0.90;

        datos.coste = coste.toFixed(2) + " €";
    } else {
        datos.coste = "RESERVA RECHAZADA";
    }

    mostrarTabla(datos, reservaValida);
}

function mostrarTabla(datos, reservaValida) {
    let html =
        "<table border='1' cellpadding='5' style='margin-top:20px;'>"+
        "<tr><th>Parámetro</th><th>Valor</th><th>Validación</th></tr>" +
        fila("Actividad", datos.actividad.valor, datos.actividad.mensaje) +
        fila("Duración", datos.duracion.valor, datos.duracion.mensaje) +
        fila("Identificador del participante", datos.id.valor, datos.id.mensaje) +
        `<tr>
            <td>Coste total</td>
            <td>${reservaValida ? datos.coste : "-"}</td>
            <td><b>${reservaValida ? "RESERVA REALIZADA" : "RESERVA RECHAZADA"}</b></td>
        </tr>` +
        "</table>";

    document.getElementById("resultado").innerHTML = html;
}

function fila(nombre, valor, validacion) {
    return `<tr>
                <td>${nombre}</td>
                <td>${valor}</td>
                <td>${validacion}</td>
            </tr>`;
}
