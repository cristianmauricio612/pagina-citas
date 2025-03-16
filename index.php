<?php
session_start(); 
require 'php/backend/get_credits.php';
require 'php/backend/config.php';

// Verificar si la cookie "mayoria_edad" está establecida
if (!isset($_COOKIE['mayoria_edad']) || $_COOKIE['mayoria_edad'] !== 'aceptado') {
    header("Location: mayoria-edad/");
    exit();
}

// Verifica si el usuario está logeado
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userType = $isLoggedIn ? $_SESSION['user_type'] : null;
$userName = isset($_SESSION['user_email']) ? explode('@', $_SESSION['user_email'])[0] : "Usuario";

$userId = $isLoggedIn ? $_SESSION['user_id'] : null;
$userCredits = getUserCredits($userId, $pdo);

function get_user_location_by_ip() {
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) { 
        $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { 
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    // Consultar la API de ipinfo.io
    $apiUrl = "https://ipinfo.io/{$ipAddress}/json";
    $response = file_get_contents($apiUrl);
    $data = json_decode($response, true);

    if (!empty($data['loc'])) {
        list($latitude, $longitude) = explode(',', $data['loc']);
        return ['latitude' => $latitude, 'longitude' => $longitude];
    }

    return null; // Si no se puede determinar la ubicación.
}

// Consulta para obtener los anuncios en orden descendente de fecha de creación
$stmt = $pdo->query("SELECT * FROM anuncios ORDER BY created_at DESC");
$anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener resultados como un arreglo asociativo

// Obtener ubicación por IP.
$location = get_user_location_by_ip();

function calcularEdad($fechaNacimiento) {
    $fechaNacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime(); // Fecha actual
    $edad = $hoy->diff($fechaNacimiento)->y; // Diferencia en años
    return $edad;
}

function tiempoTranscurrido($fecha) {
    $fecha = new DateTime($fecha);
    $ahora = new DateTime();
    $diferencia = $ahora->diff($fecha);

    if ($diferencia->y > 0) {
        return $diferencia->y . ' año' . ($diferencia->y > 1 ? 's' : '');
    }
    if ($diferencia->m > 0) {
        return $diferencia->m . ' mes' . ($diferencia->m > 1 ? 'es' : '');
    }
    if ($diferencia->d >= 7) {
        return floor($diferencia->d / 7) . ' sem';
    }
    if ($diferencia->d > 0) {
        return $diferencia->d . ' d';
    }
    if ($diferencia->h > 0) {
        return $diferencia->h . ' h';
    }
    if ($diferencia->i > 0) {
        return $diferencia->i . ' m';
    }
    return '0 m';
}
?>

<!DOCTYPE html>
<html lang="en">
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
        <link rel="stylesheet" href="/sources/bootstrap-5.3.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="/sources/nouislider-15.7.0/nouislider.min.css">
        <link rel="stylesheet" href="/sources/leaflet-1.9.4/leaflet.css" />
        <link rel="stylesheet" href="/sources/glightbox-3.3.0/css/glightbox.min.css">
        <link rel="stylesheet" href="/sources/choices-11.0.2/choices.min.css">
        <link rel="stylesheet" href="/assets/css/styles.css">
    </head>
    <body>  
        <!-- Loader -->
        <div id="loader" class="loader-wrapper" style="display: flex;">
            <div class="loader"></div>
        </div>

        <!-- Sidebar -->
        <div id="sidebar">
            <div class="sidebar-header">
                <h4>Buscador</h4>
            </div>
            <button id="nearMeButton" class="nearbutton">
            <i class="fa-solid fa-location-crosshairs"></i> Cerca de mí
            </button>
            <div class="sidebar-content">
                <!-- Categoría -->
                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoría</label>
                    <select id="categoria" class="form-select select-search">
                        <option value="todas">Todas</option>
                        <option value="escorts">Escorts</option>
                        <option value="gay">Gay</option>
                        <option value="gigolo">Gigoló</option>
                        <option value="masajes">Masajes</option>
                        <option value="trans">Trans</option>
                        <option value="webcam">Webcam</option>
                    </select>
                </div>

                <!-- Provincia -->
                <div class="mb-3">
                    <label for="provincia" class="form-label">Provincia</label>
                    <select id="provincia" class="form-select select-search">
                    </select>
                </div>

                <!-- Ciudad -->
                <div class="mb-3">
                    <label for="ciudad" class="form-label">Ciudad</label>
                    <select id="ciudad" class="form-select select-search">
                    </select>
                </div>

                <!-- Buscar -->
                <div class="mb-1">
                    <label for="buscar" class="form-label">Buscar</label>
                    <input type="text" id="buscarclave" class="form-control" placeholder="Buscar palabra...">
                </div>

                <!-- Rango de edad -->
                <div class="mb-2">
                    <label class="form-label edadlabel">Edad</label>
                    <div id="range-slider" style="margin: 20px 0;"></div>
                    <div class="d-flex justify-content-between" style="margin-top: -15px;">
                        <span class="d-none" id="edad-desde-label">18</span>
                        <span class="d-none" id="edad-hasta-label">60</span>
                        <span style="font-size: 12px;">Desde</span>
                        <span style="font-size: 12px;">Hasta</span>
                    </div>
                </div>  

                <!-- Botón -->
                <div>
                    <button id="aplicar-filtros" class="buttonfilter w-100">
                        <i class="fa-regular fa-magnifying-glass-location"></i> Aplicar filtros
                    </button>
                </div>
            </div>
        </div>


        <!-- Navbar -->
        <nav id="navbar" class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <button id="toggleSidebar" class="btn btn-outline-light"><i class="fas fa-bars"></i></button>
                <a class="navbar-brand logo" href="/">
                    <span class="marca"><b>Fanta<span class="redname">SexAnuncios</span></b>.com</span>
                    <span class="brand-logo">
                        <img src="/assets/img/logos/logo.png">
                    </span>
                    <p style="font-size: 0.8em; text-align: center; margin-top: -18px; font-family: 'Neonderthaw', cursive; margin-left: -60px; text-shadow: 0px 0px 3px black;">Tus fantasías cerca de ti</p>
                </a>
                <br>
                <div class="d-flex align-items-center">
                    <?php if ($isLoggedIn && $userType === 'advertiser'): ?>
                        <!-- Botón Publicar Anuncio para Advertisers -->
                        <a href="/anuncios/crear-anuncio.php" class="btn btn-warning me-2 glowing-button">
                            <i class="fa-regular fa-circle-plus"></i> Publicar anuncio
                        </a>
                    <?php endif; ?>

                    <?php if ($isLoggedIn): ?>
                        <!-- Contenedor de perfil -->
                        <div class="dropdown perfil">
                            <a href="#" class="d-flex align-items-center text-decoration-none text-light dropdown-toggle" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="conectado">•</span>
                                <img src="/assets/img/fotos/users/advertiser.webp" alt="Foto de perfil" class="rounded-circle me-2" width="32" height="32">
                                <span class="me-1"><?= $userName ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end text-small" aria-labelledby="userMenu">
                                <li><a class="dropdown-item" href="/perfil-advertiser/datos.php">Mi perfil</a></li>
                                <li><a class="dropdown-item" href="/perfil-user/configuracion.php">Configuración</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/php/backend/logout.php">Cerrar sesión</a></li>
                            </ul>
                        </div>
                        <?php if ($isLoggedIn && $userType === 'advertiser'): ?>
                            <!-- Mostrar créditos -->
                            <div class="ms-2 d-flex align-items-center creditosinfo">
                                <img src="/assets/img/fotos/iconos/credits.png" alt="Créditos" width="23" height="23" class="me-1">
                                <span>Créditos: <b><?= $userCredits ?></b></span>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Botones para usuarios no autenticados -->
                         <!-- Botón Publicar Anuncio para Advertisers -->
                        <a href="/auth/login.php" class="btn btn-warning me-2 glowing-button">
                            <i class="fa-regular fa-circle-plus"></i> Publicar anuncio
                        </a>
                        <a href="/auth/registro.php" class="btn btn-outline-light me-2" style="border-color: #aa3889;">
                            <i class="fa-solid fa-user-plus"></i> Registrarse
                        </a>
                        <a href="/auth/login.php" class="btn btn-outline-light loginboton">
                            Ingresar <i class="fa-solid fa-right-to-bracket"></i>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </nav>

        <!-- Main Content -->
        <div class="content">
            <div class="cinta">
                <div class="cinta-texto">
                    Las mejores escorts de la ciudad 💋 Atención al cliente 24/7 💋 Realiza tus pagos de manera virtual 💋 Las mejores putas de la zona
                </div>
            </div>

            <!-- Parrilla de anuncios -->
            <div class="anuncios-grid">

            <?php foreach ($anuncios as $anuncio): ?>
            <div 
                class="anuncio-card" 
                onclick="showPopup(this)"
                data-category="<?= htmlspecialchars($anuncio['categoria'], ENT_QUOTES, 'UTF-8'); ?>"
                data-title="<?= htmlspecialchars($anuncio['titulo'], ENT_QUOTES, 'UTF-8'); ?>"
                data-descripcion="<?= htmlspecialchars($anuncio['descripcion'], ENT_QUOTES, 'UTF-8'); ?>"
                data-latitude="<?= htmlspecialchars($anuncio['latitude'], ENT_QUOTES, 'UTF-8'); ?>"
                data-longitude="<?= htmlspecialchars($anuncio['longitude'], ENT_QUOTES, 'UTF-8'); ?>"
                data-city="<?= htmlspecialchars($anuncio['ciudad'], ENT_QUOTES, 'UTF-8'); ?>"
                data-country="<?= htmlspecialchars($anuncio['pais'], ENT_QUOTES, 'UTF-8'); ?>"
                data-flag="<?= htmlspecialchars($anuncio['bandera'], ENT_QUOTES, 'UTF-8'); ?>"
                data-locationtip="<?= htmlspecialchars($anuncio['locationtip'], ENT_QUOTES, 'UTF-8'); ?>"
                data-tar="<?= number_format($anuncio['tarifa'], 0); ?>"
                data-province="<?= htmlspecialchars($anuncio['provincia'], ENT_QUOTES, 'UTF-8'); ?>"
                data-age="<?= calcularEdad($anuncio['nacimiento']); ?>"
                data-principalPicture="<?= htmlspecialchars($anuncio['principal_picture'], ENT_QUOTES, 'UTF-8'); ?>"
                data-pictureProfile="<?= htmlspecialchars($anuncio['picture_profile'], ENT_QUOTES, 'UTF-8'); ?>"
                data-pictures="<?= htmlspecialchars($anuncio['pictures'], ENT_QUOTES, 'UTF-8'); ?>"
                data-name="<?= htmlspecialchars($anuncio['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                data-anuncio="<?= htmlspecialchars($anuncio['anuncio_id'], ENT_QUOTES, 'UTF-8'); ?>"
                data-sex="<?= htmlspecialchars($anuncio['sexo'], ENT_QUOTES, 'UTF-8'); ?>"
                data-indicativo="<?= htmlspecialchars($anuncio['indicativo'], ENT_QUOTES, 'UTF-8'); ?>"
                data-phone="<?= htmlspecialchars($anuncio['telefono'], ENT_QUOTES, 'UTF-8'); ?>"
                data-whatsapp="<?= $anuncio['whatsapp'] === null ? 'NO' : 'SI'; ?>"
                data-lang="<?= htmlspecialchars($anuncio['idiomas'], ENT_QUOTES, 'UTF-8'); ?>"
                data-dispo="<?= htmlspecialchars($anuncio['disponibilidad'], ENT_QUOTES, 'UTF-8'); ?>"
                data-times="<?= htmlspecialchars($anuncio['horarios'], ENT_QUOTES, 'UTF-8'); ?>"
                data-services="<?= htmlspecialchars($anuncio['servicios'], ENT_QUOTES, 'UTF-8'); ?>"
                data-updated="<?= htmlspecialchars((new DateTime($anuncio['updated_at']))->format('d/m/Y'), ENT_QUOTES, 'UTF-8'); ?>"
                data-vistas="<?= htmlspecialchars($anuncio['visitas'], ENT_QUOTES, 'UTF-8'); ?>"
            >
                <div class="anuncio-imagen">
                    <img 
                        src="<?= htmlspecialchars($anuncio['principal_picture'], ENT_QUOTES, 'UTF-8'); ?>" 
                        alt="<?= htmlspecialchars($anuncio['titulo'], ENT_QUOTES, 'UTF-8'); ?>"
                    >
                    <div class="anuncio-overlay">
                        <h4 class="anuncio-name"><?= htmlspecialchars($anuncio['nombre'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <a href="#" class="anuncio-badge timeclock" title="Tiempo de publicación">Hace <span class="timecount"><?= tiempoTranscurrido($anuncio['created_at']); ?></span></a>
                    </div>
                    <div class="anuncio-detalle">
                        <h5 class="anuncio-titulo"><?= htmlspecialchars($anuncio['titulo'], ENT_QUOTES, 'UTF-8'); ?></h5>
                        <p class="anuncio-ubicacion">
                            <i class="fa-solid fa-location-dot"></i> 
                            <?= htmlspecialchars($anuncio['ciudad'], ENT_QUOTES, 'UTF-8'); ?> 
                            <span class="distanciaval"></span>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>


            </div>

        </div>

        <!-- Popup -->
        <div id="overlay" class="popup-overlay"></div>
        <div id="popup" class="popup-container">
            <div class="popup-content">
                <div class="row" style="width: 100%;">
                    <div class="col-8">
                        <main class="gallery">
                            <article>
                                <img class="glightbox" src="/assets/img/fotos/karla1.webp">
                            </article>
                            <article>
                                <img class="glightbox" src="/assets/img/fotos/mujer1.webp">
                            </article>
                            <article>
                                <img class="glightbox" src="/assets/img/fotos/karla3.webp">
                            </article>
                            <article>
                                <img class="glightbox" src="/assets/img/fotos/karla2.webp">
                            </article>
                        </main>

                        <div class="card shadow-lg border-0 rounded-4 p-4">
                            <h1 class="card-title text-center mb-3 fs-4 encabezadoanuncio">
                                <img src="/assets/img/logos/logo.png" width="30px"/> <b id="tituloanuncio"></b>
                            </h1>
                            <p class="user-profile-card descripcionanuncio">
                            </p>

                            <h4 class="m-auto mb-2" style="width: 95%; text-align: left;">
                                <i class="fa-solid fa-map-location-dot" style="color: #a90b35;"></i> Ubicación: <span id="ciudadlocation" style="color: #a90b35;"></span>
                            </h4>
                            <div id="map" class="user-profile-card mapaanuncio" style="height: 300px;"></div>
                            <p class="text-center mt-3 locationanuncio">
                            </p>

                            <div class="titleservices">
                                <i class="fa-solid fa-concierge-bell" style="color: #ffcf4f;"></i> Servicios que realizo
                            </div>
                            <div class="user-profile-card" style="width: 95%; max-width: 95%; margin-bottom: 30px;">
                                <div class="services-grid">
                                </div>
                        </div>

                        <div class="titleservices">
                                <img src="/assets/img/fotos/iconos/chest.webp" width="25px"/> Cofre de Notas
                        </div>
                        <div class="user-profile-card" style="width: 95%; max-width: 95%; min-height: 280px; height: auto; max-height: 600px;">
                        </div>



                            
                        </div>

                    </div>

                    <div class="col-2">
                        <div class="card popup-perfil shadow-lg">
                            <img src="/assets/img/fotos/perfil1.webp" class="card-img-top mx-auto popup-picture" id="profilepicture" alt="Profile Picture" style="width: 150px;">
                            <div class="card-body">
                                <h5 class="card-title popup-name">Karla</h5>

                                <div class="tarifa-creative">
                                    <span class="tarifa-label">
                                        ¡Desde: <e id="tarifa"></e>€!
                                    </span>
                                </div>

                                <div class="user-profile-card">
                                    <div class="profile-item" data-visible="true">
                                        <span class="label"><i class="fa-solid fa-venus-mars"></i> Sexo</span>
                                        <span class="value" id="sexoval">Mujer</span>
                                    </div>
                                    <div class="profile-item" data-visible="true">
                                        <span class="label"><i class="fa-solid fa-heart"></i> Categoría</span>
                                        <span class="value" id="categoryval">Escort</span>
                                    </div>
                                    <div class="profile-item" data-visible="true">
                                        <span class="label"><i class="fa-solid fa-cake-candles"></i> Edad</span>
                                        <span class="value" id="ageval">28 años</span>
                                    </div>
                                    <div class="profile-item" data-visible="true">
                                        <span class="label"><i class="fa-solid fa-earth-americas"></i> Origen</span>
                                        <span class="value" id="originval" style="align-items: center; display: flex; gap: 5px;">
                                            <img src="https://flagsapi.com/ES/shiny/32.png" width="23px"/> España
                                        </span>
                                    </div>
                                    <div class="profile-item" data-visible="true">
                                        <span class="label"><i class="fa-solid fa-square-phone"></i> Teléfono</span>
                                        <span class="value" id="phoneval">613329886</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-3">
                                <button class="btn popup-button"><i class="fa-brands fa-whatsapp"></i> WhatsApp</button>
                                <button class="btn popup-button"><i class="fa-solid fa-phone"></i> Llamar</button>
                                </div>

                                <div class="user-profile-card">
                                    <div>
                                        <span class="label" style="display: block; text-align: left;"><i class="fa-solid fa-language"></i> Idiomas</span>
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
                                    <span class="label" style="display: block; text-align: left;"><i class="fa-solid fa-calendar-check"></i> Disponibilidad</span>
                                    
                                    <!-- Días de la semana -->
                                    <div class="availability-days">
                                    </div>

                                    <!-- Momentos del día -->
                                    <div class="availability-times mt-3">
                                    </div>
                                </div>


                                <div class="user-profile-card">
                                    <div style="display: inline-block; padding: 0px 0px 5px; border-bottom: 1px solid #dbdbdb; width: 100%;">
                                        <div class="badge premium">
                                            PREMIUM
                                        </div>
                                        <div class="badge autosubidas">
                                        <i class="fa-regular fa-clock-rotate-left"></i> Autorenueva
                                        </div>
                                    </div>
                                    <div class="row align-items-center" style="justify-content: center; gap: 6px; margin-top: 5px; border-bottom: 1px solid #dbdbd9; padding: 0px 0px 5px;">
                                        <!-- Columna 1: ID del Anuncio -->
                                        <div class="col-6" style="font-size: 1.3rem;">
                                            <div class="row">
                                                <div class="badge text-white p-3 w-100" style="padding: 20px 5px !important; text-align: center; background: linear-gradient(76deg, #940a2f, #a23481);">
                                                    <span class="fw-bold d-block mb-1">ID Anuncio</span>
                                                    <span class="announcement-id" id="announcement-id">xb4dol10</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Columna 2: Fecha de subida y Visitas -->
                                        <div class="col-5" style="font-size: 1rem;">
                                            <div class="row" style="margin-bottom: 2px;">
                                                <!-- Fecha de Subida -->
                                                    <div class="badge text-white p-2 w-100 costadosbox">
                                                        <span class="d-block fw-bold"><i class="fa-solid fa-clock"></i> Creación</span>
                                                        <span id="upload-date">15/01/2024</span>
                                                    </div>
                                            </div>
                                            <div class="row">
                                                <!-- Visitas -->
                                                    <div class="badge text-white p-2 w-100 costadosbox">
                                                        <span class="d-block fw-bold"><i class="fa-solid fa-eye"></i> Vistas</span>
                                                        <span id="view-count">124</span>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botón de Compartir -->
                                <div class="d-flex justify-content-between mt-3">
                                    <button class="btn popup-button" onclick="openShareModal()" style="margin: auto;">
                                        <i class="fa-solid fa-share"></i> Compartir
                                    </button>
                                </div>
                                </div>


                                <!-- Modal de compartir -->
                                <div class="modal" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="shareModalLabel">Compartir Anuncio</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <button class="btn btn-outline-primary" onclick="shareOnWhatsApp()">
                                                    <i class="fa-brands fa-whatsapp"></i> WhatsApp
                                                </button>
                                                <button class="btn btn-outline-primary" onclick="shareOnFacebook()">
                                                    <i class="fa-brands fa-facebook"></i> Facebook
                                                </button>
                                                <button class="btn btn-outline-primary" onclick="copyLink()">
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

                <button id="close-popup" class="cerrarboton" title="Cerrar">X</button>
            </div>
        </div>

        <!-- Footer -->
         <div class="include-footer">
            <?php include 'footer/footer.php'; ?>
         </div>
        
        <script src="/sources/jquery-3.7.1/jquery-3.7.1.min.js"></script>
        <script src="/sources/bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
        <script src="/sources/sweetalert-2/sweetalert2@11.js"></script>
        <script src="/sources/nouislider-15.7.0/nouislider.min.js"></script>
        <script src="/sources/leaflet-1.9.4/leaflet.js"></script>
        <script src="/sources/glightbox-3.3.0/js/glightbox.min.js"></script>
        <script src="/sources/choices-11.0.2/choices.min.js"></script>
        <script src="/assets/js/sidebar.js"></script>

        <script>
            $(document).ready(function () {
                // Mostrar el loader al iniciar la carga
                $('#loader').fadeIn();
                //checkUserProfile();

                // Ocultar el loader cuando la página haya cargado completamente
                $(window).on('load', function () {
                    $('#loader').fadeOut();
                });
            });

            jQuery.fn.extend({
                showPop: function () {
                    return this.css({ opacity: 0, transform: "scale(1)" }).show().animate(
                        { opacity: 1 },
                        {
                            duration: 300,
                            step: (now) => {
                                const scale = 0.8 + now * 0.2; // Escala de 0.8 a 1
                                this.css("transform", `scale(${scale})`);
                            }
                        }
                    );
                },
                hidePop: function () {
                    return this.animate(
                        { opacity: 0 },
                        {
                            duration: 300,
                            step: (now) => {
                                const scale = 1 - now * 0.2; // Escala de 1 a 0.8
                                this.css("transform", `scale(${scale})`);
                            },
                            complete: () => {
                                this.hide();
                            }
                        }
                    );
                }
            });

            function checkUserProfile() {
                fetch('php/backend/validate_profile.php', {
                    method: 'POST', 
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        Swal.fire({
                            title: '¡Perfil incompleto!',
                            text: data.message,
                            icon: 'warning',
                            confirmButtonText: 'Ir ahora',
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    }
                })
                .catch(error => {
                    console.error('Error al verificar el perfil:', error);
                });
            }

            let lightbox = GLightbox({
                        selector: '.glightbox', // Clase que identifica las imágenes
                        touchNavigation: true, // Navegación táctil
                        loop: true,            // Ciclo infinito de imágenes
                        fullscreen: true,      // Botón de pantalla completa
                    });


            function showPopup(element) {

                // Extraer valores de los atributos data-* del elemento
                const data = {
                    title: element.getAttribute('data-title'),
                    description: element.getAttribute('data-descripcion'),
                    location: {
                        lat: parseFloat(element.getAttribute('data-latitude')),
                        lng: parseFloat(element.getAttribute('data-longitude')),
                    },
                    city: element.getAttribute('data-city'),
                    country: element.getAttribute('data-country'),
                    bandera: element.getAttribute('data-flag'),
                    locationtip: element.getAttribute('data-locationtip'),
                    tarifa: element.getAttribute('data-tar'),
                    nombre: element.getAttribute('data-name'),
                    sexo: element.getAttribute('data-sex'),
                    categoria: element.getAttribute('data-category'),
                    edad: element.getAttribute('data-age'),
                    telefono: element.getAttribute('data-phone'),
                    indicativo: element.getAttribute('data-indicativo'),
                    idiomas: element.getAttribute('data-lang'),
                    disponibilidad: element.getAttribute('data-dispo'),
                    horarios: element.getAttribute('data-times'),
                    servicios: element.getAttribute('data-services'),
                    anuncio: element.getAttribute('data-anuncio'),
                    actualizado: element.getAttribute('data-updated'),
                    vistas: element.getAttribute('data-vistas'),
                    pictures: element.getAttribute('data-pictures'),
                    imgprincipal: element.getAttribute('data-principalPicture'),
                    profilepicture: element.getAttribute('data-pictureProfile')
                };

                // Abrir el popup
                const popup = document.getElementById('popup');
                popup.style.display = 'block'; // Mostrar popup antes de aplicar animación
                popup.classList.remove('hide'); // Quitar clase de cierre
                popup.classList.add('show');   // Agregar clase de apertura
                const popupContent = document.querySelector('.popup-content');
                if (popupContent) {
                    popupContent.scrollTop = 0; // Establece el scroll en la parte superior
                }
                $("#overlay").fadeIn();

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

                    lightbox.destroy();

                    lightbox = GLightbox({
                        selector: '.glightbox', // Clase que identifica las imágenes
                        touchNavigation: true, // Navegación táctil
                        loop: true,            // Ciclo infinito de imágenes
                        fullscreen: true,      // Botón de pantalla completa
                    });


                // Animaciones adicionales (idénticas a las anteriores)
                setTimeout(() => {

                    document.querySelectorAll('.profile-item').forEach(item => {
                        if (item.getAttribute('data-visible') === 'false') {
                            item.style.display = 'none';
                        }
                    });

                    // Ahora aplicar las animaciones
                    articles.forEach(article => {
                        article.classList.remove("reveal");
                        void article.offsetWidth; // Forzar un reflow
                    });

                    articles.forEach((article, index) => {
                        setTimeout(() => {
                            article.classList.add("reveal");
                        }, index * 250);
                    });

                    // Actualizar contenido dinámico
                if (data) {
                    // Actualizar título y descripción
                    document.querySelector('#tituloanuncio').textContent = data.title;
                    document.querySelector('.descripcionanuncio').textContent = data.description;
                    document.querySelector('.locationanuncio').innerHTML = data.locationtip;
                    document.querySelector('#ciudadlocation').innerHTML = data.city;
                    document.querySelector('#tarifa').innerHTML = data.tarifa;
                    document.querySelector('#profilepicture').src = data.profilepicture;
                    document.querySelector('.popup-name').innerHTML = data.nombre;
                    document.querySelector('#sexoval').innerHTML = data.sexo;
                    document.querySelector('#categoryval').innerHTML = data.categoria;
                    document.querySelector('#ageval').innerHTML = data.edad + " años";
                    document.querySelector('#phoneval').innerHTML = data.indicativo + " " + data.telefono;
                    document.querySelector('#originval').innerHTML = `<img src="https://flagsapi.com/${data.bandera}/shiny/32.png" width="23px"/> ${data.country}`;
                    document.querySelector('#announcement-id').innerHTML = data.anuncio;
                    document.querySelector('#upload-date').innerHTML = data.actualizado;
                    document.querySelector('#view-count').innerHTML = data.vistas;

                    const idiomasJson = data.idiomas;
                    let idiomas;
                    idiomas = JSON.parse(idiomasJson);

                    // Obtener el contenedor en el DOM
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

                    // Contenedor en el DOM para los servicios
                    const servicesContainer = document.querySelector('.services-grid');

                    // Lista de todos los servicios posibles
                    const serviciosDisponibles = [
                        "24h", "Salidas", "Novios", "Masajes", "Besos con lengua", "Francés",
                        "FR Natural", "Parejas", "Beso negro", "Anal", "Tríos", "Lésbico",
                        "Sado BDSM", "Lluvia dorada", "Fiestera"
                    ];

                    // Convertir data.servicios de JSON a arreglo
                    let serviciosUsuario;
                    try {
                        serviciosUsuario = JSON.parse(data.servicios); // Convertir JSON a objeto
                    } catch (error) {
                        console.error("Error al parsear servicios:", error);
                        serviciosUsuario = []; // Inicializar vacío si hay error
                    }

                    // Generar dinámicamente el contenido
                    servicesContainer.innerHTML = ''; // Limpiar contenido previo

                    serviciosDisponibles.forEach(servicio => {
                        const disponible = serviciosUsuario.includes(servicio); // Verificar si el servicio está disponible

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
                }
                }, 500);
            }




// Cerrar el popup
document.getElementById('close-popup').addEventListener('click', () => {
    const articles = Array.from(document.querySelectorAll("article"));
    const popup = document.getElementById('popup');
    popup.classList.remove('show'); // Quita la animación de apertura
    popup.classList.add('hide');   // Agrega la animación de cierre

    // Después de la animación, esconde el popup y el overlay
    setTimeout(() => {
        popup.style.display = 'none';
        $("#overlay").hide();
    }, 500); // Tiempo en ms, debe coincidir con la duración de la animación


    // Reiniciar las clases para que la animación se vuelva a aplicar
    articles.forEach(article => {
        article.classList.remove("reveal");
        void article.offsetWidth; // Forzar reflujo para reiniciar la animación
    });

    const popupContent = document.querySelector('.popup-content');
    if (popupContent) {
        popupContent.scrollTop = 0; // Establece el scroll en la parte superior
    }
});





        const slider = document.getElementById('range-slider');
        const desdeLabel = $('#edad-desde-label');
        const hastaLabel = $('#edad-hasta-label');

        // Inicializar noUiSlider
        noUiSlider.create(slider, {
            start: [18, 60], // Valores iniciales
            connect: true,
            range: {
                min: 18, // Valor mínimo
                max: 60  // Valor máximo
            },
            step: 1, // Incrementos de 1
            tooltips: [true, true], // Muestra tooltips con los valores
            format: {
                to: value => parseInt(value), // Formateo para valores enteros
                from: value => parseInt(value)
            }
        });

        // Actualizar etiquetas en tiempo real
        slider.noUiSlider.on('update', function (values) {
            desdeLabel.text(values[0]);
            hastaLabel.text(values[1]);
        });

            // Manejo del botón de aplicar filtros
            $('#aplicar-filtros').on('click', function () {
                const filtros = {
                    categoria: $('#categoria').val(),
                    provincia: $('#provincia').val(),
                    ciudad: $('#ciudad').val(),
                    edadDesde: $('#edad-desde-label').html(),
                    edadHasta: $('#edad-hasta-label').html(),
                    buscar: $('#buscar').val(),
                };
            });

            function openShareModal() {
                var modal = new bootstrap.Modal(document.getElementById('shareModal'));
                modal.show();
            }

            function shareOnWhatsApp() {
                var url = window.location.href; // Obtén la URL actual
                window.open('https://wa.me/?text=' + encodeURIComponent(url), '_blank');
            }

            function shareOnFacebook() {
                var url = window.location.href; // Obtén la URL actual
                window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url), '_blank');
            }

            function copyLink() {
                var url = window.location.href; // Obtén la URL actual
                navigator.clipboard.writeText(url).then(function() {
                    alert("Enlace copiado al portapapeles!");
                }, function(err) {
                    alert("Error al copiar el enlace.");
                });
            }

// Obtener elementos del DOM
const filtroBoton = document.getElementById("aplicar-filtros");
const anunciosGrid = document.querySelector(".anuncios-grid");
const selectCategoria = document.getElementById("categoria");
const selectProvincia = document.getElementById("provincia");
const selectCiudad = document.getElementById("ciudad");
const buscarClave = document.getElementById("buscarclave");
const rangeSlider = document.getElementById("range-slider");
var edadDesde = $('#edad-desde-label').html();
var edadHasta = $('#edad-hasta-label').html();

// Instancias de Choices.js para reiniciar
let provinciaChoices, ciudadChoices; 

// Flag para verificar si Choices ya está inicializado
let choicesInicializados = false;

// Filtrar anuncios
const filtrarAnuncios = () => {
    const categoria = selectCategoria.value;
    const provincia = selectProvincia.value;
    const ciudad = selectCiudad.value;
    const keyword = buscarClave.value.toLowerCase();
    var edadDesde = $('#edad-desde-label').html();
    var edadHasta = $('#edad-hasta-label').html();

    // Mostrar loader
    $("#loader").fadeIn();

    // Ocultar anuncios que no coincidan
    const anuncios = document.querySelectorAll(".anuncio-card");
    anuncios.forEach(anuncio => {
        const anuncioCategoria = anuncio.dataset.category;
        const anuncioCiudad = anuncio.dataset.city.toLowerCase();
        const anuncioProvincia = anuncio.dataset.province.toLowerCase();
        const anuncioTitle = anuncio.dataset.title.toLowerCase();
        const age = parseInt(anuncio.dataset.age);
        const coincideCategoria = categoria === "todas" || anuncioCategoria === categoria;
        const coincideProvincia = provincia === "todas" || anuncioProvincia === provincia;
        const coincideCiudad = ciudad === "todas" || anuncioCiudad === ciudad;
        const coincideEdad = age >= edadDesde && age <= edadHasta;
        const coincideClave = !keyword || anuncioTitle.includes(keyword);

        if (coincideCategoria && coincideProvincia && coincideCiudad && coincideEdad && coincideClave) {
            $(anuncio).showPop();
        } else {
            $(anuncio).hidePop();
        }
    });

    // Simular carga de 1 segundo
    setTimeout(() => {
        $("#loader").fadeOut();
    }, 500);

    // Actualizar query en la URL
    actualizarQueryURL({ categoria, provincia, ciudad, edadDesde, edadHasta, keyword });
};

// Actualizar query en URL
const actualizarQueryURL = ({ categoria, provincia, ciudad, edadDesde, edadHasta, keyword }) => {
    const params = new URLSearchParams();
    if (categoria && categoria !== "todas") params.set("categoria", categoria);
    if (provincia && provincia !== "todas") params.set("provincia", provincia);
    if (ciudad && ciudad !== "todas") params.set("ciudad", ciudad);
    if (edadDesde && edadDesde !== "18") params.set("edadDesde", edadDesde);
    if (edadHasta && edadHasta !== "60") params.set("edadHasta", edadHasta);
    if (keyword) params.set("buscar", keyword);

    window.history.replaceState({}, "", `${window.location.pathname}?${params.toString()}`);
};

// Función para llenar las provincias dinámicamente
const llenarProvincias = () => {
    const anuncios = document.querySelectorAll(".anuncio-card");
    const provinciasUnicas = new Set();

    // Recopila las provincias únicas de los anuncios
    anuncios.forEach(anuncio => {
        const provincia = anuncio.dataset.province.toLowerCase();  // Usar .toLowerCase() para evitar problemas de mayúsculas/minúsculas
        if (provincia) provinciasUnicas.add(provincia);
    });

    // Limpiar el select de provincias antes de agregar nuevas opciones
    selectProvincia.innerHTML = '<option value="todas">Todas</option>';

    // Llena el selector de provincias
    provinciasUnicas.forEach(provincia => {
        selectProvincia.innerHTML += `<option value="${provincia}">${provincia.charAt(0).toUpperCase() + provincia.slice(1)}</option>`;
    });

    // Reinicia las ciudades (asegurándote de que las ciudades se actualicen al iniciar)
    llenarCiudades(); // Para llenar las ciudades iniciales

    // Reiniciar Choices después de actualizar el contenido
    reiniciarChoices();
};

// Función para llenar las ciudades dinámicamente según la provincia seleccionada
const llenarCiudades = () => {
    const anuncios = document.querySelectorAll(".anuncio-card");
    const provinciaSeleccionada = selectProvincia.value.toLowerCase();  // Usar .toLowerCase() para asegurar comparación consistente
    const ciudadesUnicas = new Set();

    // Recopila las ciudades únicas basadas en la provincia seleccionada
    anuncios.forEach(anuncio => {
        const anuncioProvincia = anuncio.dataset.province.toLowerCase();  // .toLowerCase() para comparación insensible
        const anuncioCiudad = anuncio.dataset.city.toLowerCase();  // .toLowerCase() para comparación insensible

        // Solo se agregan las ciudades que coinciden con la provincia seleccionada
        if (
            (provinciaSeleccionada === "todas" || anuncioProvincia === provinciaSeleccionada) &&
            anuncioCiudad
        ) {
            ciudadesUnicas.add(anuncioCiudad);
        }
    });

    // Limpiar el select de ciudades antes de agregar nuevas opciones
    selectCiudad.innerHTML = '<option value="todas">Todas</option>';  // Restablecer las opciones de ciudades

    // Llena el selector de ciudades
    ciudadesUnicas.forEach(ciudad => {
        selectCiudad.innerHTML += `<option value="${ciudad}">${ciudad.charAt(0).toUpperCase() + ciudad.slice(1)}</option>`;
    });

    // Reiniciar Choices después de actualizar el contenido
    reiniciarChoices();
};

// Función para reinicializar Choices
const reiniciarChoices = () => {
    // Verificar si Choices ya ha sido inicializado
    if (!choicesInicializados) {
        provinciaChoices = new Choices(selectProvincia, {
            searchEnabled: true,
            shouldSort: false,
            searchPlaceholderValue: "Buscar...",
            itemSelectText: "Provincia"
        });

        ciudadChoices = new Choices(selectCiudad, {
            searchEnabled: true,
            shouldSort: false,
            searchPlaceholderValue: "Buscar...",
            itemSelectText: "Ciudad"
        });

        categoriaChoices = new Choices(selectCategoria, {
            searchEnabled: true,
            shouldSort: false,
            searchPlaceholderValue: "Buscar...",
            itemSelectText: "Categoría"
        });

        // Marcar como inicializado
        choicesInicializados = true;
    } else {
        // Si ya está inicializado, solo actualizamos las opciones de Choices
        provinciaChoices.setChoices(getChoicesArray(selectProvincia), 'value', 'label', true);
        ciudadChoices.setChoices(getChoicesArray(selectCiudad), 'value', 'label', true);
    }
};

// Función para generar el array de opciones para Choices
const getChoicesArray = (selectElement) => {
    const options = Array.from(selectElement.options);
    return options.map(option => ({
        value: option.value,
        label: option.textContent
    }));
};

// Inicializar las provincias y ciudades al cargar la página
llenarProvincias();

// Aplicar filtros desde la URL
const aplicarFiltrosDesdeURL = () => {
    const params = new URLSearchParams(window.location.search);
    if (params.has("categoria")) selectCategoria.value = params.get("categoria");
    if (params.has("provincia")) selectProvincia.value = params.get("provincia");
    if (params.has("ciudad")) selectCiudad.value = params.get("ciudad");
    if (params.has("edadDesde")) edadDesde = parseInt(params.get("edadDesde"));
    if (params.has("edadHasta")) edadHasta = parseInt(params.get("edadHasta"));
    if (params.has("buscar")) buscarClave.value = params.get("buscar");

    // Forzar actualización de los selects de Choices con los valores obtenidos de la URL
    if (provinciaChoices) provinciaChoices.setChoiceByValue(selectProvincia.value);
    if (categoriaChoices) categoriaChoices.setChoiceByValue(selectCategoria.value);

    llenarCiudades();

    rangeSlider.noUiSlider.set([edadDesde, edadHasta]);
    filtrarAnuncios();
};

let cercaDeMiActivo = false;
let userLat = null;
let userLon = null;


// Ejecutar filtros desde la URL
aplicarFiltrosDesdeURL();

// Event Listener para el botón de filtro
filtroBoton.addEventListener('click', () => {
    aplicarFiltrosConOrden(userLat, userLon, cercaDeMiActivo);
});


// Actualizar las ciudades cuando cambia la provincia
selectProvincia.addEventListener("change", llenarCiudades);


// Función para calcular la distancia entre dos coordenadas usando Haversine
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radio de la Tierra en km
    const toRad = (value) => (value * Math.PI) / 180;
    
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a = 
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    
    return R * c; // Distancia en km
}

// Función para actualizar las distancias en los anuncios
function updateDistances(userLat, userLon) {
    const anuncios = document.querySelectorAll('.anuncio-card');
    
    anuncios.forEach((anuncio) => {
        const lat = parseFloat(anuncio.dataset.latitude);
        const lon = parseFloat(anuncio.dataset.longitude);
        
        // Calcular distancia
        const distance = calculateDistance(userLat, userLon, lat, lon);
        
        // Mostrar distancia en el HTML
        const distanciaSpan = anuncio.querySelector('.distanciaval');
        distanciaSpan.textContent = `(${distance.toFixed(1)} km)`;
    });
}

// Función para manejar el botón "Cerca de mí"
document.getElementById('nearMeButton').addEventListener('click', function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;


                // Aplicar orden y filtros
                aplicarFiltrosConOrden(latitude, longitude, true);
            },
            function(error) {
                console.error("Error al obtener ubicación:", error);
                alert("No se pudo obtener tu ubicación. Por favor, verifica tus permisos.");
            }
        );
    } else {
        console.error("Geolocalización no es soportada por este navegador.");
        alert("Tu navegador no soporta la geolocalización.");
    }
});

