<?php
//ドライバー情報をデータベースへ投入

$id_array=array('id');
$keys=array();
$keys=array_merge( $id_array, $user_array);

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
	//会社のユーザーIDと、編集するIDがあっているか確認
	user_auth($u_id,$datas['company_id']);	

try{

	//再度データチェック
	$form_validate=new Validate();
	$errors1=$form_validate->validate_form_viewer($datas,$status);
		
	$errors=array_merge($errors1);
	
	if($errors){

		$form_validate->show_form($errors);
		$form_validate->lasturl='index.php?action=put_user';
		
	}else{
		
		//データベースへ投入
		$topic=Users::putInUser($datas,$status);
		unset($_SESSION['pre_data']);
	
		header("Location:index.php?action=message_mobile&situation=after_edit_viewer");

		exit(0);
		
	}
	
	}catch(Exception $e){
		
		die($e->getMessage());
		
	}

?>