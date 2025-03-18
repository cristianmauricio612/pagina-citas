<?php
// 1️⃣ Verificar si el ID fue enviado
if (isset($_GET['id'])) {
    $anuncio_id = $_GET['id'];

    // 2️⃣ Incluir la configuración de la base de datos
    include '../php/backend/config.php'; // Asegúrate de que este archivo conecta correctamente

    try {
        // 3️⃣ Preparar la consulta con PDO
        $query = "SELECT * FROM anuncios WHERE anuncio_id = :anuncio_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':anuncio_id', $anuncio_id, PDO::PARAM_STR);
        $stmt->execute();

        // 4️⃣ Obtener los resultados
        $anuncio = $stmt->fetch();

        if (!$anuncio) {
            echo "No se encontró el anuncio.";
            exit();
        }
    } catch (PDOException $e) {
        die("Error al obtener el anuncio: " . $e->getMessage());
    }
} else {
    echo "ID no proporcionado.";
    exit();
}

function calcularEdad($fechaNacimiento)
{
    $fechaNacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime(); // Fecha actual
    $edad = $hoy->diff($fechaNacimiento)->y; // Diferencia en años
    return $edad;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anuncio - <?php echo htmlspecialchars($anuncio['titulo']); ?></title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css">
    <link rel="stylesheet" href="/sources/bootstrap-5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="/sources/nouislider-15.7.0/nouislider.min.css">
    <link rel="stylesheet" href="/sources/leaflet-1.9.4/leaflet.css" />
    <link rel="stylesheet" href="/sources/glightbox-3.3.0/css/glightbox.min.css">
    <link rel="stylesheet" href="/sources/choices-11.0.2/choices.min.css">
    <link rel="stylesheet" href="anuncio.css">
</head>

<body>
    <div id="popup" class="popup-container">
        <div class="popup-content">
            <div class="row row-container">
                <div class="col-8">
                    <main class="gallery">
                        <article class="reveal">
                            <img class="glightbox" src="">
                        </article>
                        <article class="reveal">
                            <img class="glightbox" src="">
                        </article>
                        <article class="reveal">
                            <img class="glightbox" src="">
                        </article>
                        <article class="reveal">
                            <img class="glightbox" src="">
                        </article>
                    </main>

                    <div class="card shadow-lg border-0 rounded-4 p-4">
                        <h1 class="card-title text-center mb-3 fs-4 encabezadoanuncio">
                            <img src="/assets/img/logos/logo.png" width="30px" />
                            <b id="tituloanuncio"
                                style="font-size:24px"><?php echo htmlspecialchars($anuncio['titulo']); ?></b>
                        </h1>
                        <p class="user-profile-card descripcionanuncio">
                            <?php echo htmlspecialchars($anuncio['descripcion']); ?>
                        </p>

                        <h4 class="m-auto mb-2" style="width: 95%; text-align: left;">
                            <i class="fa-solid fa-map-location-dot" style="color: #a90b35;"></i> Ubicación: <span
                                id="ciudadlocation" style="color: #a90b35;">
                                <?php echo htmlspecialchars($anuncio['ciudad']); ?></span>
                        </h4>
                        <div id="map" class="user-profile-card mapaanuncio" style="height: 300px;"></div>
                        <p class="text-center mt-3 locationanuncio">
                            <?php echo htmlspecialchars($anuncio['locationtip']); ?>
                        </p>

                        <div class="titleservices">
                            <i class="fa-solid fa-concierge-bell" style="color: #ffcf4f;"></i> Servicios que realizo
                        </div>
                        <div class="user-profile-card" style="width: 95%; max-width: 95%; margin-bottom: 30px;">
                            <div class="services-grid">
                            </div>
                        </div>

                        <div class="titleservices">
                            <img src="/assets/img/fotos/iconos/chest.webp" width="25px" /> Cofre de Notas
                        </div>
                        <div class="user-profile-card"
                            style="width: 95%; max-width: 95%; min-height: 280px; height: auto; max-height: 600px;">
                        </div>
                    </div>

                </div>

                <div class="col-2">
                    <div class="card popup-perfil shadow-lg">
                        <img src="<?php echo htmlspecialchars($anuncio['picture_profile']); ?>"
                            class="card-img-top mx-auto popup-picture" id="profilepicture" alt="Profile Picture"
                            style="width: 150px;">
                        <div class="card-body">
                            <h5 class="card-title popup-name"><?php echo htmlspecialchars($anuncio['nombre']); ?></h5>

                            <div class="tarifa-creative">
                                <span class="tarifa-label">
                                    ¡Desde: <e id="tarifa"><?php echo htmlspecialchars($anuncio['tarifa']); ?></e>€!
                                </span>
                            </div>

                            <div class="user-profile-card">
                                <div class="profile-item" data-visible="true">
                                    <span class="label"><i class="fa-solid fa-venus-mars"></i> Sexo</span>
                                    <span class="value"
                                        id="sexoval"><?php echo htmlspecialchars($anuncio['sexo']); ?></span>
                                </div>
                                <div class="profile-item" data-visible="true">
                                    <span class="label"><i class="fa-solid fa-heart"></i> Categoría</span>
                                    <span class="value"
                                        id="categoryval"><?php echo htmlspecialchars($anuncio['categoria']); ?></span>
                                </div>
                                <div class="profile-item" data-visible="true">
                                    <span class="label"><i class="fa-solid fa-cake-candles"></i> Edad</span>
                                    <span class="value"
                                        id="ageval"><?php echo calcularEdad($anuncio['nacimiento']); ?></span>
                                </div>
                                <div class="profile-item" data-visible="true">
                                    <span class="label"><i class="fa-solid fa-earth-americas"></i> Origen</span>
                                    <span class="value" id="originval"
                                        style="align-items: center; display: flex; gap: 5px;">
                                        <img src="https://flagsapi.com/<?php echo htmlspecialchars($anuncio['bandera']); ?>/shiny/32.png"
                                            width="23px" />
                                        <?php echo htmlspecialchars($anuncio['pais']); ?>
                                    </span>
                                </div>
                                <div class="profile-item" data-visible="true">
                                    <span class="label"><i class="fa-solid fa-square-phone"></i> Teléfono</span>
                                    <span class="value"
                                        id="phoneval"><?php echo htmlspecialchars($anuncio['telefono']); ?></span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <button class="btn popup-button"><i class="fa-brands fa-whatsapp"></i> WhatsApp</button>
                                <button class="btn popup-button"><i class="fa-solid fa-phone"></i> Llamar</button>
                            </div>

                            <div class="user-profile-card">
                                <div>
                                    <span class="label" style="display: block; text-align: left;"><i
                                            class="fa-solid fa-language"></i> Idiomas</span>
                                    <div id="languagesContainer" class="languages">
                                        <div class="language" data-level="3">
                                            <span class="language-label">Español</span>
                                            <div class="language-bar">
                                                <div class="language-progress"></div>
                                            </div>
                                        </div>
                                        <div class="language" data-level="2">
                                            <span class="language-label">Inglés</span>
                                            <div class="language-bar">
                                                <div class="language-progress"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="user-profile-card">
                                <span class="label" style="display: block; text-align: left;"><i
                                        class="fa-solid fa-calendar-check"></i> Disponibilidad</span>

                                <!-- Días de la semana -->
                                <div class="availability-days">
                                </div>

                                <!-- Momentos del día -->
                                <div class="availability-times mt-3">
                                </div>
                            </div>


                            <div class="user-profile-card">
                                <div
                                    style="display: inline-block; padding: 0px 0px 5px; border-bottom: 1px solid #dbdbdb; width: 100%;">
                                    <div class="badge premium">
                                        PREMIUM
                                    </div>
                                    <div class="badge autosubidas">
                                        <i class="fa-regular fa-clock-rotate-left"></i> Autorenueva
                                    </div>
                                </div>
                                <div class="row align-items-center"
                                    style="justify-content: center; gap: 6px; margin-top: 5px; border-bottom: 1px solid #dbdbd9; padding: 0px 0px 5px;">
                                    <!-- Columna 1: ID del Anuncio -->
                                    <div class="col-6" style="font-size: 1.3rem;">
                                        <div class="row">
                                            <div class="badge text-white p-3 w-100"
                                                style="padding: 20px 5px !important; text-align: center; background: linear-gradient(76deg, #940a2f, #a23481);">
                                                <span class="fw-bold d-block mb-1">ID Anuncio</span>
                                                <span class="announcement-id"
                                                    id="announcement-id"><?php echo htmlspecialchars($anuncio['anuncio_id']); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Columna 2: Fecha de subida y Visitas -->
                                    <div class="col-5" style="font-size: 1rem;">
                                        <div class="row" style="margin-bottom: 2px;">
                                            <!-- Fecha de Subida -->
                                            <div class="badge text-white p-2 w-100 costadosbox">
                                                <span class="d-block fw-bold"><i class="fa-solid fa-clock"></i>
                                                    Creación</span>
                                                <span
                                                    id="upload-date"><?php echo htmlspecialchars((new DateTime($anuncio['updated_at']))->format('d/m/Y'), ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- Visitas -->
                                            <div class="badge text-white p-2 w-100 costadosbox">
                                                <span class="d-block fw-bold"><i class="fa-solid fa-eye"></i>
                                                    Vistas</span>
                                                <span
                                                    id="view-count"><?php echo htmlspecialchars($anuncio['visitas']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botón de Compartir -->
                                <div class="d-flex justify-content-between mt-3">
                                    <button class="btn popup-button" onclick="" style="margin: auto;">
                                        <i class="fa-solid fa-share"></i> Compartir
                                    </button>
                                </div>
                            </div>


                            <!-- Modal de compartir -->
                            <div class="modal" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="shareModalLabel">Compartir Anuncio</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <button class="btn btn-outline-primary" onclick="">
                                                <i class="fa-brands fa-whatsapp"></i> WhatsApp
                                            </button>
                                            <button class="btn btn-outline-primary" onclick="">
                                                <i class="fa-brands fa-facebook"></i> Facebook
                                            </button>
                                            <button class="btn btn-outline-primary" onclick="">
                                                <i class="fa-solid fa-link"></i> Copiar Enlace
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
<script src="/sources/leaflet-1.9.4/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1️⃣ Obtener los servicios desde PHP
        const data = {
            servicios: <?php echo json_encode($anuncio['servicios']); ?>,
            idiomas: <?php echo json_encode($anuncio['idiomas']); ?>,
            disponibilidad: <?php echo json_encode($anuncio['disponibilidad']); ?>,
            horarios: <?php echo json_encode($anuncio['horarios']); ?>,
            pictures: <?php echo json_encode($anuncio['pictures']); ?>,
            imgprincipal: <?php echo json_encode($anuncio['principal_picture']); ?>,
            profilepicture: <?php echo json_encode($anuncio['picture_profile']); ?>,
            location: {
                lat: parseFloat(<?php echo json_encode($anuncio['latitude']); ?>),
                lng: parseFloat(<?php echo json_encode($anuncio['longitude']); ?>),
            }
        };

        const idiomasJson = data.idiomas;
        let idiomas;
        idiomas = JSON.parse(idiomasJson);

        const languagesContainer = document.querySelector('#languagesContainer');
        languagesContainer.innerHTML = ''; // Limpiar contenido previo

        // Generar HTML dinámicamente si idiomas tiene datos
        idiomas.forEach(({ nivel, idioma }) => {
            const languageDiv = document.createElement('div');
            languageDiv.className = 'language';
            languageDiv.setAttribute('data-level', nivel);

            languageDiv.innerHTML = `
                            <span class="language-label">${idioma}</span>
                            <div class="language-bar">
                                <div class="language-progress"></div>
                            </div>
                        `;

            // Agregar al contenedor
            languagesContainer.appendChild(languageDiv);
        });
        document.querySelectorAll('.language').forEach(language => {
            const progress = language.querySelector('.language-progress');
            progress.style.width = '0';
            void progress.offsetWidth;

            const level = language.getAttribute('data-level');
            const progressWidth = `${(level / 3) * 100}%`;
            setTimeout(() => {
                progress.style.width = progressWidth;
            }, 100);
        });

        // 2️⃣ Contenedor en el DOM 
        const servicesContainer = document.querySelector('.services-grid');

        if (!servicesContainer) {
            console.error("No se encontró el contenedor de servicios.");
            return; // Detener ejecución si no existe el contenedor
        }

        // 3️⃣ Lista de todos los servicios posibles
        const serviciosDisponibles = [
            "24h", "Salidas", "Novios", "Masajes", "Besos con lengua", "Francés",
            "FR Natural", "Parejas", "Beso negro", "Anal", "Tríos", "Lésbico",
            "Sado BDSM", "Lluvia dorada", "Fiestera"
        ];

        // 4️⃣ Convertir data.servicios de JSON a array
        let serviciosUsuario;
        try {
            serviciosUsuario = JSON.parse(data.servicios) || []; // Convertir JSON a objeto
        } catch (error) {
            console.error("Error al parsear servicios:", error);
            serviciosUsuario = []; // Inicializar vacío si hay error
        }

        // 5️⃣ Generar dinámicamente el contenido
        servicesContainer.innerHTML = ''; // Limpiar contenido previo

        serviciosDisponibles.forEach(servicio => {
            const disponible = serviciosUsuario.includes(servicio); // Verificar si el servicio está en la lista

            // Crear elemento del servicio
            const serviceDiv = document.createElement('div');
            serviceDiv.className = 'service';

            serviceDiv.innerHTML = `
            <i title="${disponible ? '¡Lo hago!' : 'Lo siento... Pero no.'}" 
            class="fa-solid ${disponible ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'}"></i>
            <span>${servicio}</span>
        `;

            // Agregar el servicio al contenedor
            servicesContainer.appendChild(serviceDiv);
        });

        // Contenedor en el DOM para días y horarios
        const availabilityDaysContainer = document.querySelector('.availability-days');
        const availabilityTimesContainer = document.querySelector('.availability-times');

        // Convertir JSON de días de la semana
        let disponibilidadDias;
        try {
            disponibilidadDias = JSON.parse(data.disponibilidad);
        } catch (error) {
            console.error("Error al parsear disponibilidad:", error);
            disponibilidadDias = []; // Inicializar vacío si falla
        }

        // Días de la semana
        const diasSemana = [
            { letra: "L", nombre: "Lunes" },
            { letra: "M", nombre: "Martes" },
            { letra: "X", nombre: "Miércoles" },
            { letra: "J", nombre: "Jueves" },
            { letra: "V", nombre: "Viernes" },
            { letra: "S", nombre: "Sábado" },
            { letra: "D", nombre: "Domingo" }
        ];

        // Generar HTML dinámico para días de la semana
        availabilityDaysContainer.innerHTML = ''; // Limpiar contenido previo
        diasSemana.forEach(dia => {
            const disponible = disponibilidadDias.includes(dia.letra);
            const dayDiv = document.createElement('div');
            dayDiv.className = 'day';
            dayDiv.setAttribute('title', dia.nombre);

            dayDiv.innerHTML = `
                            <span class="day-label">${dia.letra}</span>
                            <i title="${disponible ? 'Disponible' : 'No Disponible'}" 
                                class="fa-solid ${disponible ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'}"></i>
                        `;
            availabilityDaysContainer.appendChild(dayDiv);
        });

        // Convertir JSON de data.horarios
        let horariosDisponibles;
        try {
            horariosDisponibles = JSON.parse(data.horarios); // Convertir texto JSON a arreglo
        } catch (error) {
            console.error("Error al parsear los horarios:", error);
            horariosDisponibles = []; // Inicializar como vacío en caso de error
        }

        // Lista de momentos del día con su configuración
        const horarios = [
            { texto: 'Mañanas', icono: 'fa-sun', color: '' },
            { texto: 'Tardes', icono: 'fa-cloud-sun', color: '#d1720c' },
            { texto: 'Noches', icono: 'fa-moon', color: '#848fad' }
        ];

        // Generar dinámicamente el contenido para horarios
        availabilityTimesContainer.innerHTML = ''; // Limpiar contenido previo
        horarios.forEach(({ texto, icono, color }) => {
            // Verificar si el horario está disponible en el arreglo
            const disponible = horariosDisponibles.includes(texto);

            // Crear el elemento del horario
            const timeSlotDiv = document.createElement('div');
            timeSlotDiv.className = 'time-slot';

            timeSlotDiv.innerHTML = `
                            <i class="fa-solid ${icono}" style="color: ${color};"></i>
                            <span>${texto}</span>
                            <i title="${disponible ? 'Disponible' : 'No Disponible'}" 
                                class="fa-solid ${disponible ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'}"></i>
                        `;

            // Agregar al contenedor
            availabilityTimesContainer.appendChild(timeSlotDiv);
        });

        const articles = Array.from(document.querySelectorAll("article"));

        // Convertir `data.pictures` a JSON
        let pictures = [];
        try {
            pictures = JSON.parse(data.pictures);
        } catch (error) {
            console.error("Error al parsear pictures:", error);
            pictures = [];
        }

        // Generar una lista completa de imágenes a usar en los articles
        const imagesToUse = [
            pictures[0] || data.imgprincipal, // Primer <article>
            data.imgprincipal,               // Segundo <article>
            pictures[1] || pictures[0] || data.imgprincipal, // Tercer <article>
            pictures[2] || pictures[0] || data.imgprincipal  // Cuarto <article>
        ];

        // Si `pictures` tiene menos de 3 imágenes, rellenar con la primera imagen
        while (imagesToUse.length < 4) {
            imagesToUse.push(imagesToUse[0]);
        }

        // Asignar los SRC de las imágenes antes de realizar las animaciones
        articles.forEach((article, index) => {
            const img = article.querySelector("img");
            if (img) {
                img.src = imagesToUse[index];
            }
        });


        // Actualizar mapa dinámicamente
        const { lat, lng } = data.location;

        // Eliminar cualquier mapa previo para evitar conflictos
        if (window.currentMap) {
            window.currentMap.remove(); // Limpia el mapa previo
        }

        // Crear el mapa y centrarlo en la nueva ubicación
        const map = L.map('map').setView([lat, lng], 15);
        window.currentMap = map; // Guardar referencia global para reutilizar

        // Agregar la capa base con estilo minimalista
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.carto.com/">Carto</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        // Agregar marcador
        const pinkIcon = L.divIcon({
            html: '<img src="/assets/img/logos/location-dot.png" class="locationdot"/>',
            className: 'custom-icon',
            iconSize: [30, 42],
            iconAnchor: [15, 42]
        });
        L.marker([lat, lng], { icon: pinkIcon }).addTo(map).bindPopup(`${data.city}, ${data.country}`);

        // Asegurarte de que el mapa esté bien centrado
        setTimeout(() => {
            map.invalidateSize(); // Forzar actualización del tamaño del mapa
        }, 300); // Un pequeño retraso para garantizar que el contenedor esté completamente visible
    });  
</script>


</html>