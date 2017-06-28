<?php
/*
 * 配送先カテゴリー情報をDBに格納する
 * @author Akiko Goto
 * @since 2014/09/12
 * @version 4.4
 */


$id_array=array('id', 'company_id');
$category_array = array('category');
$keys=array();
$keys=array_merge($id_array, $destination_array, $category_array);

//$datas[データ名]にサニタイズされたデータを入れる
//
foreach($keys as $key){

		if($key == 'category'){
			
			if($_POST['category'] != null){
				foreach($_POST['category'] as $key => $each_post_category){
					$datas['category'][$key] = htmlentities($each_post_category, ENT_QUOTES, mb_internal_encoding());
				}				
			}
			
		}else{
			$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		}

}
	
if($datas['id']){

	$status='EDIT';
		
}else{

	$status='NEWDATA';

}

$ip=getenv("REMOTE_ADDR");
	
	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $datas['company_id'], $branch_manager_id);

try{

	//再度データチェック
	$form_validate=new Validate();

	$errors1=$form_validate->validate_destination($datas);

	$errors=array_merge($errors1);

	if($errors){

		$form_validate->show_form($errors);
		$form_validate->lasturl='index.php?action=/destination/putDestinationDb';

	}else{
		//データベースへ投入
		$datas['id'] = Destination::putInDestination($datas, $status);

		//カテゴリーがあれば、カテゴリーとのリレーションテーブルを更新
		if($datas['category'] != null){
			DestinationCategory::putInDestinationCategoryDestination($datas, $status);
		}
		
		unset($_SESSION['destination_data']);

		//戻るリンクのために、会社IDをGETで送信
		$company_id = $datas['company_id'];
		header("Location:index.php?action=message_mobile&situation=after_edit_destination&company_id=$company_id");

		exit(0);
	}

}catch(Exception $e){
	die($e->getMessage());

}

?>