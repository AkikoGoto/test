<?php

//session_start();
//$_SESSION = array();
session_destroy();

setcookie('u_id','');
Data::deleteCredential();

header('Location:index.php');

?>