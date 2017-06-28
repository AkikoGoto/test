<?php
/*
 * 配送先情報入力　確認画面
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */


$id_array=array('id','company_id');
$category_array = array('category');
$keys=array();
$keys=array_merge($id_array, $destination_array, $category_array);


//$datas[データ名]でサニタイズされたデータが入っている

	foreach($keys as $key){
		
		if($key == 'category'){
			
			if($_POST['category'] != null){
				foreach($_POST['category'] as $key => $each_post_category){
					$datas['category'][$key] = htmlentities($each_post_category, ENT_QUOTES, mb_internal_encoding());
				}
				
				//カテゴリーリスト
				$categoryList = DestinationCategory::searchDestinationCategories($datas['company_id']);

				$posted_categories = is_in_categoryList($datas, $categoryList);

				$smarty->assign("posted_categories",$posted_categories);
				
			}
			
		}else{
			$datas[$key] = htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());			
		}
		
		$_SESSION['destination_data'][$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
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
			
			$errors = $form_validate->validate_destination($datas);
		
			if($errors){
		
				$form_validate->show_form($errors);
				
			}else{

			
								
				
				foreach($datas as $key => $value){					
					$smarty->assign("$key",$value);
				}
				
				
				//住所のジオコーディング 編集と新規作成時でjsを切り替える 
				if((!empty($datas['address']))&& ($status !="EDIT")){
					$smarty->assign("js",'<script src="'.GOOGLE_MAP.' "type="text/javascript"></script>
									<script type="text/javascript" src="'.GEOCODING_JS.'"></script>');
					

				}else{

					$smarty->assign("js",'<script src="'.GOOGLE_MAP.' "type="text/javascript"></script>
									<script type="text/javascript" src="'.DESTINATION_EDIT_GEOCODING_JS.'"></script>');
										
				}

				$geocode_address = $datas['address'];
				$geocode_address ='<div id="geocode_address">'.$geocode_address.'</div>';
				
					
				$smarty->assign("onload_js","onload=\"doGeocode()\"");
				$smarty->assign("geocode_address",$geocode_address);				
				
				$smarty->assign("id",$datas['id']);		
				$smarty->assign("company_id",$datas['company_id']);				
				$smarty->assign("drivers",$drivers);
				$smarty->assign("filename","destination/tryDestination.html");
				$smarty->display("template.html");
			
			}
		
		}catch(Exception $e){
			
		die($e->getMessage());
		
		}
	}

	/**
	 * POSTされたカテゴリーのIDと名前の配列を作る
	 * @param $datas, $categoryList
	 * @return $posted_category_array
	 */
	
	function is_in_categoryList($datas, $categoryList){
		
		$posted_category_array = Array();
		
		foreach($categoryList as $each_category){
	
			foreach($datas['category'] as $each_posted_category){
				
				if($each_category['id'] == $each_posted_category){
					$posted_category_array[] = array('id' => $each_category['id'],
														'name' => $each_category['name']);
					break;								
					
				}
				
			}
		}
		
		return $posted_category_array;
	}
	

?>