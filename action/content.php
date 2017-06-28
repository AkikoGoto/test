<?php

/**
 *会社情報詳細画面 
 */

try{
	
	if($_GET['id']){	
		
		$id=sanitizeGet($_GET['id']);
		
		}else{
			//不正なアクセスです、というメッセージ					
			header("Location:index.php?action=message_mobile&situation=wrong_access");	
			
		}
		
		//仮登録情報かどうかのフラグ
		$from_web=0;
	
		//会社情報の取得
		$dataList=Data::getById($id,$from_web);
	
		//サービス名の取得
	
		$service_ids=Data::getServiceForCompany($id);
	
		//サービスがあれば、次の処理
		if($service_ids){
			
			for($i=0, $num_service_ids=count($service_ids);$i<$num_service_ids;$i++){ 
				
				$service_names[]=Data::getServiceNameEach($service_ids[$i]['service_id']);
			
			}
			
			foreach($service_names as $service_name){
	
				$service_names_each[]=$service_name[0]['service_name'];			
			
			}
		}

		//登録ドライバー数をカウント
		$driverNumber_data=Driver::countDrivers($id);
		$driverNumberActual=$driverNumber_data['COUNT(*)'];
		
		//口コミの平均をそれぞれの項目で計算
		$mannerAvg=Word::totalPoint($id, 'manner');
		$serviceAvg=Word::totalPoint($id, 'service');
		$driveAvg=Word::totalPoint($id, 'drive_technique');
		$totalAvg=Word::totalPoint($id, 'total');
	
		$allAvg = ($mannerAvg->avg+$serviceAvg->avg+$driveAvg->avg+$totalAvg->avg)/4;
	
		//星表示のために、整数部分と小数点部分を分ける
		$allAvgInt = floor($allAvg);
		$allDecimal = $allAvg - $allAvgInt;
		

	}catch(Exception $e){
	
		$message=$e->getMessage();
		
	}


list($data, $links)=make_page_link($dataList);

//descriptionメタタグ用
$meta_description=$dataList[0]->company_name.OF_INFO;
$meta_description.=COMMON_HQ_JYUSYO.':';
$meta_description.=$data[0]->prefecture_name.$data[0]->city.$data[0]->ward.$data[0]->town.$data[0]->address;
$meta_description.=TEXT_END;

$smarty->assign('allAvg',$allAvg);
$smarty->assign('allAvgInt',$allAvgInt);
$smarty->assign('allDecimal',$allDecimal);

$smarty->assign('dataList',$dataList);

//営業所など、最初の1件のみ表示
$smarty->assign("data",$data[0]);

$smarty->assign("service_names_each",$service_names_each);

//実際のドライバー登録数
$smarty->assign("driver_number_actual",$driverNumberActual);

//ページのタイトルに、スレッドのタイトルをアサイン
$smarty->assign('page_title',$dataList[0]->company_name);
$smarty->assign("meta_description",$meta_description);	

//インクルードファイル
$smarty->assign("include_html",'contents_list.html');

$smarty->assign("filename","content.html");
$smarty->display("template.html");

?>