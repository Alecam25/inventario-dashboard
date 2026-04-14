<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Dashboard</title>
    <link rel="stylesheet" href="/inventario-dashboard/css/styles.css">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <h1 class="logo"><span>Inventario</span></h1>

            <nav class="nav-links">
                <a href="/inventario-dashboard/index.php">Dashboard</a>
                <a href="/inventario-dashboard/productos/listar.php">Insumos</a>
                <a href="/inventario-dashboard/movimientos/listar.php">Movimientos</a>
                <span class="user-name">
                    <?php echo isset($_SESSION["usuario_nombre"]) ? $_SESSION["usuario_nombre"] : "Usuario"; ?>
                </span>
                <a href="/inventario-dashboard/auth/logout.php" class="btn btn-logout">Salir</a>
            </nav>
        </div>
    </header>

    <main class="container">