<?php

session_start();
$_SESSION = array();
session_destroy();

setcookie('driver_id','');
Driver::deleteCredential();

header('Location:index.php?action=driverLogin');

?>