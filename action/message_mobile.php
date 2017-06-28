<?php
//メッセージ画面表示


if($_GET['situation']){
	$situation=sanitizeGet($_GET['situation']);
	}else{
	$situation=$_SESSION['situation'];
	}
	
//シチュエーションでスイッチ
switch($situation){

	case "private_company":
		//ページのタイトルに、メッセージをアサイン
		$smarty->assign('page_title',"非公開の地図への不正なアクセス");
		
		$title='非公開の地図へアクセスしようとしました。';
		$message2=<<<EOM
			<p>この会社は地図を公開していません。<br>
			お手数ですが、URLをご確認の上アクセスしてください。</p>
EOM;
	break;
	
//ニュース
case "news":

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"お知らせ");
	$smarty->assign('meta_description',"お知らせ。");
	
	$title='運営者からのニュース・お知らせ';
	$message2=<<<EOM

EOM;
	break;

	
	
//不正なアクセス
case "wrong_access":

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"不正なアクセスです。");

	$title='不正なアクセスです';
	$message2=<<<EOM
	<p>一定の時間がたつと、自動的にログアウトします。<br>
	その場合は、お手数ですが、もう一度ログインしてください。</p>

EOM;
	break;
	
/**
 * ドライバーが許可されていないのに日報のデータを編集しようとした時
 */
case "not_allowed_edit_by_driver":

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"編集が許可されていません。");

	$title='編集が許可されていません';
	$message2=<<<EOM
	<p>編集は管理者によって許可されていません。	
	</p>
	<p>
	<a href="index.php?action=driver_status">TOPページへ</a> <a href="javascript:history.back()">戻る</a>
	</p>

EOM;
	break;
	
	
	
	
/**
 * 権限がない
 */
case "not_authorized":

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"許可されていないアクセスです。");

	$title='許可されていないアクセスです';
	$message2=<<<EOM
	<p>一定の時間がたつと、自動的にログアウトします。<br>
	その場合は、お手数ですが、もう一度ログインしてください。</p>

EOM;
	break;
	
	
	
case "deleteData":	
//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"データを消去しました");

	$title='データを消去しました';
	
	if($carrier==NULL||$carrier=="Android"||$carrier=="iPhone"){
		
		$message2=<<<EOM
		<p>データを消去しました。</p>
		<A HREF="javascript:history.back()">戻る</a>
		<a href="index.php">TOPページへ</a>

EOM;
		
	}else{
		
		$message2=<<<EOM
		<p>データを消去しました。<br>
		携帯の戻るボタンでお戻りください。</p>

EOM;
		
	}
	break;
	
case "cannotDeleteData":	
//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"データを消去できませんでした");

	$title='データを消去できませんでした';
	
	if($carrier==NULL||$carrier=="Android"||$carrier=="iPhone"){
		
		$message2=<<<EOM
		<p>データを消去できませんでした。</p>
		<A HREF="javascript:history.back()">戻る</a>
		<a href="index.php">TOPページへ</a>

EOM;
		
	}else{
		
		$message2=<<<EOM
		<p>データを消去できませんでした。<br>
		携帯の戻るボタンでお戻りください。</p>

EOM;
		
	}
	break;	
	

//アラームを消去
case "deleteAlarm":
		//ページのタイトルに、メッセージをアサイン
		$smarty->assign('page_title',"データを消去しました");
	
		$title='データを消去しました';
	
		if($carrier==NULL||$carrier=="Android"||$carrier=="iPhone"){
	
			$message2=<<<EOM
		<p>データを消去しました。</p>
		<A HREF="javascript:history.back()">戻る</a>
		<a href="index.php">TOPページへ</a>
	
EOM;
	
		}else{
	
			$message2=<<<EOM
		<p>データを消去しました。<br>
		携帯の戻るボタンでお戻りください。</p>
	
EOM;
	
		}
		break;

