<?php
session_start();
session_unset();
session_destroy();

header("Location: /inventario-dashboard/auth/login.php");
exit();
?>