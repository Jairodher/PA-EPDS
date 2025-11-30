function mostrarActividadesHTML() {
    let div = document.getElementById("listaActividades");
    div.innerHTML = "";

    actividades.forEach(a => {
        div.innerHTML += `<p class="actividad"><strong>${a.nombre}</strong>: ${a.instructor} - ${a.plazas} plazas</p>`;
    });
}

function reservarPlaza(nombre) {
    let act = actividades.find(a => a.nombre.toLowerCase() === nombre.toLowerCase());
    if (!act) return "❌ La actividad no existe.";
    if (act.plazas <= 0) return "❌ No quedan plazas disponibles.";
    act.plazas--;
    return `✔ Plaza reservada en ${act.nombre}.`;
}

function liberarPlaza(nombre) {
    let act = actividades.find(a => a.nombre.toLowerCase() === nombre.toLowerCase());
    if (!act) return "❌ La actividad no existe.";
    act.plazas++;
    return `✔ Plaza liberada en ${act.nombre}.`;
}

function agregarActividad(nombre, instructor, plazas) {
    if (!nombre || !instructor || isNaN(plazas)) {
        return "❌ Datos inválidos.";
    }
    actividades.push({ nombre, instructor, plazas: Number(plazas) });
    return `✔ Actividad ${nombre} agregada con éxito.`;
}
