<?php

//テンプレートディレクトリ
define('TEMPLATE_DIR', './templates');
define('TEMPLATE_COMPILE', './templates_c');
//define('TEMPLATE_URL', 'http://localhost/smart_location_admin_pg_2_5/');

if(isset($_SERVER['HTTPS'])){
	define('TEMPLATE_URL', 'https://'.SERVER_URL);
}else{
	define('TEMPLATE_URL', 'http://'.SERVER_URL);
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
//define('GCM_API', 'AIzaSyARzgiCuwvR4FpENwzvgXKE-Ebqjff4h-4');
define('GCM_API', 'AIzaSyDoAciOSPEjG0zhxTxXMIohbLUyOZ-p91w');

//地図上に表示されるドライバーのアイコンの幅と高さ
define('ICON_WIDTH', '30');

define('ADMIN_ID', '9841');

//何用かのスイッチ
define('MODE', 'BASIC');


?>