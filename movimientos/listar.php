<?php
include("../auth/verificar_sesion.php");
include("../db/conexion.php");
include("../includes/header.php");

$sql = "SELECT movimientos.*, productos.nombre AS producto_nombre
        FROM movimientos
        INNER JOIN productos ON movimientos.producto_id = productos.id
        ORDER BY movimientos.fecha DESC, movimientos.id DESC";

$resultado = $conn->query($sql);
?>

<section class="hero">
    <h2>Movimientos de inventario</h2>
    <p>Consulta el historial de entradas y salidas de insumos del restaurante.</p>
</section>

<div class="actions">
    <a href="crear.php" class="btn">+ Registrar movimiento</a>
</div>

<div class="card">
    <h3>Historial de movimientos</h3>

    <?php if ($resultado->num_rows > 0): ?>
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Insumo</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Observación</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php while($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila["id"]); ?></td>
                        <td><?php echo htmlspecialchars($fila["producto_nombre"]); ?></td>
                        <td>
                            <?php if ($fila["tipo"] == "entrada"): ?>
                                <span class="badge badge-entrada">Entrada</span>
                            <?php else: ?>
                                <span class="badge badge-salida">Salida</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($fila["cantidad"]); ?></td>
                        <td><?php echo $fila["observacion"] ? htmlspecialchars($fila["observacion"]) : "-"; ?></td>
                        <td><?php echo htmlspecialchars($fila["fecha"]); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="font-size:16px; color:#6b7280; margin-top:10px;">
            Todavía no hay movimientos registrados.
        </p>
    <?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>