//パスワード送信完了メッセージ
case "pass":

	$title='パスワードを送信しました';
	$message2='入力されたメールアドレスへ、パスワードを送信しました。<br>
	メールをご確認ください。<br>
	注意：届かない場合は、迷惑メールへ振り分けられている場合もございます。
	';
	break;


//パスワード送信時、メールアドレスがない場合
case "no_id":

	$title='エラー';
	if($carrier==NULL||$carrier=="Android"||$carrier=="iPhone"){
		$message2='このメールアドレスでは登録がありません。<br>
		<br>
		<A HREF="javascript:history.back()">戻る</a>
	
		';
		
	}else{

		$message2='このメールアドレスでは登録がありません。<br>
		<br>
		携帯の戻るボタンで戻ってください。
		
		';
}
	break;

//ルート詳細のコピーをした後
case "after_copy_root":

	$driver_id = sanitizeGet($_GET['driver_id']);
	$company_id = sanitizeGet($_GET['company_id']);
	$date = sanitizeGet($_GET['date']);
	
//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"ルートのコピーが終わりました");

	$title='ルートのコピーが終わりました';
	$message2=<<<EOM
	<p>ルートのコピーが終わりました。</p>
	<a href="index.php?action=/root_detail/viewRootDetails&company_id=$company_id&driver_id=$driver_id&date=$date">
		戻る
	</a>
	&nbsp;&nbsp;
	<a href="index.php">TOPページへ</a>

EOM;
	break;
	
	
//ルート詳細を指定した後
case "after_edit_root_detail":

	$driver_id = sanitizeGet($_GET['driver_id']);
	$company_id = sanitizeGet($_GET['company_id']);
	$date = sanitizeGet($_GET['date']);
	
//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"ルート詳細の変更が終わりました");

	$title='ルート詳細の変更が終わりました';
	$message2=<<<EOM
	<p>ルート詳細の変更が終わりました。</p>
	<a href="index.php?action=/root_detail/viewRootDetails&company_id=$company_id&driver_id=$driver_id&date=$date">
		戻る
	</a>
	&nbsp;&nbsp;
	<a href="index.php">TOPページへ</a>

EOM;
	break;
			
//配送先を指定した後
case "after_edit_destination":
	
	$company_id = sanitizeGet($_GET['company_id']);
	
//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"配送先の変更が終わりました");

	$title='配送先の変更が終わりました';
	$message2=<<<EOM
	<p>配送先の変更が終わりました。</p>
	<a href="index.php?action=/destination/viewDestinations&company_id=$company_id">
		戻る
	</a>
	&nbsp;&nbsp;
	<a href="index.php">TOPページへ</a>

EOM;
	break;
		
//配送先カテゴリを指定した後
case "after_edit_destination_category":
	
	$company_id = sanitizeGet($_GET['company_id']);
	
	$smarty->assign('page_title',"配送先カテゴリーの変更が終わりました");

	$title='配送先カテゴリーの変更が終わりました';
	$message2=<<<EOM
	<p>配送先カテゴリーの変更が終わりました。</p>
	<a href="index.php?action=/destination_category/viewDestinationCategories&company_id=$company_id">
		戻る
	</a>
	&nbsp;&nbsp;
	<a href="index.php">TOPページへ</a>

EOM;
	break;
		
		
	
	
//配送先を指定した後
case "after_edit_root":
	
	$company_id = sanitizeGet($_GET['company_id']);
	$driver_id = sanitizeGet($_GET['driver_id']);
		
//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"ルートの編集が終わりました");

	$title='ルートの編集が終わりました';
	$message2=<<<EOM
	<p>ルートの編集が終わりました。</p>
	<a href="index.php?action=/root/viewRoots&driver_id=$driver_id&company_id=$company_id">
		戻る
	</a>
	&nbsp;&nbsp;
	<a href="index.php">TOPページへ</a>

EOM;
	break;
		
//ドライバーカスタマイズの編集完了
case "after_driver_customized":
	
	$company_id = sanitizeGet($_GET['company_id']);
	
