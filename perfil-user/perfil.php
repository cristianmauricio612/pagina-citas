<?php
session_start();
require '../php/backend/config.php';
include '../php/backend/obtener_perfil.php';

$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

if (!$isLoggedIn) {
    header("Location: /");
    exit();
}
$isAdvertiser = isset($usuario_type) && $usuario_type === "advertiser";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="perfil.css">
</head>

<body>
    <div class="perfil-container">
        <h2>Perfil de Usuario</h2>
        <div class="foto-section">
            <img src="<?php echo $usuario['fotografia'] ? $usuario['fotografia'] : '/assets/img/fotos/foto-perfil.webp'; ?>"
                class="foto-perfil" alt="Foto de perfil">
        </div>

        <h3>Datos de Usuario</h3>
        <div class="row">
            <div class="campo">
                <label>Nombre</label>
                <input type="text" value="<?php echo $usuario['nombre']; ?>" disabled>
            </div>
            <div class="campo">
                <label>Fecha de nacimiento</label>
                <input type="text" value="<?php echo $usuario['nacimiento']; ?>" disabled>
            </div>
        </div>
        <div class="row">
            <div class="campo">
                <label>Sexo</label>
                <input type="text" value="<?php echo $usuario['sexo']; ?>" disabled>
            </div>
            <div class="campo">
                <label>Pais</label>
                <input type="text" value="<?php echo $usuario['pais']; ?>" disabled>
            </div>
        </div>
        <div class="row">
            <div class="campo">
                <label>Bandera</label>
                <input type="text" value="<?php echo strtoupper($usuario['bandera']); ?>" disabled>
            </div>
            <div class="campo">
                <label>Categoria</label>
                <input type="text" value="<?php echo $usuario['categoria']; ?>" disabled>
            </div>
        </div>
        <div class="row">
            <div class="campo">
                <label>Indicativo</label>
                <input type="text" value="<?php echo $usuario['indicativo']; ?>" disabled>
            </div>
            <div class="campo">
                <label>Telefono</label>
                <input type="text" value="<?php echo $usuario['telefono']; ?>" disabled>
            </div>
        </div>
        <div class="row">
            <div class="form-check mt-2">
                <input type="checkbox" class="checkwsp" <?php echo ($usuario['whatsapp'] == 1) ? 'checked' : ''; ?>
                    disabled>
                <label class="form-check-label" for="whatsapp">Usa este número para Whatsapp</label>
            </div>
        </div>
        <div style="display: flex; justify-content: right;">
            <a href="/" class="btn-regresar btn">Regresar</a>
        </div>
    </div>

    <div class="perfil-container" id="anuncios-container" style="display:none;">
        <h2>Publicaciones de Usuario</h2>
        <div class="table-container">
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoria</th>
                        <th>Telefono</th>
                        <th>Titulo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($isAdvertiser) {
                        include '../php/backend/obtener_anuncios.php';
                        foreach ($anuncios as $anuncio) {
                            echo "<tr>
                                <td>{$anuncio['anuncio_id']}</td>
                                <td>{$anuncio['nombre']}</td>
                                <td>{$anuncio['categoria']}</td>
                                <td>{$anuncio['telefono']}</td>
                                <td>{$anuncio['titulo']}</td>
                                <td>
                                    <a class='btn-view show-visa' href='anuncio.php?id={$anuncio['anuncio_id']}'>
                                        <i class='fa-solid fa-eye'></i>
                                    </a>
                                    <a class='btn-view show-visa' href='/payment/anuncios/?id={$anuncio['anuncio_id']}'>
                                        <i class='fa-solid fa-calendar'></i>
                                    </a>
                                </td>
                            </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
<script>
    let isAdvertiser1 = <?php echo $isAdvertiser ? 'true' : 'false'; ?>;

    if (isAdvertiser1) {
        let anunciosContainer = document.getElementById('anuncios-container');
        if (anunciosContainer) {
            anunciosContainer.style.display = 'block';
        }
        document.body.style.height = '1%';
    }
</script>


</html>