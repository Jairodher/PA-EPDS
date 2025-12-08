let organismoIdCounter = 0;
let generacion = 1;
let intervaloSimulacion = null
const DISTANCIA_INTERACCION = 50;
const ECO_CONTAINER = document.getElementById('ecosistema');
const TIPOS_ESPECIES = {
    'herbivoro': { icono: '🐇', energia: 100, color: 'orange', vel: 50 },
    'carnivoro': { icono: '🐆', energia: 150, color: 'red', vel: 70 },
    'planta': { icono: '🌳', energia: 40, color: 'green', vel: 0 },
    'omnivoro': { icono: '🐻', energia: 120, color: 'purple', vel: 60 }
};
let isPaused = false;

function generarIdUnico() {
    return `org-${++organismoIdCounter}`;
}

function crearOrganismo(tipo, x, y, energiaInicial = null) {
    const props = TIPOS_ESPECIES[tipo];
    const organismo = document.createElement('div');
    const id = generarIdUnico();

    organismo.className = `organismo ${tipo}`;
    organismo.id = id;
    organismo.innerHTML = props.icono;

    organismo.dataset.tipo = tipo;
    organismo.dataset.energia = energiaInicial !== null ? energiaInicial : props.energia;
    organismo.dataset.edad = 0;
    organismo.dataset.velocidad = props.vel;
    organismo.dataset.id = id;

    organismo.style.position = 'absolute';
    organismo.style.left = `${x}px`;
    organismo.style.top = `${y}px`;

    organismo.addEventListener('click', mostrarInfoOrganismo);
    organismo.addEventListener('dblclick', eliminarOrganismoDirecto);
    organismo.setAttribute('draggable', true); 
    organismo.addEventListener('dragstart', handleDragStart);
    organismo.addEventListener('dragend', handleDragEnd);

    ECO_CONTAINER.appendChild(organismo); 
    actualizarEstadisticas();
    return organismo;
}

function añadirEspecie(tipo) {
    const x = Math.floor(Math.random() * (ECO_CONTAINER.clientWidth - 50)) + 25;
    const y = Math.floor(Math.random() * (ECO_CONTAINER.clientHeight - 50)) + 25;
    crearOrganismo(tipo, x, y);
    registrarEvento(`Nuevo ${tipo} añadido en (${x}, ${y})`);
}

function eliminarOrganismo(elemento, causa) {
    if (!elemento) return;
    registrarEvento(`Un ${elemento.dataset.tipo} murió. Causa: ${causa}`);
    elemento.classList.add('muerte');  
    setTimeout(() => {
        if (elemento.parentNode) {
            elemento.parentNode.removeChild(elemento); 
            actualizarEstadisticas();
        }
    }, 1000); 
}

function eliminarOrganismoDirecto(event) {
    eliminarOrganismo(event.currentTarget, 'Doble clic de usuario');
}

// Muestra información detallada en un tooltip 
function mostrarInfoOrganismo(event) {
    const org = event.currentTarget;
    const tooltip = document.getElementById('tooltip');

    tooltip.style.display = 'block';
    tooltip.style.left = `${event.clientX + 10}px`;
    tooltip.style.top = `${event.clientY + 10}px`;
    
    tooltip.innerHTML = `
        **${org.dataset.tipo.toUpperCase()}**<br>
        Energía: ${org.dataset.energia}<br>
        Edad: ${org.dataset.edad}<br>
        ID: ${org.dataset.id}
    `;
}

function handleDragStart(event) {
    event.dataTransfer.setData('text/plain', event.target.id);
    event.target.style.opacity = '0.5';
}


function handleDragEnd(event) {
    event.target.style.opacity = '1';

    const rect = ECO_CONTAINER.getBoundingClientRect();

    const newX = event.clientX - rect.left - (event.target.clientWidth / 2);
    const newY = event.clientY - rect.top - (event.target.clientHeight / 2);

    event.target.style.left = `${Math.max(0, newX)}px`;
    event.target.style.top = `${Math.max(0, newY)}px`;
    
    event.preventDefault();
}

