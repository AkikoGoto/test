<?php
//ドライバー情報をデータベースへ投入

$id_array=array('id');
$keys=array();
$keys=array_merge($id_array,$drivers_array);

	//$datas[データ名]にサニタイズされたデータを入れる
	//	
	foreach($keys as $key){

		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		
	}
			
/*	foreach($datas_shift_jis as $key => $value){
		
		//携帯版のため、Shift-JISからUTF-8に文字コードを変換		
		$datas[$key]=mb_convert_encoding($value, "UTF-8", "Shift-JIS");
	
	}
*/		
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
	
	$errors1=$form_validate->validate_form_driver($datas,$status);
		
	$errors=array_merge($errors1);
	
	if($errors){

		$form_validate->show_form($errors);
		$form_validate->lasturl='index.php?action=putin';
		
		}else{
		
		//データベースへ投入
		$topic=Driver::putInDriver($datas,$status);
		unset($_SESSION['pre_data']);
	
		$company_id = $datas['company_id'];
		header("Location:index.php?action=message_mobile&situation=after_edit_driver&company_id=$company_id");

		exit(0);
		}
	
	}catch(Exception $e){
	die($e->getMessage());
	
	}

?>