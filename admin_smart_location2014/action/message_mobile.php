<?php
//メッセージ画面表示


if($_GET['situation']){
	$situation=sanitizeGet($_GET['situation']);
	}else{
	$situation=$_SESSION['situation'];
	}
	
//シチュエーションでスイッチ
switch($situation){

//ニュース
case news:

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"お知らせ");
	$smarty->assign('meta_description',"お知らせ。");
	
	$title='運営者からのニュース・お知らせ';
	$message2=<<<EOM

EOM;
	break;

	
	
//不正なアクセス
case wrong_access:

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"不正なアクセスです。");

	$title='不正なアクセスです';
	$message2=<<<EOM
	<p>一定の時間がたつと、自動的にログアウトします。<br>
	その場合は、お手数ですが、もう一度ログインしてください。</p>

EOM;
	break;
	

case deleteData:	
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
	

//アラームを消去
case deleteAlarm:
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
case pass:

	$title='パスワードを送信しました';
	$message2='入力されたメールアドレスへ、パスワードを送信しました。<br>
	メールをご確認ください。<br>
	注意：届かない場合は、迷惑メールへ振り分けられている場合もございます。
	';
	break;


//パスワード送信時、メールアドレスがない場合
case no_id:

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
case after_copy_root:

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
case after_edit_root_detail:

	$driver_id = sanitizeGet($_GET['driver_id']);
	$company_id = sanitizeGet($_GET['company_id']);
	$date = sanitizeGet($_GET['date']);
	
//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"ルート詳細の変更が終わりました");

	$title='ルート詳細の変更が終わりました';
	$message2=<<<EOM
	<p>配送先の変更が終わりました。</p>
	<a href="index.php?action=/root_detail/viewRootDetails&company_id=$company_id&driver_id=$driver_id&date=$date">
		戻る
	</a>
	&nbsp;&nbsp;
	<a href="index.php">TOPページへ</a>

EOM;
	break;
			
//配送先を指定した後
case after_edit_destination:
	
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
		
	
	
	
//配送先を指定した後
case after_edit_root:
	
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
case after_driver_customized:
	
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
	

	//ドライバーカスタマイズのアップデート完了
	case after_driverUpdate_customized:
	
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
		case after_mail_customized:
		
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
		case after_mail_customized:
			
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
		case after_error_mail1:
		
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
		case after_error_mail2:
				
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
case after_edit_barnch:

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"営業所情報の変更が終わりました");

	$title='営業所情報の変更が終わりました';
	$message2=<<<EOM
	<p>営業所情報の変更が終わりました。</p>
	<a href="index.php">TOPページへ</a>

EOM;
	break;
		
//ドライバー情報編集後のメッセージ
case after_edit_driver:

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"ドライバー情報の変更が終わりました");

	$title='ドライバー情報の変更が終わりました';
	$message2=<<<EOM
	<p>ドライバー情報の変更が終わりました。</p>
	<a href="index.php">TOPページへ</a>

EOM;
	break;
//モバイル版のみ携帯用の、識別番号送信ができなかった場合
case no_unit_no:

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
case unit_no:

	$title='かんたんログインをするためには、一度ログイン画面でログイン情報を入力して携帯電話情報の送信をしてください。';
	$message2=<<<EOM
	<p>現在、個体識別番号が登録されていないため、かんたんログインができません。<br>
	かんたんログインをするためには、一度ログイン画面でログイン情報を入力して携帯電話情報の送信をしてください。<br>
	<a href="index.php?action=login">ログイン</a><br>
	<br>
	
	個体識別番号が送信できない場合、ご使用の携帯のマニュアルをご参照ください。</p>
	

EOM;
	
	break;


//モバイル版のみ
case owner:

//携帯用の、運営者表示
	$smarty->assign('page_title',"運営会社");
	$title='運営会社';
	
	$message2 = <<<EOM
	<p>「タクシー検索　たくる」は神奈川県横浜市にあるホームページ作成・Webシステム開発の株式会社オンラインコンサルタントが運営しています。<br>
	【連絡先】<br>
	TEL:045-306-9506<br>
	FAX:03-6862-5814<br>
	住所:横浜市神奈川区鶴屋町2-21-1　ダイヤビル5F<br>
	メールアドレス:<a href="mailto:takuru@onlineconsultant.jp">web@onlineconsultant.jp</a><br>
	<a href="http://onlineconsultant.jp/m/">横浜のホームページ作成会社　オンラインコンサルタントモバイルサイト</a><br>
	
	<a href="http://onlineconsultant.jp">横浜のホームページ作成会社　オンラインコンサルタント　PCサイト</a><br>
	<a href="index.php?action=message_mobile&situation=tokusyoho">特定商取引法に基づく表示</a><br>
	</p>
	
