<?php

//session_start();
//$_SESSION = array();
session_destroy();

setcookie('user_id','');

header('Location:index.php');

?>