<?php
//ドライバー情報をデータベースへ投入
require_once('admin_check_session.php');
$keys = $drivers_array;
$id_array = array('id');
$keys = array_merge($keys, $id_array);

/*$keys=array('company_id','last_name','first_name','furigana','experience','no_accident',
	'car_type','equipment','sex','birthday','erea','regist_number','message');
*/

//$datas[データ名]でサニタイズされたデータが入っている
	
foreach($keys as $key){
	$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}
	
	
	$ip=getenv("REMOTE_ADDR");

	//携帯版の場合、Shift_jisからUTF-8に変換する
/*	$script_path=$_SERVER['SCRIPT_NAME'];
		if(preg_match('/m/',$script_path)){
			$title=mb_convert_encoding($title, "UTF-8", "Shift-JIS");
			$topic=mb_convert_encoding($topic, "UTF-8", "Shift-JIS");
			$name=mb_convert_encoding($name, "UTF-8", "Shift-JIS");
			$sex=mb_convert_encoding($sex, "UTF-8", "Shift-JIS");
			$cat=mb_convert_encoding($cat, "UTF-8", "Shift-JIS");			
		}	
*/

try{
	
	//新規データか、編集か
	if($datas['id']){
		$status = 'EDIT';					
	}else{
		$status = 'NEWDATA';
	}	

	//再度データチェック
	$form_validate=new Validate();
	
	$errors=$form_validate->validate_form_driver($datas, $status, $_FILES);
	
	if($errors){

		$form_validate->show_form($errors);
		$form_validate->lasturl='index.php?action=putin';
		
		}else{
		
		//データベースへ投入
	
		$topic=Driver::putInDriver($datas,$status);
			
		header("Location:index.php");
	
		exit(0);
		}
	
	}catch(Exception $e){
	die($e->getMessage());
	
	}

?>