//ページのタイトルに、メッセージをアサイン
	$smarty->assign('pegetitle','アラーム設定が終わりました');
	
	$title = 'アラーム設定が終わりました';
	$message2=<<<EOM
	<p>アラーム設定が終わりました。</p>
	<a href="index.php?action=alarm/set_alarm&company_id=$company_id">
		戻る
	</a>
	&nbsp;&nbsp;
	<a href="index.php">TOPページへ</a>
	
EOM;
	break;
	
	//配送先を指定した後
case "after_edit_setting":
	
	$company_id = sanitizeGet($_GET['company_id']);
	
//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"設定の変更が終わりました");

	$title='設定の変更が終わりました';
	$message2=<<<EOM
	<p>設定の変更が終わりました。</p>
	<a href="index.php?action=setting&company_id=$company_id">
		戻る
	</a>
	&nbsp;&nbsp;
	<a href="index.php">TOPページへ</a>
	
EOM;
	break;

	//ドライバーカスタマイズのアップデート完了
	case "after_driverUpdate_customized":
	
		$company_id = sanitizeGet($_GET['company_id']);
	
		//ページのタイトルに、メッセージをアサイン
		$smarty->assign('pegetitle','アラームの編集が終わりました');
	
		$title = 'アラームの編集が終わりました';
		$message2=<<<EOM
	<p>アラームの編集が終わりました。</p>
	<a href="index.php?action=alarm/viewAlarms&company_id=$company_id">
		戻る
	</a>
	&nbsp;&nbsp;
	<a href="index.php">TOPページへ</a>
	
EOM;
		break;


		//メール送信完了
		case "after_mail_customized":
		
			$company_id = sanitizeGet($_GET['company_id']);
		
			//ページのタイトルに、メッセージをアサイン
			$smarty->assign('pegetitle','メール送信が完了しました。');
		
			$title = 'メール送信が完了しました。';
			$message2=<<<EOM
	<p>メール送信が完了しました。</p>
	<a href="index.php?action=alarm/viewAlarms&company_id=$company_id">
		戻る
	</a>
	&nbsp;&nbsp;
	<a href="index.php">TOPページへ</a>
		
EOM;
			break;
	
		//メール送信エラー　
		case "after_mail_customized":
			
				$company_id = sanitizeGet($_GET['company_id']);
			
				//ページのタイトルに、メッセージをアサイン
				$smarty->assign('pegetitle','メール送信エラー。');
			
				$title = 'メール送信エラーが発生しました。';
				$message2=<<<EOM
	<p>メール送信エラーが発生しました。<br>メールを既に送っています。</p>
	<a href="index.php?action=alarm/viewAlarms&company_id=$company_id">
		戻る
	</a>
	&nbsp;&nbsp;
	<a href="index.php">TOPページへ</a>
			
EOM;
				break;

				
		//メール送信エラー　
		case "after_error_mail1":
		
			$company_id = sanitizeGet($_GET['company_id']);
		
			//ページのタイトルに、メッセージをアサイン
			$smarty->assign('pegetitle','メール送信エラー。');
		
			$title = 'メール送信エラーが発生しました。';
			$message2=<<<EOM
	<p>メール送信エラーが発生しました。<br>アラームメールを送る時間ではありません。</p>
	<a href="index.php?action=alarm/viewAlarms&company_id=$company_id">
		戻る
	</a>
	&nbsp;&nbsp;
	<a href="index.php">TOPページへ</a>
				
EOM;
					break;
				
		//メール送信エラー　
		case "after_error_mail2":
				
			$company_id = sanitizeGet($_GET['company_id']);
				
			//ページのタイトルに、メッセージをアサイン
			$smarty->assign('pegetitle','メール送信エラー。');
				
			$title = 'メール送信エラーが発生しました。';
			$message2=<<<EOM
	<p>メール送信エラーが発生しました。<br>メールを既に送っています。</p>
	<a href="index.php?action=alarm/viewAlarms&company_id=$company_id">
		戻る
	</a>
	&nbsp;&nbsp;
	<a href="index.php">TOPページへ</a>
		
EOM;
					break;
				
			
