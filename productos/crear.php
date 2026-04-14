<?php
include("../auth/verificar_sesion.php");
include("../db/conexion.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $categoria = trim($_POST["categoria"]);
    $stock = intval($_POST["stock"]);
    $stock_minimo = intval($_POST["stock_minimo"]);

    if ($nombre === "" || $categoria === "") {
        $error = "Completa todos los campos.";
    } elseif ($stock < 0 || $stock_minimo < 0) {
        $error = "No se permiten valores negativos.";
    } else {
        $stmt = $conn->prepare("INSERT INTO productos (nombre, categoria, stock, stock_minimo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $nombre, $categoria, $stock, $stock_minimo);

        if ($stmt->execute()) {
            header("Location: listar.php");
            exit();
        } else {
            $error = "Error al guardar el insumo.";
        }
    }
}
?>

<?php include("../includes/header.php"); ?>

<section class="hero">
    <h2>Registrar insumo</h2>
    <p>Completa la información para registrar un nuevo insumo en el inventario.</p>
</section>

<?php if (!empty($error)): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="POST" action="">
        <div class="form-group">
            <label>Nombre</label>
            <input 
                type="text" 
                name="nombre" 
                required
                value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>"
            >
        </div>

        <div class="form-group">
            <label>Categoría</label>
            <select name="categoria" required>
                <option value="">Seleccione una categoría</option>
                <option value="Granos" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'Granos') ? 'selected' : ''; ?>>Granos</option>
                <option value="Carnes" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'Carnes') ? 'selected' : ''; ?>>Carnes</option>
                <option value="Vegetales" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'Vegetales') ? 'selected' : ''; ?>>Vegetales</option>
                <option value="Bebidas" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'Bebidas') ? 'selected' : ''; ?>>Bebidas</option>
                <option value="Lácteos" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'Lácteos') ? 'selected' : ''; ?>>Lácteos</option>
                <option value="Desechables" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'Desechables') ? 'selected' : ''; ?>>Desechables</option>
                <option value="Limpieza" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'Limpieza') ? 'selected' : ''; ?>>Limpieza</option>
                <option value="Condimentos" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'Condimentos') ? 'selected' : ''; ?>>Condimentos</option>
                <option value="Pollos" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'Pollos') ? 'selected' : ''; ?>>Pollos</option>
            </select>
        </div>

        <div class="form-group">
            <label>Stock</label>
            <input 
                type="number" 
                name="stock" 
                min="0" 
                required
                value="<?php echo isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : ''; ?>"
            >
        </div>

        <div class="form-group">
            <label>Stock mínimo</label>
            <input 
                type="number" 
                name="stock_minimo" 
                min="0" 
                required
                value="<?php echo isset($_POST['stock_minimo']) ? htmlspecialchars($_POST['stock_minimo']) : ''; ?>"
            >
        </div>

        <button type="submit" class="btn">Guardar</button>
    </form>
</div>

<?php include("../includes/footer.php"); ?>