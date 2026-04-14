<?php
include("auth/verificar_sesion.php");
include("db/conexion.php");
include("includes/header.php");

// Total de insumos
$sql_productos = "SELECT COUNT(*) as total FROM productos";
$resultado_productos = $conn->query($sql_productos);
$total_productos = $resultado_productos->fetch_assoc()["total"];

// Total de existencias
$sql_stock = "SELECT SUM(stock) as total_stock FROM productos";
$resultado_stock = $conn->query($sql_stock);
$total_stock = $resultado_stock->fetch_assoc()["total_stock"];

// Insumos con stock bajo
$sql_bajo = "SELECT COUNT(*) as stock_bajo FROM productos WHERE stock <= stock_minimo";
$resultado_bajo = $conn->query($sql_bajo);
$stock_bajo = $resultado_bajo->fetch_assoc()["stock_bajo"];

// Lista de insumos con stock bajo
$sql_insumos_bajos = "SELECT nombre, categoria, stock, stock_minimo 
                      FROM productos 
                      WHERE stock <= stock_minimo
                      ORDER BY stock ASC";
$resultado_insumos_bajos = $conn->query($sql_insumos_bajos);

// Últimos movimientos
$sql_movimientos = "SELECT movimientos.*, productos.nombre AS producto_nombre
                    FROM movimientos
                    INNER JOIN productos ON movimientos.producto_id = productos.id
                    ORDER BY movimientos.fecha DESC, movimientos.id DESC
                    LIMIT 5";
$resultado_movimientos = $conn->query($sql_movimientos);
?>

<section class="hero">
    <h2>Dashboard de Inventario</h2>
    <p>
        Visualiza métricas clave, alertas de existencias y los últimos movimientos del restaurante.
    </p>
</section>

<section class="cards">
    <div class="card">
        <h3>Total de insumos</h3>
        <p><?php echo htmlspecialchars($total_productos); ?></p>
    </div>

    <div class="card">
        <h3>Existencias totales</h3>
        <p><?php echo $total_stock ? htmlspecialchars($total_stock) : 0; ?></p>
    </div>

    <div class="card">
        <h3>Insumos con stock bajo</h3>
        <p><?php echo htmlspecialchars($stock_bajo); ?></p>
    </div>
</section>

<div class="dashboard-grid">
    <div class="card">
        <h3>Alerta de existencias bajas</h3>

        <?php if ($resultado_insumos_bajos->num_rows > 0): ?>
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Insumo</th>
                        <th>Categoría</th>
                        <th>Existencias</th>
                        <th>Mínimo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($fila = $resultado_insumos_bajos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila["nombre"]); ?></td>
                            <td><?php echo htmlspecialchars($fila["categoria"]); ?></td>
                            <td>
                                <span class="badge badge-salida">
                                    <?php echo htmlspecialchars($fila["stock"]); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($fila["stock_minimo"]); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="empty-text">No hay insumos con stock bajo en este momento.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Últimos movimientos</h3>

        <?php if ($resultado_movimientos->num_rows > 0): ?>
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Insumo</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($mov = $resultado_movimientos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($mov["producto_nombre"]); ?></td>
                            <td>
                                <?php if ($mov["tipo"] == "entrada"): ?>
                                    <span class="badge badge-entrada">Entrada</span>
                                <?php else: ?>
                                    <span class="badge badge-salida">Salida</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($mov["cantidad"]); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="empty-text">Todavía no hay movimientos registrados.</p>
        <?php endif; ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>