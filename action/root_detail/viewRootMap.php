<?php
/*
 * 配送先一覧をMAPで表示
 * @author Akiko Goto
 * @since 2013/2/7
 * @version 2.6
 */


//会社ID, ドライバーID、日付が来ているか

$keys=array('company_id', 'driver_id', 'date');

foreach($keys as $key){
	if($_GET[$key]){
		$datas[$key] = sanitizeGet($_GET[$key],ENT_QUOTES);
	}else{
		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");	
	}
}

	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $datas['company_id'], $branch_manager_id);

	if(!empty($branch_manager_id)){
				
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $datas['driver_id']);
	
	}


try{		

	$dataList = RootDetail::viewRootDetails($datas['company_id'], $datas['driver_id'],
		$datas['date']);
	
	if($dataList){
		foreach($dataList as $key=>$each_dataList){
			$root_locations[$key]['deliver_time']=$each_dataList['deliver_time'];
			$root_locations[$key]['destination_name']=$each_dataList['destination_name'];
			$root_locations[$key]['address']=$each_dataList['root_address'];
			$root_locations[$key]['latitude']=$each_dataList['latitude'];
			$root_locations[$key]['longitude']=$each_dataList['longitude'];
			$root_locations[$key]['information']=$each_dataList['information'];			
		}
	}

	//ドライバー氏名を取得
	$driver_name_array = Driver::getNameById($datas['driver_id']);
	
	$driver_last_name= $driver_name_array[0]['last_name'];
	$driver_first_name= $driver_name_array[0]['first_name'];
	
}catch(Exception $e){

	$message=$e->getMessage();

}

list($data, $links)=make_page_link($dataList);	

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("date",$datas['date']);
$smarty->assign("driver_last_name",$driver_last_name);
$smarty->assign("driver_first_name",$driver_first_name);
$smarty->assign("root_id",$dataList[0]['root_id']);

$encoded_roots = json_encode($root_locations);
$smarty->assign("root_locations", $encoded_roots);

//最後の場所をGoogle Mapの中心にする
$last_latitude = $root_locations[0]['latitude'];
$last_longitude = $root_locations[0]['longitude'];

//Google Map
$smarty->assign("last_latitude", $last_latitude);
$smarty->assign("last_longitude", $last_longitude);


$smarty->assign("js",'<script src="'.GOOGLE_MAP.'" type="text/javascript"></script>
<script type="text/javascript" src="'.SHORTEST_ROOT_JS.'"></script>');
//$smarty->assign("onload_js","onload=\"initialize($last_latitude,$last_longitude)\"");


$smarty->assign("links",$links['all']);
$smarty->assign("company_id",$datas['company_id']);
$smarty->assign("filename","root_detail/view_root_map.html");
$smarty->display("template.html");

?>