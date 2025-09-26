<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo S_NAME; ?> - Inicio</title>
    
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="vista/recursos/css/inicio.css">
</head>
<body>
    <header class="hero">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="vista/recursos/img/collageComidaRapida.png" class="d-block w-100" alt="Comida Rápida">
                </div>
                <div class="carousel-item">
                    <img src="vista/recursos/img/collageHelado.png" class="d-block w-100" alt="Helados">
                </div>
                <div class="carousel-item">
                    <img src="vista/recursos/img/collageUtiles.png" class="d-block w-100" alt="Útiles Escolares">
                </div>
            </div>
        </div>
        <div class="hero-overlay"></div>
        <div class="container hero-container">
            <div class="hero-content text-center" data-aos="fade-up">
                <h1 class="hero-title display-3 fw-bold text-white mb-4">Bienvenido a <?php echo S_NAME; ?></h1>
                <p class="hero-text lead text-white mb-5">Tu solución rápida y confiable para entregas y servicios</p>
                <a href="#servicios" class="btn btn-custom hero-button">
                    Descubre nuestros servicios
                    <i class="fas fa-chevron-right ms-2"></i>
                </a>
            </div>
        </div>
    </header>

    <section id="servicios" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Nuestros Servicios</h2>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-utensils fa-3x mb-3 text-primary"></i>
                            <h3 class="card-title">Entrega de Comida</h3>
                            <p class="card-text">Disfruta de tus platos favoritos en la comodidad de tu hogar.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-shopping-bag fa-3x mb-3 text-success"></i>
                            <h3 class="card-title">Compras y Mandados</h3>
                            <p class="card-text">Te ayudamos con tus compras y encargos diarios.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-truck fa-3x mb-3 text-warning"></i>
                            <h3 class="card-title">Envíos Expresos</h3>
                            <p class="card-text">Entrega rápida y segura de paquetes y documentos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="como-funciona" class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Cómo Funciona</h2>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-center">
                        <i class="fas fa-mobile-alt fa-4x mb-3 text-primary"></i>
                        <h3>1. Haz tu pedido</h3>
                        <p>Selecciona lo que necesitas desde nuestro sitio web.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-center">
                        <i class="fas fa-bicycle fa-4x mb-3 text-success"></i>
                        <h3>2. Asignamos un repartidor</h3>
                        <p>Un repartidor cercano tomará tu pedido y lo llevará a su destino.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-center">
                        <i class="fas fa-check-circle fa-4x mb-3 text-warning"></i>
                        <h3>3. Recibe tu pedido</h3>
                        <p>¡Listo! Tu pedido llegará en tiempo récord.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Productos Recientes -->
    <section class="py-5">
        <div class="container">
            <h2 class="mb-4">Productos Recientes</h2>
            <div class="row">
                <?php foreach ($productosRecientes as $producto): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo $producto['imagen']; ?>" class="card-img-top" alt="<?php echo $producto['nombre']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $producto['nombre']; ?></h5>
                                <p class="card-text"><?php echo $producto['descripcion']; ?></p>
                                <p class="card-text"><strong>Precio: $<?php echo number_format($producto['precio'], 2); ?></strong></p>
                                <p class="card-text"><small class="text-muted">Tienda: <?php echo $producto['nombre_tienda']; ?></small></p>
                            </div>
                            <div class="card-footer">
                                <form action="<?php echo S_URL_PED; ?>agregarAlCarrito" method="post">
                                    <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                    <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['cantidad']; ?>" class="form-control mb-2">
                                    <button type="submit" class="btn btn-primary btn-block">Comprar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Sección "Sé parte de nosotros" -->
    <section class="py-5 bg-gradient-primary text-dark join-us-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-4 mb-4 fw-bold" data-aos="fade-up">¿Quieres ser parte de nosotros?</h2>
                    <p class="lead mb-5" data-aos="fade-up" data-aos-delay="100">Únete a nuestra plataforma y descubre nuevas oportunidades para crecer.</p>
                    <a href="<?php echo S_URL; ?>unete" class="btn btn-light btn-lg px-5 py-3 rounded-pill shadow-sm" data-aos="zoom-in" data-aos-delay="400">
                        Descubre más <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="vista/recursos/js/inicio.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html>
