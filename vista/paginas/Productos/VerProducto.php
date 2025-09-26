<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Producto - <?php echo S_NAME; ?></title>
    <style>
        .product-image-container {
            width: 100%;
            height: 400px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .product-price {
            font-size: 1.5em;
            color: #28a745;
            font-weight: bold;
        }
        .product-stock {
            font-size: 0.9em;
            color: #6c757d;
        }
        .btn-edit-product {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-edit-product:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="product-image-container">
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="product-image">
                </div>
            </div>
            <div class="col-md-6">
                <h1 class="mb-4"><?php echo htmlspecialchars($producto['nombre']); ?></h1>
                <div class="product-details">
                    <p class="product-price mb-3">$<?php echo number_format($producto['precio'], 2); ?></p>
                    <p class="product-stock mb-3">
                        <i class="fas fa-box"></i> 
                        <?php echo $producto['cantidad'] > 0 ? 'En stock' : 'Agotado'; ?> 
                        (<?php echo $producto['cantidad']; ?> disponibles)
                    </p>
                    <p class="mb-4"><strong>Descripción:</strong> <?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    
                    <a href="<?php echo S_URL_P; ?>editarProducto&id=<?php echo $producto['id']; ?>" class="btn btn-edit-product">
                        <i class="fas fa-edit"></i> Editar Producto
                    </a>
                </div>
            </div>
        </div>
        
        <div class="mt-5">
            <a href="<?php echo S_URL_T; ?>perfilTienda" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Volver a mi perfil
            </a>
        </div>
    </div>
</body>
</html>
