<?php
require '../php/backend/auth.php';
require '../php/backend/get_credits.php';
require_once '../php/backend/config.php';

// Verifica si el usuario está logeado
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userType = $isLoggedIn ? $_SESSION['user_type'] : null;
$userName = isset($_SESSION['user_email']) ? explode('@', $_SESSION['user_email'])[0] : "Usuario";

$userId = $_SESSION['user_id'];
$userCredits = getUserCredits($userId, $pdo);

?>

<!DOCTYPE html>
<html lang="es">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Crear anuncio • FantaSexAnuncios.com 💋</title>
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
        <link rel="stylesheet" href="/sources/leaflet-1.9.4/leaflet.css" />
        <link rel="stylesheet" href="/sources/choices-11.0.2/choices.min.css">
        <link rel="stylesheet" href="/sources/cropper-1.6.2/cropper.min.css">
        <link rel="stylesheet" href="/assets/css/styles.css">
        <link rel="stylesheet" href="/assets/css/ads.css">
</head>
<body>
<!-- Navbar -->
<nav id="navbar" class="navbar navbar-expand-lg navbar-dark w-100" style="left: 0;">
            <div class="container-fluid">
                <a class="navbar-brand logo" href="/">
                    <span class="marca"><b>Fanta<span class="redname">SexAnuncios</span></b>.com</span>
                    <span class="brand-logo">
                        <img src="/assets/img/logos/logo.png">
                    </span>
                    <p style="font-size: 0.8em; text-align: center; margin-top: -18px; font-family: 'Neonderthaw', cursive; margin-left: -60px; text-shadow: 0px 0px 3px black;">Tus fantasías cerca de ti</p>
                </a>
                <br>
                <div class="d-flex align-items-center">

                <?php if ($isLoggedIn): ?>
                    <!-- Contenedor de perfil -->
                    <div class="dropdown perfil">
                        <a href="#" class="d-flex align-items-center text-decoration-none text-light dropdown-toggle" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="conectado">•</span>
                            <img src="/assets/img/fotos/users/advertiser.webp" alt="Foto de perfil" class="rounded-circle me-2" width="32" height="32">
                            <span class="me-1"><?= $userName ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end text-small" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="../perfil-user/perfil.php">Mi perfil</a></li>
                            <li><a class="dropdown-item" href="../perfil-user/configuracion.php">Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../php/backend/logout.php">Cerrar sesión</a></li>
                        </ul>
                    </div>
                    <!-- Mostrar créditos -->
                    <div class="ms-2 d-flex align-items-center creditosinfo">
                                <img src="/assets/img/fotos/iconos/credits.png" alt="Créditos" width="23" height="23" class="me-1">
                                <span>Créditos: <b><?= $userCredits ?></b></span>
                            </div>
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
</nav  >

