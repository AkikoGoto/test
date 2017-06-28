<?php
/*
 * ルートコピー　確認画面
 * @author Akiko Goto
 * @since 2013/2/22
 * @version 2.8
 */


$id_array=array('id','company_id');
$date_array=array('year', 'month', 'day');
$copied_array=array('copied_driver_last_name', 'copied_driver_first_name', 'copied_driver_id', 'copied_date');
$keys=array();
$keys=array_merge($id_array, $date_array, $root_array, $copied_array);

//$datas[データ名]でサニタイズされたデータが入っている


	foreach($keys as $key){
		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		$_SESSION['root_data'][$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}
	
	$datas['date'] = $datas['year']."-".$datas['month']."-". $datas['day'];	

	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $datas['company_id'], $branch_manager_id);
	
	if(!empty($branch_manager_id)){
			
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $datas['driver_id']);
	
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
			
			$errors = $form_validate->validate_root($datas);
		
			if($errors){
		
				$form_validate->show_form($errors);
				
			}else{
					
				foreach($datas as $key => $value){					
					$smarty->assign("$key",$value);
					
				}
				
				//ドライバー氏名をIDから取得
				$driver_name = Driver::getNameById($datas['driver_id']);
				
				$smarty->assign("driver_name",$driver_name[0]['last_name'].' '.$driver_name[0]['first_name']);		

				$smarty->assign("id",$datas['id']);		
				$smarty->assign("company_id",$datas['company_id']);			
				$smarty->assign("filename","root/tryCopyRoot.html");
				$smarty->display("template.html");
			
			}
		
		}catch(Exception $e){
			
		die($e->getMessage());
		
		}
	}

?>