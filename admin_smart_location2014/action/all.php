<?php

/**
 * 管理画面全投稿表示
 */

require_once('admin_check_session.php');

try{

//投稿一覧
	$topicList=Topic::getTopicList(Topic::ALLTOPICS,$u_roll,$u_no);
	
	}catch(Exception $e){
	
		$message=$e->getMessage();
		}

//複数ページ表示
list($data, $links)=make_page_link($topicList);

$smarty->assign('topicList',$topicList);

$smarty->assign("links",$links['all']);
$smarty->assign("data",$data);

//ランキングのため
$smarty->assign('ranking',$ranking);

$smarty->assign("filename","all.html");

$smarty->display('template.html');

?>