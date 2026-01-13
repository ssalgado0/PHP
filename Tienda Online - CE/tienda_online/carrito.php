<?php
session_start();
include("includes/conexion.php");

// === Añadir producto ===
if (isset($_GET['add'])) {
    $id = intval($_GET['add']);

    // Sumar cantidad al carrito
    if (!isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id] = ['cantidad' => 1];
    } else {
        $_SESSION['carrito'][$id]['cantidad']++;
    }

    header("Location: carrito.php");
    exit;
}

// === Eliminar producto o vaciar ===
if (isset($_GET['del'])) {
    if ($_GET['del'] === 'all') {
        unset($_SESSION['carrito']);
    } else {
        $id = intval($_GET['del']);
        unset($_SESSION['carrito'][$id]);
    }

    header("Location: carrito.php");
    exit;
}

// === Vaciar con botón ===
if (isset($_POST['vaciar_carrito'])) {
    unset($_SESSION['carrito']);
    header("Location: carrito.php");
    exit;
}

// === SUMAR ===
if (isset($_POST['sumar']) && isset($_POST['id_producto'])) {
    $id = $_POST['id_producto'];
    $_SESSION['carrito'][$id]['cantidad']++;
    header("Location: carrito.php");
    exit;
}

// === RESTAR ===
if (isset($_POST['restar']) && isset($_POST['id_producto'])) {
    $id = $_POST['id_producto'];

    if ($_SESSION['carrito'][$id]['cantidad'] > 1) {
        $_SESSION['carrito'][$id]['cantidad']--;
    } else {
        unset($_SESSION['carrito'][$id]);
    }

    header("Location: carrito.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MotorWare | Carrito de compra</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="img/favicon.ico">
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
                <a href="usuario.php" class="icon-button">
                    <img src="img/user.png" alt="Usuario">
                    <span>Mi cuenta</span>
                </a>
                <a href="carrito.php" class="icon-button">
                    <img src="img/shopping-cart.png" alt="Carrito">
                    <span>Carrito</span>
                </a>
            </div>
        </div>
    </div>

    <nav class="header-categorias">
        <a href="index.php">Todas las categorías</a>
        <?php
        $cat = $conexion->query("SELECT * FROM categorias");
        while ($c = $cat->fetch_assoc()) {
            echo "<a href='index.php?cat={$c['id']}'>{$c['nombre']}</a>";
        }
        ?>
    </nav>
</header>

<!-- === CONTENIDO === -->
<main class="main-content">
    <div class="carrito-container">
        <h2>Carrito de compra</h2>

        <?php
        $total = 0;

        if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {

            echo "<p class='no-productos'>Tu carrito está vacío.</p>
                  <div style='text-align:center; margin-top:20px;'>
                    <a href='index.php' class='btn'>Volver a la tienda</a>
                  </div>";

        } else {

            echo "<table class='tabla-carrito'>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($_SESSION['carrito'] as $id => $item) {

                $cantidad = $item['cantidad'];

                $producto = $conexion->query("SELECT * FROM productos WHERE id=$id")->fetch_assoc();
                if (!$producto) continue;

                $subtotal = $producto['precio'] * $cantidad;
                $total += $subtotal;

                $img = (!empty($producto['imagen']) && file_exists($producto['imagen'])) ?
                    $producto['imagen'] : "img/placeholder.png";

                echo "<tr>
                        <td class='col-producto'>
                            <img src='{$img}' alt='{$producto['nombre']}'>
                            <span>{$producto['nombre']}</span>
                        </td>

                        <td>{$producto['precio']} €</td>

                        <td>
                            <div class='cantidad-control'>
                                <form action='carrito.php' method='POST' class='cantidad-form'>
                                    <input type='hidden' name='id_producto' value='{$id}'>

                                    <button type='submit' name='restar' class='btn-cantidad'>-</button>

                                    <span class='cantidad-valor'>{$cantidad}</span>

                                    <button type='submit' name='sumar' class='btn-cantidad'>+</button>
                                </form>
                            </div>
                        </td>

                        <td>{$subtotal} €</td>

                        <td>
                            <a href='carrito.php?del={$id}' class='btn btn-eliminar'>Eliminar</a>
                        </td>
                    </tr>";
            }

            echo "</tbody>
                </table>

                <div class='total-carrito'>
                    <p><strong>Total:</strong> {$total} €</p>

                    <form action='carrito.php' method='POST'>
                        <button type='submit' name='vaciar_carrito' class='btn-vaciar'>Vaciar carrito</button>
                    </form>

                    <div class='acciones-carrito'>
                        <a href='index.php' class='btn'>Seguir comprando</a>
                        <a href='checkout.php' class='btn btn-finalizar'>Finalizar compra</a>
                    </div>
                </div>";
        }
        ?>

    </div>
</main>

<!-- === FOOTER === -->
<footer>
    <p>&copy; 2025 <strong>MotorWare</strong> - Todos los derechos reservados.</p>
</footer>

</body>
</html>
