<?php
//会社情報をデータベースへ投入 新規データ・編集データ

require_once('admin_check_session.php');

$id_array=array('id','is_group_manager','invoice');
$keys=array();

//仮登録情報かどうかのフラグ
	$datas['activation']=htmlentities($_POST['activation'],ENT_QUOTES, mb_internal_encoding());	

			
	//仮登録データ承認時と、データ編集時、投入時で投入する値が違う
		if($datas['activation']){
			
			$keys=array_merge($id_array,$company_array,$registration_array,$geographic_array);
					
		}else{
	
			$keys=array_merge($id_array,$company_array,$geographic_array,$invices_array);
				
		}	
		
//$datas[データ名]でサニタイズされたデータが入っている
	
foreach($keys as $key){
	$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}


	//serviceがある場合
	
	if($_POST['service']){
		//serviceのみ配列
		foreach($_POST['service'] as $services){
		  $datas['services'][]=htmlentities($services,ENT_QUOTES, mb_internal_encoding());
		}
	}
	
	$ip=getenv("REMOTE_ADDR");
	
try{

	//再度データチェック
	$form_validate=new Validate();
	
	$errors1=$form_validate->validate_form_company($datas);
	$errors2=$form_validate->validate_form_geo($datas);
		
	if($datas['id']){
			
		$errors3=$form_validate->validate_form_fromWeb_exited($datas);
		$errors4=Array();
	
	}else{
			
		$errors3=$form_validate->validate_form_fromWeb($datas);
		$errors4=$form_validate->validate_form_mail($datas);
	}
	
	$errors=array_merge($errors1,$errors2,$errors3,$errors4);
			
	if($errors){

		$form_validate->show_form($errors);
		$form_validate->lasturl='index.php?action=putin';
		
		}else{
		
		$mobile=0;	
		
		//新規か、編集か仮データ承認か分岐
			
		if($datas['activation']){
				
			//仮データの承認
				$status='NEWDATA';
				$mobile=2;
				
			}elseif(!$datas['id']){
				
			//新規データ
				$status='NEWDATA';
				
			}elseif($datas['id']){
		
			//編集
				$status='EDIT';
			}
		
		//データベースへ投入
		Data::PutIn($datas,$status,$mobile);
		
		foreach($keys as $key){
			unset($_SESSION[$key]);
		}		
		
		//仮登録情報承認の場合は、承認されましたとメール
		if($mobile==2){
			mail_notify_activate($datas['email'],$datas['company_name']);
		}
		
		
		header("Location:index.php");
	
		}
	
	}catch(Exception $e){
	die($e->getMessage());
	
	}

?>