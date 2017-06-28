<?php

		try{
			
				//メールの本文
				$message = <<<EOM
いつもSmart動態管理をご利用頂き、ありがとうございます。
---------------------------------
【アラーム番号】
$contact_number
---------------------------------
【ドライバー名】
$driver_name
---------------------------------
【設定された時間】
$setting_time
---------------------------------
【ご連絡事項】
$driver_name$middle_language$address$mystery

このメールは、Smart動態管理　アラーム機能を利用して送信しています。
問い合わせは、御社のSmart動態管理を利用されている管理者にお問い合わせください。

メールが間違って送信されていると思われる場合は、このメールに直接返信をせず、
下記のSmart動態管理開発会社までご連絡ください。

---------------------------------
Smart動態管理
http://doutaikanri.com/

株式会社オンラインコンサルタント
TEL：045-306-9506
EMAIL：oc@onlineconsultant.jp
---------------------------------
EOM;
			
				//指定した住所が誤差の範囲内かどうか(指定した住所にいるかどうか)
				if($distance < $accuracy && !($distance==null)){
				
					//メールの送信先の人が指定した時間にいるかどうか
					if($mail_data[0]['alert_when_there']){
				
						//メールを送る関数
						if(mb_send_mail($to,$subject,$message,$add_header)){
				
							echo '正常にメールを送りました。<br><br>';
							$data['alarm_sent'] = 1;
							return($data['alarm_sent']);
				
						}else{
							echo 'メールを遅れませんでした。<br><br>';
							$data['alarm_sent'] = 0;
							return($data['alarm_sent']);
						}
							
					}
				
					//指定した住所が誤差の範囲外かどうか(指定した住所にいないかどうか)
				}elseif($distance > $accuracy || $distance==null){
				
					//メールの送信先の人が指定した時間にいないかどうか
					if($mail_data[0]['alert_when_not_there']){
				
						//メールを送る関数
						if(mb_send_mail($to,$subject,$message,$add_header)){
						
							echo '正常にメールを送りました。<br><br>';
							$data['alarm_sent'] = 1;
							return($data['alarm_sent']);
						
						}else{
							echo 'メールを遅れませんでした。<br><br>';
							$data['alarm_sent'] = 0;
							return($data['alarm_sent']);
						}
						
							
					}
				
				}
				
				
				//header("Location:index.php?action=message_mobile&situation=after_driver_customized&company_id=$company_id");
							
				//exit(0);
					
		}catch(Exception $e){
			die($e->getMessage());
		
		}		
	
		
	
?>