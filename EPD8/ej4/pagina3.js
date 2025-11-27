// Snippet basado en CSS-Tricks: htmlentities()
// viene de https://css-tricks.com/snippets/javascript/htmlentities-for-javascript/
function escaparHTML(str) {
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

document.getElementById("mostrar").addEventListener("click", () => {
    const texto = document.getElementById("entrada").value;
    document.getElementById("salida").textContent = escaparHTML(texto);
});