EOM;
	
	break;

	
//特定商取引法に基づいた表示
case tokusyoho:

	$smarty->assign('page_title',"特定商取引法に基づく表示");
	$title='特定商取引法規に基づく通信販売の表示';
	$message2=<<<EOM
	<div class="item_name"><b>販売店名</b></div>
	<p>株式会社オンラインコンサルタント</p>
	<div class="item_name"><b>サイト名</b></div>
	<p>タクシー検索　たくる</p>
	<div class="item_name"><b>代表者</b></div>
	<p>後藤　暁子</p>
	<div class="item_name"><b>所在地</b></div>
	<p>横浜市神奈川区鶴屋町2-21-1　ダイヤビル5F</p>
	<div class="item_name"><b>電話番号</b></div>
	<p>045-306-9506</p>
	<div class="item_name"><b>E-mailアドレス</b></div>
	<p>takuru@onlineconsultant.jp</p>
	<div class="item_name"><b>販売価格および商品内容</b></div>
	<p><a href="index.php?action=message_mobile&situation=howtoUse">利用方法</a>のページに掲載</p> 
	<div class="item_name"><b>代金の支払い時期</b></div>
	<p>申込み時</p>
	<div class="item_name"><b>商品の引渡し時期</b></div>
	<p>申込内容確認後</p>
	<div class="item_name"><b>申し込み方法</b></div>
	<p><a href="index.php?action=putin">登録フォーム</a>からの申し込み</p>
	<div class="item_name"><b>返品条件</b></div>
	<p>料金のお支払後、営業日10日以内にお申し込みの場合のみ、受け付けます。振り込み手数料は、お客様のご負担となります。
	</p> 
	<div class="item_name"><b>商品間違い</b></div>
	<p>商品の性格上、商品間違いはございません。</p> 
	
	
EOM;
	
	break;
	
	
case register_campaign:

	$smarty->assign('page_title',"タクシー会社　新規登録キャンペーン");
	$title='新規登録キャンペーン';
	$message2=<<<EOM
	<p>
	「たくる」ではシステム刷新にともない、新規登録キャンペーンを実施中です。<br>
	キャンペーンの概要は次の通りです。</p>
	<ul>
		<li>新規登録120社まで、<font color="#ff0000"><b>通常年額6000円（月額500円）を、1年間無料！</b></font></li>
		<li>登録申請日から、1年間無料です。</li>
		<li>課金が始まる前には、メールでご連絡をいたします。ご返答がない場合、情報が削除されるだけです。こちらから予告もなくご請求するということはありません。</li>
		<li>キャンペーン終了後は、次のような料金体系です。<br> 
		登録1社　年額　6000円（月額500円）（何営業所でも可能）<br>
		登録ドライバー2名以上から、1名　年額　4200円（月額　350円）<br>
		</li>
	</ul>
	</p>
	<div><a href="index.php?action=putin">ご登録はこちらから</a></div>
	<div>お問い合わせ　TEL：045-306-9506　株式会社オンラインコンサルタント　たくる係まで</div>
EOM;
	
	
	break;

case after_regist:

	$title='登録のご依頼、有難うございました。';
	$message2=<<<EOM
	<p>
	ご登録のご依頼、誠に有難うございました。<br>
	弊社にて、審査の上、掲載をいたします。<br>
	個人タクシーの方は事業者運転証をFaxで03-6862-5814までお送りください。<br> 
	Fax・メールどちらの場合も必ず申し込みと同じ氏名を添えて提出してください。</p>
	<div><a href="index.php">TOPページへ</a></div>
	<div>お問い合わせ　TEL：045-306-9506</div>
EOM;
	
	
	break;
	
