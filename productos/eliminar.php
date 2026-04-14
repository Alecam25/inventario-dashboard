<?php
include("../auth/verificar_sesion.php");
include("../db/conexion.php");

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: listar.php");
    exit();
}

$id = intval($_GET["id"]);

$stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: listar.php");
    exit();
} else {
    echo "Error al eliminar: " . htmlspecialchars($conn->error);
}
?>