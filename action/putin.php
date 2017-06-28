<?php
//タクシー会社の情報登録依頼フォームを表示するだけ

//エラーなどの場合、前回入力した値をデフォルトにする
$session=$_SESSION;

	if($_GET['id']){	

		$id=sanitizeGet($_GET['id']);
		//会社のユーザーIDと、編集するIDがあっているか確認
		user_auth($u_id,$id);
		$smarty->assign("filename","edit.html");
	
	}else{
		//会社登録画面へ遷移した場合
		if ($u_id) {
			//ログイン中の場合はtopページへ戻す
			header('Location: index.php');
			exit;
		}
		$smarty->assign("filename","putin.html");
	}

try{

//県名、サービス一覧の取得
	$prefecturesList=Data::getPrefectures();
	
	//新規データ投入ではなく、データ編集で、セッションに入っている会社IDとIDが同じ場合　データの取得	

	if($id){
		$dataList=Data::getById($id,$from_web);
						
	}
	
}catch(Exception $e){
	
		$message=$e->getMessage();
		}



$smarty->assign("prefecturesList",$prefecturesList);


	if($dataList){
		//編集画面、仮データ登録時のデータ表示
		$smarty->assign('data',$dataList[0]);
		
	}else{
		//エラーで戻ってきているときは、エラー前の情報表示
		$smarty->assign('session',$session);
			
	}

//メッセージの最大文字数
$smarty->assign('max_message',MESSAGE_MAX);

$smarty->display("template.html");



?>