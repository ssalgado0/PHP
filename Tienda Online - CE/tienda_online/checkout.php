<?php
session_start();
include("includes/conexion.php");

// Si el carrito está vacío, volvemos a la tienda
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: index.php");
    exit;
}

// Función para generar contraseñas aleatorias
function generar_password($len = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*?';
    $pw = '';
    for ($i = 0; $i < $len; $i++) {
        $pw .= $chars[random_int(0, strlen($chars)-1)];
    }
    return $pw;
}

/* ==========================================
   CARGAR PRODUCTOS DEL CARRITO
========================================== */
$items = [];
$total = 0.0;
$ids = array_keys($_SESSION['carrito']);

if (!empty($ids)) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $conexion->prepare("SELECT id, nombre, precio, imagen FROM productos WHERE id IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $res = $stmt->get_result();

    $products_by_id = [];
    while ($row = $res->fetch_assoc()) {
        $products_by_id[$row['id']] = $row;
    }

    foreach ($_SESSION['carrito'] as $id => $item) {
        if (!isset($products_by_id[$id])) continue;

        $p = $products_by_id[$id];
        $qty = $item['cantidad'];
        $subtotal = $p['precio'] * $qty;
        $total += $subtotal;

        $img = (!empty($p['imagen']) && file_exists(__DIR__ . "/" . $p['imagen']))
            ? $p['imagen']
            : "img/placeholder.png";

        $items[] = [
            'id' => $id,
            'nombre' => $p['nombre'],
            'precio' => $p['precio'],
            'cantidad' => $qty,
            'subtotal' => $subtotal,
            'imagen' => $img
        ];
    }

    $stmt->close();
}


