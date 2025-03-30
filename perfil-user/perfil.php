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
                        <th>Foto</th>
                        <th>Información</th>
                        <th>Activación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($isAdvertiser) : ?>
                    <?php include '../php/backend/obtener_anuncios.php'; ?>
                    <?php foreach ($anuncios as $anuncio) : ?>
                        <tr>
                        <td>
                            <img src='<?= $anuncio['picture_profile']; ?>' class='foto-anuncio <?= $anuncio['hidden'] == 1 ? 'foto-hidden' : '' ?>' alt='Foto de anuncio'>
                        </td>
                        <td>
                            <div info>
                                <span title><?= $anuncio['titulo']; ?></span>
                                <span category><?= $anuncio['categoria']; ?></span>
                                <span number><?= $anuncio['indicativo'] . ' ' . $anuncio['telefono']; ?></span>
                            </div>
                        </td>
                        <td>
                            <?php
                            // Activate if now if less than $anuncio['expires_at']
                            $now = new DateTime();
                            $expiresAt = new DateTime($anuncio['expires_at']);
                            $isExpired = $now > $expiresAt;
                            // "au_active","au_start_day","au_end_day","au_days","au_times","au_interval","au_total","au_current","au_start_hour","au_end_hour","activated_at","expires_at"
                            // dias hasta expires_at + 20 days
                            $nowPlus20Days = (clone $expiresAt)->add(new DateInterval('P20D'));
                            $daysUntilElimination = $now->diff($nowPlus20Days)->days;
                            $daysUntilExpiration = $now->diff((clone $expiresAt)->add(new DateInterval('P1D')))->days;
                            ?>

                            <div info>
                            <?php if (!$isExpired) : ?>
                                <span class='ad-active'>Activo</span>
                                <span>Se Desactivará en <?= $daysUntilExpiration; ?> días</span>
                            <?php else: ?>
                                <span class='ad-inactive'>Inactivo</span>
                                <span>Se eliminará en <?= $daysUntilElimination; ?> días</span>
                            <?php endif; ?>
                            <!-- $anuncio['au_active'] -->
                            <?php if ($anuncio['au_active']) : ?>
                                <span class='ad-active'>Subidas Automaticas</span>
                            <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class='acciones'/>
                            <a class='btn-view show-visa' href='anuncio.php?id=<?= $anuncio['anuncio_id']; ?>'>
                                <i class='fa-solid fa-external-link'></i>
                                Ver
                            </a>
                            <a class='btn-view show-visa' href='/anuncios/visibilidad.php/?id=<?= $anuncio['anuncio_id']; ?>'>
                                <i class='fa-solid fa-eye'></i>
                                Visibilidad (<?= $anuncio['hidden'] == 1 ? 'Oculto' : 'Visible'; ?>)
                            </a>
                            <a aria-disabled="<?= $anuncio['au_active'] ? 'true' : 'false' ?>" class='btn-view show-visa' href='/payment/anuncios/?id=<?= $anuncio['anuncio_id']; ?>'>
                                <i class='fa-solid fa-clock'></i>
                                Subidas Automáticas
                            </a>
                            <a class='btn-view show-visa' href='/anuncios/subida-manual.php/?id=<?= $anuncio['anuncio_id']; ?>'>
                                <i class='fa-solid fa-wrench'></i>
                                Subida Manual
                            </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        let isAdvertiser1 = <?= $isAdvertiser ? 'true' : 'false'; ?>;
    
        if (isAdvertiser1) {
            let anunciosContainer = document.getElementById('anuncios-container');
            if (anunciosContainer) {
                anunciosContainer.style.display = 'block';
            }
            document.body.style.height = '1%';
        }
    </script>
</body>
</html>
