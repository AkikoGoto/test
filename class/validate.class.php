<?php

//データ検証

class Validate{

	//ルート詳細の検証

	public function validate_root_details($datas, $is_edit = null){

		$errors=array();

		if(empty($datas)){

			$errors[] = DATE_VERI_NOCNTRBT;

		}else{

			foreach($datas as $data){

				if(! strlen(trim($data['destination_name']))){
					$errors[] = DESTINATION.DATE_VERI_NO;
				}

				if(!$is_edit){
					if(empty($data['address'])){
						$errors[] = DESTINATION.COMMON_JYUSYO.DATE_VERI_NO;
					}
				}

				if( mb_strlen($data['information']) > INFORMATION_MAX){
					$errors[]=DATE_VERI_MESLO. INFORMATION_MAX. DATE_VERI_CHAORLE;
				}

			}

		}

	 	return $errors;

	}

	//最短ルート詳細の検証
	public function validate_shortest_root_details( $datas ) {

		$errors = Validate::validate_root_details($datas, null);
		$has_same_destination = false;
		$tmp = array();
		foreach( $datas as $key => $value ){
			if( !in_array( $value['address'], $tmp ) ) {
				$tmp[] = $value['address'];
			} else {
				$errors[] = VERI_SAME_ADDRESS;
			}
		}

		return $errors;

	}


	//ルートの検証