/* ==========================================
   PROCESAR EL FORMULARIO
========================================== */

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_pedido'])) {

    $tipo = $_POST['tipo'] ?? 'guest';

    if ($tipo === 'guest') { // Invitado

        $email = filter_var(trim($_POST['guest_email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $direccion = trim($_POST['guest_direccion'] ?? '');

        if (!$email) $errors[] = "Introduce un correo válido.";
        if (empty($direccion)) $errors[] = "Introduce una dirección de envío.";

        $nombre = null;
        $password = null;

    } else { // Registrado
        $nombre = trim($_POST['reg_nombre'] ?? '');
        $email = filter_var(trim($_POST['reg_email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $direccion = trim($_POST['reg_direccion'] ?? '');
        $password = $_POST['reg_password'] ?? '';

        if (empty($nombre)) $errors[] = "Introduce tu nombre completo.";
        if (!$email) $errors[] = "Introduce un correo válido.";
        if (empty($direccion)) $errors[] = "Introduce la dirección de envío.";
    }


    /* ==========================================
       GUARDAR PEDIDO
    ========================================== */
    if (empty($errors)) {

        try {
            $conexion->begin_transaction();

            /* ========== GESTIÓN DE USUARIO ========== */

            if ($tipo === 'registered') {

                // ¿Existe usuario?
                $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $res = $stmt->get_result();

                if ($res->num_rows > 0) {  
                    $row = $res->fetch_assoc();
                    $user_id = $row['id'];
                    $stmt->close();

                    // Actualizar dirección
                    $stmt = $conexion->prepare("UPDATE usuarios SET direccion = ? WHERE id = ?");
                    $stmt->bind_param("si", $direccion, $user_id);
                    $stmt->execute();
                    $stmt->close();

                } else {
                    // Crear usuario nuevo
                    if (empty($password)) {
                        $password = generar_password();
                    }

                    $pw_hash = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $conexion->prepare(
                        "INSERT INTO usuarios (nombre, email, direccion, password_hash, is_guest)
                         VALUES (?, ?, ?, ?, 0)"
                    );
                    $stmt->bind_param("ssss", $nombre, $email, $direccion, $pw_hash);
                    $stmt->execute();
                    $user_id = $stmt->insert_id;
                    $stmt->close();
                }

            } else {
                // Usuario invitado
                $stmt = $conexion->prepare(
                    "INSERT INTO usuarios (nombre, email, direccion, password_hash, is_guest)
                     VALUES (NULL, ?, ?, NULL, 1)"
                );
                $stmt->bind_param("ss", $email, $direccion);
                $stmt->execute();
                $user_id = $stmt->insert_id;
                $stmt->close();
            }


            /* ========== INSERTAR PEDIDO ========== */
            $stmt = $conexion->prepare(
                "INSERT INTO pedidos (id_usuario, fecha, total)
                 VALUES (?, NOW(), ?)"
            );
            $stmt->bind_param("id", $user_id, $total);
            $stmt->execute();
            $pedido_id = $stmt->insert_id;
            $stmt->close();


            /* ========== INSERTAR DETALLES ========== */

            $stmt_item = $conexion->prepare(
                "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario)
                 VALUES (?, ?, ?, ?)"
            );

            foreach ($items as $it) {
                $stmt_item->bind_param(
                    "iiid",
                    $pedido_id,
                    $it['id'],
                    $it['cantidad'],
                    $it['precio']
                );
                $stmt_item->execute();
            }

            $stmt_item->close();

            $conexion->commit();

            unset($_SESSION['carrito']);
            header("Location: gracias.php?order_id=" . $pedido_id);
            exit;

        } catch (Exception $e) {
            $conexion->rollback();
            $errors[] = "Error al procesar el pedido: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>MotorWare | Checkout</title>
<link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
<link rel="icon" type="image/png" href="img/favicon.ico">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="header-top">
        <div class="header-content">
            <a href="index.php" class="logo"><img src="img/logo.png" alt="MotorWare"></a>
            <form class="search-bar" method="GET" action="buscar.php">
                <input type="text" name="q" placeholder="Buscar productos...">
                <button type="submit">🔍</button>
            </form>
            <div class="header-icons">
                <a href="usuario.php" class="icon-button"><img src="img/user.png" alt="Usuario">Mi cuenta</a>
                <a href="carrito.php" class="icon-button"><img src="img/shopping-cart.png" alt="Carrito">Carrito</a>
            </div>
        </div>
    </div>
</header>

<main class="main-content">
<div class="carrito-container">

    <!-- RESUMEN DEL PEDIDO -->
    <h2>Resumen del pedido</h2>

    <?php if (!empty($errors)): ?>
        <div style="background:#ffe6e6; border:1px solid #f5c2c2; padding:12px; border-radius:8px; margin-bottom:16px;">
            <ul><?php foreach($errors as $er) echo "<li>".htmlspecialchars($er)."</li>"; ?></ul>
        </div>
    <?php endif; ?>

    <table class="tabla-carrito">
        <thead>
            <tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
        <?php foreach ($items as $it): ?>
            <tr>
                <td class="col-producto">
                    <img src="<?php echo $it['imagen']; ?>" alt="<?php echo htmlspecialchars($it['nombre']); ?>">
                    <span><?php echo htmlspecialchars($it['nombre']); ?></span>
                </td>
                <td><?php echo number_format($it['precio'], 2, ',', '.'); ?> €</td>
                <td><?php echo (int)$it['cantidad']; ?></td>
                <td><?php echo number_format($it['subtotal'], 2, ',', '.'); ?> €</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-carrito">
        <strong>Total: <?php echo number_format($total, 2, ',', '.'); ?> €</strong>
    </div>

<!-- FORMULARIO -->
<form method="POST" novalidate>

    <input type="hidden" name="confirmar_pedido" value="1">

    <fieldset class="tipo-compra-cards" style="margin-bottom: 25px;">
        <legend>Tipo de compra</legend>
        <div class="cards-container">

            <label class="card-radio <?php if(!isset($_POST['tipo']) || $_POST['tipo']==='guest') echo 'selected'; ?>">
                <input type="radio" name="tipo" value="guest"
                    <?php if (!isset($_POST['tipo']) || $_POST['tipo']==='guest') echo 'checked'; ?>>
                <span class="card-title">Invitado</span>
                <span class="card-desc">Compra sin registrarte</span>
            </label>

            <label class="card-radio <?php if(isset($_POST['tipo']) && $_POST['tipo']==='registered') echo 'selected'; ?>">
                <input type="radio" name="tipo" value="registered"
                    <?php if (isset($_POST['tipo']) && $_POST['tipo']==='registered') echo 'checked'; ?>>
                <span class="card-title">Registrado</span>
                <span class="card-desc">Compra con cuenta de usuario</span>
            </label>

        </div>
    </fieldset>

    <!-- CAMPOS INVITADO -->
    <div id="campos-guest" style="margin-bottom: 30px;">
        <label>Email *</label>
        <input type="email" name="guest_email"
            value="<?php echo htmlspecialchars($_POST['guest_email'] ?? ''); ?>">

        <label>Dirección *</label>
        <input type="text" name="guest_direccion"
            value="<?php echo htmlspecialchars($_POST['guest_direccion'] ?? ''); ?>">
    </div>

    <!-- CAMPOS REGISTRADO -->
    <div id="campos-registered" style="display:none; margin-bottom: 30px;">
        <label>Nombre completo *</label>
        <input type="text" name="reg_nombre"
            value="<?php echo htmlspecialchars($_POST['reg_nombre'] ?? ''); ?>">

        <label>Email *</label>
        <input type="email" name="reg_email"
            value="<?php echo htmlspecialchars($_POST['reg_email'] ?? ''); ?>">

        <label>Dirección *</label>
        <input type="text" name="reg_direccion"
            value="<?php echo htmlspecialchars($_POST['reg_direccion'] ?? ''); ?>">

        <label>Contraseña (opcional)</label>
        <input type="password" name="reg_password">
    </div>

    <!-- BOTONES -->
    <div class="acciones-carrito"
         style="margin-top: 35px; display: flex; gap: 15px;">

        <a href="carrito.php" class="btn" style="background: #777;">Volver al carrito</a>

        <button type="submit" class="btn btn-finalizar">
            Confirmar pedido
        </button>

    </div>

</form>

</div>
</main>

<footer>
    <p>&copy; 2025 <strong>MotorWare</strong> - Todos los derechos reservados.</p>
</footer>

<script>
const radios = document.querySelectorAll('input[name="tipo"]');
const guestFields = document.getElementById('campos-guest');
const registeredFields = document.getElementById('campos-registered');

function updateFields() {
    const type = document.querySelector('input[name="tipo"]:checked').value;
    guestFields.style.display = (type === 'guest') ? 'block' : 'none';
    registeredFields.style.display = (type === 'registered') ? 'block' : 'none';
}

radios.forEach(r => r.addEventListener('change', updateFields));
updateFields();

// Cartas Invitado o Registrado
const cards = document.querySelectorAll('.card-radio');
cards.forEach(card => {
    const input = card.querySelector("input[type=radio]");
    input.addEventListener("change", () => {
        cards.forEach(c => c.classList.remove("selected"));
        card.classList.add("selected");
    });
});
</script>


</body>
</html>