const ordenarAnunciosPorDistancia = (userLat, userLon) => {
    if (userLat === null || userLon === null) return;

    const anuncios = document.querySelectorAll('.anuncio-card');
    const anunciosArray = Array.from(anuncios);

    // Ordenar anuncios por distancia
    const anunciosOrdenados = anunciosArray.sort((a, b) => {
        const distA = calculateDistance(
            userLat, userLon,
            parseFloat(a.dataset.latitude),
            parseFloat(a.dataset.longitude)
        );
        const distB = calculateDistance(
            userLat, userLon,
            parseFloat(b.dataset.latitude),
            parseFloat(b.dataset.longitude)
        );
        return distA - distB;
    });

    // Reordenar el DOM sin eliminar ni recrear elementos
    const anunciosGrid = document.querySelector('.anuncios-grid');
    anunciosOrdenados.forEach(anuncio => anunciosGrid.appendChild(anuncio));

    // Actualizar las distancias mostradas
    updateDistances(userLat, userLon);
    filtrarAnuncios();
};


const aplicarFiltrosConOrden = (userLat, userLon, cercaDeMiActivo) => {
    // Primero, ordenar los anuncios si "Cerca de Mí" está activo
    if (cercaDeMiActivo) {
        ordenarAnunciosPorDistancia(userLat, userLon);
    } else {
        filtrarAnuncios();
    }
};


        </script>

<?php if ($location): ?>
    <script>
        const userLocation = {
            latitude: <?= json_encode($location['latitude']) ?>,
            longitude: <?= json_encode($location['longitude']) ?>
        };

        // Actualizar distancias al cargar la página
        updateDistances(userLocation.latitude, userLocation.longitude);
    </script>
<?php endif; ?>
    </body>
</html>