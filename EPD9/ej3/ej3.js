/* Variables globales del juego */
const SUMA_MAGICA = 15;
let celdaActiva;
let intervaloCronometro;
let segundos = 0;
let juegoTerminado = false;


const celdas = Array.from(document.querySelectorAll('#cuadrado-magico td'));
const celdasDOM = document.querySelectorAll('#cuadrado-magico td');

function mostrarMensaje(texto, tipo = 'info') {
    const mensajesDiv = document.getElementById('mensajes');
    mensajesDiv.innerHTML = texto;
    mensajesDiv.className = '';
    
    if (tipo === 'error') {
        mensajesDiv.classList.add('aviso-error');
    } else if (tipo === 'victoria') {
        mensajesDiv.classList.add('aviso-victoria');
    }
}
function iniciarCronometro() {
    segundos = 0;
    document.getElementById('cronometro').textContent = 'Tiempo: 0s';
    if (intervaloCronometro) clearInterval(intervaloCronometro);
    intervaloCronometro = setInterval(() => {
        segundos++;
        document.getElementById('cronometro').textContent = `Tiempo: ${segundos}s`;
    }, 1000);
}
function seleccionarCelda(event) {
    if (juegoTerminado) return;
    if (celdaActiva) {
        celdaActiva.classList.remove('celda-activa');
    }
    celdaActiva = event.target;
    celdaActiva.classList.add('celda-activa');
    mostrarMensaje(`Celda activa: ${celdaActiva.id}. Introduce un número (1-9).`);
}
function obtenerValoresCuadrado(conMatriz = false) {
    const valoresPlanos = [];
    celdasDOM.forEach(celda => {
        const valor = parseInt(celda.textContent.trim());
        valoresPlanos.push(isNaN(valor) ? null : valor);
    });

    if (conMatriz) {
        const matriz = [];
        for (let i = 0; i < 9; i += 3) {
            matriz.push(valoresPlanos.slice(i, i + 3));
        }
        return matriz;
    }
    return valoresPlanos;
}
function moverCeldaActiva(direccion) {
    if (!celdaActiva) return;
    const indiceActual = celdas.indexOf(celdaActiva);
    let nuevoIndice = indiceActual;
    while (true) {
        nuevoIndice += direccion;
        if (nuevoIndice < 0) nuevoIndice = celdas.length - 1;
        if (nuevoIndice >= celdas.length) nuevoIndice = 0;
        if (nuevoIndice === indiceActual || celdas[nuevoIndice].textContent.trim() === '') {
            break;
        }
    }

    if (nuevoIndice !== indiceActual) {
        celdaActiva.classList.remove('celda-activa');
        celdaActiva = celdas[nuevoIndice];
        celdaActiva.classList.add('celda-activa');
    }
}

function validarTecla(event) {
    if (juegoTerminado || !celdaActiva) {
        if (!celdaActiva) mostrarMensaje('¡ERROR! Haz clic en una celda para activarla.', 'error');
        event.preventDefault();
        return;
    }
    const key = event.key;
    const esCifra = key >= '1' && key <= '9';
    if (key === 'Escape') {
        reiniciarJuego();
        return;
    }

    if (esCifra) {
        const valor = parseInt(key);
        const valoresPlanos = obtenerValoresCuadrado();
        const numerosUsados = valoresPlanos.filter(n => n !== null && n !== parseInt(celdaActiva.textContent.trim()));
        if (numerosUsados.includes(valor)) {
            mostrarMensaje('¡ERROR! No se pueden repetir números. Cada cifra del 1 al 9 debe aparecer solo una vez.', 'error');
            event.preventDefault(); 
            return;
        }
        celdaActiva.textContent = valor;
        moverCeldaActiva(1);
    } else if (key === 'Backspace' || key === 'Delete') {
        celdaActiva.textContent = '';
        moverCeldaActiva(-1);
    } else {
        mostrarMensaje('¡ADVERTENCIA! Solo se aceptan números del 1 al 9.', 'error');
        event.preventDefault();
    }
}


function verificarCuadradoMagico() {
    if (juegoTerminado) return;
    
    const M = obtenerValoresCuadrado(true);
    const valoresPlanos = M.flat();
    const celdasVacias = valoresPlanos.filter(v => v === null).length;
    celdasDOM.forEach(celda => {
        celda.classList.remove('celda-vacia', 'celda-magica');
    });

    document.getElementById('celdas-restantes').textContent = `Celdas restantes: ${celdasVacias}`;

    if (celdasVacias > 0) {
        valoresPlanos.forEach((val, index) => {
            if (val === null) {
                celdasDOM[index].classList.add('celda-vacia');
            }
        });
        mostrarMensaje('El cuadrado está incompleto. Faltan ' + celdasVacias + ' celdas.','info');
        return; 
    }
    
    
    
    const sumas = [];
    
    
    M.forEach(fila => sumas.push(fila.reduce((a, b) => a + b, 0)));

    
    for (let j = 0; j < 3; j++) {
        sumas.push(M[0][j] + M[1][j] + M[2][j]);
    }

    
    sumas.push(M[0][0] + M[1][1] + M[2][2]); 
    sumas.push(M[0][2] + M[1][1] + M[2][0]);
    
    const esMagico = sumas.every(suma => suma === SUMA_MAGICA); 

    if (esMagico) {
        juegoTerminado = true;
        clearInterval(intervaloCronometro); 
        
        
        mostrarMensaje(`
            <span style="font-size:1.2em">¡FELICIDADES!</span>
            <br>Has resuelto el cuadrado mágico en **${segundos}** segundos.
            <br>Suma mágica: ${SUMA_MAGICA}. ¿Quieres intentar otro?
        `, 'victoria');
        
        
        celdasDOM.forEach(celda => celda.classList.add('celda-magica'));
        celdaActiva.classList.remove('celda-activa');
    } else {
        
        mostrarMensaje(`
            Este no es un cuadrado mágico.
            <br>Las sumas no coinciden. ¡Sigue intentándolo!
        `, 'error');
    }
}


function reiniciarJuego() {
    juegoTerminado = false;
    iniciarCronometro();

    celdasDOM.forEach(celda => {
        
        celda.textContent = '';
        celda.classList.remove('celda-activa', 'celda-magica', 'celda-vacia');
    });

    
    celdaActiva = document.getElementById('c-1-1');
    celdaActiva.classList.add('celda-activa');
    
    mostrarMensaje('¡Juego Reiniciado! Comienza a introducir números (1-9).');
    document.getElementById('celdas-restantes').textContent = 'Celdas restantes: 9';
}



document.addEventListener('DOMContentLoaded', () => {
    
    celdasDOM.forEach(celda => {
        celda.addEventListener('click', seleccionarCelda);
    });
    
    
    document.addEventListener('keydown', validarTecla);
    document.addEventListener('keyup', verificarCuadradoMagico);
    
    
    reiniciarJuego();
});