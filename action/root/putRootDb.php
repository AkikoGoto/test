<?php
/*
 * ルート情報をDBに格納する
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */


$id_array=array('id', 'company_id');
$keys=array();
$keys=array_merge($id_array,$root_array);

//$datas[データ名]にサニタイズされたデータを入れる
//
foreach($keys as $key){

	$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());

}
	
if($datas['id']){
	$status='EDIT';
}else{
	$status='NEWDATA';
}

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

		//データベースへ投入
		Root::putInRoot($datas, $status);
		unset($_SESSION['root_data']);
		
		$company_id = $datas['company_id'];
		$driver_id = $datas['driver_id'];
		
		$root_data = ROOT::getByDate($driver_id, $datas['date']);
		$root_id = $root_data['id'];
		
		header("Location:index.php?action=/root_detail/putRootDetail&root_id=$root_id&company_id=$company_id");
		
		exit(0);
	}

}catch(Exception $e){
	die($e->getMessage());

}

?>