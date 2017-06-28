<?php

session_start();
$_SESSION = array();
session_destroy();

setcookie('user_id','');
Users::deleteCredential();

header('Location:index.php?action=user/userLogin');

?>