<?php
//ドライバーデータ登録確認画面
$id_array=array('id');
$keys=array();
$keys=array_merge($id_array, $user_array);

//会社のユーザーIDと、編集するIDがあっているか確認
user_auth($u_id,$_POST['company_id']);
//$datas[データ名]でサニタイズされたデータが入っている
foreach($keys as $key){

	$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	
	//フォームに戻った場合のため、過去のデータを保持
	$_SESSION['pre_data'][$key]=$_POST[$key];						

}

//編集画面か、新規データ投入か

if($_POST['id']){

	$status=EDIT;
	
	//編集画面の場合は、営業所番号を投入
	$datas['id']=htmlentities($_POST['id'],ENT_QUOTES, Shift_JIS);
	
}else{

	$status=NEWDATA;	
	
}
	
$ip=getenv("REMOTE_ADDR");

$ban_ip=Data::banip();

//禁止リストにあるIPとの照合
if($ip==$ban_ip){

	//メッセージ画面を表示する
      header('Location:index.php?action=message&situation=ban_ip');

}else{

	try{
	
	//入力データ検証

	$form_validate = new Validate();
	
	$errors = $form_validate->validate_form_viewer( $datas, $status );
	
		if($errors){
	
			$form_validate->show_form($errors);
			$form_validate->lasturl='index.php?action=putUser';
		
		}else{
		
		//確認画面表示
	
			foreach($datas as $key => $value){

				$smarty->assign("$key",$value);
			
			}
			
			//画像ファイルをアップロード
			$files = $_FILES;
			$image_name = try_image_upload($files, $datas);
			
			$smarty->assign("image_name",$image_name);
			
			$smarty->assign("branch_name",$branch_name);
			$smarty->assign("company_name",$company_name);
			$smarty->assign("target",'putindbUser');
			$smarty->assign("filename","confirmUser.html");
			$smarty->display("template.html");
			
		}
	
	}catch(Exception $e){
	die($e->getMessage());
	
	}
}
?>