case after_regist_user:

	$title='ユーザー登録、有難うございました。';
	$message2=<<<EOM
	<p>
	ご登録のご依頼、誠に有難うございました。<br>
	お客さまのメールアドレスの確認のため、先ほど入力されたメールアドレスへメールをお送りしました。<br>
	メール文中にありますリンクをクリックすると、ユーザー登録が完了します。<br> 
	メールが届かない場合は、ドメインで受信拒否をしていないか、ご確認ください。onlineconsultant.jpからのメールを受信する必要があります。<br>
	または入力されたメールアドレスが間違っている場合もございますので、その場合はお手数ですが
	再度ご登録ください。</p>
	<div><a href="index.php">TOPページへ</a></div>
EOM;
	
	
	break;	
	
case after_put_new_company:
	$title='会員登録ありがとうございました。';
	$message2=<<<EOM
	<div>Smart動態管理を使う手順</div>
	<div class="new_features_list">
		<ul>
			<li><div>会社情報編集から管理したいドライバーを登録しましょう。</div>
				<div><a href="?action=putDriver&company_id=$u_id">管理したいドライバーを追加する</a></div></li>
			<li><div>登録したドライバーを管理するためには、登録したドライバーにGoogle Play よりSmart動態管理をインストールさせてください。</div>
				<div><a href="https://play.google.com/store/apps/details?id=smart.location" target="_blank">Smart動態管理 Androidアプリ</a></div></li>
		</ul>
	<div>月間たった950円で、車両の管理が楽になり、ドライバーへの連絡の手間も減り、日々運行するルートを管理することができます。</div>
	<div class="recommend_way">※管理画面はパソコンから見ることをオススメします。</div>
	<div><a href="index.php">TOPページへ</a></div>
EOM;
	break;
	
case after_edit:
	$title='情報の編集が終わりました。';
	$message2=<<<EOM
	
	<div><a href="index.php">TOPページへ</a></div>
EOM;
	break;

//ドライバーステータス情報編集後のメッセージ
case after_edit_driver_record:

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"ドライバーステータス情報の変更が終わりました");
	
	$driver_id = sanitizeGet($_GET['driver_id']);
	$company_id = sanitizeGet($_GET['company_id']);

	$title='ドライバーステータス情報の変更が終わりました';
	$message2=<<<EOM
	<p>ドライバー情報の変更が終わりました。</p>
	<a href="index.php?action=driver_record_map&driver_id=$driver_id&company_id=$company_id">
	ドライバー業務日誌MAPへ</a>&nbsp;|&nbsp;	
	<a href="index.php">TOPページへ</a>

EOM;
	break;	

case after_worktime_record:
	$title='作業時間の編集が終わりました。';
	$driver_id = sanitizeGet($_GET['driver_id']);
	$company_id = sanitizeGet($_GET['company_id']);
	
	$message2=<<<EOM
	<p>作業時間の変更が終わりました。</p>

	<div>
	<a href="index.php?action=worktime&driver_id=$driver_id&company_id=$company_id">
	作業履歴へ戻る</a>
	&nbsp;|&nbsp;	
	<a href="index.php">TOPページへ</a></div>
EOM;
	break;	
	
case after_day_report:
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

case after_target_edit:
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
	
	
case privacy:

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

case fail_login:

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


