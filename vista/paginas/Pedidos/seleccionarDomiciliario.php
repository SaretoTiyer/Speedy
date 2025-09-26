<?php
// Asegúrate de que $datos['idPedido'] y $datos['domiciliariosDisponibles'] estén disponibles
?>

<div class="container mt-5">
    <h2 class="mb-4 text-primary">Seleccionar Domiciliario para el Pedido #<?php echo htmlspecialchars($datos['idPedido']); ?></h2>
    
    <?php if (empty($datos['domiciliariosDisponibles'])): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No hay domiciliarios disponibles en este momento.
        </div>
    <?php else: ?>
        <form action="<?php echo S_URL_PED; ?>asignarDomiciliario" method="post">
            <input type="hidden" name="id_pedido" value="<?php echo htmlspecialchars($datos['idPedido']); ?>">
            
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($datos['domiciliariosDisponibles'] as $domiciliario): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm hover-card">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img src="<?php echo !empty($domiciliario['dom_foto']) ? htmlspecialchars($domiciliario['dom_foto']) : 'vista/recursos/img/default-profile.jpg'; ?>" 
                                         alt="Foto de <?php echo htmlspecialchars($domiciliario['nombre']); ?>" 
                                         class="rounded-circle img-thumbnail" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <h5 class="card-title text-center mb-3">
                                    <?php echo htmlspecialchars($domiciliario['nombre'] . ' ' . $domiciliario['apellido']); ?>
                                </h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <i class="fas fa-star text-warning me-2"></i>
                                        Calificación: <?php echo isset($domiciliario['calificacion']) ? number_format($domiciliario['calificacion'], 1) : 'N/A'; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <i class="fas fa-motorcycle me-2"></i>
                                        Vehículo: <?php echo isset($domiciliario['vehiculo']) ? htmlspecialchars($domiciliario['vehiculo']) : 'No especificado'; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <i class="fas fa-phone me-2"></i>
                                        Teléfono: <?php echo isset($domiciliario['telefono']) ? htmlspecialchars($domiciliario['telefono']) : 'No disponible'; ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="id_domiciliario" 
                                           id="domiciliario<?php echo $domiciliario['id']; ?>" 
                                           value="<?php echo $domiciliario['id']; ?>" required>
                                    <label class="form-check-label" for="domiciliario<?php echo $domiciliario['id']; ?>">
                                        Seleccionar este domiciliario
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-check-circle me-2"></i>Asignar Domiciliario
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<style>
    .hover-card {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
    .form-check-input:checked + .form-check-label {
        font-weight: bold;
        color: #007bff;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    gsap.from('.card', {
        duration: 0.6,
        y: 50,
        opacity: 0,
        stagger: 0.1,
        ease: "power3.out"
    });
});
</script>
