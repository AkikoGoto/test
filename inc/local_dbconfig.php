<?php
//データベースのアカウント・パスワード

/*　サーバーによって切り替える項目　*/
define('DB_HOST', 'localhost');//データベースサーバー

define('DB_USER', 'root');//ローカル
//define('DB_USER', 'admin');
//define('DB_USER', 'mysqladmin');//テストサーバー
//define('DB_USER', 'smart_test');//テスト環境
//define('DB_USER', 'admin');//サーバー

// define('DB_PASS', 'yumeji');
define('DB_PASS', 'admin');

//define('DB_PASS', 'akagi999');//テストサーバー
//define('DB_PASS', 'KD6bZB9y');
//define('DB_PASS', 'e5XTcD6b7eQJJb7P');;// テスト環境
//define('DB_PASS', 'cxg9SCYF');//サーバー
//define('DB_PASS', 'yumeji');//ローカル

//define('DB_NAME', 'concrete_location');//データベース名
//define('DB_NAME', 'smart_location_in_service');//データベース名
// define('DB_NAME', 'smart_location2');//テストデータベース名
define('DB_NAME', 'smart_location_edison');//テストデータベース名

define('DATA_SOURCE_NAME','mysql:dbname='.DB_NAME.';host='.DB_HOST.';charset=utf8');

?>