	public function validate_root($datas){

		$errors=array();

		if(! strlen(trim($datas['date']))){
			$errors[] = DELIVER_DATE.DATE_VERI_NO;
		}else{
			//おかしな日付が入っていないかチェック　2月30日、など

			if(!empty($datas['year'])){
				if(! checkdate($datas['month'], $datas['day'], $datas['year'])){
					$errors[] = DELIVER_DATE.COMMON_INCORRECT_VALUE;
				}
			}
		}


		if(empty($datas['driver_id'])){
			$errors[] = COMMON_DRIVER.COMMON_JYUSYO.DATE_VERI_NO;
		}

		//新規データ登録の時は同じ日付、同じドライバーのデータがないか確認
		$dbh=SingletonPDO::connect();
		$date = $datas['date'];
		$driver_id = $datas['driver_id'];
		$sql = "SELECT
					*
				FROM
					roots
				WHERE
					driver_id = $driver_id
				AND
					date = CAST(\"$date\" as DATE)";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$data=$stmt->fetchAll();
		if($data){
			$errors[]=DUPLICATE_ROOT;
		}

	 	return $errors;

	}

	//配送先の検証

	public function validate_destination($datas){

		$errors=array();

		if(! strlen(trim($datas['destination_name']))){
			$errors[] = DESTINATION.DATE_VERI_NO;
		}

		//編集の時は住所がなくてもOK
		if(empty($datas['id'])){
			if(empty($datas['address'])){
				$errors[] = DESTINATION.COMMON_JYUSYO.DATE_VERI_NO;
			}
		}

	 	return $errors;

	}

	//配送先カテゴリの検証
	public function validate_destination_category($datas){

		$errors=array();

		if(! strlen(trim($datas['name']))){
			$errors[] = CATRGORY_NAME.DATE_VERI_NO;
		}

		if(! strlen(trim($datas['color']))){
			$errors[] = COLOR.DATE_VERI_NO;
		}

	 	return $errors;

	}

	//メッセージの検証

	public function validate_message($datas){

		$errors=array();

		if(! strlen(trim($datas['gcm_message']))){
			$errors[] = MESSAGE.DATE_VERI_NO;
		}


		 if( mb_strlen($datas['gcm_message']) > MESSAGE_MAX){
			$errors[]=DATE_VERI_MESLO. MESSAGE_MAX. DATE_VERI_CHAORLE;
		}


		if(empty($datas['driver_id'])){
			$errors[] = MESSAGE_TO.DATE_VERI_NO;
		}

	 	return $errors;

	}


	//日報用のデータ　検証
	public function validate_day_report_data($datas){

		$errors=array();

		if($datas['start_meter']){
			if(!preg_match("/^[0-9]+$/",$datas['start_meter'])){
				$errors[] = START_METER .ERROR_INT;
			}
		}

		if($datas['arrival_meter']){
			if(!preg_match("/^[0-9]+$/",$datas['arrival_meter'])){
				$errors[] = ARRIVAL_METER .ERROR_INT;
			}
		}

		//運行日が既存のデータにかぶっていないか検証
		$dbh=SingletonPDO::connect();
		$driver_id = $datas['driver_id'];
		$data_id = $datas['id'];
		$drive_date = $datas['drive_date'];


		if($data_id){

			//編集の場合
			$sql = "SELECT
						drive_date
					FROM
						day_report
					WHERE
						id <> $data_id
					AND
						drive_date = \"$drive_date\"";

			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();

			if($data){
				$errors[]=DUPLICATED_DAY_REPORT;
			}

		}else{

			//新規の場合
			$sql = "SELECT
						drive_date
					FROM
						day_report
					WHERE
						driver_id = $driver_id
					AND
						drive_date = \"$drive_date\"";

			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();


			if($data){
				$errors[]=DUPLICATED_DAY_REPORT;
			}

		}

		return $errors;

	}

	//ターゲットのデータ　検証
	public function validate_target($datas){

		$errors=array();

		if(! strlen(trim($datas['target_id']))){
			$errors[] = TARGET_ID.DATE_VERI_NO;
		}


		//設置日、引き取り日、は整数
		$int_array = array('target_set_date_year','target_set_date_month','target_set_date_day',
							'picked_date_year','picked_date_month','picked_date_day');

		foreach($int_array as $each_int){

			if(!empty($datas["$each_int"])){
				if(!preg_match("/^[0-9]+$/",$datas["$each_int"])){
					$errors[] = COMMON_DATETIME.ERROR_INT;
					}
			}
		}
		return $errors;

	}


	//運行履歴の編集時、開始時間と終了時間が既存の作業時間とかぶっていないか検証
	public function validate_worktime($start_worktime, $end_worktime, $datas){

		//通行料金は整数
		if($datas['toll_fee']){
		if(!preg_match("/^[0-9]+$/",$datas['toll_fee'])){
			$errors[] = TOLL_FEE.ERROR_INT;
			}
		}

		//距離は小数
		if($datas['distance']){
		if(! preg_match('/^[0-9]+(\.[0-9]*)?$/', $datas['distance'])){
			$errors[]= DISTANCE.ERROR_FLOAT;
			}
		}

		//積載量は小数
		if($datas['amount']){
		if(! preg_match('/^[0-9]+(\.[0-9]*)?$/', $datas['amount'])){
			$errors[]= AMOUNT.ERROR_FLOAT;
			}
		}

		$dbh=SingletonPDO::connect();
		$driver_id = $datas['driver_id'];
		$data_id = $datas['id'];

		if($start_worktime && $end_worktime){

			//作業テーブルに時間が重複している記録がないか調べる
			//そのデータ自身の編集の場合は含まない

			//データが新規の追加の場合と、編集の場合でSQLを分ける
			if($data_id){
				$sql = "SELECT
							*
						FROM
							work
						WHERE
						(
							(start
								BETWEEN
								CAST('$start_worktime' AS DATETIME)
								AND
								CAST('$end_worktime' AS DATETIME)
							)
						OR
							(end
						BETWEEN
							CAST('$start_worktime' AS DATETIME)
						AND
							CAST('$end_worktime' AS DATETIME)
							)
						)
						AND
							 driver_id = $driver_id
						AND
							id <> $data_id
						";
			}else{
				$sql = "SELECT
							*
						FROM
							work
						WHERE
						(
							(start
						BETWEEN
							CAST('$start_worktime' AS DATETIME)
						AND
							CAST('$end_worktime' AS DATETIME)
							)
						OR
							(end
						BETWEEN
							CAST('$start_worktime' AS DATETIME)
						AND
							CAST('$end_worktime' AS DATETIME)
							)
						)
						AND
							 driver_id = $driver_id

						";

			}

		}

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$data=$stmt->fetchAll();

		if($data){
			$errors[]=DUPLICATED_WORK_TIME;

		}
		return $errors;
	}

//アプリステータスの必須項目
public function validate_form_app_status($datas){

	$errors=array();


	if(! strlen(trim($datas['action_1']))){
		$errors[] = '作業ステータス1がありません';
	}


	if(! strlen(trim($datas['action_2']))){
		$errors[] = '作業ステータス2がありません';
	}

	if(! strlen(trim($datas['action_3']))){
		$errors[] = '作業ステータス3がありません';
	}

	if(! strlen(trim($datas['action_4']))){
		$errors[] = '作業ステータス4がありません';
	}

	$distance = trim($datas['distance']);
	if(! strlen($distance)){
		$errors[] = '移動距離がありません';
	} else if($distance == 0 or !preg_match('/^\d+$/', $distance)){
		$errors[] = '移動距離は1以上の整数を入力してください';
	}

	$time = trim($datas['time']);
	if(! strlen($time)){
		$errors[] = '移動時間がありません';
	} else if($time == 0 or !preg_match('/^\d+$/', $time)){
		$errors[] = '移動時間は1以上の整数を入力してください';
	}

	return $errors;
}

//アプリステータスの必須項目
public function validate_form_app_setting_status($datas){

	$errors=array();

	if(! strlen(trim($datas['track_always']))){
		$errors[] = '「常駐して動作させる」に値が入っておりません';
	}

	$accuracy = trim($datas['accuracy']);
	if(! strlen($accuracy)){
		$errors[] = '許容する誤差の範囲がありません';
	} else if($accuracy == 0 or !preg_match('/^\d+$/', $accuracy)){
		$errors[] = '誤差の範囲は1以上の整数を入力してください';
 	} else if( 500 > $accuracy || 3000 < $accuracy ){
 		$errors[] = '誤差の範囲は500mから3000mで入力してください';
 	}

	return $errors;
}


//会社名入力の際の必須項目
public function validate_form_company($datas){

	$errors=array();

	if(6>strlen(trim($datas['company_group_id']))){
	$errors[]=COMMON_GROUP_ID.DATE_VERI_WA.'6'.DATE_VERI_CHAORMO;;
	}

	if(! preg_match('/^[_-a-zA-Z0-9]+$/i', $datas['company_group_id'])){
		$errors[]= COMMON_GROUP_ID.DATE_VERI_WA.DATE_VERI_EISUU.DATE_VERI_AND.DATE_VERI_LINE_SYMBOL.DATE_VERI_PIIB;
	}

	if(! strlen(trim($datas['company_name']))){
	$errors[]=COMMON_COMPANY.DATE_VERI_NO;
	}

	//会社名はリンクになるので、URL属性への変数出力のセキュリティ対策
	if( preg_match('/[\x00-\x19\r\n]/',$datas['company_name'])){
	$errors[]=DATE_VERI_PROHITI.DATE_VERI_SIGNCNBU;
	}

	$message_max=MESSAGE_MAX;

	 if( mb_strlen($datas['info']) > MESSAGE_MAX){
	$errors[]=DATE_VERI_MESLO. $message_max. DATE_VERI_CHAORLE;
	}

	 if( mb_strlen($datas['fare']) > MESSAGE_MAX){
	$errors[]=DATE_VERI_MESLO. $message_max. DATE_VERI_CHAORLE;
	}
	return $errors;

}

//会社名入力の際の必須項目
public function validate_form_driverCheck($datas){

	$errors=array();

	if(! strlen(trim($datas['driver_id']))){
		$errors[]="ドライバーが選択されていません";
	}

	if(! strlen(trim($datas['address']))){
		$errors[]="住所がありません";
	}

	if(! strlen(trim($datas['daily'])) && ! strlen(trim($datas['weekly'])) && ! strlen(trim($datas['date']))){
		$errors[]="指定された日にちがありません";
	}

	if(! strlen(trim($datas['alert_when_not_there'])) && ! strlen(trim($datas['alert_when_there']))){
		$errors[]="指定した時間の判断項目がないため、メールを送るかどうか判断できません。";
	}

	return $errors;

}

public function delete_hyphen_tel_number($tel) {
	ereg("^[0-9,(,),ー,-]+$",$tel);
	return $tel;
}

public function validate_tel_number( $tel ) {
	if(!ctype_lower($tel)){
		return $errors[]=COMMON_MOBILE_TEL.ERROR_INT;
	}

	$letter_count = mb_strlen($tel);
	if ($letter_count != 11) {
		return $errors[]=COMMON_MOBILE_TEL.DATE_VERI_WA.'11'.DATE_VERI_CHAORMO;
	}
}

//会社名の重複がないか調査　データ一括投入のとき利用　同じ会社名・同じ都道府県の会社があれば投入しない
public function validate_same_company($company_name,$prefecture){

	$errors=array();

	$dbh=SingletonPDO::connect();


	$sql="	SELECT
				company.*,
				geographic.id as geographic_id,
				geographic.*
			FROM company
			JOIN geographic
			ON company.id=geographic.company_id
			WHERE
				company.company_name='".
					$company_name."'
				AND
					$prefecture=geographic.prefecture

			";

	$stmt=$dbh->prepare($sql);
	$stmt->execute();

	$same_names=$stmt->fetchAll();

	if($same_names){

		$errors[]=DATE_ERROR_SAME_COMPANY;
		$errors[]=$same_names[0]['company_name'];
		return $errors;
		exit;
	}

	return $errors;
}


//メールアドレスの重複がないか調査
public function validate_form_mail($datas){

	$errors=array();

	$dbh=SingletonPDO::connect();

	$sql="SELECT id,email FROM company";

	$stmt=$dbh->prepare($sql);
	$stmt->execute();

	$emails=$stmt->fetchAll(PDO::FETCH_ASSOC);

	if($datas['email']){

		foreach($emails as $email){

			if($email['email']==$datas['email']){

				if($email['id']!==$datas['id']){

					$errors[]=DATE_ERROR_BEENUSED;
					return $errors;
					exit;

				}

			}
		}
	}
		//データのEmailがない場合は、空の値を返す　他でバリデートしているため
		return $errors;

}

//メールアドレスの重複がないか調査 ユーザー登録用
public function validate_form_mail_user($datas){

	$errors=array();

	$dbh=SingletonPDO::connect();

	$sql="SELECT id,u_email FROM user";

	$stmt=$dbh->prepare($sql);
	$stmt->execute();

	$emails=$stmt->fetchAll(PDO::FETCH_ASSOC);

	if($datas['u_email']){

		foreach($emails as $email){

			if($email['u_email']==$datas['u_email']){

				if($email['id']!==$datas['id']){

					$errors[]=DATE_ERROR_BEENUSED;
					return $errors;
					exit;

				}

			}
		}
	}
		//データのEmailがない場合は、空の値を返す　他でバリデートしているため
		return $errors;

}




//営業所および会社名入力の際の必須項目

public function validate_form_geo($datas){

	$errors=array();


	if(! strlen(trim($datas['postal']))){
	$errors[]=COMMON_POSTAL.DATE_VERI_NO;
	}

	if(! strlen(trim($datas['prefecture']))){
	$errors[]=COMMON_PREFECTURE.DATE_VERI_NO;
	}


	if(! strlen(trim($datas['town']))){
	$errors[]=COMMON_TOWN.DATE_VERI_NO;
	}

	if(! strlen(trim($datas['tel']))){
	$errors[]=COMMON_TEL.DATE_VERI_NO;
	}

 	return $errors;

}

//ドライバーデータ入力の際の必須項目

public function validate_form_driver($datas,$status){

	$errors=array();


	if(! strlen(trim($datas['last_name']))){
	$errors[]=COMMON_LAST_NAME.DATE_VERI_NO;
	}

	if(! strlen(trim($datas['first_name']))){
	$errors[]=COMMON_FIRST_NAME.DATE_VERI_NO;
	}


	//新規データの時のみ、パスワードを入力させる
	if($status=='NEWDATA'){

		if(! strlen(trim($datas['passwd']))){
		$errors[]=COMMON_PWD.DATE_VERI_NO;
		}

		if(6>strlen(trim($datas['passwd']))){
		$errors[]=COMMON_PWD.DATE_VERI_WA.'6'.DATE_VERI_CHAORMO;
		}

		if(! preg_match('/^[a-zA-Z0-9]+$/i', $datas['passwd'])){
		$errors[]= COMMON_PWD.DATE_VERI_WA.DATE_VERI_EISUU.DATE_VERI_PIIB;

		}
	}

	if(6>strlen(trim($datas['login_id']))){
	$errors[]=DRIVER_ACCOUNT.DATE_VERI_WA.'6'.DATE_VERI_CHAORMO;
	}


	//ドライバーアカウントの重複がないか調査
	if($status=='NEWDATA'){
		if($datas['login_id']){

			$login_id=$datas['login_id'];
			$dbh=SingletonPDO::connect();
			$sql="SELECT
					*
				  FROM
				  	drivers
				  WHERE
				  	login_id=\"$login_id\"
				  	";

			$stmt=$dbh->prepare($sql);
			$stmt->execute();

			$login_ids=$stmt->fetchAll(PDO::FETCH_ASSOC);

			if($login_ids){

				$errors[]=DATA_VERI_ACCOUNT;

			}
		}
	}

	if(! preg_match('/^[-a-zA-Z0-9]+$/i', $datas['login_id'])){
		$errors[]= DRIVER_ACCOUNT.DATE_VERI_WA.DATE_VERI_EISUU.DATE_VERI_PIIB;
	}



	if($datas['mobile_email']){
		if(! preg_match('/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i', $datas['mobile_email'])){
			$errors[]= DATE_VERI_EMAILIL;
		}
	}

 	return $errors;

}

//閲覧ユーザーデータの入力必須項目
public function validate_form_viewer ( $datas, $status ) {

	$errors=array();

	//名前のチェック
	if( ! strlen( trim( $datas['last_name'] ) ) &&
		! strlen( trim( $datas['first_name'] ) ) ) {
		$errors[] = VIEWER_NAME.DATE_VERI_NO;
	}

	//ログインIdのチェック
	if( 6 > strlen(trim($datas['login_id']))){
		$errors[] = DRIVER_ACCOUNT.DATE_VERI_WA.'6'.DATE_VERI_CHAORMO;
	} else if(! preg_match('/^[-a-zA-Z0-9]+$/i', $datas['login_id'])){
		$errors[]= DRIVER_ACCOUNT.DATE_VERI_WA.DATE_VERI_EISUU.DATE_VERI_PIIB;
	}

	//新規データの時のみ
	if( $status == 'NEWDATA' ) {
		//パスワードを入力させる
		if( ! strlen( trim( $datas['passwd'] ) ) )
			$errors[] = COMMON_PWD.DATE_VERI_NO;

		if( 6 > strlen( trim( $datas['passwd'] ) ) )
			$errors[] = COMMON_PWD.DATE_VERI_WA.'6'.DATE_VERI_CHAORMO;

		if( ! preg_match( '/^[a-zA-Z0-9]+$/i', $datas['passwd'] ) )
			$errors[] = COMMON_PWD.DATE_VERI_WA.DATE_VERI_EISUU.DATE_VERI_PIIB;

		//ドライバーアカウントの重複がないか調査
		if($datas['login_id']){

			$login_id=$datas['login_id'];
			$dbh=SingletonPDO::connect();
			$sql="SELECT
					id
				  FROM
				  	users
				  WHERE
				  	login_id=\"$login_id\"
				  	";

			$stmt=$dbh->prepare($sql);
			$stmt->execute();

			$login_ids=$stmt->fetchAll(PDO::FETCH_ASSOC);

			if($login_ids){

				$errors[]=DATA_VERI_ACCOUNT;

			}
		}
	}

 	return $errors;

}


//会社名入力の際の必須項目
public function validate_form_fromWeb($datas){

	$errors=array();

	if(! strlen(trim($datas['email']))){
	$errors[]=COMMON_EMAIL.DATE_VERI_NO;
	}

	if(! preg_match('/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i', $datas['email'])){
	$errors[]= DATE_VERI_EMAILIL;

	}

	if(! strlen(trim($datas['passwd']))){
	$errors[]=COMMON_PWD.DATE_VERI_NO;
	}

	return $errors;

}

//ドライバーステータスアップデート時の必須項目
public function validate_driver_status($datas){

	$errors=array();

	if($datas['sales']){
		if(!preg_match("/^[0-9]+$/",$datas['sales'])){
			$errors[]=DRIVER_SALES.ERROR_INT;
		}
	}
	$message_max=DRIVER_MEMO_MAX;

	if($datas['detail']){
		if( mb_strlen($datas['detail']) >$message_max ){
		$errors[]=DATE_VERI_MESLO. $message_max. DATE_VERI_CHAORLE;
		}
	}

	return $errors;

}

//会社名入力の際の必須項目 編集版
public function validate_form_fromWeb_exited($datas){

	$errors=array();

	if(! strlen(trim($datas['email']))){
	$errors[]=COMMON_EMAIL.DATE_VERI_NO;
	}

	return $errors;

}

//データ検証　ユーザーデータ入力時
public function validate_form_user($datas){

	$errors=array();
	if(! preg_match('/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i', $datas['u_email'])){
	$errors[]= DATE_VERI_EMAILIL;

	}

	 if(! strlen(trim($datas['nick_name']))){
		$errors[]=NICK_NAME.DATE_VERI_NO;
	}

	 if(! strlen(trim($datas['last_name']))){
		$errors[]=COMMON_LAST_NAME.DATE_VERI_NO;
	 }

	 if(! strlen(trim($datas['first_name']))){
		$errors[]=COMMON_FIRST_NAME.DATE_VERI_NO;
	 }

	 if(! strlen(trim($datas['u_pass']))){
	$errors[]=DATE_VERI_PLEASEPASS;
	}


 return $errors;
 }

public function validate_form_update_user($datas){

	$errors=array();
	if(! preg_match('/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i', $datas['u_email'])){
	$errors[]= DATE_VERI_EMAILIL;

	}

	 if(! strlen(trim($datas['nick_name']))){
		$errors[]=NICK_NAME.DATE_VERI_NO;
	}

	 if(! strlen(trim($datas['last_name']))){
		$errors[]=COMMON_LAST_NAME.DATE_VERI_NO;
	 }

	 if(! strlen(trim($datas['first_name']))){
		$errors[]=COMMON_FIRST_NAME.DATE_VERI_NO;
	 }

 return $errors;
 }

//データ検証　ユーザーデータアップデート時

public function u_update_validate_form($u_id,$u_name){

	$errors=array();

	if(! preg_match('/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i', $u_id)){
	$errors[]= 'Emailアドレスが不正な値です。';

	}

	 if(! strlen(trim($u_name))){
	$errors[]='名前がありません。';
	}

 return $errors;
 }

//データ検証　ユーザーのアドレス入力時　パスワード送信用

public function u_validate_address($u_id){

	$errors=array();

	if(! preg_match('/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i', $u_id)){
	$errors[]= DATE_VERI_EMAILIL;

	}

 return $errors;
 }

//データ検証　口コミ投稿時

public function validate_form_word($datas){

	$errors=array();

	 if(! strlen(trim($datas['company_id']))){
		$errors[]=COMMON_COMPANY.DATE_VERI_NO;
	}

	 if(! strlen(trim($datas['user_id']))){
		$errors[]=COMMON_USER.DATE_VERI_NO;
	 }

 	return $errors;
 }



//エラー表示
public function show_form($errors){
//$errors=array();
$smarty=new Smarty();

//config.phpで指定したものが入らなかったためディレクトリを指定

//$smarty->template_dir='./templates';
//$smarty->compile_dir='./templates_c';

$smarty->template_dir=TEMPLATE_DIR;
$smarty->compile_dir=TEMPLATE_COMPILE;


$smarty->assign("errors",$errors);

	//キャリア判別
	$carrier=get_carrier();

//携帯版の場合、Shift_jisフィルタをかぶせる
	$script_path=$_SERVER['SCRIPT_NAME'];

	if( $carrier=='docomo'||$carrier=='au'||$carrier=='softbank'){
		//モバイル版のUTF-8からShift_JIS変換
		$smarty->register_outputfilter("Encode_utf8tosjis");
	}


	//CSS割り当て
	if($carrier=='softbank'||$carrier=='au'){

		$smarty->assign("css",'<LINK rel="stylesheet" href="templates/css/mobile.css"
			type="text/css" media="handheld">');

	}elseif($carrier=='docomo'){

	}elseif($carrier==''){

		$smarty->assign("css",'<LINK rel="stylesheet" href="templates/css/pc.css"
			type="text/css" media="screen">');

	}

	$smarty->assign("carrier",$carrier);
	$smarty->assign("filename","error.html");
	$smarty->display("template.html");

	}




//確認画面表示
/*const NEWDATA=0; //新規データ
const EDIT=1; //編集画面
const NEWBRANCH=2; //営業所情報追加画面
*/
public function confirm_form($datas,$status,$prefecture_name,$service_names){


	}

public function confirm_form_user($datas,$status,$prefecture_name,$service_names){

	$smarty=new Smarty();


	//携帯版の場合、Shift_jisフィルタをかぶせる
	$smarty->register_outputfilter("Encode_utf8tosjis");

	//config.phpで指定したものが入らなかったためディレクトリを指定

	$smarty->template_dir=TEMPLATE_DIR;
	$smarty->compile_dir=TEMPLATE_COMPILE;

	foreach($datas as $key => $value){

		//携帯版のため、文字コードを変換
		$value=mb_convert_encoding($datas[$key], "UTF-8", "Shift-JIS");
		$smarty->assign("$key",$value);

	}


		switch($status){
		//新規データ登録
			case NEWDATA:
			$target="putindb";
			break;

		//データ編集
			case EDIT:
			$target="editdb";
			break;

		//営業所データ追加
			case NEWBRANCH:
			$target="putindbBranch";
			break;
		default:
			$target="putindb";
		}

	$smarty->assign("filename","confirm.html");
	$smarty->assign("prefecture_name",$prefecture_name);
	$smarty->assign("service_names",$service_names);
	$smarty->assign("target",$target);
	$smarty->display("template.html");

	}


public function confirm_form_driver($datas,$status){

	}

public function confirm_form_branch($datas,$status,$prefecture_name){

	//config.phpで指定したものが入らなかったためディレクトリを指定




	}
/**
 * ナビエリア登録フォームバリデーション
 * @param array $data
 * @return array $errors
 */
public function validate_form_area($data){

	$errors=array();

	if(! strlen(trim($data['name']))){
		$errors[]=NAVI_AREA_NAME.DATE_VERI_NO;
	}

	if(!strlen(trim($data['radius']))){
		$errors[]=RADIUS_FROM_CENTER.DATE_VERI_NO;
	}elseif(!preg_match('/^[0-9]+(\.[0-9]*)?$/',$data['radius'])){
		$errors[]=RADIUS_FROM_CENTER.ERROR_INT;
	}

	if(! strlen(trim($data['navi_message']))){
		$errors[]=NAVI_MESSAGE.DATE_VERI_NO;
	}

	if(! strlen(trim($data['latitude']))){
		$errors[]=LATITUDE.DATE_VERI_NO;
	}elseif(!preg_match('/^[0-9]+(\.[0-9]*)?$/', $data['latitude'])){
		$errors[]=LATITUDE.ERROR_FLOAT;
	}

	if(! strlen(trim($data['longitude']))){
		$errors[]=LONGITUDE.DATE_VERI_NO;
	}elseif(!preg_match('/^[0-9]+(\.[0-9]*)?$/', $data['longitude'])){
		$errors[]=LONGITUDE.ERROR_FLOAT;
	}

	return $errors;
}

/**
 * 輸送ルート作成フォームバリデーション
 * @param array $data
 * @return array $errors
 */
public function validate_form_transport_route($data){

	$errors=array();

	if(! strlen(trim($data['name']))){
		$errors[]=TRANSPOERT_ROUTE_NAME.DATE_VERI_NO;
	}

	if(!strlen(trim($data['geo_json']))){
		$errors[]=GEO_JSON.DATE_VERI_NO;
	}elseif(!json_decode($data['geo_json'])){
		$errors[]=GEO_JSON_ERROR;
	}

	return $errors;
}


/**
 * 輸送ルートコピーバリデーション
 * @param array $data
 * @return array $errors
 */
public function validate_form_transport_copy_route($data){

	$errors=array();


	if(! strlen(trim($data['name']))){
		$errors[]=TRANSPOERT_ROUTE_NAME.DATE_VERI_NO;
	}

	if(! strlen(trim($data['select_root_id']))){
		$errors[]=EMPTY_SELECT_ROOT;
	}

	return $errors;
}


/**
 * ルートマスタ編集時の変更
 * @param array $data
 * @return array $errors
 */
public function validate_form_transport_route_edit($data){

	$errors=array();

	if(! strlen(trim($data['name']))){
		$errors[]=TRANSPOERT_ROUTE_NAME.DATE_VERI_NO;
	}

	return $errors;
}


/**
 * ドライバーに紐づくルートバリデーション(ルートのIDがあるかどうか)
 * @param array $data
 * @return array $errors
 */
public function validate_form_transport_route_for_driver($data){

	$errors=array();

	if(! strlen(trim($data['select_root_id']))){
		$errors[]=EMPTY_SELECT_ROOT;
	}

	return $errors;
}

/**
 * 日付の文字列かのバリデーション。20170101形式
 * @param string $date
 */
public function validate_date_string($date){

	$errors=array();
	
	if(preg_match('/^([0-9]{8})$/', $date)){
		$year = mb_substr($date, 0, 4);
		$month = mb_substr($date, 4, 2);
		$day = mb_substr($date, 6, 2);
		if(!checkdate($month, $day, $year)){
			$errors[] = DATE_VALIDATION;
		}	
	}else{
		$errors[] = DATE_VALIDATION;
	}
	
	return $errors;
}


}
?>