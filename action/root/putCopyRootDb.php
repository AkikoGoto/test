<?php
/*
 * ルート情報コピー　DBに格納する
 * @author Akiko Goto
 * @since 2013/2/22
 * @version 2.8
 */


$id_array=array('id', 'company_id');
$copied_array=array('copied_driver_id', 'copied_date');
$keys=array();
$keys=array_merge($id_array,$root_array,$copied_array);


//$datas[データ名]にサニタイズされたデータを入れる
//
foreach($keys as $key){

	$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());

}
	
	$status='NEWDATA';

$ip=getenv("REMOTE_ADDR");


	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $datas['company_id'], $branch_manager_id);
	
	if(!empty($branch_manager_id)){
			
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $datas['driver_id']);
	
	}

try{

	//再度データチェック
	$form_validate=new Validate();

	$errors1=$form_validate->validate_root($datas);

	$errors=array_merge($errors1);

	if($errors){

		$form_validate->show_form($errors);
		$form_validate->lasturl='index.php?action=/destination/putDestinationDb';

	}else{

		//まずはコピーの大元となる、ルートを作成
		$root_id = Root::putInRoot($datas, $status);	
		
		//その後、ルート詳細をコピーする
		$copied_data['driver_id'] = $datas['copied_driver_id'];
		$copied_data['date'] = $datas['copied_date'];
		
		$copy_to_data['driver_id'] = $datas['driver_id'];
		$copy_to_data['date'] = $datas['date'];
		
		RootDetail::copyRootDetails($copied_data, $root_id, $datas['company_id']);
		
		unset($_SESSION['root_data']);
		
		$company_id = $datas['company_id'];
		$driver_id = $datas['driver_id'];
		$date = $datas['date'];
		
		header("Location:index.php?action=message_mobile&situation=after_copy_root&driver_id=$driver_id&company_id=$company_id&date=$date");

		exit(0);
	}

}catch(Exception $e){
	die($e->getMessage());

}

?>