//不正なアクセス
case "after_edit_barnch":

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',BRANCH."情報の変更が終わりました");
	$company_id = sanitizeGet($_GET['company_id']);
	$branch = BRANCH;

	$title=BRANCH.'情報の変更が終わりました';
	$message2=<<<EOM
	<p>サブグループ情報の変更が終わりました。</p>
	<a href="index.php">TOPページへ</a>｜<a href="index.php?action=viewBranches&company_id=$company_id&by=company">サブグループ一覧へ</a>

EOM;
	break;
		
//ドライバー情報編集後のメッセージ
case "after_edit_driver":

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"ドライバー情報の変更が終わりました");
	$company_id = sanitizeGet($_GET['company_id']);

	$title='ドライバー情報の変更が終わりました';
	$message2=<<<EOM
	<p>ドライバー情報の変更が終わりました。<br>
	<br>
	新しく追加したドライバーには、先ほど入力したIDとパスワードを教えてください。<br>
	ドライバーはアプリから、そのIDとパスワードを利用して、ログインできます。
	</p>
	<a href="index.php">TOPページへ</a>｜<a href="index.php?action=viewDrivers&id=$company_id">ドライバー情報一覧へ</a>

EOM;
	break;
	
case "after_edit_viewer":

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title', "閲覧ユーザー情報の変更が終わりました");

	$title='閲覧ユーザー情報の変更が終わりました';
	$message2=<<<EOM
	<p>閲覧ユーザー情報の変更が終わりました。</p>
	<a href="index.php">TOPページへ</a>

EOM;
	break;
		
//モバイル版のみ携帯用の、識別番号送信ができなかった場合
case "no_unit_no":

	$title='エラー';
	$message2=<<<EOM
	<p>現在、個体識別番号が登録されていないため、かんたんログインができません。<br>
	かんたんログインをするためには、一度<a href="index.php?action=driverLogin">ログイン画面</a>でログイン情報を入力して携帯電話情報の送信をしてください。<br>
	かんたんログインのためには携帯電話の個体識別番号を使用します。<br>	
	うまくいかない場合は、ご使用の携帯のマニュアルを見て個体識別番号が送信されるように設定しなおしてください。<br>
	また、別の携帯電話からログインしたい場合、携帯を変更した場合も、一度ログインが必要です。<br>
	スマートフォンは現在未対応です。<br>
	<a href="index.php?action=driverLogin">ログイン</a><br>
	</p>
	

EOM;
	
	break;


//モバイル版のみ　携帯識別番号の登録がなかった場合
case "unit_no":

	$title='かんたんログインをするためには、一度ログイン画面でログイン情報を入力して携帯電話情報の送信をしてください。';
	$message2=<<<EOM
	<p>現在、個体識別番号が登録されていないため、かんたんログインができません。<br>
	かんたんログインをするためには、一度ログイン画面でログイン情報を入力して携帯電話情報の送信をしてください。<br>
	<a href="index.php?action=login">ログイン</a><br>
	<br>
	
	個体識別番号が送信できない場合、ご使用の携帯のマニュアルをご参照ください。</p>
	

EOM;
	
	break;

case "after_edit":
	$title='情報の編集が終わりました。';
	$message2=<<<EOM
	
	<div><a href="index.php">TOPページへ</a></div>
EOM;
	break;

	

	
//ドライバーステータス情報編集後のメッセージ
case "after_edit_driver_record":

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"ドライバーステータス情報の変更が終わりました");
	
	$driver_id = sanitizeGet($_GET['driver_id']);
	$company_id = sanitizeGet($_GET['company_id']);


	$title='ドライバーステータス情報の変更が終わりました';
	$message2=<<<EOM
	<p>ドライバー情報の変更が終わりました。</p>
