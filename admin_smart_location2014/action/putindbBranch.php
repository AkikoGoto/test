<?php
//営業所情報をデータベースへ投入 新規・編集はフラグで判断

require_once('admin_check_session.php');

$id_array=array('id');
$keys=array();
$keys=array_merge($id_array,$geographic_array);

	//$datas[データ名]でサニタイズされたデータが入っている
		
	foreach($keys as $key){

		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}
	

	//編集画面か、新規データ投入か

	if($datas['id']){
			
		$status=EDIT;
			
			//編集画面の場合は、営業所番号を投入
		$datas['id']=htmlentities($_POST['id'],ENT_QUOTES, mb_internal_encoding());
		
	}else{

		$status=NEWDATA;	
	
	}			
	
		
try{

	//再度データチェック
	$form_validate=new Validate();
	
	//Google Map APIが反応しない時のために、バリデーションしない
	//$errors2=$form_validate->validate_form_geo($datas);
		
	//$errors=array_merge($errors2);		
	
	if($errors){

		$form_validate->show_form($errors);
		$form_validate->lasturl='index.php?action=putBranch';
		
		}else{
		
			//データベースへ投入
			$topic=Branch::putInBranch($datas,$status);		
			$company_id=$datas['company_id'];
			header("Location:index.php?action=message&situation=editData");
	
		exit(0);
		}
	
	}catch(Exception $e){
	die($e->getMessage());
	
	}

?>