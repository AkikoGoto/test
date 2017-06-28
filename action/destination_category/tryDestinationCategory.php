<?php
/*
 * 配送先カテゴリー入力　確認画面
 * @author Akiko Goto
 * @since 2014/09/12
 * @version 4.4
 */


$id_array=array('id','company_id');
$keys=array();
$keys=array_merge($id_array,$destination_category_array);


//$datas[データ名]でサニタイズされたデータが入っている

	foreach($keys as $key){
		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		$_SESSION['destination_category_data'][$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}

	if($datas['id']){
		
		$status='EDIT';
		$smarty->assign("status",$status);	
	
	}
	
	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $datas['company_id'], $branch_manager_id);
				
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
			
			$errors = $form_validate->validate_destination_category($datas);
		
			if($errors){
		
				$form_validate->show_form($errors);
				
			}else{
					
				foreach($datas as $key => $value){					
					$smarty->assign("$key",$value);
				}		
				
				$smarty->assign("id",$datas['id']);		
				$smarty->assign("company_id",$datas['company_id']);				
				$smarty->assign("drivers",$drivers);
				$smarty->assign("filename","destination_category/try_destination_category.html");
				$smarty->display("template.html");
			
			}
		
		}catch(Exception $e){
			
		die($e->getMessage());
		
		}
	}

?>