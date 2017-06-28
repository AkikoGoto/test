<?php
/*
 * ルート詳細情報入力　確認画面
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */
$root_making_way = htmlentities( $_POST['root_making_way'], ENT_QUOTES, mb_internal_encoding());
$is_shortest_root = false;
if ( $root_making_way == '_2_' )
	$is_shortest_root = true;

//会社ID 配列ではないので、別の処理
$company_id = htmlentities($_POST['company_id'], ENT_QUOTES, mb_internal_encoding());

//ルートID 配列ではないので、別の処理
$root_id = htmlentities($_POST['root_id'], ENT_QUOTES, mb_internal_encoding());

//日付
$date = htmlentities($_POST['date'], ENT_QUOTES, mb_internal_encoding());

	//それぞれの配送先ごとの配列に組み直す
	foreach($_POST as $key => $value){

		if(!empty($value) &&
			($key != "root_making_way") && 
			($key != "company_id") && 
			($key != "submit") && 
			($key != "root_id") && 
			($key != "date")){
			$key_array = explode('-', $key);
			$datas[$key_array[0]][$key_array[1]] = htmlentities($value, ENT_QUOTES, mb_internal_encoding());
			
			//エラーで戻った場合のためにセッションにデータを格納
			$_SESSION['root_detail'][$key_array[0]][$key_array[1]] = htmlentities($value, ENT_QUOTES, mb_internal_encoding());
		}
		
	}

	//会社のユーザーIDと、編集するIDがあっているか確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
				
	$ip=getenv("REMOTE_ADDR");

	$ban_ip=Data::banip();
	
	//編集か新規か
	if($datas[0]['id']){
		$is_edit = true;
	}else{
		$is_edit = false;		
	}

	
	//禁止リストにあるIPとの照合
	if($ip==$ban_ip){

	//メッセージ画面を表示する
      header('Location:index.php?action=message&situation=ban_ip');

	}else{

		try{
		
			//入力データ検証
			$form_validate = new Validate();
			
			if ( !$is_shortest_root )
				//入力順
				$errors = $form_validate->validate_root_details($datas, $is_edit);
			else
				//最短経路検索
				$errors = $form_validate->validate_shortest_root_details($datas, $is_edit);
		
			if($errors){
		
				$form_validate->show_form($errors);
				
			}else{
					
				
				$smarty->assign("datas", $datas);
				
				//入力順
				if (!$is_shortest_root ) {
					
					$smarty->assign("onload_js","onload=\"doGeocode()\"");
					//編集とそうでない場合で、Javascriptを分ける
					if($is_edit){
						$smarty->assign("js",'<script src="'.GOOGLE_MAP.' "type="text/javascript"></script>
											<script type="text/javascript" src="'.ROOT_EDIT_GEOCODING_JS.'"></script>
											');
					}else{
						$smarty->assign("js",'<script src="'.GOOGLE_MAP.' "type="text/javascript"></script>
											<script type="text/javascript" src="'.MULTI_GEOCODING_JS.'"></script>
											');
					}
					
				} else {
					//最短経路検索
					$destination_count = count( $datas );
					$smarty->assign("onload_js","onload=\"shortestRootMapGeocode( $destination_count )\"");
					$smarty->assign("js",'<script src="'.GOOGLE_MAP.' "type="text/javascript"></script>
										<script type="text/javascript" src="'.SHORTEST_ROOT_JS.'"></script>
										<script type="text/javascript" src="'.ROOT_ANIMATION_JS.'"></script>
										');
					$smarty->assign("is_shortest", true);
				}
				
				
				
				$smarty->assign("id",$datas['id']);
				$smarty->assign("company_id",$company_id);
				$smarty->assign("root_id",$root_id);
				$smarty->assign("drivers",$drivers);
				$smarty->assign("date",$date);
				
				if($is_edit){
					$smarty->assign("filename","root_detail/tryEditRootDetail.html");
				}else{
					$smarty->assign("filename","root_detail/tryRootDetail.html");
				}
				$smarty->display("template.html");
			
			}
		
		}catch(Exception $e){
			
		die($e->getMessage());
		
		}
	}

?>