EOM;
	if(MODE =="CONCRETE"){
	$message2.=<<<EOM
	<a href="index.php?action=concrete/driver_record_map&driver_id=$driver_id&company_id=$company_id">
EOM;
	}else{
	$message2.=<<<EOM
	<a href="index.php?action=driver_record_map&driver_id=$driver_id&company_id=$company_id">
EOM;
	}
	$message2.=<<<EOM
	ドライバー業務日誌MAPへ</a>&nbsp;|&nbsp;	
	<a href="index.php">TOPページへ</a>

EOM;
	break;	

case "after_worktime_record":
	$title='日報の編集が終わりました。';
	$driver_id = sanitizeGet($_GET['driver_id']);
	$company_id = sanitizeGet($_GET['company_id']);
	
	$message2=<<<EOM
	<p>作業時間の変更が終わりました。</p>
EOM;
	if(MODE == "CONCRETE"){
		$message2.=<<<EOM
	<div>
	<a href="index.php?action=concrete/worktime&driver_id=$driver_id&company_id=$company_id">
EOM;
	}else{
	$message2.=<<<EOM
	<div>
	<a href="index.php?action=worktime&driver_id=$driver_id&company_id=$company_id">
EOM;
	}
	$message2.=<<<EOM
	日報へ戻る</a>
	&nbsp;|&nbsp;	
	<a href="index.php">TOPページへ</a></div>
EOM;
	break;	
	
case "after_day_report":
	$title='日報用データの編集が終わりました。';
	$driver_id = sanitizeGet($_GET['driver_id']);
	$company_id = sanitizeGet($_GET['company_id']);
	
	$message2=<<<EOM
	<p>日報用データの変更が終わりました。</p>

	<div>
	<a href="index.php?action=/day_report/day_report_data&driver_id=$driver_id&company_id=$company_id">
	日報用データ一覧へ戻る</a>
	&nbsp;|&nbsp;	
	<a href="index.php">TOPページへ</a></div>
EOM;
	break;	

case "after_target_edit":
	$title='データの編集が終わりました。';
	$driver_id = sanitizeGet($_GET['driver_id']);
	$company_id = sanitizeGet($_GET['company_id']);
	
	$message2=<<<EOM
	<p>データの変更が終わりました。</p>

	<div>
	<a href="index.php?action=/target/getTargetMap&driver_id=$driver_id&company_id=$company_id">
	マップへ戻る</a>
	&nbsp;|&nbsp;	
	<a href="index.php">TOPページへ</a></div>
EOM;
	break;	
	
	
case "privacy":

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"個人情報保護");

	$title='個人情報保護について';
	$message2=<<<EOM
	
	<p>当サイトは個人情報に関する法令およびその他の規範を遵守し、お客さまの大切な個人情報の保護に万全を尽くします。<br>お客さまの個人情報については、下記の目的の範囲内で適正に取り扱いさせていただきます。</p>
    <ol>
	<li>お問合せ、ご相談にお答えすること</li>
	<li>電話、電子メール、郵送等各種媒体により、広報・アンケート調査及び景品、資料等の送付を行うこと、資料等の送付を行うこと</li>
    <li>サービスの改善又は新たなサービスの開発を行うこと</li>
  	<li>ご了解いただいた目的の範囲内で、お客様の個人情報を利用いたします。</li>
    <li>あらかじめお客様からご了解いただいている場合、法令で認められている場合を除き、お客様の個人情報を第三者に提供または開示いたしません。</li>
    </ol>
    <p>お客さまの個人情報の保護を図るために、また、法令その他の規範の変更に対応するために、プライバシーポリシーを改定する事がございます。改定があった場合はホームページにてお知らせいたします。
      </p>
	

EOM;
	
	break;

//ログインのアカウントとパスワード違いの場合

case "fail_login":

	$title='アカウントとパスワードが違います。';
	if($carrier==NULL||$carrier=="Android"||$carrier=="iPhone"){
	$message2=<<<EOM
	<div>
		<a href="javascript:history.back()">
			<div style="padding-left:30px">
				戻る
			</div>
		</a>
	</div>
	
EOM;
	}else{
	$message2=<<<EOM
	<div>
	携帯の戻るボタンでお戻りください。
	</div>
	
EOM;
		
	}
	break;	
	