//遊び方
case howtoUse:

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"利用方法と注意");

	$title='たくるの利用方法';
	$message2=<<<EOM
	<h2>利用のしかた</h2>
	<ol>
	<li>「たくる」ではワンクリックで携帯やiPhone,Android携帯から、近くのタクシー営業所・空車を検索することができます。ただ単に現在いる位置から近くの営業所を検索したい場合は、
	<a href="index.php">TOPページ</a>から、現在地から近いタクシーを検索というリンクをクリックしてください。パソコンからも操作可能ですが、下記の
	"パソコンでの現在位置情報取得について"の項目をご覧ください。</li>
	<li>空車検索については、空車検索サービスを利用されているタクシー会社様、個人タクシーしか検索できません</li>
	<li>Android携帯でご利用の場合は、設定→無線ネットワークを使用およびGPS機能を使用をONにしてください。</li>
	<li>住所から営業所を検索したい場合は、
		<a href="index.php?action=prefecture">住所から検索</a>から、探したい住所をクリックしてください。登録がない住所は表示されていません。</li>
	<li>住所とサービスから検索したい場合は、1に記載している、「現在地から近いタクシーを検索」してから、サービスを絞りこむか、<a href="index.php?action=searchByServiceForm"> 
	住所とサービスからタクシー検索</a>から
	検索してください。</li>
		</ol>
	<h2>表示される情報について</h2>
	<ol>
	<li>「たくる」のサービスは、タクシー情報を検索できたり、タクシー情報を掲載できるサイトです。掲載については、運営会社が公知の情報から収集したものを掲載している場合と
	掲載の申し込みがあったものの2通りが掲載されています。</li>
	<li>タクシーの情報を閲覧・検索する場合は、一切料金がかかることはありません。</li>
	<li>厳密な審査をした情報や、常に最新情報が掲載されているわけではないことに、注意し、ご自身の責任において利用してください。</li>
	<li>現在地の収集については、携帯電話各社が提供している簡易な位置情報サービスを利用しています。必ずしも正確ではありません。</li>
	<li>位置情報をもとにした検索サービスのため、同じ会社でも、営業所単位で情報が表示されます。</li>
	<li>登録は、法人・個人関係なく登録ができます。登録をご希望の方は<a href="index.php?action=putin">タクシー会社専用・情報登録依頼フォーム</a>から
	ご登録ください。登録料は無料です。</li>
	<li><a href="index.php?action=taxi_merit">タクシー会社・個人タクシー様向けの資料はこちら</a>をご覧ください。</li>
	<li>自社で登録した情報については、登録申し込みの後、ログインをすると自社の情報が編集できます。</li>
	<li>ケアドライバー・福祉車両などのサービス名の定義については、<a href="index.php?action=services_index">タクシーサービス名の定義</a>をご覧ください。</li>
	<li>自社が登録したわけでない場合、現在掲載されている情報を削除・変更したい場合は、運営会社までお知らせください。<br>
	株式会社オンラインコンサルタント　たくる運営局<br>
	TEL:045-306-9506<br>
	メールアドレス:<a href="mailto:web@onlineconsultant.jp">web@onlineconsultant.jp</a><br>

	<a href="http://onlineconsultant.jp/m/">横浜のホームページ作成・Webシステム会社　オンラインコンサルタントモバイルサイト</a><br>
	
	<a href="http://onlineconsultant.jp">横浜のホームページ・Webシステム作成会社　オンラインコンサルタント　PCサイト</a><br>
	</li>
	</ol>
	<h2>掲載のご依頼について（タクシー会社様、個人タクシー様へ）<a name="#fortaxi">&nbsp;</a></h2>
	<ol>
	<li><a href="index.php?action=taxi_registration">タクシー会社・タクシードライバーさんむけ　情報掲載依頼について</a>をご覧ください。</li>
	<li>掲載のご依頼は、<a href="index.php?action=putin">登録フォーム</a>から行ってください。</li>
	</ol>
	<h2>パソコンでの現在位置情報取得について</h2> 	
	<ol>
	 <li>パソコンでの現在位置情報取得は、Webブラウザの機能に依存しています。ご利用のブラウザが、位置情報サービスに対応している必要があります。<br> 
	 FirefoxやChromeなどのブラウザか、Google Tool Barを利用してください。</li>
	 <li>クリックした後、位置情報を求めるダイアログが表示されたら、「許可」などをクリックしてください。</li>
	 <li>パソコンでの現在位置情報取得は、近隣の無線 LAN アクセスポイントに関する情報や、IPアドレスから住所を割り出します。
	 そのため、正確ではないケースも多く発生します。</li>
	</ol>
	<h2>クチコミの投稿について</h2>
		<ol>
		<li>クチコミをどなたでも投稿できます。事前に<a href="index.php?action=user/putUser">ユーザー登録</a>が必要です。</li>
		<li>クチコミをしたい会社のページの上下にある「クチコミを投稿する」というボタンをクリックして投稿してください。</li>
		<li>現在投稿されている<a href="index.php?action=wordOfMouth/viewWordOfMouths">タクシーのクチコミの一覧</a>はこちら</li>
		<li>クチコミをしたい会社が登録されていない場合、お手数ですが、
		<a href="mailto:web@onlineconsultant.jp">メール</a>で管理者まで、会社名、電話番号、都道府県などの情報をお知らせください。審査の上、登録します。</li>
		<li>当社は、掲載されたクチコミの内容、画像に関していかなる保証もいたしません。
		 お客様のご判断でご利用ください。
		 また、掲載された書き込み内容、画像によって生じた損害（お客様が作成した各種コンテンツによるコンピュータ・ウィルス感染被害なども含みます）や、
		 お客様同士のトラブル等に対し、当社は一切の補償および関与をいたしませんので、予めご了承下さい。</li>
		</ol>
	<h2>詳細な規約</h2>
	<ol>
	<li>詳細な規約は<a href="index.php?action=message_mobile&situation=kiyaku">利用規約</a>をご覧ください。</li>
	</ol>
	
