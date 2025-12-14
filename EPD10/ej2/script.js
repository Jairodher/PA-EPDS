$(document).ready(function() {

    $('#email').on('blur', function() {
        $(this).mailcheck({
            suggested: function(element, suggestion) {
                $('#suggestion')
                    .html("¿Quiso decir <b>" + suggestion.full + "</b>?")
                    .fadeIn();
            },
            empty: function(element) {
                $('#suggestion').hide();
            }
        });
    });

    $('#suggestion').on('click', function() {
        $('#email').val($(this).find('b').text());
        $(this).fadeOut();
    });


    $("#fecha").datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        showAnim: "fadeIn"
    });


    $("#slider").slider({
        range: "min",
        value: 50,
        min: 0,
        max: 100,
        slide: function(event, ui) {

            $("#slider-value").text(ui.value);
            $("#prioridad").val(ui.value);
        }
    });

    $('#formulario').submit(function(e) {
        e.preventDefault();
        alert("Formulario enviado correctamente.");
    });
});