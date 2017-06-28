<?php

/*サーバーにより、切り替える項目*/
//サーバーURL
//define('SERVER_URL', 'localhost/smart_location_admin_pg_2_5/');

//サーバーのパス
//define('SERVER_PATH', 'C:\xampp\htdocs\smart_location_admin_pg_2_5\\');
//define('SERVER_IMAGE_PATH', 'C:\xampp\htdocs\smart_location_admin_pg_2_5\uploaded\images\\');

/**
 *	サーバーパス
 */

//test
define('SERVER_URL', 'doutaikanri.com/concrete_location_admin');
define('SERVER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/concrete_location_admin');
define('SERVER_IMAGE_PATH', '/var/www/vhosts/doutaikanri.com/public_html/concrete_location_admin/uploaded/images/');
define('PUSH_NOTIFICATION_CER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/concrete_location_admin/inc/push_notification_certificates/');//プッシュ通知
//define('TEMPLATE_URL', 'http://doutaikanri.com/concrete_location_admin/');
//demo
//define('SERVER_URL', 'doutaikanri.com/smart_location_demo/');
//define('SERVER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/smart_location_demo/');
//define('SERVER_IMAGE_PATH', '/var/www/vhosts/doutaikanri.com/public_html/smart_location_demo/uploaded/images/');
//define('PUSH_NOTIFICATION_CER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/smart_location_demo/inc/push_notification_certificates/');//プッシュ通知

//is in service
/*define('SERVER_URL', 'doutaikanri.com/is_in_service/');
define('SERVER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/is_in_service/');
define('SERVER_IMAGE_PATH', '/var/www/vhosts/doutaikanri.com/public_html/is_in_service/uploaded/images/');
define('PUSH_NOTIFICATION_CER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/is_in_service/inc/push_notification_certificates/');//プッシュ通知
*/

//テンプレートディレクトリ
// define('TEMPLATE_DIR', '../smart_location_admin_pg_2_5/templates');
// define('TEMPLATE_COMPILE', '../smart_location_admin_pg_2_5/templates_c');
define('TEMPLATE_DIR', '../smart_location_test/templates');
define('TEMPLATE_COMPILE', '../smart_location_test/templates_c');

if(isset($_SERVER['HTTPS'])){
	define('TEMPLATE_URL', 'https://doutaikanri.com/smart_location_test/');
}else{
	define('TEMPLATE_URL', 'http://doutaikanri.com/smart_location_test/');
}

/**
 *プッシュ通知
 */

/*　サーバーによって切り替える項目ここまで　*/

//カウンターに使用するタイムアウトの時間
define('TIMEOUTSECONDS', '600');

//投稿タイトルの最大文字数
define('TITLEMAX', '100');

//投稿の最小文字数
define('MESSAGEMINIMUM', '20');

//1ページの最大表示数
define('PAGE_MAX', '10');


//新着として表示させる時間 単位は時間
define('IS_NEW','8');

//表示件数の最大表示数
define('DATA_MAX', '10000');

//緯度・経度の差
//define('GPS_DISTANCE', '0.25');
define('GPS_DISTANCE', '0.10');

//半径何キロで検索しているか
define('GPS_DISTANCE_KM', '10');

//近い順を表示する件数
define('NEAR_DATA_NUMBER', '40');

//データベースに現在位置を記録する最低間隔　単位　秒
//define('MIN_INTERVAL', '60');

//テスト用　秋川牧園用
define('MIN_INTERVAL', '10');


//誤差とみなす秒速（単位キロ） 新幹線が秒速300M
define('MIN_SPEED_PER_SECONDS', '0.3');

//空車を表示するドライバーの更新時間が何時間以内か
define('UPDATE_INTERVAL', '3:00:00.00');
define('UPDATE_INTERVAL_TEXT', '3');


//管理者メールアドレス
//define('ADMIN_MAIL','web@onlineconsultant.jp');
//define('ADMIN_MAIL','"たくる運営事務局"<web@onlineconsultant.jp>');
define('ADMIN_MAIL','admin@doutaikanri.com');
define('FROM_STRING','【Smart動態管理】運営事務局');


//各種メッセージの長さ
define('MESSAGE_MAX','1000');
define('DRIVER_MEMO_MAX','60'); //ドライバー業務メモ
define('INFORMATION_MAX','180'); //備考の長さ

//ドライバー業務日誌MAPの取得件数
define('MAX_DRIVER_RECORDS','100');

//GCM APIキー
define('GCM_API', 'AIzaSyARzgiCuwvR4FpENwzvgXKE-Ebqjff4h-4');

//地図上に表示されるドライバーのアイコンの幅と高さ
define('ICON_WIDTH', '30');

//コンクリート用かどうかのフラグ
define('MODE', 'CONCRETE');


?>