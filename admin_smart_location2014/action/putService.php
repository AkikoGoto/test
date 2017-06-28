<?php

//サービス追加画面表示
require_once('admin_check_session.php');

$smarty->assign("filename","put_service.html");
$smarty->display('template.html');


?>