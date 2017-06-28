<?php
//日報用データ確認画面
$id_array=array('id','driver_id','company_id');
$keys=array();
$keys=array_merge($id_array,$day_report_array);
//$datas[データ名]でサニタイズされたデータが入っている

	foreach($keys as $key){
		if(!empty($_POST[$key])){
			$datas[$key] = htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
			if($key != "driver_id"){
				$_SESSION[$key] = $datas[$key];
			}
		}
	}

	if(!empty($branch_manager_id)){
			
		//営業所のマネージャーがその会社の情報にアクセスしてよいか
		branch_manager_auth($branch_manager_id, $datas['company_id']);
	
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $datas['driver_id']);
	
	}else{
		
		//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
		driver_company_auth($datas['driver_id'],$session_driver_id,$datas['company_id'],$u_id);
		
		//ドライバー本人がログインしている場合、会社が編集許可を子なっていなければエラー表示
		driver_editing_banned_check($datas['driver_id'],$session_driver_id,$datas['company_id'],$u_id);
	}
	
	//開始時刻、終了時刻
	$datas['drive_date'] = $datas['year_day_report']."-".$datas['month_day_report']."-". $datas['day_day_report'];

try{
				
	$ip=getenv("REMOTE_ADDR");

	$ban_ip=Data::banip();
	
	//禁止リストにあるIPとの照合
	if($ip==$ban_ip){

	//メッセージ画面を表示する
      header('Location:index.php?action=message&situation=ban_ip');

	}else{


		//入力データ検証
		//開始と終了が、既存のデータにかぶっていないか
		$form_validate=new Validate();
		
		$errors=$form_validate->validate_day_report_data($datas);
		
		if($errors){
		
				$form_validate->show_form($errors);
				$form_validate->lasturl='index.php?action=day_report_putin';
				
		}else{		
						
			foreach($datas as $key => $value){
				
					//	$value=mb_convert_encoding($value, "UTF-8", "SJIS");									
				$smarty->assign("$key",$value);
	
			}
			
			$smarty->assign("filename","day_report/day_report_confirm.html");
			$smarty->assign("target","day_report_putindb");
			$smarty->display("template.html");	
			
		}
			
	}
				
			
	}catch(Exception $e){

		die($e->getMessage());
		
	}
	

	
?>