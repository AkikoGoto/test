<?php
/*
 * 配送先情報をDBに格納する
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */
$id_array=array('company_id', 'login_id', 'passwd');
$keys=array();
$keys=array_merge($id_array,$destination_array);
$status = "REGISTER_FAILED";
$err = null;

foreach($keys as $key){

	if ($key != 'information' && empty($_POST[$key])) {
		$err = "NOT_POST_ID"." ".$key;
	}
	
	$datas[$key] = htmlentities( $_POST[$key], ENT_QUOTES, mb_internal_encoding());

}

// NOT_POST_IDではなかったら処理を進める
if (empty($err)) {
	
	$ip=getenv("REMOTE_ADDR");
	
	// 存在すれば、ログイン成功
	$idArray = Driver::login( $datas['login_id'], $datas['passwd']);
	if(!isSet($idArray[0]['last_name'])) {
		$err = "LOGIN_FAILED";
	}
	
	// エラーLOGIN_FAILEDではなかったら処理を進める
	if (empty($err)) {
		try{
		
			$status='NEWDATA';
			//再度データチェック
			$form_validate=new Validate();
		
			$errors1=$form_validate->validate_destination($datas);
		
			$errors=array_merge($errors1);
		
			if($errors){
				$err = "DB_ERROR";
			}else{
				//データベースへ投入
				Destination::putInDestination($datas, $status);
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

$response = array("status" => $status);

echo json_encode( $response );

?>