if (ECO_CONTAINER) {
    ECO_CONTAINER.addEventListener('dragover', (e) => e.preventDefault());
}
// Inicializa la simulación [cite: 400]
function iniciarEcosistema() {
    // Inicializa con algunos organismos base
    añadirEspecie('planta');
    añadirEspecie('planta');
    añadirEspecie('herbivoro');
    añadirEspecie('carnivoro');
    
    // Establece el ciclo de vida automático [cite: 401, 417]
    intervaloSimulacion = setInterval(simularCicloVida, VELOCIDAD_SIMULACION);
    registrarEvento('Ecosistema iniciado');
}

// Pausa/Reanuda la simulación [cite: 342]
function pausarSimulacion() {
    isPaused = !isPaused;
    const btn = document.getElementById('btn-pausa');
    if (isPaused) {
        clearInterval(intervaloSimulacion);
        btn.textContent = 'Reanudar';
        registrarEvento('Simulación pausada');
    } else {
        intervaloSimulacion = setInterval(simularCicloVida, VELOCIDAD_SIMULACION);
        btn.textContent = 'Pausar';
        registrarEvento('Simulación reanudada');
    }
}

// El corazón de la simulación, se ejecuta cada ciclo [cite: 415]
function simularCicloVida() {
    if (isPaused) return;

    const organismos = Array.from(document.querySelectorAll('.organismo'));
    
    // 1. Ejecutar interacciones (comer, reproducir)
    interaccionOrganismos(organismos); // [cite: 426]

    // 2. Mover y envejecer a todos
    organismos.forEach(org => {
        // Muerte por edad o hambre [cite: 334]
        let energia = parseInt(org.dataset.energia);
        let edad = parseInt(org.dataset.edad);
        const tipo = org.dataset.tipo;

        // Pérdida de energía por ciclo
        org.dataset.energia = energia - 5;
        org.dataset.edad = edad + 1;

        if (energia <= 0) {
            eliminarOrganismo(org, 'Hambre');
            return;
        }

        if (tipo !== 'planta' && edad > 100) { // Límite de vida para animales
            eliminarOrganismo(org, 'Vejez');
            return;
        }
        
        moverOrganismo(org); // [cite: 421]
    });
    
    // 3. Crecimiento de Plantas (Espontáneo) [cite: 336]
    if (Math.random() < 0.2) { // 20% de probabilidad de que una planta nueva crezca
        añadirEspecie('planta');
    }

    actualizarEstadisticas(); // [cite: 420]
    generacion++;
    document.getElementById('generacion').textContent = generacion;
}

// Mueve un organismo a una posición aleatoria cercana [cite: 421]
function moverOrganismo(elemento) {
    if (elemento.dataset.tipo === 'planta') return; // Las plantas no se mueven

    const vel = parseInt(elemento.dataset.velocidad);
    const containerRect = ECO_CONTAINER.getBoundingClientRect();
    
    let currentX = parseFloat(elemento.style.left) || 0;
    let currentY = parseFloat(elemento.style.top) || 0;
    
    // Movimiento aleatorio
    const newX = currentX + (Math.random() - 0.5) * vel;
    const newY = currentY + (Math.random() - 0.5) * vel;

    // Asegurar que el organismo permanezca dentro del contenedor
    const clampedX = Math.max(0, Math.min(newX, containerRect.width - elemento.clientWidth));
    const clampedY = Math.max(0, Math.min(newY, containerRect.height - elemento.clientHeight));
    
    elemento.style.left = `${clampedX}px`; // [cite: 424]
    elemento.style.top = `${clampedY}px`; // [cite: 424]
}

