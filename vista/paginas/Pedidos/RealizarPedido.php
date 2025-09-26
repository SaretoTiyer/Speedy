<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Realizar Pedido</h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo S_URL_PED ?>crearPedido" method="post">
                        <div class="mb-4">
                            <label for="direccion_entrega" class="form-label">Dirección de Entrega</label>
                            <div class="input-group">
                                <input type="text" id="direccion_entrega" name="direccion_entrega" required class="form-control" placeholder="Ingrese su dirección">
                                <button type="button" class="btn btn-outline-secondary" id="btn-ubicacion-actual">
                                    <i class="fas fa-map-marker-alt"></i> Usar ubicación actual
                                </button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div id="map" style="height: 300px; width: 100%;"></div>
                        </div>
                        <div class="mb-4">
                            <label for="comentarios" class="form-label">Comentarios adicionales</label>
                            <textarea id="comentarios" name="comentarios" class="form-control" rows="3" placeholder="Instrucciones especiales, referencias, etc."></textarea>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Confirmar Pedido</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script>
let map, marker;

function initMap() {
    map = L.map('map').setView([-34.397, -58.644], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    document.getElementById('btn-ubicacion-actual').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                map.setView([pos.lat, pos.lng], 15);
                setMarker([pos.lat, pos.lng]);
                reverseGeocode(pos);
            }, function() {
                handleLocationError(true);
            });
        } else {
            handleLocationError(false);
        }
    });

    map.on('click', function(e) {
        setMarker(e.latlng);
        reverseGeocode(e.latlng);
    });
}

function setMarker(latlng) {
    if (marker) {
        map.removeLayer(marker);
    }
    marker = L.marker(latlng).addTo(map);
}

function reverseGeocode(latlng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`)
        .then(response => response.json())
        .then(data => {
            if (data.display_name) {
                document.getElementById('direccion_entrega').value = data.display_name;
            }
        })
        .catch(error => console.error('Error:', error));
}

function handleLocationError(browserHasGeolocation) {
    alert(browserHasGeolocation ?
        'Error: El servicio de geolocalización falló.' :
        'Error: Tu navegador no soporta geolocalización.');
}

document.addEventListener('DOMContentLoaded', initMap);
</script>
