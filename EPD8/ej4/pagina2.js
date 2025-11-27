// Snippet adaptado desde CSS-Tricks: Get URL and URL parts
// viene de https://css-tricks.com/snippets/javascript/get-url-and-url-parts-in-javascript/
function getUrlParts() {
    const { protocol, host, pathname, search, hash } = window.location;
    return { protocol, host, pathname, search, hash };
}

function parseQuery(qs) {
    const params = new URLSearchParams(qs);
    const obj = {};
    for (const [key, value] of params) {
        obj[key] = value;
    }
    return obj;
}

const partes = getUrlParts();
const parametros = parseQuery(partes.search);

document.getElementById("resultado").innerHTML = `
    <h2>Partes de la URL</h2>
    <p><strong>Protocolo:</strong> ${partes.protocol}</p>
    <p><strong>Host:</strong> ${partes.host}</p>
    <p><strong>Pathname:</strong> ${partes.pathname}</p>
    <p><strong>Search:</strong> ${partes.search}</p>
    <p><strong>Hash:</strong> ${partes.hash}</p>

    <h2>Parámetros detectados:</h2>
    <pre>${JSON.stringify(parametros, null, 2)}</pre>
`;
