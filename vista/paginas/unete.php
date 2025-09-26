<div class="container-fluid py-5 bg-gradient-primary text-white join-us-section">
    <div class="container">
        <h1 class="display-4 text-center mb-5 fw-bold" data-aos="fade-up">Únete a nuestra plataforma</h1>
        <div class="row justify-content-center g-4">
            <div class="col-md-6 col-lg-5" data-aos="fade-right" data-aos-delay="100">
                <div class="card bg-white text-dark h-100 hover-card">
                    <div class="card-body d-flex flex-column justify-content-between text-center p-4">
                        <div>
                            <i class="fas fa-store fa-5x mb-4 text-primary"></i>
                            <h3 class="card-title h3 mb-3">Registra tu tienda</h3>
                            <p class="card-text">Expande tu negocio y llega a más clientes a través de nuestra plataforma. Aumenta tus ventas y crece con nosotros.</p>
                        </div>
                        <a href="<?php echo S_URL; ?>FormTien" class="btn btn-primary btn-lg mt-4">
                            Registrar tienda <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-5" data-aos="fade-left" data-aos-delay="200">
                <div class="card bg-white text-dark h-100 hover-card">
                    <div class="card-body d-flex flex-column justify-content-between text-center p-4">
                        <div>
                            <i class="fas fa-motorcycle fa-5x mb-4 text-success"></i>
                            <h3 class="card-title h3 mb-3">Conviértete en domiciliario</h3>
                            <p class="card-text">Genera ingresos adicionales realizando entregas en tu tiempo libre. Sé parte de nuestra red de repartidores confiables.</p>
                        </div>
                        <a href="<?php echo S_URL; ?>FormDomi" class="btn btn-success btn-lg mt-4">
                            Registrarse como domiciliario <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="300">
            <a href="<?php echo S_URL; ?>inicio" class="btn btn-light btn-lg px-5 py-3 rounded-pill shadow-sm">
                <i class="fas fa-home me-2"></i> Volver al inicio
            </a>
        </div>
    </div>
</div>

<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true
    });
</script>
