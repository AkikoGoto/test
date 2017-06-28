<?php
/** ドライバーがコンテナを引き取るアクション
 * ver2.5から追加
 * 画面なし
 */

//ドライバーのログインチェック
//ログインIDとパスワードでチェック

//テストデータ
/*$_POST['login_id']='ichiro';
$_POST['passwd']='ichiro';

$_POST['id']="1";
$_POST['target_id']="12345";
*/
if($_POST['login_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	
	$idArray=Driver::login($login_id,$passwd);
	
	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
			
		$driver_id = $idArray[0]['id'];
		
		$id_array=array('id');
		$keys=array();
		$keys = array_merge($id_array, $target_array);
		
	
		foreach($keys as $key){
			$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		}
		
		
		try{
			
			if(!$datas["id"]){
				print "NO_ID";
			}elseif(!$datas["target_id"]){
				print "NO_TARGET_ID";
			}else{

				$datas = Target::PickTarget($datas);
				
				if($datas){

					if($datas == 1){
						print "SUCCESS";
					}elseif($datas == 2){
						print "NO_DATA";
					}elseif($datas == 3){
						print "DB_ERROR";
					}
				}
			}
			

		}catch(Exception $e){

			$message=$e->getMessage();
			//DBエラーの場合の画面出力
			print "DB_ERROR";
		}


	}else{

		//ログインに失敗した場合の画面出力
		print "LOGIN_FAILED";
	}
}else{

	//IDがPOSTされていない場合
	print "INVALID_ACCESS";
}

?>