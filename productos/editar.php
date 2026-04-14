<?php
include("../auth/verificar_sesion.php");
include("../db/conexion.php");

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: listar.php");
    exit();
}

$id = (int) $_GET["id"];

$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header("Location: listar.php");
    exit();
}

$producto = $resultado->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $categoria = trim($_POST["categoria"]);
    $stock = (int) $_POST["stock"];
    $stock_minimo = (int) $_POST["stock_minimo"];

    $stmt_update = $conn->prepare("UPDATE productos SET nombre = ?, categoria = ?, stock = ?, stock_minimo = ? WHERE id = ?");
    $stmt_update->bind_param("ssiii", $nombre, $categoria, $stock, $stock_minimo, $id);

    if ($stmt_update->execute()) {
        header("Location: listar.php");
        exit();
    } else {
        $error = "Error al actualizar el insumo.";
    }

    $stmt_update->close();
}
?>

<?php include("../includes/header.php"); ?>

<section class="hero">
    <h2>Editar insumo</h2>
    <p>Modifica la información del insumo seleccionado.</p>
</section>

<?php if (isset($error)): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="POST">
    <div class="form-group">
        <label>Nombre</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto["nombre"]); ?>" required>
    </div>

    <div class="form-group">
        <label>Categoría</label>
        <select name="categoria" required>
            <option value="">Seleccione una categoría</option>
            <option value="Granos" <?php echo ($producto["categoria"] == "Granos") ? "selected" : ""; ?>>Granos</option>
            <option value="Carnes" <?php echo ($producto["categoria"] == "Carnes") ? "selected" : ""; ?>>Carnes</option>
            <option value="Vegetales" <?php echo ($producto["categoria"] == "Vegetales") ? "selected" : ""; ?>>Vegetales</option>
            <option value="Bebidas" <?php echo ($producto["categoria"] == "Bebidas") ? "selected" : ""; ?>>Bebidas</option>
            <option value="Lácteos" <?php echo ($producto["categoria"] == "Lácteos") ? "selected" : ""; ?>>Lácteos</option>
            <option value="Desechables" <?php echo ($producto["categoria"] == "Desechables") ? "selected" : ""; ?>>Desechables</option>
            <option value="Limpieza" <?php echo ($producto["categoria"] == "Limpieza") ? "selected" : ""; ?>>Limpieza</option>
            <option value="Condimentos" <?php echo ($producto["categoria"] == "Condimentos") ? "selected" : ""; ?>>Condimentos</option>
            <option value="Pollos" <?php echo ($producto["categoria"] == "Pollos") ? "selected" : ""; ?>>Pollos</option>
        </select>
    </div>

    <div class="form-group">
        <label>Stock</label>
        <input type="number" name="stock" min="0" value="<?php echo htmlspecialchars($producto["stock"]); ?>" required>
    </div>

    <div class="form-group">
        <label>Stock mínimo</label>
        <input type="number" name="stock_minimo" min="0" value="<?php echo htmlspecialchars($producto["stock_minimo"]); ?>" required>
    </div>

    <button type="submit" class="btn">Actualizar</button>
</form>
</div>

<?php include("../includes/footer.php"); ?>