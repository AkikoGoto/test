<?php

//require_once('inc/config.php');

class SingletonPDO extends PDO{

	
//クラス定数
const DUPLICATED =23000;

//スコープ演算子 static で宣言しておいて後で：：でアクセスできる
protected static $dbh;
	
protected static $dsn=DATA_SOURCE_NAME; 	

//DBへの接続
public function __construct(){

	parent::__construct(self::$dsn,DB_USER,DB_PASS);
	}

public static function connect(){
	if(!self::$dbh){
	self::$dbh=new self();
	self::$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	}
	return self::$dbh;
	}
}
?>