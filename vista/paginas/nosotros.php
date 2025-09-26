<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nosotros - <?php echo S_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="vista/recursos/css/nosotros.css">
</head>
<body>
    <section class="about-us">
        <div class="container">
            <h1 class="section-title" data-aos="fade-up">Nuestro Equipo</h1>
            <div class="team-members">
                <div class="member-card" data-aos="flip-left" data-aos-delay="100">
                    <div class="card-inner">
                        <div class="card-front">
                            <img src="vista/recursos/img/programador-lider.jpg" alt="Programador Líder">
                            <h3>Santiago Reyes</h3>
                            <p>Programador Líder</p>
                        </div>
                        <div class="card-back">
                            <h3>Santiago Reyes </h3>
                            <p>Aprendiz de arquitectura de software y líder de equipo.</p>
                            <div class="social-icons">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="member-card" data-aos="flip-left" data-aos-delay="200">
                    <div class="card-inner">
                        <div class="card-front">
                            <img src="vista/recursos/img/disenador.jpg" alt="Diseñador">
                            <h3>Norley De Jesús</h3>
                            <p>Diseñador</p>
                        </div>
                        <div class="card-back">
                            <h3>Norley De Jesús</h3>
                            <p>Diseñador  creativo con un ojo para los detalles y la experiencia del usuario.</p>
                            <div class="social-icons">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-behance"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="member-card" data-aos="flip-left" data-aos-delay="300">
                    <div class="card-inner">
                        <div class="card-front">
                            <img src="vista/recursos/img/co-disenador.jpg" alt="Co-Diseñador">
                            <h3>Johan Rámos</h3>
                            <p>Co-Diseñador</p>
                        </div>
                        <div class="card-back">
                            <h3>Johan Rámos</h3>
                            <p>Aportando creatividad a el proyecto.</p>
                            <div class="social-icons">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-dribbble"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="member-card" data-aos="flip-left" data-aos-delay="400">
                    <div class="card-inner">
                        <div class="card-front">
                            <img src="vista/recursos/img/co-programador.jpg" alt="Co-Programador">
                            <h3>Cristian Holguin</h3>
                            <p>Co-Programador</p>
                        </div>
                        <div class="card-back">
                            <h3>Cristian Holguin</h3>
                            <p>Aportando soluciones a el proyecto.</p>
                            <div class="social-icons">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="our-mission">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Nuestra Misión</h2>
            <p data-aos="fade-up" data-aos-delay="200">En Speedy, nos dedicamos a proporcionar soluciones de entrega rápidas, seguras y eficientes. Nuestro objetivo es simplificar la vida de nuestros clientes, ofreciendo un servicio de mensajería confiable y de alta calidad.</p>
        </div>
    </section>

    <section class="values">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Nuestros Valores</h2>
            <div class="value-items">
                <div class="value" data-aos="zoom-in" data-aos-delay="100">
                    <i class="fas fa-rocket"></i>
                    <h3>Innovación</h3>
                    <p>Buscamos constantemente nuevas formas de mejorar nuestros servicios.</p>
                </div>
                <div class="value" data-aos="zoom-in" data-aos-delay="200">
                    <i class="fas fa-handshake"></i>
                    <h3>Confianza</h3>
                    <p>Construimos relaciones sólidas basadas en la confianza mutua.</p>
                </div>
                <div class="value" data-aos="zoom-in" data-aos-delay="300">
                    <i class="fas fa-clock"></i>
                    <h3>Puntualidad</h3>
                    <p>Nos comprometemos a entregar cada paquete a tiempo, siempre.</p>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html>