EOM;

	break;

//利用規約
case kiyaku:

//ページのタイトルに、メッセージをアサイン
	$smarty->assign('page_title',"利用規約");

	$title='利用規約';
	$message2=<<<EOM
<div>たくるのサービスをご利用いただくには、下記の規約への同意が必要です。</div>

        <div id="content">
        <div class="small_text">
<h4>第1条（利用規約の適用範囲および変更）</h4>
<ol>
<li>本利用規約は、株式会社オンラインコンサルタント（以下「当社」といいます）が運営するインターネット・サービス（以下「本サービス」といいます）をご利用いただく際の、当社と利用者との間の一切の関係に適用します。</li>

<li>会員とは、所定の方法により会員登録をした者をいい、利用者とは、会員を含む当社が提供する本サービスを利用する全ての者とします。利用者は、本利用規約を遵守のうえ、本サービスを利用するものとします。</li>

<li>当社は、利用者に事前告知をすることなく、本利用規約を変更することができます。本利用規約が変更された場合、変更後の規約が適用されるものとし、利用者は変更後の規約に同意したものとみなされます。</li>
</ol>

<h4>第2条（本サービスの概要と範囲）</h4>

<ol>
<li>本サービスは、会員による情報掲載、利用者における情報収集および閲覧の機会、ならびにそれらの環境を提供することを目的とします。</li>

<li>当社は利用者に事前告知することなく、本サービスに新しいサービスを追加、または変更することができるものとします。</li>

<li>本サービスは、複数の他のウェブサイトのサービスと連携しており、本サイト上にて掲載された内容は、一定条件の下、他のウェブサイトにも公開されます。 </li>
</ol>

<h4>第3条（会員に関する情報の開示と利用）</h4>
<ol>
<li>当社は原則として、会員が会員登録および本サービスの利用において当社に対して開示した個人情報（以下「会員情報」といいます）のうち、本サービス上で開示される会員情報以外の会員情報について、事前の同意なく当社が第三者に対しこれらの会員情報を開示することはありません。ただし、公的機関からの照会および当社が法令によって開示義務を負う場合などはその限りではありません。 </li>

<li>会員は、会員登録の際に当社に開示した情報に変更が生じた場合には、速やかに変更登録を行うものとします。</li>

<li>当社は、会員情報を当社が有益だと判断する情報（当社、広告主および提携先の商品、サービスなどの情報を含む）を提供する目的で利用することができるものとします。</li>

<li>当社は、本サービスの向上および当社のマーケティングなどの目的で会員情報を集計および分析などするものとします。</li>

<li>会員情報は、当社のプライバシーポリシーに従い、当社が管理します。 </li>

<li>会員は、本条に定めるとおりに当社が会員情報を扱い、保有することに同意し、異議を申し立てないこととします。</li>
</ol>

<h4>第4条（パスワードの管理）</h4>

<p>会員は、会員本人の責任の下でパスワードを管理するものとし、パスワードの盗用、不正利用その他の事情により当該会員以外の者が本サービスを利用し会員に損害が生じた場合でも、入力された会員ＩＤおよびパスワードが登録されたものと一致することを所定の方法により確認したとき、当該会員による本サービスの利用があったものとみなし、当社は一切の責任を負わないものとします。</p>


<h4>第5条（退会）</h4>
<ol>
<li>当社は、会員が以下の事由に該当すると判断した場合には、事前に通知することなく、かつ、会員の承諾を得ることなく、当該会員による本サービスの利用停止、当該会員のパスワードなどの変更、または当該会員の会員資格の取消しを行うことができるものとし、当該理由を開示する義務を負わないものとします。<br><br>


（1）	本利用規約に違反した場合<br>
（2）	パスワードを不正に使用した場合<br>

（3）	本サービスによって提供された情報を不正に使用した場合<br>
（4）	当該会員が利用者、第三者および当社に損害を与える危険があると判断した場合<br>
（5）	第7条に定める禁止行為を行った場合<br>
（6）	その他、当社が本サービスの利用について不適当と判断した場合<br><br>
</li>