// Maneja las interacciones: alimentación y reproducción [cite: 426]
function interaccionOrganismos(organismos) {
    organismos.forEach((org1, i) => {
        if (!org1.parentNode) return; // Ya fue eliminado

        for (let j = i + 1; j < organismos.length; j++) {
            const org2 = organismos[j];
            if (!org2.parentNode) continue;

            const dist = calcularDistancia(org1, org2);

            if (dist < DISTANCIA_INTERACCION) { // Están lo suficientemente cerca
                // Lógica de Alimentación (Comer) [cite: 330]
                if (org1.dataset.tipo === 'carnivoro' && org2.dataset.tipo === 'herbivoro') {
                    alimentar(org1, org2);
                } else if (org2.dataset.tipo === 'carnivoro' && org1.dataset.tipo === 'herbivoro') {
                    alimentar(org2, org1);
                } else if (org1.dataset.tipo === 'herbivoro' && org2.dataset.tipo === 'planta') {
                    alimentar(org1, org2);
                } else if (org2.dataset.tipo === 'herbivoro' && org1.dataset.tipo === 'planta') {
                    alimentar(org2, org1);
                } else if (org1.dataset.tipo === 'omnivoro' && (org2.dataset.tipo === 'planta' || org2.dataset.tipo === 'herbivoro')) {
                    alimentar(org1, org2);
                } else if (org2.dataset.tipo === 'omnivoro' && (org1.dataset.tipo === 'planta' || org1.dataset.tipo === 'herbivoro')) {
                    alimentar(org2, org1);
                }

                // Lógica de Reproducción [cite: 331]
                if (org1.dataset.tipo === org2.dataset.tipo && org1.dataset.tipo !== 'planta') {
                    reproducirEspecie(org1, org2);
                }
            }
        }
    });
}

function alimentar(depredador, presa) {
    const ganancia = parseInt(presa.dataset.energia) * 0.5;
    depredador.dataset.energia = parseInt(depredador.dataset.energia) + ganancia;
    eliminarOrganismo(presa, 'Cazado'); // [cite: 334]
    registrarEvento(`${depredador.dataset.tipo} cazó a ${presa.dataset.tipo}`);
}


function reproducirEspecie(padre1, padre2) {
    if (Math.random() < 0.1) return; 

    const tipo = padre1.dataset.tipo;
    

    const x = parseFloat(padre1.style.left) + Math.random() * 20 - 10;
    const y = parseFloat(padre1.style.top) + Math.random() * 20 - 10;
    

    const energiaHijo = (parseInt(padre1.dataset.energia) + parseInt(padre2.dataset.energia)) / 2;

    crearOrganismo(tipo, x, y, energiaHijo);
    registrarEvento(`Nació un nuevo ${tipo}`);
    
    
    padre1.dataset.energia = parseInt(padre1.dataset.energia) - 20;
    padre2.dataset.energia = parseInt(padre2.dataset.energia) - 20;
}

function calcularDistancia(org1, org2) {
    const x1 = parseFloat(org1.style.left);
    const y1 = parseFloat(org1.style.top);
    const x2 = parseFloat(org2.style.left);
    const y2 = parseFloat(org2.style.top);
    return Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
}


function actualizarEstadisticas() {
    const organismos = document.querySelectorAll('.organismo');
    const counts = { herbivoro: 0, carnivoro: 0, planta: 0, omnivoro: 0 };
    
    organismos.forEach(org => {
        counts[org.dataset.tipo]++;
    });

    document.getElementById('count-herbivoro').textContent = counts.herbivoro; 
    document.getElementById('count-carnivoro').textContent = counts.carnivoro;
    document.getElementById('count-planta').textContent = counts.planta;
    document.getElementById('count-omnivoro').textContent = counts.omnivoro;
    

    let estado = 'Equilibrado';
    if (counts.herbivoro > counts.planta * 1.5 || counts.carnivoro > counts.herbivoro * 1.5) {
        estado = 'Sobrepoblación'; 
    } else if (counts.planta === 0 || counts.herbivoro === 0) {
        estado = 'Extinción inminente'; 
    } else if (counts.herbivoro + counts.carnivoro + counts.omnivoro < 3) {
        estado = 'Colapso'; 
    }
    document.getElementById('estado-ecosistema').textContent = estado;
}

function registrarEvento(mensaje) {
    const logList = document.getElementById('eventos-lista');
    const nuevoEvento = document.createElement('p');
    const timestamp = new Date().toLocaleTimeString();
    
    nuevoEvento.innerHTML = `[${timestamp}] ${mensaje}`; 

    if (logList.firstChild) {
        logList.insertBefore(nuevoEvento, logList.firstChild);
    } else {
        logList.appendChild(nuevoEvento);
    }

    while (logList.children.length > 10) {
        logList.removeChild(logList.lastChild);
    }
}