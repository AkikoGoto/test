<?php
/**
 * JVごとによって変更する定数等をここに記述する
 * テストサーバー用
 * @var unknown
 */

define('DOMAIN_BY_JV', 'localhost'); //ドメイン
define('DIRECTORY_JV', 'sla_edison_git'); //Smart動態管理のある　テストディレクトリか本番か
define('COMMON_SITE_TTL' ,'テストサイト　中間貯蔵運行・動態管理システム');//サイトタイトル
define('COMMON_SITE_VERSION' ,'2.0');//中間貯蔵運行・動態管理システムのバージョン
define('COMMON_SITE_LOGO' ,'logo_test.png');//ロゴ

//サーバーのパス
define('SERVER_PATH', 'C:\xampp\htdocs\smart_location_admin_edison\\');
define('SERVER_IMAGE_PATH', 'C:\xampp\htdocs\smart_location_admin_edison\uploaded\images\\');
//define('SERVER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/'.DIRECTORY_JV.'/');
//define('SERVER_IMAGE_PATH', '/var/www/vhosts/doutaikanri.com/public_html/'.DIRECTORY_JV.'/uploaded/images/');

define('DB_HOST', 'localhost');//データベースサーバーローカル
//define('DB_HOST', '160.16.70.168');//データベースサーバー

define('DB_USER', 'root');//ローカル
//define('DB_USER', 'mysqladmin');//テストサーバー

define('DB_PASS', 'yumeji'); //ローカル
//define('DB_PASS', 'kaiji3838');//テストサーバー

define('DB_NAME', 'smart_location_admin');//テストデータベース名

function initByJV(){
	ini_set( 'display_errors', 1 ); 
}

?>