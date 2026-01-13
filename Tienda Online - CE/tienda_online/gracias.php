<?php
session_start();
include("includes/conexion.php");

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($order_id <= 0) {
    header("Location: index.php");
    exit;
}

// ===============================
// Datos del pedido + usuario
// ===============================
$stmt = $conexion->prepare("
    SELECT p.id, p.fecha, p.total, 
           u.nombre AS nombre_usuario, 
           u.email, 
           u.direccion
    FROM pedidos p
    JOIN usuarios u ON u.id = p.id_usuario
    WHERE p.id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$res = $stmt->get_result();
$pedido = $res->fetch_assoc();
$stmt->close();

if (!$pedido) {
    echo "Pedido no encontrado.";
    exit;
}

// ===============================
// Productos del pedido (JOIN)
// ===============================

$stmt_items = $conexion->prepare("
    SELECT 
        pr.nombre AS nombre_producto,
        pr.imagen,
        d.cantidad,
        d.precio_unitario
    FROM detalle_pedido d
    JOIN productos pr ON pr.id = d.id_producto
    WHERE d.id_pedido = ?
");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$res_items = $stmt_items->get_result();
$items = [];
while ($row = $res_items->fetch_assoc()) {
    $items[] = $row;
}
$stmt_items->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>MotorWare | Pedido confirmado</title>
<link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
</head>
<body>
<header>
    <div class="header-top">
        <div class="header-content">
            <a href="index.php" class="logo"><img src="img/logo.png" alt="MotorWare"></a>
        </div>
    </div>
</header>

<main class="main-content">
<div class="carrito-container">
    <h2>¡Gracias por tu compra!</h2>

    <p><strong>Pedido #<?php echo $pedido['id']; ?></strong> confirmado.</p>
    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($pedido['nombre_usuario'] ?? 'Invitado'); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($pedido['email']); ?></p>
    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($pedido['direccion']); ?></p>
    <p><strong>Total:</strong> <?php echo number_format($pedido['total'], 2, ',', '.'); ?> €</p>

    <h3>Productos comprados</h3>

    <table class="tabla-carrito">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio unitario</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $it): 
                $subtotal = $it['precio_unitario'] * $it['cantidad'];
                $img = (!empty($it['imagen']) && file_exists(__DIR__ . "/" . $it['imagen']))
                      ? $it['imagen']
                      : "img/placeholder.png";
            ?>
                <tr>
                    <td class="col-producto">
                        <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($it['nombre_producto']); ?>">
                        <?php echo htmlspecialchars($it['nombre_producto']); ?>
                    </td>
                    <td><?php echo number_format($it['precio_unitario'], 2, ',', '.'); ?> €</td>
                    <td><?php echo (int)$it['cantidad']; ?></td>
                    <td><?php echo number_format($subtotal, 2, ',', '.'); ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn" style="margin-top:20px;">Volver a la tienda</a>

</div>
</main>

<footer>
    <p>&copy; 2025 <strong>MotorWare</strong> - Todos los derechos reservados.</p>
</footer>
</body>
</html>
