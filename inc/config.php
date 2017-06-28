<?php
/*日本語用設定ファイル*/
//ini_set( 'display_errors', "1" );
require_once('GlobalFunction.php');
require_once('mobile_function.php');
require_once('SingletonPDO.class.php');
require_once('data.class.php');
require_once('driver.class.php');
require_once('branch.class.php');
require_once('validate.class.php');
require_once('getdata.class.php');
require_once('work.class.php');
require_once('dayReport.class.php');
require_once('target.class.php');
require_once('message.class.php');
require_once('destination.class.php');
require_once('destination_category.class.php');
require_once('root.class.php');
require_once('rootDetail.class.php');
require_once('lite.class.php');
require_once('komatsu_data.class.php');
require_once('komatsu_driver.class.php');
require_once('komatsu_validate.class.php');
require_once('komatsu_obstacle.class.php');
require_once('alarms.class.php');
require_once('receipts.class.php');
require_once('driver_options.class.php');
require_once('company_options.class.php');
require_once('users.class.php');
require_once('movie.class.php');
require_once('information.class.php');
require_once('PushNotification/ApnsPHP/Autoload.php');
require_once('PushNotification/notification.php');
require_once('transport.class.php');
require_once('communication_log.class.php');
require_once('send_id.class.php');
require_once('communication.class.php');
require_once('communication_driver_status.php');
require_once('transport_route.class.php');
require_once('navi_area.class.php');
require_once('transport_route.class.php');
require_once('transport_route_driver.class.php');
require_once('deviated.class.php');
require_once('last_driver_status.class.php');
require_once('relay_server.class.php');

//言語ファイル
require_once('language_ja.php');
//レンタルサーバー用
//require_once('../Smarty/libs/Smarty.class.php');
require_once('Smarty.class.php');

//ドコモの場合、セッションを明示する。
if(get_carrier()=='docomo'){
	ini_set('session.use_trans_sid', 1);
}


/**
 * ユーザー情報の初期化
 */
if (empty($_SESSION['user_id'])) {
	if (isset($_COOKIE['user_credential'])) {
		$credential = $_COOKIE['user_credential'];
		$idArray = Users::authenticateCredential($credential);

		if(isset($idArray[0]['id'])) {
			$_SESSION['user_id'] = $idArray[0]['id'];
			$_SESSION['user_last_name'] = $idArray[0]['last_name'];
			$_SESSION['user_first_name'] = $idArray[0]['first_name'];
			// credentialを新しいものに置き換える
			$credential = Users::regenerateCredential($idArray[0]['id']);
		}
	}
}


//内部文字エンコーディング
mb_internal_encoding("UTF-8");
//mysql_query('SET NAMES utf8', $sql ); // ←これ
//mysql_set_charset('utf8'); // ←変更

//各データの配列
$company_array=array('is_company','company_name','business_hours_24','business_hours','car_number','info','email','contact_person_last_name'
,'contact_person_first_name','contact_tel','note','url','login_id','passwd','is_ban_driver_editing','company_group_id',
	'pick_up','credit','debit','fare','from_web','email_display');

$geographic_array=array('company_id','latitude','longitude','postal','prefecture','city','ward','town','address','tel','name');

$subscription_array = array ('receipt_unique_id','expiration_date', 'wallet_order_id');

$drivers_array=array('company_id','last_name','first_name','furigana','mobile_tel', 'mobile_email','experience','no_accident',
	'car_type','equipment','sex','birthday','erea','regist_number','driver_message','geographic_id', 'is_group_manager' ,
	'login_id','passwd','registration_id', 'ios_device_token', 'image_name', 'is_branch_manager');

$user_array = array('company_id', 'last_name', 'first_name', 'furigana', 'mobile_tel', 'mobile_email', 'login_id', 'passwd');

$realtime_map_setting_array = array( 'visible_driver_id', 'is_public', 'viewed_driver_id', 'is_users_viewing' );

$app_setting_array=array('company_id','action_1','action_2','action_3','action_4','time','distance','photo_interval_distance','photo_interval_time','accuracy','track_always');

$driver_status_array=array('driver_id','status','latitude','longitude','sales','address','detail','speed','start','end','saved_time','isTransporting');

$registration_array=array('driver_last_name','driver_first_name');

$geo_array=array('lat','long');

$recreated_array=array('time_from_year','time_from_month','time_from_day','time_from_hour','time_from_minit','time_from_seconds');

$chk_start_end_array=array('start','end');

$worktime_array=array('driver_id','status','time_from_year','time_from_month','time_from_day','time_from_hour',
						'time_from_minit','time_from_second',
						'time_to_year','time_to_month','time_to_day','time_to_hour','time_to_minit',
						'time_to_second');

$work_array=array('driver_id','company_id','created','start','end','start_address','end_address',
				'start_latitude','start_longitude','end_latitude','end_longitude','status','distance','plate_number',
				'destination_company_name', 'amount', 'toll_fee', 'drive_memo');

$day_report_array=array('start_meter','arrival_meter','supplied_oil','drive_date',
				'year_day_report','month_day_report','day_day_report');

$target_array=array('target_id','driver_id','latitude','longitude',
				'address','target_time','is_picked','picked_date','created','target_set_date_year',
				'target_set_date_month','target_set_date_day','picked_date_year','picked_date_month','picked_date_day');

$message_array=array('message_id','gcm_message','address','message_longitude', 'message_latitude', 'thread_id', 'branch_id');

$destination_array=array('destination_name','destination_kana','postal','address','tel','fax','department','contact_person','email',
'information','latitude','longitude');

$root_array=array('date','driver_id','information');

$root_detail_array=array('root_id', 'deliver_time', 'destination_id', 'root_address',
					'latitude', 'longitude', 'information');

$comment_array=array('status','comment');

$alerms_array=array('driver_id','daily','weekly','date','address','latitude','longitude','accuracy','alert_when_not_there','alert_when_there',
					'mail_timing','mail_before_or_after','email_other_admin','mail_time');

$invices_array=array('iphone_device_number','android_device_number','remarks');

$destination_category_array=array('name','color');

$area_array = array('name', 'navi_message', 'latitude', 'longitude', 'radius', 'transport_route_id');

$transport_route_array = array('name', 'geo_json', 'information', 'destination_id', 'select_root_id');

//スーパーユーザーのID
define('SUPER_USER', 1);
?>