<div id="content" class="content" style="margin-left: 0; position: relative;">
    <div class="container">
        <form id="createAdForm" method="POST" enctype="multipart/form-data">

            <div id="carousel" class="carousel-container">
                <!-- Progress Bar -->
                <div class="progress-bar">
                    <div class="progress" id="progress"></div>
                </div>


                <div class="card mb-3 active">
                    <div class="card-header cabeceros">
                        <div class="seccion">• <i class="fa-solid fa-address-card"></i> Información •</div>
                        <div>Cuéntanos, ¿Quién eres?</div>
                    </div>
                    <div class="card-body">
                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label">¿Cuál es tu nombre?</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ejemplo: Karla" required autocomplete="off">
                        </div>

                        <!-- Sexo -->
                        <div class="mb-3">
                            <label for="sexo" class="form-label">¿Cuál es tu sexo?</label>
                            <select class="form-select pointer" id="sexo" name="sexo" required>
                                <option disabled selected>Selecciona tu Sexo</option>
                                <option value="Mujer">Mujer</option>
                                <option value="Hombre">Hombre</option>
                                <option value="Trans">Trans</option>
                            </select>
                        </div>

                        <!-- Fecha de Nacimiento -->
                        <div class="mb-3">
                            <label for="nacimiento" class="form-label">¿Cuándo naciste<span class="nameport"></span>?</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="nacimiento" name="nacimiento" required>
                                <span class="input-group-text" id="edad">Edad: 0 años</span>
                            </div>
                        </div>

                        <!-- País de Nacimiento -->
                        <div class="mb-3">
                            <label for="pais" class="form-label">¿En dónde naciste?</label>
                            <div class="input-group">
                                <span class="input-group-text" id="flagcountry">
                                    <img src="https://flagsapi.com/AF/shiny/32.png" width="23px"/>
                                </span>
                                <select class="form-select pointer" id="pais" name="pais" required></select>
                                <input type="hidden" name="bandera" id="bandera" value="AF"/>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Idiomas -->
                <div id="idiomasContainer" class="card mb-3">
                    <div class="card-header cabeceros">
                        <span class="seccion">• <i class="fa-solid fa-language"></i> Idiomas •</span>
                        <span>¿Qué idiomas manejas<span class="nameport"></span>?</span>
                    </div>
                    <div class="card-body cardidioma">
                        <input type="text" id="verificacion" style="display: none;"/>
                        <button type="button" id="addIdioma" class="d-block m-auto btn btn-primary"><i class="fa-regular fa-circle-plus"></i> Añadir idiomas</button>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header cabeceros">
                        <span class="seccion">• <i class="fa-solid fa-rectangle-ad"></i> Anuncio •</span>
                        <span>Ok<span class="nameport"></span>, configuremos tu anuncio</span>
                    </div>
                    <div class="card-body">

                        <!-- Categoría -->
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoría</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <img src="/assets/img/logos/logo.png" width="23px"/>
                                </span>
                                <select class="form-select pointer" id="categoria" name="categoria" required>
                                    <option value="Escorts">Escorts</option>
                                    <option value="Masajes">Masajes</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tarifa mínima -->
                        <div class="mb-3">
                            <label for="tarifa" class="form-label">¿Cuál es tu tarifa mínima?</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="tarifa" name="tarifa" required>
                                <span class="input-group-text"><i class="fa-solid fa-euro-sign" style="margin-right: 5px;"></i> Euros</span>
                            </div>
                        </div>

                        <!-- Titulo -->
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Titulo</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
                        </div>

                        <!-- Número de Contacto -->
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Número de Contacto</label>
                            <div class="input-group">
                                <span class="input-group-text" id="flagindicativo">
                                    <img src="https://flagsapi.com/AF/shiny/32.png" width="23px"/>
                                </span>
                                <select class="form-select pointer" style="max-width: 40%;" id="indicativo" name="indicativo" required></select>
                                <input class="form-control" type="number" name="telefono" id="telefono" style="margin: 0 3px; max-width: 100%;" placeholder="Número de teléfono"/>
                            </div><br>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <img src="/assets/img/fotos/iconos/whatsapp-icon.svg" width="23px"/>
                                    ¿Atiendes WhatsApp en este número?
                                </span>
                                <span class="input-group-text">
                                    <b style="margin-right: 5px; pointer-events: none;">Sí</b> 
                                    <input class="form-check m-auto pointer" type="checkbox" name="whatsapp"/>
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Provincia y Ciudad -->
                <div class="card mb-3">
                    <div class="card-header cabeceros">
                        <span class="seccion">• <i class="fa-solid fa-location-dot"></i> Ubicación •</span>
                        <span>¿En dónde te encuentras?</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="provincia" class="form-label">Provincia</label>
                                <input type="text" class="form-control" id="provincia" name="provincia" placeholder="Escribe tu provincia" required>
                            </div>

                            <div class="col-md-6">
                                <label for="ciudad" class="form-label">Ciudad</label>
                                <input type="text" class="form-control" id="ciudad" name="ciudad" placeholder="Escribe tu ciudad" required>
                            </div>
                        </div>

                        <!-- Locationtip -->
                        <div class="mb-3">
                            <label for="locationtip" class="form-label">Nota de ayuda</label>
                            <input type="text" class="form-control" id="locationtip" name="locationtip" placeholder="Ejemplo: Me encuentro cerca a la calle central." required>
                        </div>

                        <input type="text" id="mapvalid" style="display: none;"/>
                    
                    <e id="mapaselect" style="display: none;">
                        <!-- Ubicación (Mapa) -->
                        <div id="mapContainer" class="mb-3">
                            <label class="form-label">* <i class="fa-solid fa-location-dot" style="color: #df43b0;"></i> Marca en el mapa tu ubicación, o una ubicación en donde puedas verte con tus Clientes.</label>
                            <div id="map" style="height: 300px;"></div>
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                        </div>
                        
                        <div class="tostada">
                            <b><i class="fa-solid fa-triangle-exclamation"></i> Importante:</b> La ubicación ayudará a tus Clientes a encontrarte mediante la función 
                            <b title="Esta función le permite a los Clientes encontrar los anuncios más cercanos a su ubicación. Dándoles así mayor relevancia.">"<i class="fa-solid fa-location"></i> Cerca de mí"</b>.
                            <br>Asegúrate de que sea cercana a tu ubicación real para obtener mejores resultados.
                        </div>
                    </e>

                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-header cabeceros">
                        <span class="seccion">• <i class="fa-solid fa-calendar-days"></i> Disponibilidad •</span>
                        <span>¿Cuándo estás disponible?</span>
                    </div>
                    <div class="card-body">
                        <!-- Días de la Semana -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Días de la Semana</label>
                            <div class="d-flex flex-column">
                                <!-- Checkbox para cada día -->
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input day-checkbox pointer" type="checkbox" id="lunes" value="L">
                                    <label class="form-check-label pointer" for="lunes">Lunes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input day-checkbox pointer" type="checkbox" id="martes" value="M">
                                    <label class="form-check-label pointer" for="martes">Martes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input day-checkbox pointer" type="checkbox" id="miercoles" value="X">
                                    <label class="form-check-label pointer" for="miercoles">Miércoles</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input day-checkbox pointer" type="checkbox" id="jueves" value="J">
                                    <label class="form-check-label pointer" for="jueves">Jueves</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input day-checkbox pointer" type="checkbox" id="viernes" value="V">
                                    <label class="form-check-label pointer" for="viernes">Viernes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input day-checkbox pointer" type="checkbox" id="sabado" value="S">
                                    <label class="form-check-label pointer" for="sabado">Sábado</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input day-checkbox pointer" type="checkbox" id="domingo" value="D">
                                    <label class="form-check-label pointer" for="domingo">Domingo</label>
                                </div>
                            </div>
                            <!-- Seleccionar Todos -->
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="selectAllDays">
                                <label class="form-check-label pointer" for="selectAllDays">
                                    <i class="fa-solid fa-check-double"></i> Seleccionar Todos
                                </label>
                            </div>

                            <input type="hidden" id="disponibilidadcheck" name="disponibilidad"/>
                        </div>

                        <!-- Horarios -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Horarios</label>
                            <div class="form-check">
                                <input class="form-check-input horario-checkbox" type="checkbox" id="mananas" value="Mañanas">
                                <label class="form-check-label pointer" for="mananas">
                                    <i class="fa-solid fa-sun"></i> Mañanas
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input horario-checkbox" type="checkbox" id="tardes" value="Tardes">
                                <label class="form-check-label pointer" for="tardes">
                                    <i class="fa-solid fa-cloud-sun"></i> Tardes
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input horario-checkbox" type="checkbox" id="noches" value="Noches">
                                <label class="form-check-label pointer" for="noches">
                                    <i class="fa-solid fa-moon"></i> Noches
                                </label>
                            </div>

                            <input type="hidden" id="horarioscheck" name="horarios"/>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header cabeceros">
                        <span class="seccion">• <i class="fa-solid fa-bell-concierge"></i> Servicios •</span>
                        <span>¿Qué puedes ofrecer<span class="nameport"></span>?</span>
                    </div>
                    <div class="card-body">
                        <!-- Botón Seleccionar Todos -->
                        <div class="mb-3">
                            <!-- Seleccionar Todos -->
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="selectAllServices">
                                <label class="form-check-label pointer" for="selectAllServices">
                                <i class="fa-solid fa-check-double"></i> Seleccionar Todos
                                </label>
                            </div>
                        </div>
                        <!-- Servicios -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-24h" value="24h">
                                    <label class="form-check-label pointer" for="service-24h">24h</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-masajes" value="Masajes">
                                    <label class="form-check-label pointer" for="service-masajes">Masajes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-fr-natural" value="FR Natural">
                                    <label class="form-check-label pointer" for="service-fr-natural">FR Natural</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-anal" value="Anal">
                                    <label class="form-check-label pointer" for="service-anal">Anal</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-sado-bdsm" value="Sado BDSM">
                                    <label class="form-check-label pointer" for="service-sado-bdsm">Sado BDSM</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-salidas" value="Salidas">
                                    <label class="form-check-label pointer" for="service-salidas">Salidas</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-besos-con-lengua" value="Besos con lengua">
                                    <label class="form-check-label pointer" for="service-besos-con-lengua">Besos con lengua</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-parejas" value="Parejas">
                                    <label class="form-check-label pointer" for="service-parejas">Parejas</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-trios" value="Tríos">
                                    <label class="form-check-label pointer" for="service-trios">Tríos</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-lluvia-dorada" value="Lluvia dorada">
                                    <label class="form-check-label pointer" for="service-lluvia-dorada">Lluvia dorada</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-novios" value="Novios">
                                    <label class="form-check-label pointer" for="service-novios">Novios</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-frances" value="Francés">
                                    <label class="form-check-label pointer" for="service-frances">Francés</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-beso-negro" value="Beso negro">
                                    <label class="form-check-label pointer" for="service-beso-negro">Beso negro</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-lesbico" value="Lésbico">
                                    <label class="form-check-label pointer" for="service-lesbico">Lésbico</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox pointer" type="checkbox" id="service-fiestera" value="Fiestera">
                                    <label class="form-check-label pointer" for="service-fiestera">Fiestera</label>
                                </div>
                            </div>

                            <input type="hidden" id="servicioscheck" name="servicios"/>
                        </div>
                    </div>
                </div>
                

                <div class="card mb-3">
                    <div class="card-header cabeceros">
                        <span class="seccion">• <i class="fa-solid fa-images"></i> Imágenes •</span>
                        <span>¡Muéstrales lo que tienes!</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label picturelabel"><i class="fa-solid fa-camera" style="color: #8c00db;"></i> Imágen Principal</label>
                            <div class="image-grid">
                                <!-- Cuadros de imágenes dinámicos -->
                                <div class="image-box" title="Subir imágen" data-index="0">
                                    <span class="add-icon">+</span>
                                    <input type="file" class="hidden-input" name="principalPicture" id="principalPicture" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label picturelabel"><i class="fa-regular fa-images" style="color: #b533db;"></i> Imágenes Adicionales</label>
                            <div class="image-grid">
                                <!-- Cuadros de imágenes dinámicos -->
                                <div class="image-box" title="Subir imágen" data-index="1">
                                    <span class="add-icon">+</span>
                                    <input type="file" name="pictures[]" class="hidden-input" accept="image/*">
                                </div>
                                <div class="image-box" title="Subir imágen" data-index="2">
                                    <span class="add-icon">+</span>
                                    <input type="file" name="pictures[]" class="hidden-input" accept="image/*">
                                </div>
                                <div class="image-box" title="Subir imágen" data-index="3">
                                    <span class="add-icon">+</span>
                                    <input type="file" name="pictures[]" class="hidden-input" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label picturelabel"><i class="fa-solid fa-circle-user" style="color: #b533db;"></i> Imágen de Perfil</label>
                            <div class="image-grid">
                                <!-- Cuadros de imágenes dinámicos -->
                                <div class="image-box" title="Subir imágen" data-index="4">
                                    <span class="add-icon">+</span>
                                    <input type="file" name="profilePicture" id="profilePicture" class="hidden-input" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    

                <!-- Navigation Buttons -->
                <div class="navigation-buttons">
                    <button type="button" id="prev" class="btn btn-secondary" style="display: none;">Anterior</button>
                    <button type="button" id="next" class="btn btn-primary" disabled>Siguiente</button>
                </div>
            </div>

            <!-- Botón Enviar -->
            <div style="display: none;" class="text-center mt-4" id="post">
                <button type="submit" class="btn btn-primary">Publicar Anuncio</button>
            </div>
        </form>
    </div>
</div>

<script src="/sources/jquery-3.7.1/jquery-3.7.1.min.js"></script>
<script src="/sources/bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="/sources/sweetalert-2/sweetalert2@11.js"></script>
<script src="/sources/leaflet-1.9.4/leaflet.js"></script>
<script src="/sources/choices-11.0.2/choices.min.js"></script>
<script src="/sources/cropper-1.6.2/cropper.min.js"></script>
<script src="/assets/js/ads.js"></script>
</body>
</html>