case "fail_concrete_report":
	$title='日報データがありませんでした。';
	$driver_id = sanitizeGet($_GET['driver_id']);
	$company_id = sanitizeGet($_GET['company_id']);
	
	$message2=<<<EOM
	<p>日報データがありませんでした。</p>

	<a href="index.php">TOPページへ</a></div>
EOM;
	break;
	
	//ナビエリア登録後のメッセージ
	case "after_put_navi_area":
	
		//ページのタイトルに、メッセージをアサイン
		$smarty->assign('page_title',"ナビエリアの新規追加が終わりました");
	
		$title='ナビエリアの新規追加が終わりました';
		
		$transport_route_id = $_GET['transport_route_id'];
		$company_id = $_GET['company_id'];
		
		$message2=<<<EOM
	<p>ナビエリアの新規追加が終わりました<br>
	<br>
	</p>
	<a href="index.php?action=navi_area/putArea&company_id={$company_id}&transport_route_id={$transport_route_id}">
		続けてナビエリアを登録する
	</a>
	&nbsp;|&nbsp;
	<a href="index.php">TOPページへ</a>
	
EOM;
		break;
		
		//ナビエリア編集後のメッセージ
	case "after_edit_navi_area":
	
		//ページのタイトルに、メッセージをアサイン
		$smarty->assign('page_title',"ナビエリアの編集が終わりました");
	
		$title='ナビエリアの編集が終わりました';
		
		$transport_route_id = $_GET['transport_route_id'];
		$company_id = $_GET['company_id'];
		
		$message2=<<<EOM
	<p>ナビエリアの編集が終わりました<br>
	<br>
	</p>
	<a href="index.php?action=transport_route/viewRoute&company_id={$company_id}&transport_route_id={$transport_route_id}">
		ルート及びエリア詳細に戻る
	</a>
	&nbsp;|&nbsp;
	<a href="index.php">TOPページへ</a>
		
EOM;
		
		break;
		
	//ドライバー輸送ルート登録後のメッセージ
	case "after_put_transport_route_drivers":
	
		//ページのタイトルに、メッセージをアサイン
		$smarty->assign('page_title',"ドライバーへのルート一括設定が終わりました");
	
		$title='ドライバーへのルート一括設定が終わりました';
		
		$company_id = $_GET['company_id'];
		
		$message2=<<<EOM
	<p>ドライバーへのルート一括設定が終わりました<br>
	<br>
	</p>
	<a href="index.php?action=transport_route/setRouteToDriver&company_id=$company_id">
		続けてドライバーへルートを一括設定する
	</a>
	&nbsp;|&nbsp;
	<a href="index.php?action=transport_route/viewDriversRouteArea&company_id=$company_id">
		ドライバールート一覧へ戻る
	</a>
	&nbsp;|&nbsp;
	<a href="index.php">TOPページへ</a>
	
EOM;
		break;
		//ナビエリア削除後のメッセージ
		case "after_delete_navi_area":
		
			$smarty->assign('page_title',"ナビエリアを消去しました");
		
			$title='ナビエリアを消去しました';
			
			$transport_route_id = $_GET['transport_route_id'];
			$company_id = $_GET['company_id'];
			$driver_id = $_GET['driver_id'];
			
			if(empty($driver_id)){
				$action = "transport_route/viewRoute";
			}else{
				$date = $_GET['date'];
				$action = "transport_route/viewDriverRouteArea&driver_id=$driver_id&date=$date";
			}
			
			$message2=<<<EOM
	<p>ナビエリアを消去しました<br>
	<br>
	</p>
	<a href="index.php?action=$action&company_id=$company_id&transport_route_id=$transport_route_id">
		ルート詳細へ戻る
	</a>
	&nbsp;|&nbsp;
	<a href="index.php">TOPページへ</a>
EOM;
		break;
	}
	
$smarty->assign('title',$title);
$smarty->assign('message2',$message2);

$smarty->assign("filename","message.html");
$smarty->display("template.html");

?>