<?php

/**
 *管理画面　データ単位で消去 
 */

require_once('admin_check_session.php');

$id=$_GET['id'];

//仮情報テーブルかどうかのフラグ
$from_web=$_GET['from_web'];
	
	$select_driver_id = Data::select_driver_id($id);
	$count=count($select_driver_id);
	
	for($i=0;$i<$count;$i++){
		$driver_id=$select_driver_id[$i]['id'];
		$del_th=Data::deleteData($id, $from_web, $driver_id,$i);
	}
		
    header('Location:index.php?action=message&situation=deleteData');
	
?>