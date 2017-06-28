<?php
//会社データ登録・編集する画面・仮登録情報承認画面を表示するだけ

require_once('admin_check_session.php');

//エラーなどの場合、前回入力した値をデフォルトにする
$session=$_SESSION;

if($_GET['id']){

	$id=$_GET['id'];

}

try{

	//県名、サービス一覧の取得
	$prefecturesList=Data::getPrefectures();
	
	if($id){
		$dataList=Data::getById($id,0);
	}


}catch(Exception $e){

	$message=$e->getMessage();
}

$smarty->assign("prefecturesList",$prefecturesList);

//画像

$smarty->assign('image_info',$image_info);

$smarty->assign('id',$id);
//仮登録情報かどうかのフラグ
$smarty->assign('activation',$activation);
//フォームの情報送信先
$smarty->assign('action','tryPutin');
//編集画面、仮データ登録時のデータ表示
$smarty->assign('data',$dataList[0]);

if($dataList){
	//編集画面、仮データ登録時のデータ表示
	$smarty->assign('data',$dataList[0]);

}else{
	//エラーで戻ってきているときは、エラー前の情報表示
	$smarty->assign('session',$session);

}



$smarty->assign("filename","putin.html");


$smarty->display("template.html");

?>