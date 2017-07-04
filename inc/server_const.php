<?php

/*サーバーにより、切り替える項目*/
//サーバーURL
define('SERVER_URL', DOMAIN_BY_JV.'/'.DIRECTORY_JV.'/');


/**
 *	サーバーパス
 */
//test

define('SERVER_URL_WITHOUT_SLASH', DOMAIN_BY_JV.'/'.DIRECTORY_JV);
define('PUSH_NOTIFICATION_CER_PATH', '/var/www/vhosts/doutaikanri.com/public_html/'.DIRECTORY_JV.'/inc/push_notification_certificates/');


define('SOCKETIO_SERVER_HOST', 'wss://'.DOMAIN_BY_JV.'/');
define('SOCKETIO_REALTIMEMAP_PATH', '/ws/test/rtmap/socket.io');
define('SOCKETIO_ALERT_HOST', 'wss://'.DOMAIN_BY_JV.'/');
define('SOCKETIO_ALERT_PATH', '/ws/test/alert/socket.io');

define('SOCKETIO_ALERT_API_HOST', 'http://localhost:23000/');