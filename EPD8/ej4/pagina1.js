// Snippet adaptado desde CSS-Tricks: KeyboardEvent Keycodes
// Viene de https://css-tricks.com/snippets/javascript/javascript-keycodes/

window.addEventListener('keydown', function(e) {
    const key = e.key.toLowerCase();

    if (key === "a") {
        alert("Has pulsado la tecla A");
    } 
    else if (key === "c") {
        document.body.style.background =
            document.body.style.background === "lightyellow" ? "white" : "lightyellow";
    }
    else if (key === "h") {
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
});
