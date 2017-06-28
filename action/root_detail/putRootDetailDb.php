<?php
/*
 * ルート詳細情報をDBに格納する
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */

//会社ID 配列ではないので、別の処理
$company_id = htmlentities($_POST['company_id'], ENT_QUOTES, mb_internal_encoding());

//ルートID 配列ではないので、別の処理
$root_id = htmlentities($_POST['root_id'], ENT_QUOTES, mb_internal_encoding());

//$datas[データ名]にサニタイズされたデータを入れる
foreach($_POST as $key=>$value){
	
	if(!empty($value) && ($key != "company_id") && ($key !="submit")&& ($key !="root_id")){
		foreach($value as $key2=>$value2){
		
			//POSTのデータを行列入れ替えする
			$datas[$key2][$key] = htmlentities($value2,ENT_QUOTES, mb_internal_encoding());
			
			//ルートIDを入れておく
			$datas[$key2]['root_id'] = $root_id;			
			
		
		}	
	}

}
	

if($datas[0]['id']){

	$status='EDIT';
		
}else{

	$status='NEWDATA';

}

$ip=getenv("REMOTE_ADDR");
//会社のユーザーIDと、編集するIDがあっているか確認
//user_auth($u_id,$company_id);

try{

	//再度データチェック
//	$form_validate=new Validate();

//	$errors1=$form_validate->validate_destination($datas);

//	$errors=array_merge($errors1);

	if($errors){

		$form_validate->show_form($errors);
		$form_validate->lasturl='index.php?action=/destination/putDestinationDb';

	}else{

		//戻りリンクのために、ルートのドライバーIDと日付を取得
		$root_information = Root::getById($root_id);
		$date = $root_information[0]['date'];
		$driver_id = $root_information[0]['driver_id'];
		
		
		//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
		company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
		
		if(!empty($branch_manager_id)){
			
			//このドライバーが許可された営業所のドライバー情報か
			branch_manager_driver_auth($branch_manager_id, $driver_id);
		
		}
		
		//データベースへ投入
		RootDetail::putInRootDetail($datas, $status);
		unset($_SESSION['root_detail']);

		header("Location:index.php?action=message_mobile&situation=after_edit_root_detail&company_id=$company_id&driver_id=$driver_id&date=$date");

		exit(0);
	}

}catch(Exception $e){
	die($e->getMessage());

}

?>