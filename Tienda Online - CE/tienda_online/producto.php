<?php include("includes/conexion.php"); ?>

<?php
// Verificar que se recibe el ID del producto
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

// Vamos a buscar el producto con ese ID a la base de datos, y devolvemos un mensaje si no se ha encontrado
$id_producto = intval($_GET['id']);
$resultado = $conexion->query("SELECT * FROM productos WHERE id = $id_producto");

if ($resultado->num_rows === 0) {
    echo "<p class='no-productos'>Producto no encontrado.</p>";
    exit();
}

$producto = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MotorWare | <?php echo htmlspecialchars($producto['nombre']); ?></title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<!-- === HEADER === -->
<header>
    <div class="header-top">
        <div class="header-content">
            <a href="index.php" class="logo">
                <img src="img/logo.png" alt="MotorWare">
            </a>

            <form class="search-bar" method="GET" action="buscar.php" novalidate>
                <input type="text" name="q" placeholder="Buscar productos...">
                <button type="submit">🔍</button>
            </form>

            <div class="header-icons">
                <a href="usuario.php" class="icon-button" title="Mi cuenta">
                    <img src="img/user.png" alt="Usuario">
                    <span>Mi cuenta</span>
                </a>
                <a href="carrito.php" class="icon-button" title="Mi carrito">
                    <img src="img/shopping-cart.png" alt="Carrito">
                    <span>Carrito</span>
                </a>
            </div>
        </div>
    </div>
</header>

<!-- === CONTENIDO PRINCIPAL === -->
<main class="main-content">
    <div class="producto-detalle">
        <?php
        $rutaImagen = $producto['imagen'];
        if (!file_exists($rutaImagen) || empty($producto['imagen'])) {
            $rutaImagen = "img/placeholder.png";
        }
        ?>
        
        <img src="<?php echo $rutaImagen; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">

        <!-- Nombre, descripción y precio del producto -->
        <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
        <p class="precio"><?php echo number_format($producto['precio'], 2); ?> €</p>
        <a href="carrito.php?add=<?php echo $producto['id']; ?>" class="btn">Añadir al carrito</a>
    </div>
</main>

<!-- === FOOTER === -->
<footer>
    <p>&copy; 2025 <strong>MotorWare</strong> - Todos los derechos reservados.</p>
</footer>

</body>
</html>
