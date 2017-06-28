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

ini_set( 'display_errors', 1 ); 

//test
define('SERVER_URL', 'edison.doutaikanri.com/smart_location_test/');
define('SERVER_URL_WITHOUT_SLASH', 'edison.doutaikanri.com/smart_location_test');
define('SERVER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/smart_location_test/');
define('SERVER_IMAGE_PATH', '/var/www/vhosts/doutaikanri.com/public_html/smart_location_test/uploaded/images/');
define('PUSH_NOTIFICATION_CER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/smart_location_test/inc/push_notification_certificates/');


define('SOCKETIO_SERVER_HOST', 'wss://edison.doutaikanri.com/');
define('SOCKETIO_REALTIMEMAP_PATH', '/ws/test/rtmap/socket.io');
define('SOCKETIO_ALERT_HOST', 'wss://edison.doutaikanri.com/');
define('SOCKETIO_ALERT_PATH', '/ws/test/alert/socket.io');

define('SOCKETIO_ALERT_API_HOST', 'http://localhost:23000/');

//さくらのサーバー用
//define('SERVER_URL', 'edsakura.doutaikanri.com/smart_location_test/');
//define('SERVER_URL_WITHOUT_SLASH', 'edsakura.doutaikanri.com/smart_location_test');
//define('SERVER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/smart_location_test/');
//define('SERVER_IMAGE_PATH', '/var/www/vhosts/doutaikanri.com/public_html/smart_location_test/uploaded/images/');
//define('PUSH_NOTIFICATION_CER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/smart_location_test/inc/push_notification_certificates/');//プッシュ通知

// define('SOCKETIO_SERVER_HOST', 'http://edsakura.doutaikanri.com:23000/');
// define('SOCKETIO_REALTIMEMAP_PATH', '/socket.io');
// define('SOCKETIO_ALERT_HOST', 'http://edsakura.doutaikanri.com:3000/');
// define('SOCKETIO_ALERT_PATH', '/socket.io');
//demo
//define('SERVER_URL', 'doutaikanri.com/smart_location_demo/');
//define('SERVER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/smart_location_demo/');
//define('SERVER_IMAGE_PATH', '/var/www/vhosts/doutaikanri.com/public_html/smart_location_demo/uploaded/images/');
//define('PUSH_NOTIFICATION_CER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/smart_location_demo/inc/push_notification_certificates/');//プッシュ通知

//is in service
//define('SERVER_URL', 'doutaikanri.com/is_in_service/');
/*define('SERVER_URL', 'localhost/smart_location_admin_pg_2_5/');
define('SERVER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/is_in_service/');
define('SERVER_IMAGE_PATH', '/var/www/vhosts/doutaikanri.com/public_html/is_in_service/uploaded/images/');
define('PUSH_NOTIFICATION_CER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/is_in_service/inc/push_notification_certificates/');//プッシュ通知*/