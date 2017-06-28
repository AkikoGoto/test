<?php
ini_set( 'display_errors', 1 );
	//データベースからアラームメールを送る時間のプラスマイナス5分以内のデータを取得
	$NextAlarms = Alarms::getNextAlarm();
	
		try{
			
			if($NextAlarms){
			
			foreach($NextAlarms as $data){
					
					$alarm_id = $data['alarm_id'];
					$id = $data['id'];
					
					//アラームログのデータを取得
					$datas = Alarms::getAlarmLog($alarm_id);
					if($datas == null){
						$datas['is_alarm_on'] = 0;
					}
					
					//アラームを送るべき判定が0
					if($datas['is_alarm_on'] == 0){
					
						$data['is_alarm_on'] = 1;
						
					//データベースから取得
					$mail_data = Alarms::getAlarm($alarm_id);
					$email_other_admin = $mail_data[0]['email_other_admin'];
					
					//管理者のメールアドレス取得
					$company_id = $mail_data[0]['company_id'];
					$admin = Alarms::adminEmail($company_id);
					$admin_email = $admin[0]['email'];
					
					//メール送信情報
					$to = "$admin_email,$email_other_admin" ;//送信先
					$middle_language = '様は、指定された';
					
					//アラームで設定されているgocodeを取得
					$alarm_latitude = $mail_data[0]['latitude'];
					$alarm_longitude = $mail_data[0]['longitude'];
					$driver_id = $mail_data[0]['driver_id'];
					$accuracy = $mail_data[0]['accuracy'];
					$distance = null;
					
					//直近のgeocodeを取得
					$alarm_time = $mail_data[0]['alarm_time'];
					$date = substr($alarm_time,0,11);
					$alarm_time = substr($alarm_time,-8,5);
					
					$explode = explode(":", $alarm_time);
					$ten_minite = array(00,10);

					//10分後、10分前
					$increase_time = $date.date('H:i',mktime($explode[0]+$ten_minite[0],$explode[1]+$ten_minite[1]));
					$decrease_time = $date.date('H:i',mktime($explode[0]-$ten_minite[0],$explode[1]-$ten_minite[1]));
					
					$last_driver_status = Alarms::last_driver_status($driver_id);
					
					//ドライバーのデータが10分前・10分後以内だったら通す
					if($last_driver_status[0]['updated'] < $increase_time && $last_driver_status[0]['updated'] > $decrease_time){
						
						$near_latitude = $last_driver_status[0]['latitude'];
						$near_longitude = $last_driver_status[0]['longitude'];
						
						//2直線間の距離を求める
						$earth_r = 6378.137; //地球の半径
						$idoSa   = deg2rad($near_latitude - $alarm_latitude);     //緯度差をラジアンに
						$keidoSa = deg2rad($near_longitude - $alarm_longitude); //経度差をラジアンに
						$nanbokuKyori =  $earth_r * $idoSa;                        //南北の距離
						$touzaiKyori  = cos(deg2rad($aIdo)) * $earth_r * $keidoSa; //東西の距離
						$distance = (sqrt(pow($touzaiKyori,2) + pow( $nanbokuKyori,2)))*1000; //三平方の定理でdを求める
					}
						
						$contact_number = date('YmdGis');//問い合わせ番号
						$from_address = mb_encode_mimeheader('Smart動態管理アラーム').ADMIN_MAIL; //どこから来たか（送信元）
						$add_header = "From:$from_address";
						
						$address=$mail_data[0]['address'];
						$driver_name = $mail_data[0]['last_name'].$mail_data[0]['first_name'];
						
						$setting_time = substr($mail_data[0]['mail_time'], -5 , 5);
						$day = substr($mail_data[0]['mail_time'], 0 , -6);
					
							//指定した住所が誤差の範囲内かどうか(指定した住所にいるかどうか)
							if($distance < $accuracy && !($distance==null)){
						
								//メールの送信先の人が指定した時間にいるかどうか
								if($mail_data[0]['alert_when_there']){
						
									//題名
									$subject = "【Smart動態管理】 管理者が設定した住所にいます。";
									$mystery = 'にいました。';
									$subject  = mb_convert_encoding( $subject, "iso-2022-jp", "auto" );
									$message  = mb_convert_encoding( $message, "iso-2022-jp", "auto" );
									$from_address = mb_convert_encoding( $from_address, "iso-2022-jp", "auto" );
									$add_header = "From:$from_address";
										
								}else{
									echo '指定した時間に指定した住所の範囲内にいた。<br>
						     しかし現在のアラームの設定が、指定した時間、指定した場所にいなかったらメールを送る設定なので、メールを送りません。<br><br>';
									continue;
								}
						
								//指定した住所が誤差の範囲外かどうか(指定した住所にいないかどうか)
							}elseif($distance > $accuracy || $distance==null){
						
								//メールの送信先の人が指定した時間にいないかどうか
								if($mail_data[0]['alert_when_not_there']){
						
									//題名
									$subject = "【Smart動態管理】 管理者が設定した住所にいません。";
									$mystery = 'にいません。';
									$subject  = mb_convert_encoding( $subject, "iso-2022-jp", "auto" );
									$message  = mb_convert_encoding( $message, "iso-2022-jp", "auto" );
									$from_address = mb_convert_encoding( $from_address, "iso-2022-jp", "auto" );
						
								}else{
									echo '指定した時間に指定した住所の範囲外にいた。<br>
						     しかし現在のアラームの設定が、指定した時間・指定した場所にいたらメールを送る設定なので、メールを送りません。<br><br>';
									continue;
								}
						
							}
							
							//アラームのIDをアラームに送信するプログラムに渡す
							require('setAlarm_mail.php');
							
	 						//アラームログinsert						
	 						Alarms::insertAlarmLog($data);
							
	 						$set_alarmId = $data['alarm_id'];
	 						$alarm_time = substr($data['alarm_time'], 11);
	 						$next_week = substr($data['alarm_time'], 0 , -9);
	 						
	 						$data['alarm_time'] = '';
	 						
	  						if($data['daily'] == 1){
	  							
	  							$data['alarm_time'] = date("Y-m-d", strtotime("+1 day")).' '.$alarm_time;
	  							
	  						}
	 						
	  						if($data['weekly']){
	
	  							$data['alarm_time'] = date("Y-m-d",strtotime("+1 week" ,strtotime("$next_week"))).' '.$alarm_time;
	 							
	 						}
	 						
						Alarms::updateNextAlarmDB($data,$set_alarmId);
					
					
 					}else{
 						echo 'エラー アラームを既に送っています。<br><br>';
 					}
				}
			
 			}else{
 				echo 'メールを送る時間ではありません。<br><br>';
 			}
			
		}catch(Exception $e){
			die($e->getMessage());
		
		}		
	
		
	
?>