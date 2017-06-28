<?php
/**
 * 配送先カテゴリー情報をDBに格納する
 * @author Akiko Goto
 * @since 2014/9/18
 * @version 4.3
 */


$id_array=array('id','company_id');
$keys=array();
$keys=array_merge($id_array,$destination_category_array);

//$datas[データ名]にサニタイズされたデータを入れる
//
foreach($keys as $key){

	$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());

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

	$errors1=$form_validate->validate_destination_category($datas);

	$errors=array_merge($errors1);

	if($errors){

		$form_validate->show_form($errors);
		$form_validate->lasturl='index.php?action=/destination_category/putDestinationCategory';

	}else{

		//データベースへ投入
		DestinationCategory::putInDestinationCategory($datas, $status);
		unset($_SESSION['destination_category_data']);

		//戻るリンクのために、会社IDをGETで送信
		$company_id = $datas['company_id'];
		header("Location:index.php?action=message_mobile&situation=after_edit_destination_category&company_id=$company_id");

		exit(0);
	}

}catch(Exception $e){
	die($e->getMessage());

}

?>