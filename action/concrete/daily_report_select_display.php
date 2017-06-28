<?php
// $_POST['driver_id']=1130;
// $_POST['time_from_year']=2014;
// $_POST['time_from_month']=6;
// $_POST['time_from_day']=24;
// $_POST['time_to_year']=2014;
// $_POST['time_to_month']=6;
// $_POST['time_to_day']=24;
// $_POST['work_id']=44662;
var_dump($_POST);
exit;
	try{
		if(!empty($_POST['driver_id'])){
			
			foreach($daily_select_report as $key){
				$_POST[$key]=trim( mb_convert_kana($_POST[$key], "s"));
				$datas[$key] = htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
			}
			$exploded_date = explode("-", $datas['date']);
			$datas['time_from_year'] = $exploded_date[0];
			$datas['time_from_month'] = $exploded_date[1];
			$datas['time_from_day'] = $exploded_date[2];
			//データチェック
			$form_validate = new Concrete_Validate();
			
			$errors = $form_validate->validate_form_daily_report_select($datas);
			
			if($errors){
					
				$form_validate->show_form($errors);
					
			}else{
				
				foreach($datas as $key => $value){
					$smarty->assign("$key",$value);
				}
				
				$smarty->assign("filename","concrete/daily_report_select_display.html");
				$smarty->display("template.html");
			}
		}
	
	}catch(Exception $e){
		
		$message=$e->getMessage();
	
	}

?>