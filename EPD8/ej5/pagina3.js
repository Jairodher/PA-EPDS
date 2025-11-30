function mostrarSeccion(seccion) {
    // Ocultar todas
    document.getElementById("seccionMostrar").style.display = "none";
    document.getElementById("seccionReservar").style.display = "none";
    document.getElementById("seccionLiberar").style.display = "none";
    document.getElementById("seccionAgregar").style.display = "none";

    if (seccion === "mostrar") {
        document.getElementById("seccionMostrar").style.display = "block";
        mostrarActividadesHTML();
    }

    if (seccion === "reservar") {
        document.getElementById("seccionReservar").style.display = "block";
    }

    if (seccion === "liberar") {
        document.getElementById("seccionLiberar").style.display = "block";
    }

    if (seccion === "agregar") {
        document.getElementById("seccionAgregar").style.display = "block";
    }
}

function accionReservar() {
    let nombre = document.getElementById("nombreReservar").value;
    document.getElementById("resultadoReservar").innerText = reservarPlaza(nombre);
    mostrarActividadesHTML();
}

function accionLiberar() {
    let nombre = document.getElementById("nombreLiberar").value;
    document.getElementById("resultadoLiberar").innerText = liberarPlaza(nombre);
    mostrarActividadesHTML();
}

function accionAgregar() {
    let n = document.getElementById("nuevoNombre").value;
    let i = document.getElementById("nuevoInstructor").value;
    let p = document.getElementById("nuevasPlazas").value;

    document.getElementById("resultadoAgregar").innerText =
        agregarActividad(n, i, p);

    mostrarActividadesHTML();
}
