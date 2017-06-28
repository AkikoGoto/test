<?php
/**
 * 管理画面　メッセージ画面表示
 */

require_once('admin_check_session.php');

//ランキングなど表示のため、セッションでスレッドIDが来ているか、来ていなければGETの値

if($_GET['situation']){
	$situation=$_GET['situation'];
	}else{
	$situation=$_SESSION['situation'];
	}
	

//シチュエーションでスイッチ
switch($situation){

	//ログインのアカウントとパスワード違いの場合
	
	case fail_login:

	$title='アカウントとパスワードが違います。';
	$message2=<<<EOM
	<div><A HREF="javascript:history.back()"><div style="padding-left:30px">戻る</div></div>
	
EOM;

	break;
	
	//データ変更後のメッセージ
	
	case editData:

	$title='データを変更しました。';
	$message2=<<<EOM
	
EOM;

	break;
	
	//スレッド削除後のメッセージ
	
	case deleteData:

	$title='データを削除しました。';
	$message2=<<<EOM
	<div><A HREF="javascript:history.back()"><div style="padding-left:30px">戻る</div></div>
	
EOM;

	break;
	
	//投稿削除後のメッセージ
	
	case deleteTopic:

	$title='投稿を削除しました。';
	$message2=<<<EOM
	<div><A HREF="javascript:history.back()"><div style="padding-left:30px">戻る</div></div>
	
EOM;

	break;
	
	}
	
$smarty->assign('title',$title);
$smarty->assign('message2',$message2);
	
$smarty->assign("filename","message.html");

$smarty->assign("u_id",$u_id);
$smarty->assign("u_name",$u_name);
$smarty->assign("cat",$cat);


//ランキングのため
$smarty->assign('ranking',$ranking);


$smarty->assign("filename","message.html");
$smarty->display("template.html");

//ランキングなどで更新される場合のためにスレッドIDを保持
$_SESSION['situation'] = $situation;


?>