<?php
/*
 * 配送先情報をJSONで出力する
 * @author Akiko Goto
 * @since 2014/9/11
 * @version 4.4
 */

/*
$_POST['login_id'] = "hanako";
$_POST['passwd']="hanako";
$_POST['latitude'] = "35.681382";
$_POST['longitude']="139.766084";
$_POST['category']="";
$_POST['name']="";
*/

$id_array=array('login_id', 'passwd');
$search_array=array('category','latitude','longitude','name');
$keys=array();
$keys=array_merge($id_array, $search_array);
$err = null;

foreach($keys as $key){

	if ( !($key == 'category' || $key == 'name') && empty($_POST[$key])) {
		$err = "NOT_POST_ID"." ".$key;
	}
	
	$datas[$key] = htmlentities( $_POST[$key], ENT_QUOTES, mb_internal_encoding());

}


// NOT_POST_IDではなかったら処理を進める
if (empty($err)) {
	
	
	// 存在すれば、ログイン成功
	$idArray = Driver::login( $datas['login_id'], $datas['passwd']);

	if(!isSet($idArray[0]['last_name'])) {
		$err = "LOGIN_FAILED";
	}
	
	// エラーLOGIN_FAILEDではなかったら処理を進める
	if (empty($err)) {
		try{
		

			$company_id = $idArray[0]['company_id'];
			
			
			$destinations = Destination::getNearDestinationsForSp($datas['latitude'], 
																$datas['longitude'], 
																$company_id,
																$datas['category'],
																$datas['name']);
			$categories = DestinationCategory::searchDestinationCategories($company_id);
														
																
			if(empty($destinations)){
				$status = "NO_RESULT";
			}else{
				$status = "RESULT_EXIST";
			}
		
		}catch(Exception $e){
			$err = "DB_ERROR";
		}
	}
}

if (!empty($err)) {
	$status = $err;
}

$response = array("status" => $status,
					"destinations" => $destinations,
					"categories" => $categories);
 
echo json_encode( $response );

?>