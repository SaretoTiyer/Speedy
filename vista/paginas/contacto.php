<?php
require_once 'modelos/Notificacion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contact'])) {
    $nombre = $_POST['name'];
    $email = $_POST['email'];
    $telefono = $_POST['phone'];
    $asunto = $_POST['subject'];
    $mensaje = $_POST['message'];

    $notificacion = new Notificaciones();
    $mensajeCompleto = "Nuevo mensaje de contacto de $nombre ($email):\n\nAsunto: $asunto\n\nMensaje: $mensaje\n\nTeléfono: $telefono";
    
    if ($notificacion->crearNotificacion('solicitud', $mensajeCompleto, null, true)) {
        $mensajeExito = "Su mensaje ha sido enviado con éxito. Nos pondremos en contacto pronto.";
    } else {
        $mensajeError = "Hubo un problema al enviar su mensaje. Por favor, inténtelo de nuevo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contacto - <?php echo S_NAME; ?></title>
        <link rel="stylesheet" href="vista/recursos/css/contacto.css">
</head>
<body class="contacto">
    <div class="contact-container">
        <h1 class="contact-title">Contáctanos</h1>
        <div class="contact-content">
            <div class="contact-info">
                <p>Estamos aquí para ayudarte. No dudes en ponerte en contacto con nosotros.</p>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>123 Calle Santa Margarita, Medellín, Antioquia</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <span>+57 300 234 5678</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <span>contacto@speedy.com</span>
                </div>
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.9728391215!2d-74.07882492426825!3d4.598920042750612!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3f99a7eccfe58f%3A0x99cb72b35351e45a!2sUniversidad%20Nacional%20de%20Colombia!5e0!3m2!1ses!2sco!4v1683595648395!5m2!1ses!2sco" width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
            <form class="contact-form" method="post" autocomplete="on">
                <div class="form-group">
                    <div class="form-content">
                        <label for="name">Nombre</label>
                        <input type="text" id="name" name="name" placeholder="Tu nombre" required>
                    </div>
                    <div class="form-content">
                        <label for="email">Correo</label>
                        <input type="email" id="email" name="email" placeholder="Tu correo electrónico" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-content">
                        <label for="phone">Teléfono</label>
                        <input type="tel" id="phone" name="phone" placeholder="Tu número de teléfono">
                    </div>
                    <div class="form-content">
                        <label for="subject">Asunto</label>
                        <input type="text" id="subject" name="subject" placeholder="Asunto del mensaje">
                    </div>
                </div>
                <div class="form-content">
                    <label for="message">Mensaje</label>
                    <textarea name="message" id="message" rows="5" placeholder="Tu mensaje" required></textarea>
                </div>
                <button class="btn" type="submit" name="contact">Enviar mensaje</button>
            </form>
        </div>
    </div>
    <?php if (isset($mensajeExito)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $mensajeExito; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($mensajeError)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $mensajeError; ?>
        </div>
    <?php endif; ?>
</body>
</html>
