<?php
include("../auth/verificar_sesion.php");
include("../db/conexion.php");
include("../includes/header.php");

$categoria_filtro = $_GET["categoria"] ?? "";

$sql = "SELECT * FROM productos";
$params = [];
$types = "";

if (!empty($categoria_filtro)) {
    $sql .= " WHERE categoria = ?";
    $params[] = $categoria_filtro;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();
?>

<section class="hero">
    <h2>Insumos</h2>
    <p>Aquí vas a poder ver y administrar todos los insumos registrados.</p>
</section>

<div class="actions" style="display:flex; justify-content:space-between; align-items:center; gap:15px; flex-wrap:wrap;">
    <a href="crear.php" class="btn">+ Registrar insumo</a>

    <form method="GET" action="" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <select name="categoria">
            <option value="">Todas las categorías</option>
            <option value="Granos" <?php if($categoria_filtro=="Granos") echo "selected"; ?>>Granos</option>
            <option value="Carnes" <?php if($categoria_filtro=="Carnes") echo "selected"; ?>>Carnes</option>
            <option value="Vegetales" <?php if($categoria_filtro=="Vegetales") echo "selected"; ?>>Vegetales</option>
            <option value="Bebidas" <?php if($categoria_filtro=="Bebidas") echo "selected"; ?>>Bebidas</option>
            <option value="Lácteos" <?php if($categoria_filtro=="Lácteos") echo "selected"; ?>>Lácteos</option>
            <option value="Desechables" <?php if($categoria_filtro=="Desechables") echo "selected"; ?>>Desechables</option>
            <option value="Limpieza" <?php if($categoria_filtro=="Limpieza") echo "selected"; ?>>Limpieza</option>
            <option value="Condimentos" <?php if($categoria_filtro=="Condimentos") echo "selected"; ?>>Condimentos</option>
        </select>

        <button type="submit" class="btn">Filtrar</button>
        <a href="listar.php" class="btn btn-secondary">Limpiar</a>
    </form>
</div>

<div class="card">
    <h3>Lista de insumos</h3>

    <?php if ($resultado->num_rows > 0): ?>
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Existencias</th>
                    <th>Existencia mínima</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($fila = $resultado->fetch_assoc()): ?>
                    <?php $stock_bajo = $fila["stock"] <= $fila["stock_minimo"]; ?>

                    <tr class="<?php echo $stock_bajo ? 'fila-stock-bajo' : ''; ?>">
                        <td><?php echo $fila["id"]; ?></td>
                        <td><?php echo htmlspecialchars($fila["nombre"]); ?></td>
                        <td><?php echo htmlspecialchars($fila["categoria"]); ?></td>
                        <td>
                            <?php if ($stock_bajo): ?>
                                <span class="badge badge-salida"><?php echo $fila["stock"]; ?></span>
                            <?php else: ?>
                                <span class="badge badge-entrada"><?php echo $fila["stock"]; ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $fila["stock_minimo"]; ?></td>
                        <td>
                            <?php if ($stock_bajo): ?>
                                <span class="badge badge-alerta">Stock bajo</span>
                            <?php else: ?>
                                <span class="badge badge-ok">Normal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="editar.php?id=<?php echo $fila['id']; ?>" class="btn btn-edit">Editar</a>
                            <a href="eliminar.php?id=<?php echo $fila['id']; ?>" class="btn btn-delete" onclick="return confirm('¿Seguro que deseas eliminar este insumo?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty-text">No hay insumos registrados para esa categoría.</p>
    <?php endif; ?>
</div>

<?php
$stmt->close();
include("../includes/footer.php");
?>