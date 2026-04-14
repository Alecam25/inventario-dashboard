<?php
include("../auth/verificar_sesion.php");
include("../db/conexion.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $producto_id = intval($_POST["producto_id"]);
    $tipo = trim($_POST["tipo"]);
    $cantidad = intval($_POST["cantidad"]);
    $observacion = trim($_POST["observacion"]);

    if ($producto_id <= 0 || $cantidad <= 0) {
        $error = "Debes seleccionar un insumo y una cantidad válida.";
    } elseif ($tipo !== "entrada" && $tipo !== "salida") {
        $error = "Tipo de movimiento no válido.";
    } else {
        $stmt_producto = $conn->prepare("SELECT stock FROM productos WHERE id = ?");
        $stmt_producto->bind_param("i", $producto_id);
        $stmt_producto->execute();
        $resultado_producto = $stmt_producto->get_result();

        if ($resultado_producto->num_rows > 0) {
            $producto = $resultado_producto->fetch_assoc();
            $stock_actual = intval($producto["stock"]);

            if ($tipo == "entrada") {
                $nuevo_stock = $stock_actual + $cantidad;
            } else {
                if ($cantidad > $stock_actual) {
                    $error = "No hay existencias suficientes para registrar la salida.";
                } else {
                    $nuevo_stock = $stock_actual - $cantidad;
                }
            }

            if (empty($error)) {
                $conn->begin_transaction();

                try {
                    $stmt_movimiento = $conn->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, observacion) VALUES (?, ?, ?, ?)");
                    $stmt_movimiento->bind_param("isis", $producto_id, $tipo, $cantidad, $observacion);
                    $stmt_movimiento->execute();

                    $stmt_stock = $conn->prepare("UPDATE productos SET stock = ? WHERE id = ?");
                    $stmt_stock->bind_param("ii", $nuevo_stock, $producto_id);
                    $stmt_stock->execute();

                    $conn->commit();
                    header("Location: listar.php");
                    exit();
                } catch (Exception $e) {
                    $conn->rollback();
                    $error = "Error al registrar el movimiento.";
                }
            }
        } else {
            $error = "El insumo seleccionado no existe.";
        }
    }
}

$sql_productos = "SELECT id, nombre, stock FROM productos ORDER BY nombre ASC";
$resultado_productos = $conn->query($sql_productos);
?>

<?php include("../includes/header.php"); ?>

<section class="hero">
    <h2>Registrar movimiento</h2>
    <p>Registra entradas de insumos o salidas por uso en cocina.</p>
</section>

<?php if (!empty($error)): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="POST" action="">
        <div class="form-group">
            <label>Insumo</label>
            <select name="producto_id" required>
                <option value="">Seleccione un insumo</option>
                <?php while($fila = $resultado_productos->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($fila['id']); ?>">
                        <?php echo htmlspecialchars($fila['nombre']); ?> (Existencias: <?php echo htmlspecialchars($fila['stock']); ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Tipo de movimiento</label>
            <select name="tipo" required>
                <option value="">Seleccione una opción</option>
                <option value="entrada">Entrada de insumo</option>
                <option value="salida">Salida por uso</option>
            </select>
        </div>

        <div class="form-group">
            <label>Cantidad</label>
            <input type="number" name="cantidad" min="1" required>
        </div>

        <div class="form-group">
            <label>Observación</label>
            <input type="text" name="observacion" placeholder="Ej: compra al proveedor / uso en cocina">
        </div>

        <button type="submit" class="btn">Guardar movimiento</button>
    </form>
</div>

<?php include("../includes/footer.php"); ?>