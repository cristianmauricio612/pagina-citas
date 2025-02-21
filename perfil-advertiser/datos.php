<?php

require '../vendor/autoload.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FantaSexAnuncios.com 💋</title>
    <link rel="shortcut icon" href="/assets/img/logos/logo.png" type="image/x-icon">



    <!-- SEO CONFING -->
    <meta name="author" content="Cyco Design's">
    <meta name="description" content="Encuentra las mejores Escorts de la zona facilmente.">
    <meta property="og:site_name" content="FantaSexAnuncios.com">
    <meta property="og:title" content="FantaSexAnuncios.com 💋">
    <meta property="og:description" content="Encuentra las mejores Escorts de la zona facilmente.">
    <meta property="og:url" content="https://fantasexanuncios.com/">
    <meta property="og:image" content="https://fantasexanuncios.com/assets/img/logos/logo.png">
    <meta property="og:type" content="website">
    <meta name="robots" content="index, follow">

    <!-- DEPENDENCIAS -->
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    <link href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider/distribute/nouislider.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #ff006e, #8338ec);
            color: white;
            font-family: 'Roboto', sans-serif;
        }
        .question-container {
            display: none;
        }
        .active {
            display: block;
            animation: fadeIn 0.5s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .btn-next {
            background: #ff006e;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            border-radius: 25px;
            color: white;
        }
        .btn-next:hover {
            background: #d6005b;
        }
        .btn-back {
            background: #ff006e;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            border-radius: 25px;
            color: white;
        }
        .btn-back:hover {
            background: #d6005b;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <h1 class="text-center mb-4">Completa tu perfil</h1>
                <form id="interactiveForm">
                    <!-- Preguntas -->
                    <div class="question-container active" data-step="1">
                        <label class="form-label">Nombre de Usuario</label>
                        <input type="text" class="form-control" name="nombre" required>
                        <button type="button" class="btn btn-next mt-4">Siguiente</button>
                    </div>

                    <div class="question-container" data-step="2">
                        <label class="form-label">Sube tu fotografía</label>
                        <input type="file" class="form-control" name="fotografia" accept="image/*" required>
                        <button type="button" class="btn btn-back mt-4">Anterior</button>
                        <button type="button" class="btn btn-next mt-4">Siguiente</button>
                    </div>

                    <div class="question-container" data-step="3">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" name="nacimiento" required>
                        <button type="button" class="btn btn-back mt-4">Anterior</button>
                        <button type="button" class="btn btn-next mt-4">Siguiente</button>
                    </div>

                    <div class="question-container" data-step="4">
                        <label class="form-label">Sexo</label>
                        <select class="form-select" name="sexo" required>
                            <option value="Hombre">Hombre</option>
                            <option value="Mujer">Mujer</option>
                            <option value="Trasn">Trans</option>
                            <option value="Otro">Otro</option>
                        </select>
                        <button type="button" class="btn btn-back mt-4">Anterior</button>
                        <button type="button" class="btn btn-next mt-4">Siguiente</button>
                    </div>

                    <div class="question-container" data-step="5">
                        <label class="form-label">País</label>
                        <select class="form-select" name="pais" id="countrySelect" required>
                            <!-- Más opciones aquí -->
                        </select>
                        <button type="button" class="btn btn-back mt-4">Anterior</button>
                        <button type="button" class="btn btn-next mt-4">Siguiente</button>
                    </div>

                    <div class="question-container" data-step="6">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" name="categoria" required>
                            <option value="Escort">Escort</option>
                            <option value="Gay">Gay</option>
                            <option value="Webcam">Webcam</option>
                            <!-- Más opciones aquí -->
                        </select>
                        <button type="button" class="btn btn-back mt-4">Anterior</button>
                        <button type="button" class="btn btn-next mt-4">Siguiente</button>
                    </div>

                    <div class="question-container" data-step="7">
                        <label class="form-label">Teléfono y Whatsapp</label>
                        <div class="input-group">
                            <select class="form-select" name="indicativo" id="indicativo" required>
                            </select>
                            <input type="tel" class="form-control" name="telefono" required>
                        </div>
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" name="whatsapp" id="whatsapp">
                            <label class="form-check-label" for="whatsapp">Usa este número para Whatsapp</label>
                        </div>
                        <button type="submit" class="btn btn-next mt-4">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/nouislider/distribute/nouislider.min.js"></script>

    <script>
      $(document).ready(function () {
            let currentStep = 1;

            // Cargar países dinámicamente
            fetch('../php/backend/datalist/countries.json')
                .then(response => response.json())
                .then(countries => {
                    countries.forEach(country => {
                        $('#countrySelect').append(
                            `<option value="${country.code}" data-flag="${country.flag}">
                                <img src="https://flagsapi.com/${country.code.toUpperCase()}/shiny/32.png" width="10px"/> ${country.name}
                            </option>`
                        );
                    });
                });

             // Cargar indicativos dinámicamente
             fetch('../php/backend/datalist/indicativos.json')
                .then(response => response.json())
                .then(countries => {
                    countries.forEach(country => {
                        $('#indicativo').append(
                            `<option value="${country.dial_code}" data-flag="${country.flag}" data-dial="${country.dial_code}">
                                <img src="https://flagsapi.com/${country.code.toUpperCase()}/shiny/32.png" width="10px"/> ${country.dial_code} (${country.name})
                            </option>`
                        );
                    });
                });

            // Botón siguiente
            $(".btn-next").on("click", function () {
                const currentContainer = $(`.question-container[data-step="${currentStep}"]`);
                const nextContainer = $(`.question-container[data-step="${currentStep + 1}"]`);

                currentContainer.removeClass("active");
                nextContainer.addClass("active");
                currentStep++;

                // Mostrar botón "Atrás" solo después de la primera pregunta
                if (currentStep > 1) {
                    $(".btn-back").removeClass("d-none");
                }
            });

            // Botón atrás
            $(".btn-back").on("click", function () {
                const currentContainer = $(`.question-container[data-step="${currentStep}"]`);
                const previousContainer = $(`.question-container[data-step="${currentStep - 1}"]`);

                currentContainer.removeClass("active");
                previousContainer.addClass("active");
                currentStep--;

                // Ocultar botón "Atrás" en la primera pregunta
                if (currentStep === 1) {
                    $(".btn-back").addClass("d-none");
                }
            });

            // Enviar formulario
            $("#interactiveForm").on("submit", function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Subir imagen
                fetch('../php/backend/upload-image.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        formData.append('fotografia', data.path);
                        
                        // Guardar perfil
                        fetch('../php/backend/save_profile.php', {
                            method: 'POST',
                            body: formData
                        }).then(res => res.json())
                        .then(result => {
                            if (result.success) {
                                Swal.fire('Éxito', 'Perfil completado.', 'success');
                            } else {
                                Swal.fire('Error', 'No se pudo guardar el perfil.', 'error');
                            }
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
            });
        });

    </script>
</body>
</html>
