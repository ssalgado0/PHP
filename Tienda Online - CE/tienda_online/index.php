<?php include("includes/conexion.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MotorWare | Accesorios y equipamiento para motos</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="img/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<!-- === HEADER === -->
<header>
    <div class="header-top">
        <div class="header-content">
            <!-- Logo -->
            <a href="index.php" class="logo">
                <img src="img/logo.png" alt="MotorWare">
            </a>

            <!-- Buscador -->
            <form class="search-bar" method="GET" action="buscar.php" novalidate>
                <input type="text" name="q" placeholder="Buscar productos...">
                <button type="submit">🔍</button>
            </form>

            <!-- Iconos de Usuario y Carrito -->
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

    <!-- Menú de categorías -->
    <nav class="header-categorias">
        <a href=http://localhost/tienda_online/index.php>Todas las categorías</a>
        <?php
        $cat = $conexion->query("SELECT * FROM categorias");
        while ($c = $cat->fetch_assoc()) {
            echo "<a href='index.php?cat={$c['id']}'>{$c['nombre']}</a>";
        }
        ?>
    </nav>
</header>

<!-- === CONTENIDO PRINCIPAL === -->
<main class="main-content">
<?php
if (!isset($_GET['cat'])) {
    echo "<h2>Categorías</h2>";
    echo "<div class='categorias'>";

    // Mostramos las categorías
    $cat = $conexion->query("SELECT * FROM categorias");
    while ($c = $cat->fetch_assoc()) {
        $img = "img/categorias/{$c['id']}.png";
        if (!file_exists($img)) {
            $img = "img/placeholder.png";
        }

        echo "
        <div class='categoria'>
            <a href='index.php?cat={$c['id']}'>
                <img src='{$img}' alt='{$c['nombre']}'>
                <div class='cat-overlay'>
                    <span>{$c['nombre']}</span>
                </div>
            </a>
        </div>";
    }

    echo "</div>";

} else {

    // Listamos los productos de la categoría seleccionada
    $cat_id = intval($_GET['cat']);
    $resultado = $conexion->query("SELECT nombre FROM categorias WHERE id=$cat_id");
    $categoria = $resultado->fetch_assoc();

    echo "<h2>{$categoria['nombre']}</h2>";
    echo "<div class='productos'>";

    $productos = $conexion->query("SELECT * FROM productos WHERE id_categoria=$cat_id");
    if ($productos->num_rows === 0) {
        echo "<p class='no-productos'>No hay productos disponibles en esta categoría.</p>";
    }

    while ($p = $productos->fetch_assoc()) {
        $rutaImagen = $p['imagen'];
        if (!file_exists($rutaImagen) || empty($p['imagen'])) {
            $rutaImagen = "img/placeholder.png";
        }

        echo "
        <div class='producto'>
            <a href='producto.php?id={$p['id']}'>
                <img src='{$rutaImagen}' alt='{$p['nombre']}'>
            </a>
            <h3><a href='producto.php?id={$p['id']}' class='nombre-producto'>{$p['nombre']}</a></h3>
            <p class='precio'>{$p['precio']} €</p>
            <a href='carrito.php?add={$p['id']}' class='btn'>Añadir al carrito</a>
        </div>";
    }

    echo "</div>";
}
?>
</main>

<!-- === FOOTER === -->
<footer>
    <p>&copy; 2025 <strong>MotorWare</strong> - Todos los derechos reservados.</p>
</footer>

</body>
</html>
