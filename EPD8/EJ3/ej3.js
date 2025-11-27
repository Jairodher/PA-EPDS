// Ejercicio 3 EPD 8

let cadena = prompt("Introduce un código de actividad:");
let resultado = "";
for (let i = 0; i < cadena.length; i++) {
    let char = cadena.charAt(i);
    if (i !== 0 && i % 5 === 0) {
        continue;
    }
    if (i !== 0 && i % 3 === 0) {
        resultado += char.toLowerCase();
        continue;
    }
    if (i % 2 === 0) {
        resultado += char.toUpperCase();
        continue;
    }
    resultado += char;
}
alert("Resultado: " + resultado);

