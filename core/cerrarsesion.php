<?php
session_start();
$_SESSION=array();
session_destroy();

header('Location: ../core/login.php');
exit;

?>