<li>会員が前項各号に該当するため、当社が前項に定める措置をとった場合において、当該会員に損害が発生したとしても、当社は一切責任を負わないものとします。</li>
</ol>


<h4>第6条（免責事項）</h4>

<ol>
<li>当社は、本サービスによって提供する情報の正確性、完全性などを保証するものではありません。当該情報に起因して利用者および第三者に損害が発生したとしても、当社は一切責任を負わないものとします。 </li>

<li>当社は、利用者に発生した使用機会の逸失、データの滅失、業務の中断、またはあらゆる種類の損害（間接損害、特別損害、付随損害、派生損害、逸失利益を含む）に対して、たとえ当社がかかる損害の可能性を事前に通知されていたとしても、当社は一切責任を負わないものとします。</li>

<li>当社は、利用者が本サービスの利用によって、他の利用者および第三者に対して損害を与え、生じた侵害および紛争に対し、また、利用者自身に損害が生じ、発生した侵害および紛争に対して、一切責任を負わないものとします。</li>

<li>本サービスを受けるためのウェブサイトへの接続は、利用者が自己の費用で行うものとし、当社は一切の費用および責任を負わないものとします。</li>

<li>会員登録の際に当社に開示した情報の変更登録がなされなかったことにより生じた損害について、当社は一切責任を負わないものとします。</li>

</ol>


<h4>第7条（禁止事項）</h4>
<ol>
<li>当社は、利用者が本サービスを利用するにあたり、以下の事由に該当する行為を禁止します。<br><br>
	<ol>
		<li>公序良俗に反する行為 </li>
		<li>犯罪的行為を助長、またはその実行を暗示する行為 </li>
		<li>利用者、第三者および当社の知的財産権、肖像権、パブリシティ権などの正当な権利を侵害する、または侵害のおそれがある行為 </li>
		<li>利用者または第三者の財産、信用、名誉またはプライバシーを侵害する、または侵害のおそれがある行為 </li>
		<li>法令に反する行為</li>
		<li>他の利用者または第三者に不利益または損害を与える行為 </li>
		<li>他の利用者または第三者に対する誹謗中傷 </li>
		<li>選挙の事前運動、選挙運動またはこれらに類似する行為、および公職選挙法に抵触する行為 </li>
		<li>未成年者に対し悪影響があると判断される行為 </li>
		<li>アダルトサイト・出会い系サイトなど年齢制限を有するサイトや、違法・有害サイトなどへのリンク行為</li>
		<li>会員の資格を第三者に譲渡、貸与すること、または第三者と共用する行為</li>
		<li>本サービスの運営を妨げる行為</li>
		<li>当社の信用を毀損する行為 </li>
		<li>本利用規約に違反する行為 </li>
		<li>その他、当社が不適当と判断する行為 </li>
	</ol>
</li>

<li>前項各号に該当する行為によって、当社が何らかの損害を被った場合には、当社が被った損害を、その利用者に賠償するよう請求することができるものとします。</li>

</ol>


<h4>第8条（投稿内容の変更および削除）</h4>
<ol>
<li>当社は、以下の事由がある場合には、会員に承諾を得ることなく投稿、表示される内容を変更および削除することができるものとします。また、当社は投稿者である会員への対応業務に従事した者に係る人件費、その他の費用に相当する金額を含め、当社が被った損害を当該会員に賠償するよう請求することができるものとします。<br><br>

<ol>
	<li>法令または本利用規約などに違反したものである場合</li>
	<li>第7条に定める禁止事項に該当する場合</li>
	<li>当社が直ちに変更または削除する必要があると判断した場合</li>
	<li>他者を誹謗中傷するもの（個人、店舗への誹謗中傷）</li>
	<li>警察などしかるべき当局へご連絡いただく内容のもの</li>
	<li>内容の確認が困難と思われるもの（根拠のないもの）</li>
	<li>他者の権利やプライバシーを侵害するもの</li>
	<li> 店舗などへの個人的なクレーム、及びトラブルや支払いに関するもの</li>
	<li>たくるの趣旨に合わないもの</li>
	<li>有害なプログラム・スクリプト等を含むもの</li>
	<li>営利を目的としたものや個人的な売買・譲渡を持ちかける内容、宣伝行為</li>
</ol>
</li>
</ol>



