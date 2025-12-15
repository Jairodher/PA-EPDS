(function ($) {
    $.fn.sportValidator = function (options) {

        return this.each(function () {
            var $form = $(this);

            var $fields = $form.find('[data-sport-type]');

            $fields.each(function () {
                var $field = $(this);

                $field.on('focus', function () {
                    setFieldState($field, 'pending');
                    showMessage($field, '', '');
                });

                if ($field.is('input[type="text"], input[type="email"], input[type="number"]')) {
                    $field.on('input', function () {
                        validateField($field);
                    });
                }

                $field.on('blur', function () {
                    validateField($field);
                });

                if ($field.is('select')) {
                    $field.on('change', function () {
                        validateField($field);
                    });
                }
            });

            $form.on('submit', function (e) {
                var isFormValid = true;
                $form.find('[data-sport-type]').each(function () {
                    if (!validateField($(this))) {
                        isFormValid = false;
                    }
                });

                if (!isFormValid) {
                    e.preventDefault();
                    alert('Por favor, corrige los campos inválidos antes de inscribirte.');
                }
            });

            $form.attr('novalidate', 'novalidate');
        });
    };

    function validateField($field) {
        var type = $field.data('sport-type');
        var value = $field.val().trim();
        var isValid = true;
        var message = '';
        var messageClass = 'success';

        if (value === '' && type !== 'actividad' && type !== 'experiencia') {
            if ($field.is(':focus')) {
                setFieldState($field, 'pending');
                showMessage($field, '', '');
                return false;
            }
            isValid = false;
            message = "Este campo es obligatorio.";
            messageClass = 'error';
        }

        if (isValid) {
            switch (type) {
                case 'nombre':
                    if (value.length < 2) {
                        isValid = false;
                        message = "El nombre debe tener al menos 2 caracteres";
                        messageClass = 'error';
                    } else if (value.length > 50) {
                        isValid = false;
                        message = "El nombre es demasiado largo para el sistema";
                        messageClass = 'error';
                    } else {
                        message = "Nombre válido para inscripción";
                    }
                    break;

                case 'email':
                    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        isValid = false;
                        message = "Formato de email incorrecto";
                        messageClass = 'error';
                    } else {
                        message = "Email de contacto correcto";
                    }
                    break;

                case 'edad':
                    var edad = parseInt(value, 10);
                    if (isNaN(edad) || !Number.isInteger(edad)) {
                        isValid = false;
                        message = "La edad debe ser un número entero.";
                        messageClass = 'error';
                    } else if (edad < 16) {
                        isValid = false;
                        message = "Edad mínima: 16 años";
                        messageClass = 'error';
                    } else if (edad > 65) {
                        isValid = false;
                        message = "Edad máxima: 65 años";
                        messageClass = 'error';
                    } else {
                        message = "Edad apropiada para actividades";
                    }
                    break;

                case 'actividad':
                    if (value === '') {
                        isValid = false;
                        message = "Debes elegir una actividad deportiva";
                        messageClass = 'error';
                    } else {
                        message = "Actividad seleccionada";
                    }
                    break;

                case 'experiencia':
                    if (value === '') {
                        isValid = false;
                        message = "Selecciona tu nivel de experiencia";
                        messageClass = 'warning';
                    } else {
                        message = "Nivel de experiencia registrado";
                    }
                    break;

                default:
                    break;
            }
        }


        if (type === 'experiencia' && !isValid) {
            setFieldState($field, 'pending');
            showMessage($field, message, messageClass);
            return true;
        } else if (isValid) {
            setFieldState($field, 'valid');
            showMessage($field, message, messageClass);
            return true;
        } else {
            setFieldState($field, 'invalid');
            showMessage($field, message, messageClass);
            return false;
        }
    }

    function setFieldState($field, state) {
        $field.removeClass('sport-pending sport-valid sport-invalid');

        switch (state) {
            case 'pending':
                $field.addClass('sport-pending');
                break;
            case 'valid':
                $field.addClass('sport-valid');
                break;
            case 'invalid':
                $field.addClass('sport-invalid');
                break;
        }
    }

    function showMessage($field, message, messageClass) {
        $field.next('.sport-message').remove();

        if (message && messageClass) {
            var $msgElement = $('<div class="sport-message"></div>')
                .text(message)
                .addClass('sport-' + messageClass);

            $msgElement.insertAfter($field);
        }
    }


    $(document).ready(function () {
        $('#inscripcionDeportiva').sportValidator();
    });

})(jQuery);