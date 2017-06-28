<?php
/*
 * 配送先情報をDBに格納する
 * @author Ichinose
 * @since 2012/12/21
 * @version 2.6
 */
//$id_array=array('company_id', 'login_id', 'passwd');
/*
$_POST['login_id']="ichiro";
$_POST['passwd']="ichiro";
$_POST['destination_name']="うげっ";
$_POST['address']="横浜";
$_POST['latitude']="35";
$_POST['longitude']="135";
*/

$id_array=array('login_id', 'passwd', 'category_names');
$keys=array();
$keys=array_merge($id_array, $destination_array);
$status = "REGISTER_FAILED";
$err = null;

foreach($keys as $key){
	
	if ( !in_array( $key, $keys ) && empty($_POST[$key])) {
		$err = "NO_POST_IDS";
		$not_post_error[] = $key;
	}
	
	if ( $key == "category_names" && !empty($_POST["category_names"]) )
	{
		 foreach ( $_POST["category_names"] as $value) {
		 	$datas['category_names'][] = $value;
		 }
	} else {
		$datas[$key] = htmlentities( $_POST[$key], ENT_QUOTES, mb_internal_encoding());
	}
}

// NOT_POST_IDではなかったら処理を進める
if (empty($err)) {
	
	$ip=getenv("REMOTE_ADDR");
	
	// 存在すれば、ログイン成功
	$idArray = Driver::login( $datas['login_id'], $datas['passwd']);
	
	if(isSet($idArray[0]['last_name'])) {
		
		// エラーLOGIN_FAILEDではなかったら処理を進める
		try{

			$datas['company_id'] = $idArray[0]['company_id'];
			$status='NEWDATA';
			//再度データチェック
			$form_validate=new Validate();
	
			$errors1=$form_validate->validate_destination($datas);
		
			$errors=array_merge($errors1);
		
			if($errors){
				$err = "NO_ENOUGH_DATA";
				$not_post_error[] = $errors1;
			}else{
				//データベースへ投入
			
				$datas['id'] = Destination::putInDestination($datas, $status);
				//カテゴリーがあれば、カテゴリーとのリレーションテーブルを更新
				if( $datas['category_names'] != null ){
					foreach ( $datas['category_names'] as $category_name ) {
						if ( $category_name != null ) {
							$datas['category'][] = DestinationCategory::putInAndReturnIdDestinationCategory( $datas['company_id'], $category_name, null );
						}
					}
					DestinationCategory::putInDestinationCategoryDestination( $datas , $status );
				}
				$status = 'SUCCESS';
			}
		
		}catch(Exception $e){
			$err = "DB_ERROR";
		}
	}
}

if (!empty($err)) {
	$status = $err;
}

$response = array("status" => $status, "errors" => $not_post_error);

echo json_encode( $response );

?>