<h4>第9条（情報の無断使用の禁止）</h4>
<ol>
<li>本サービス上の文章、画像、イラストおよび動画などを含めた情報の無断使用を禁止します。</li>
<li>本サービスに関わる記載（ウェブ上での告知、メールマガジンなどを含む）について、無断で編集、複製または転載することを禁止します。</li>
<li>第１項または第２項に違反し、当社が何らかの損害を被った場合には、利用者は当社に対して損害の賠償をしなければならないものとします。</li>
</ol>


<h4>第10条（投稿内容の利用権）</h4>

<ol>
<li>当社は、会員が本サービス上に登録した情報など（以下「掲載内容」といいます）を保存し、利用することができるものとします。
なお、当社が必要と認めた場合には、投稿者の承諾を得ることなく、保存されている投稿内容の削除または修正を行う場合があります。</li>

<li>会員が本サービス上に投稿した投稿内容の著作権の帰属などについては以下の各号に定めるとおりとします。
<br><br>
（1）	著作権法第18条から第20条までにおいて定義される権利については、会員は当社または当社の指定する第三者に対して行使しないものとします。<br>
（2）	著作権法21条から第28条までにおいて定義される権利については、会員から譲渡され当社に帰属します。<br>
（3）	第１号および第２号の対価として、当社は、会員に対し、何らの支払も要しないものとします。<br>

</li>

<li>当社は、前項に基づく著作権の帰属に基づき、掲載内容の編集、複製、転載などを行い、
当社が有益であると判断した場合にはその内容を利用（出版、映像、翻訳、放送、演劇化などの利用の場合を含む）することができます。
これらを行う場合でも、当社は会員に対し、何らの支払も要しないものとし、また、当社は会員の氏名、会員IDおよびハンドルネーム、その他の会員を表象する名称および情報を表示しないことができるものとします。</li>

<li>掲載内容の利用について、利用者および第三者に損害が発生したとしても、当社は一切責任を負わないものとします。</li>
</ol>


<h4>第11条（本サービスの一時的な中断）</h4>
<p>当社は以下の事由により、利用者に事前に連絡することなく、一時的に本サービスの提供を中断することがあります。本サービスの中断による損害について、当社は一切責任を負わないものとします。</p>

（1）	当社のシステムの保守、点検、修理などを行う場合<br>
（2）	火災、停電または天災地変により本サービスの提供ができなくなった場合 <br>
（3）	運用上または技術上、本サービスの提供ができなくなった場合<br>

（4）	その他、当社が中断をせざるを得ないと判断した場合<br>


<h4>第12条（提供サービスの変更・停止・廃止）</h4>
<ol>
<li>当社は、本サービスの内容を、利用者への事前告知なく変更することができます。</li>

<li>当社は本サービスを、事前告知なしに停止または廃止することができるものとします。</li>

<li>本サービスにおいて、利用者および第三者に損害が発生したとしても、当社は一切責任を負わないものとします。</li>
</ol>

<h4>第13条（国外からの利用）</h4>
<ol>
<li>日本国外から本サービスを利用する場合には、居住国の法律および関係する国際条約に従うものとします。</li>

<li>日本国外からの本サービスの利用者につき、当該利用者の本サービスの利用が居住国の法律および関係する国際条約に違反しないにもかかわらず、
本サービスの利用が、日本法に違反し当該利用者に損害が発生した場合、または、第三者に損害を発生させた場合、当社は一切責任を負わないものとします。</li>
</ol>

<h4>第14条（準拠法）</h4>
<p>本利用規約は、日本法に準拠して解釈されるものとします。</p>


<h4>第15条（管轄裁判所）</h4>
<p>本サービスに関して当社と利用者または第三者間で紛争が生じた際には、横浜地方裁判所を第一審の合意管轄裁判所とします。</p>

<h4>第16条（本利用規約の効力）</h4>
<p>本利用規約は2010年6月14日から発効するものとし、過去の規約に優先して適用されるものとします。</p>

<p>以上</p>

<div>改訂履歴</div>
<div>2010年6月29日　一部改訂　</div>
<div>2011年9月27日　一部改訂　</div>

</div>

EOM;

	break;
	
	}
	
$smarty->assign('title',$title);
$smarty->assign('message2',$message2);

$smarty->assign("filename","message.html");
$smarty->display("template.html");

?>