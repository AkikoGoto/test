<?php
//日本語言語ファイル

//************************************************************
//共通
//************************************************************
//-------------------------------
//外部ファイル読み込み
//-------------------------------

define('COMMON_TEMP_PATH' //共通テンプレートファイルへのパス
	,'templates');
define('COMMON_MESSAGEFILE' //メッセージファイル
	,'message');
//-------------------------------
//タブ
//-------------------------------
define('TABLINK_TOP'	,'トップ');
define('TABLINK_CATADD'	,'カテゴリー追加');	//カテゴリー追加
define('TABLINK_HOWTO'	,'利用方法');	//利用方法
define('TABLINK_INFO'	,'お知らせ');//お知らせ
define('TABLINK_AGREE'	,'利用規約');	//利用規約


//その他共通-------------------------------------


//define('COMMON_VERSION'	,'中間貯蔵運行・動態管理システム ver2.0　Smart動態管理 ver5.4');//バージョン
define('COMMON_VERSION'	, COMMON_SITE_TTL.' '.COMMON_SITE_VERSION.'  Smart動態管理 ver5.4');//バージョン
define('COMMON_SITE_KEYWORD'	,'');//キーワード
define('COMMON_SITE_LANGUAGE'	,'ja');//言語
define('COMMON_IND_POLICY'	,'個人情報保護方針');//個人情報保護方針
define('COMMON_COM_NAME'	,'株式会社オンラインコンサルタント');//株式会社オンラインコンサルタント
define('COMMON_MNG_COMP'	,'運営会社');//運営(管理)会社
define('COMMON_SUGGUESTION'	,'お気づきの点・リクエスト');//お気づきの点・リクエスト
define('COMMON_THIS_SITE','サイト内検索');	//サイト内検索
define('COMMON_DESCRIPTION','');
define('COMMON_NO_ARTCL','該当記事がありません。');//該当記事がありません。
define('COMMON_LOGIN'	,'ログイン');
define('COMMON_LOGOUT'	,'ログアウト');
define('COMMON_USERREG'	,'ユーザー登録');	//ユーザー登録
define('COMMON_USER_ID'	,'ユーザーID');
define('COMMON_PWD'	,'パスワード');
define('COMMON_FOLD'	,'ランキング欄を折りたたむ');
define('COMMON_EXEREG'	,'ユーザー登録');//登録
define('COMMON_USRINFO'	,'ユーザー情報');
define('COMMON_INDISP'	,'*');	//必須
define('COMMON_CANCEL'	,'戻る');
define('COMMON_SUBMIT'	,'送信');//送信
define('COMMON_OF_TAXI'	,'のタクシー');//のタクシー
define('COMMON_DATETIME','日時');
define('COMMON_ADD','追加');
define('VIEW_ALL','一覧');
define('COMMON_HOUR','時');
define('COMMON_MINIT','分');

define('OF_INFO','の電話番号、予約、住所などの情報。');//meta description用
define('TEXT_END','。');//meta description用
define('COMMON_FRGT_PW'			//パスワードを忘れた方
		,'パスワードを忘れた方は<a href="index.php?action=password">こちら</a>');
define('COMMON_YET_REG'			//ユーザ登録がまだの方
		,'ユーザ登録がまだの方は <a href="index.php?action=putin">こちら</a>');
define('COMMON_NOWSEEING' //今このサイトを見ている人
	,'今このサイトを見ている人');
define('CHAR_LIMIT', '文字以下');
define('CHAR_MIN', '文字以上');
define('AGE_UNIT', '才');
define('COMMON_REG_USER', '登録ユーザー');	//登録ユーザー
define('COMMON_UNDEFINE', '不詳');	//不詳
define('COMMON_REPLY', '返信');	//返信
define('COMMON_DETAIL', '詳しく見る');	//詳しく見る
define('COMMON_DELETE', '削除');	//削除
define('COMMON_COPY', 'コピー');	//削除
define('COMMON_DELETE_ICON', '<img src="'.TEMPLATE_URL.'templates/image/icon_delete.gif" alt="消去" title="消去">');	//詳しく見る
define('COMMON_DOWNLOAD_ICON', '<img src="'.TEMPLATE_URL.'templates/image/icon_download.png" alt="ダウンロード" title="ダウンロード">');	//詳しく見る
define('COMMON_DELETE_ICON_MINI', '<img src="'.TEMPLATE_URL.'/templates/image/icon_delete_mini.gif" alt="消去" title="消去">');	//詳しく見る
define('COMMON_AGREE', '賛成');	//賛成
define('COMMON_TOP', '▲ページのTOPへ');	//ページのTOPへ
define('COMMON_RES_ZERO'	,'レスがないスレッド');//レスが０のスレッド
define('COMPELLATION2'	,'さん');//敬称
define('COMMON_LINKTOCOMPANY'	,'http://www.onlineconsultant.jp/');//会社へのリンク
define('HELLO'	,'ようこそ');//あいさつ
define('COMMON_BLOG','<a href="http://ameblo.jp/online-takuru/">たくる ブログ</a>');//ブログ
define('COMMON_CONFIRM_MESSAGE','下記の内容で登録します。よろしいでしょうか？');
define('COMMON_INCORRECT_VALUE','が正しくありません');



//-------------------------------
//検索条件
//-------------------------------
define('COMMON_CAT_ALL'	,'全カテゴリ');//全カテゴリ
define('COMMON_RANK'	,'何位まで');	//何位まで
define('COMMON_RNK_05'	,'5位まで');	// 5位まで
define('COMMON_RNK_10'	,'10位まで');	//10位まで
define('COMMON_RNK_25'	,'25位まで');	//25位まで
define('COMMON_RNK_50'	,'50位まで');	//50位まで
define('COMMON_PERIOD'	,'期間');	//期間
define('COMMON_PRD_THS'	,'今月');	//今月
define('COMMON_PRD_TTL'	,'トータル');		//トータル
define('COMMON_PRD_01'	,'1月');	//1月
define('COMMON_PRD_02'	,'2月');	//2月
define('COMMON_PRD_03'	,'3月');		//3月
define('COMMON_PRD_04'	,'4月');		//4月
define('COMMON_PRD_05'	,'5月');		//5月
define('COMMON_PRD_06'	,'6月');		//6月
define('COMMON_PRD_07'	,'7月');		//7月
define('COMMON_PRD_08'	,'8月');		//8月
define('COMMON_PRD_09'	,'9月');	//9月
define('COMMON_PRD_10'	,'10月');	//10月
define('COMMON_PRD_11'	,'11月');	//11月
define('COMMON_PRD_12'	,'12月');	//12月
define('COMMON_YEAR'	,'年');	//年
define('COMMON_MONTH'	,'月');	//月
define('COMMON_DAY'	,'日');	//月

define('NO_DATA','条件にあうデータがありません。');	//検索結果が0の場合

//-------------------------------
//ボタン
//-------------------------------
define('COMMON_BTN_GSRCH','Google検索');//Google検索
define('COMMON_BTN_SRCH','送信');//送信・検索

//-------------------------------
//データ新規登録欄項目
//-------------------------------
define('COMPANY_INFO','グループ情報');

define('COMMON_CORPORATE','法人');
define('COMMON_INDIVIDUAL','個人');
define('COMMON_IS_COMPANY'	,'法人か個人か');	//会社名
define('COMMON_GROUP_ID'	,'グループID');	//グループID
define('COMMON_COMPANY'	,'会社名あるいは氏名');	//会社名
define('COMMON_PERSONAL_NAME','個人名');
define('COMMON_BUSINESS_HOURS_24','24時間営業');
define('COMMON_BUSINESS_HOURS_NOT24','24時間営業以外の場合はご記入ください。');
define('TEL_NOTICE','※配車室のTELをご記入ください。個人タクシーの場合はドライバーの携帯電話でOKです。');
define('COMMON_BUSINESS_HOURS','営業時間');
define('COMMON_ACCEPT_EDITING','許可');
define('COMMON_BAN_EDITING','許可しない');
define('COMMON_IS_DRIVER_BANNED_EDITIG'	,'ドライバーが業務日報を編集');
define('COMMON_CAR_NUMBER','車両数');
define('COMMON_PICK_UP','迎車料金');
define('COMMON_CREDIT','クレジットカード利用可否');
define('COMMON_CREDIT_OK','クレジットカード可');
define('COMMON_NO_INFO','情報なし');
define('COMMON_OK','OK');
define('COMMON_NG','不可');
define('COMMON_DEBIT','デビットカード利用可否');
define('COMMON_DEBIT_OK','デビットカード可');
define('COMMON_INFO','その他の情報');
define('COMMON_FARE_INFO','料金情報');
define('COMMON_LAT','緯度');
define('COMMON_LONG','経度');
define('COMMON_DECIMAL_BASE','記入する場合は必ず10進法で記入してください。');
define('COMMON_PWD_NOTICE','※4文字以上10文字以下');
define('COMMON_PWD_EDIT_NOTICE','※変更する場合のみ、入力してください。');
define('COMMON_POSTAL','郵便番号');
define('COMMON_PREFECTURE','県名');
define('COMMON_CITY','市名');
define('COMMON_GUN','郡名');
define('COMMON_WARD','区名');
define('PWD_NO_DISPLAY','セキュリティのため、パスワードは確認画面へ表示していません。');
define('ADDRESS_NOTICE','住所はWebに公開されます。位置情報取得のため、なるべく詳しく記入してください。<br>
個人の方で、公開を控えたい場合は番地以下は不要です。');
define('COMMON_TOWN','町村名');
define('COMMON_ADDRESS','番地');
define('COMMON_JYUSYO','住所');
define('COMMON_HQ_JYUSYO','本社住所');
define('COMMON_TEL','電話番号');
define('COMMON_MOBILE_TEL','携帯電話番号');
define('COMMON_FAX','FAX');
define('COMMON_EMAIL','Email');
define('COMMON_DEPARTMENT','部署');
define('COMMON_CONTACT_PERSON_NAME','担当者氏名');
define('COMMON_MAP','地図');

define('COMMON_EMAIL_SHOWN_NOTE','※メールアドレスをWebに公開するかどうかは、登録後自由に選択できます。');
define('WHAT_BILL','請求書払いかどうか');
define('YES','はい');
define('NO','いいえ');
define('COMMON_CONFIRM','確認');


define('COMMON_URL','会社URL');
define('COMMON_MOBILE_EMAIL','携帯Email');
define('COMMON_FROM_WEB','Webからの登録か');
define('COMMON_CONTACT_PERSON','ご担当者様 お名前');
define('COMMON_CONTACT_TEL','ご担当者様 お電話番号');
define('COMMON_CONTACT_NOT_SHOW','※システム運営会社からご連絡させて頂く場合に利用します。');
define('CONTACT_TAKURU','ご不明な点は、お気軽にお電話でご相談ください。TEL045-306-9506');


define('COMMON_NOTE','ご連絡事項');
define('COMMON_CREATE_DATE','データ登録日');
define('COMMON_UPDATE','最終更新日');
define('COMMON_COL_SERVICE','サービス名');
define('COMMON_SERVICE_CHOICE','サービス選択');
define('NOTICE_SERVICE','※必ずひとつ選択してください。情報がない場合は、情報なし<br>');
define('FORM_BACK','戻る場合は、戻るボタンでお戻りください。');
//ドライバー情報
define('COMMON_DRIVER_ADD','ドライバー情報追加');
define('COMMON_LAST_NAME','姓');
define('COMMON_FIRST_NAME','名');
define('COMMON_NAME','氏名');
define('COMMON_FURIGANA','フリガナ');
define('COMMON_EXPERIENCE','運転歴');
define('COMMON_NO_ACCIDENT','無事故違反歴');
define('COMMON_CAR_TYPE','車輌');
define('COMMON_EQUIPMENT','装備');
define('COMMON_BIRTHDAY','生年月日');
define('COMMON_EREA','得意な地域');
define('COMMON_COL_SEX'	,'性別');
define('COMMON_REGIST_NUMBER','登録番号');
define('COMMON_MESSAGE','備考');
define('COMMON_COMMENT','コメント');
define('COMMON_DRIVER_MOBILE_MAIL','ドライバー携帯Email');
define('COMMON_DRIVER_MOBILE','ドライバー携帯番号');
define('DRIVER_NUMBER_ACTUAL','実際のドライバー登録数');
define('REGISTER_MERIT_SUMMARY','・登録・ご利用は<font color="#ff0000">完全無料！</font>登録タクシー会社さん
<font color="#ff0000">続々増加中！</font></span><br>
・貴社のホームページ代わりにも！（PC・携帯・スマートフォン対応です。）<br>
・画期的！<a href="index.php?action=taxi_merit">空車検索サービスと連動した配車支援システム</a>が利用できます。
デジタル無線の代わりに、初期投資なしで利用可能。（<a href="index.php?action=taxi_merit">詳細資料はこちら</a>）');

//ドライバー情報一覧
define('COMMON_DRIVERS','ドライバー情報一覧');
define('COMMON_DRIVERS_ROUTES','ドライバールート一覧');
define('COMMON_DRIVERS_WORKTIMES','ドライバー運転（履歴）日報一覧');
define('COMMON_DRIVER','ドライバー情報');

//閲覧者
define('VIEWER','閲覧ユーザー');
define('VIEWER_INFO','閲覧ユーザー情報');
define('VIEWER_LIST','閲覧ユーザー一覧');
define('VIEWER_NAME','閲覧ユーザー名');
define('VIEWER_LOGIN_ID','閲覧ユーザー用ログインID');
define('VIEWER_PASSWORD','閲覧ユーザー用パスワード');
define('VIEWED_DRIVERS','閲覧できるドライバー');
define('VIEWERS_SETTING','閲覧ユーザーの公開設定');
define('DRIVERS_TO_VIEWERS_SETTING','閲覧ユーザー毎の公開するドライバーの選択');

//サブグループ情報
define('BRANCH','サブグループ');
define('BRANCH_NAME','サブグループ名');
define('PUT_BRANCH_TITLE','サブグループ情報入力');
define('VIEW_BRANCH_TITLE','サブグループ情報');
define('COMMON_BRANCH_ID','サブグループ番号');
define('COMMON_NEAR','現在地から近いサブグループ一覧');
define('COMMON_NEAR_ORDER','近い順番に表示されています。');
define('COMMON_TEL_TAKURU',
'お電話の際はひとこと、「たくる」を見てお電話したとお伝え頂けるとありがたく存じます。');
define('COMMON_SORT','他の条件で絞り込む');
define('COMMON_NARROW','絞り込む');

//絞り込み検索画面
define('REFINE_NOTICE','<font color="#ff0000">※</font>法人と個人両方にチェックをつけると、一致するデータがありません');

//サービスから検索画面
define('GPS_NOTICE','<font color="#ff0000">※</font>位置情報が確認できる携帯・iPhone・アンドロイド・パソコン・iPadのみ対応です。');
define('GPS_NOTICE_ANDROID','<b><font color="#ff0000">※</font>
アンドロイド携帯でご覧の場合は、現在位置情報を取得するために
設定→無線ネットワークを使用およびGPS機能を使用をONにしてください。</b>');
define('GPS_ONCE_NOTICE','サービス検索結果は引き継がれませんので、一度現在地から近いタクシーを検索してから、絞り込み検索を行ってください。');
define('NO_GPS','現在位置情報が取得できませんでした。<br>
このサービスは、位置情報が確認できる携帯・iPhone・パソコン・iPadのみ対応です。<br>
パソコンの場合は、Firefox、Chromeなどのブラウザで閲覧するか、Google Tool Barの導入などで取得できる場合があります。<br>
あるいは、<a href="index.php?action=prefecture">住所から検索</a>で検索してください。');

//-------------------------------
//データ編集画面
//-------------------------------

define('EDIT_DATA','データ編集画面');
define('NEW_DATA','データ新規登録画面');

define('COMMON_ID','ID');
define('COMMON_COL_USERNM'	,'ユーザー名');	//ユーザー名
define('COMMON_COL_CNTRBT'	,'投稿');	//投稿
define('COMMON_COL_POSTDT'	,'投稿日');
define('COMMON_COL_VOTE','投票');
define('COMMON_COL_AGE'	,'年齢');	//年齢
define('COMMON_COL_DATE','日付');	//
define('COMMON_COL_UPDATE','最新更新');	//最新更新
define('COMMON_COL_CMMNTS'	,'投稿');//
define('COMMON_COL_WOMAN'	,'女');//女
define('COMMON_COL_MAN'	,'男');//男
define('COMMON_COL_MAIL','E-mailアドレス');
define('COMMON_COL_USRESSY'	,'紹介文');//紹介文
define('COMMON_COL_ACSCNT'	,'アクセス');//アクセス数
define('COMMON_COL_REPCNT'	,'返信数');//返信数
define('COMMON_COL_ATTRI'	,'属性');//属性　by kajiura
define('COMMON_COL_DETAIL','記事内容');	//記事内容

//データ検証
define('DATE_VERI_NO'	,'がありません。');//ありません。
define('DATE_VERI_NOCNTRBT'	,'送信内容がありません。');//投稿内容がありません。
define('DATE_VERI_MESLO'	,'メッセージが長すぎます。');//メッセージが長すぎます。
define('DATE_VERI_MESSH'	,'メッセージが短すぎます。');//メッセージが短すぎます。
define('DATE_VERI_PIIB'	,'で入力してください。');//入力してください。
define('DATE_VERI_CHAORLE'	,'文字以下で入力してください。');//文字以下で。
define('DATE_VERI_WA'	,'は');//文字以上で。
define('DATE_VERI_CHAORMO'	,'文字以上で入力してください。');//文字以上で。
define('DATE_VERI_HYPHEN' ,'※ハイフンはOK。');//ハイフンはOK。
define('DATE_VERI_TITLELO'	,'が長すぎます。');//タイトルが長すぎます。
define('DATE_VERI_PLINNA'	,'名前を入力してください。');//名前を入力してください。
define('DATE_VERI_PROHITI'	,'禁止の文字列が含まれています。');//タイトルに禁止の文字列が含まれています。
define('DATE_VERI_SIGNCNBU'	,'記号などは使えません。');//記号などは使えません。
define('DATE_VERI_PROHI'	,'禁止の文字列が含まれています。');//禁止の文字列が含まれています。
define('DATE_VERI_CHANA'	,'という文字は');//という文字は
define('DATE_VERI_CNBU'	,'使えません。');//使えません。
define('DATE_VERI_EISUU'	,'半角英数');//
define('DATE_VERI_AND'	,'と');//
define('DATE_VERI_LINE_SYMBOL'	,'-、_');//
define('DATE_VERI_OVER6'	,'6文字以上');//

//最短経路検索時の検証
define('VERI_SAME_ADDRESS'	,'同じ住所を２回以上入力して最短経路を出すことはできません。');//


//データ検証　ユーザーデータ入力時
define('DATE_VERI_EMAILIL'	,'Emailアドレスが不正な値です。');//Emailアドレスが不正な値です。
define('DATE_VERI_NONAME'	,'名前がありません。');//名前がありません。
define('DATE_VERI_NO_NICKNAME'	,'投稿用の名前がありません。');//名前がありません。
define('DATE_VERI_PLEASEPASS'	,'パスワードを入力してください。');//パスワードを入力してください。



//----------------------------------------------------
//メール文面//
// レスがあった場合にメールをする　//
define('MAIL_SITE_NAME'	,'【たくる】');//【PC用】
define('DATE_MAIL_ANNREP'	,'返信のお知らせ');//返信のお知らせ
define('DATE_MAIL_HELLO'	,'');//090522ここまで
define('DATE_MAIL_KEISHO'	,'様');//様
define('DATE_MAIL_THWEARELM'	,'いつもご利用ありがとうございます。たくる運営局です。');//いつもご利用ありがとうございます。たくる運営局です。
define('DATE_MAIL_YOUREPOSTED'	,'あなたが投稿したスレッドに返信がありました。');//あなたが投稿したスレッドに返信がありました。
define('DATE_MAIL_PLEACC'	,'下記のアドレスにアクセスして、内容をご覧ください。');//下記のアドレスにアクセスして、内容をご覧ください。
define('DATE_MAIL_FORPC'	,'【PC用】');//【PC用】
define('DATE_MAIL_EN'	,'');//【PC用URL】
define('DATE_MAIL_FORMOBILES'	,'【モバイル用】');//【モバイル用】
define('DATE_MAIL_IFYOUWANT'	,'レスがついた時に自動でメールを送信する機能をOFFにしたい場合は、サイトへログインし、ユーザー情報画面で変更してください。');//レスがついた時に自動でメールを送信する機能をOFFにしたい場合は、サイトへログインし、ユーザー情報画面で変更してください。
define('DATE_MAIL_TEAMLOVETALK'	,'Smart動態管理より');//Smart動態管理より
define('DATE_MAIL_WEBTEAM'	,'株式会社オンラインコンサルタント');//株式会社オンラインコンサルタント
define('DATE_MAIL_ADD'	,'横浜市神奈川区鶴屋町2-21-1　ダイヤビル5F');//会社住所
define('MAIL_URL'	,'http://doutaikanri.com/'); //TOP画面URL
define('MAIL_TEL'	,'TEL：045-306-9506'); //TEL
define('MAIL_FAX'	,'FAX：03-6862-5814'); //FAX

//----------------------------------------------------
// パスワードを忘れた人へメール送信
define('DATE_MAIL_SUBMITPASS'	,'パスワードの送信');//パスワードの送信
define('DATE_MAIL_WEARELM'	,'Smart動態管理　運営局からです。');//
define('DATE_MAIL_YOUPASS'	,'');//{$u_name}様のパスワードは以下の通りです。
define('DATE_MAIL_YOUPASS2'	,'様のパスワードは以下の通りです。');//{$u_name}様のパスワードは以下の通りです。 続き
define('DATE_MAIL_CHAPASS'	,'下記のURLからログインした後、ユーザー情報編集画面から覚えやすいパスワードへ変更してください。');//下記のURLからログインした後、ユーザー情報編集画面から覚えやすいパスワードへ変更してください。
define('DATE_MAIL_PLDELMAIL'	,'このメールに覚えがない場合はメールを削除してください。');//このメールに覚えがない場合はメールを削除してください。

//----------------------------------------------------
// 仮登録者へメール送信
define('DATE_MAIL_REGCONF'	,'会員登録の確認');//会員登録の確認
define('DATE_MAIL_THANKJOINING'	,'データ登録のお申し込みありがとうございます。');//会員登録ありがとうございます。
define('DATE_MAIL_PLACC'	,'下のリンクにアクセスして仮データ登録を完了してください。');//下のリンクにアクセスして会員登録を完了してください。
define('DATE_MAIL_PLCOPY'	,'リンクが動作していない時は、URLをコピーして、ブラウザのアドレスバーへペーストしてください。');//（リンクが動作していない時は、URLをコピーして、ブラウザのアドレスバーへペーストしてください。）

//----------------------------------------------------
// 管理者へメール送信
define('MAIL_REG_NOTICE' ,'Webから申し込みがありました。');//申込の確認

//----------------------------------------------------
// 承認のメール送信
define('MAIL_NOTIFY_ACTIVATE' ,'ご登録内容を確認しました。');//申込の確認
define('MAIL_ACTIVATE_NOTIFY', 'この度は、「タクシー検索　たくる」にご登録頂き誠にありがとうございます。');
define('MAIL_ACTIVATE_NOTIFY2','お客様の情報が“たくる”に掲載されましたことをお知らせします。');
define('MAIL_ACTIVATE_NOTIFY3','「タクシー検索　たくる」は全ての機能が無料でご利用頂けます。どうぞお気軽にご利用ください。');
define('MAIL_ACTIVATE_NOTIFY4','貴社のサービス、料金、情報掲載などのご編集、ドライバー情報の入力は、下記のリンクからログインして行ってください。');
define('MAIL_LOGIN_SCREEN','【タクシー会社、個人タクシー様専用ログイン画面】');
define('MAIL_NO_FROM_MOBILE_EDIT','なお、サブグループ情報の追加・変更、グループ情報の変更は、携帯電話からは行わないようにお願い申し上げます。');
define('MANUAL_INFO' ,"空車検索サービスのマニュアルは、下記のURLにございます。");
define('MANUAL_URL', 'http://takuru.jp/files/takuru_manual.pdf');
define('IF_ANY_CONTACT', 'ご不明な点がありましたら、どうぞご遠慮なくお問合せください。');
define('MAIL_CONTACT_TEL', '(TEL：045-306-9506 株式会社オンラインコンサルタント内　たくる事務局まで）');
define('MAIL_REGARDS', '今後とも何卒よろしくお願いいたします。');

//----------------------------------------------------

//----------------------------------------------------
// 会社新規登録時の管理者へ案内メール送信
define('MAIL_NEW_COMPANY_SUBJECT' ,'Smart動態管理へのご登録有難うございます');
define('MAIL_NEW_COMPANY_THANKS' ,'Smart動態管理へのご登録誠に有難うございました。');
define('MAIL_NEW_COMPANY_LOGIN', <<<EOL
Smart動態管理の管理画面のログインURLは下記になります。
ご登録のメールアドレスがログインIDとなります。
ご登録のパスワードを用いてログインを行ってください。
EOL
);
define('MAIL_NEW_COMPANY_PASSWORD' ,'パスワードを忘れてしまった場合は、下記からパスワードを再発行してください。');

define('MAIL_NEW_COMPANY_GROUPID' ,'他の参加者があなたの作ったグループに参加する場合は、下記のグループIDを利用してください。');
define('MAIL_NEW_COMPANY_SMARTPHONE_APP' ,<<<EOL
まだアプリをダウンロードでない場合や、他の方の用のアプリのダウンロードは下記からお願いします。

【Android版】
https://play.google.com/store/apps/details?id=smart.location.admin
無料お試し　7日間　月額　950円

【iPhone版】
https://itunes.apple.com/jp/app/smart_location_admin/id827594007?l=ja&ls=1&mt=8

月額　2000円
EOL
);
define('MAIL_NEW_COMPANY_SUPPORT', <<<EOL
【マニュアル（PDF）】
豊富な機能をご利用頂くために、マニュアルをご一読頂くことをお勧めします。
http://doutaikanri.com/files/manual.pdf

【サポート窓口】
株式会社オンラインコンサルタント　Smart動態管理　サポート担当

平日　10時～19時
TEL：045-306-9506
メールアドレス：web@onlineconsultant.jp
EOL
);

define('MAIL_NEW_COMPANY_NOTICE', <<<EOL
なお、一定期間アプリのご利用がない管理画面のご登録については、削除させて頂き
ます。
あらかじめ、ご了承ください。
EOL
);

//----------------------------------------------------

/*------------------- コンクリート用 ---------------------- */
//demand_time.php
define('DEMAND_TIME_TITLE' , '請求時間の編集');
define('OIL_DATA' , '軽油(3)入力');
define('OIL_DATA_INSERT' , '軽油(3)のデータ入力');
define('OIL_REPLENISHMENT' , '軽油補給量');
define('BEFORE_REPAIR_TIME' , '修正前の');
define('AFTER_REPAIR_TIME' , '修正後の');
define('DAILY_WAGE_PDF' , '賃金用日報PDF');
define('DEMAND_DISPLAY' , '請求用で出力');

define('OFFICE_START' , '営業所を出発');
define('SCENE_START' , '現場へ出発中');
define('FROM_SCENE_START' , '現場から出発中');
define('OFFICE_RETURN' , '営業所へ戻り中');
define('LUNCH' , '昼食');
define('CONTACT_HOLD' , '連絡待ち');
define('CAR_WASH_HOLD' , '洗車待ち');
define('CAR_WASH' , '洗車');
define('FEED' , '給油');
define('COPY' , 'コピー');
define('OTHER' , 'その他');


//----------------------------------------------------
// エラーメッセージ表示
//passward.php
define('DATE_ERROR_PASS'	,'Location:index.php?action=message_mobile&situation=pass');//パスワード送信メール確認画面
define('DATE_ERROR_NOID'	,'Location:index.php?action=message_mobile&situation=no_id');//このメールアドレスでは登録がありません。

//login.php
define('DATE_ERROR_FAILLOGIN'	,'Location:index.php?action=message_mobile&situation=fail_login');//アカウントとパスワードが違います。

//registUser.php
define('DATE_ERROR_PREUSER'	,'Location:index.php?action=message&situation=pre_user');//ユーザーの仮登録が終了しました。
define('DATE_ERROR_BEENUSED'	,'そのメールアドレス（ID）は既に使用されています。');//そのIDは既に使用されています。
define('DATE_ERROR_SAME_COMPANY'	,'そのグループは既に登録されています。');//その会社は既に登録されています。

//-------------------------------
//個別ページ
//-------------------------------

/* top.html TOPページ*/

define('TOP_NEW_POST'	,'＜新着投稿・レス　新着順からから5件＞');//＜新着投稿・レス　新着順からから5件＞
define('TOP_MORE'	,'続き');//続き
define('TOP_NODATA'	,'データがありません。');//投稿がまったくない場合のメッセージ
define('TOP_TITLE' ,//タイトル
	'新着更新スレッド一覧');
define('TOP_NEW_EXPLAIN_1' ,//○時間以内の投稿前半
	'');
define('TOP_NEW_EXPLAIN_2' ,//○時間以内の投稿後半
	'時間内の投稿・レスがついたスレッドに、NEWマークがついています。');

/* all.html　全投稿記事*/
define('ALL_TITLE','全投稿一覧');

/* putin.html　投稿画面*/
define('BBS_TITLE','新規にデータを登録します。');
define('PUTIN_TITLE','グループ登録フォーム');
define('REGISTER_CAMPAIGN','<font color="#ff0000">※掲載120社まで無料キャンペーン中！</font><br>
<a href="index.php?action=message_mobile&situation=register_campaign">キャンペーン詳細</a>
');
define('REGISTER_CONDITION1','※掲載依頼後に、個人タクシーの方は事業者運転証を、
Faxで03-6862-5814までお送りいただくか、写真を撮って<a href="mailto:takuru@onlineconsultant.jp">takuru@onlineconsultant.jp</a>へお送りください。（写メール可）');
define('REGISTER_CONDITION2','※法人・個人ともに、内容を審査の後登録となります。');
define('REGISTER_CONDITION3','※登録お申し込みの前に、
<a href="index.php?action=message_mobile&situation=kiyaku">利用規約</a>
をよくお読みになり、同意の後に登録の申し込みを行ってください。');
define('REGISTER_EXPLANATION','下記に内容を入力し、最後に送信をクリックしてください。<br>
<font color="#FF0000">*</font>に関しては、必ず入力してください。');
define('BBS_CHOOSE','選べない場合は「その他」を選択してください。');
define('REGISTER_MERIT','<a href="'.TEMPLATE_URL.'/templates/files/taxi_flyer.pdf">登録のメリット詳細（リンク先PDF）</a>');
define('REGISTER_DETAIL','<a href="index.php?action=taxi_registration">掲載料金など詳細</a>');
define('DRIVER_NAME','ドライバー氏名');
define('DRIVER_NUMBER','ドライバー希望登録人数');
define('MAN_NUMBER','名');
define('DRIVER_NAME_NOTICE','※法人タクシーのドライバーが登録する場合のみ記入');
define('DRIVER_NUMBER_NOTICE','※2名以上のドライバーの登録をする場合のみ記入');
define('EMAIL_NOTICE','@onlineconsultant.jpからのメールを受け取ることができるようにしてください。');
define('EMAIL_DISPLAY','メールアドレスをWeb上に表示させるかどうか');

/*ログイン画面*/
define('FOR_TAXI_COMPANY','グループ用');
define('FOR_TAXI_COMPANY_REGIST','登録がまだの方はこちら');
define('TAKURU_MERIT','たくるに登録すると、次のようなメリットがあります。<br>
<div>&#149;たくるの検索画面に表示される</div>
<div>&#149;たくるの管理画面から、自社の情報・営業所情報・PR・ドライバー情報が掲載可能</div>'
);


/*bycat.html カテゴリ別記事 */
define('THREAD_LIST' ,//タイトル
	'スレッド一覧');

/*confirm_res.html 返信確認画面 */
define('COMFIRM_RES_TITLE' ,//タイトル
	'下記の内容でよろしければ、下の送信ボタンを押してください。');

/*contents_bypoll.html コンテンツ投票数順記事 */
define('CONTENT_TITLE1' ,//タイトル
	'スレッド');
define('CONTENT_TITLE2' ,//タイトル
	'の質問と回答 投票順');
define('CONTENT_POST_ORDER' ,//投稿された順番にみる
	'投稿された順番にみる▼');
define('FIRST_QUESTION' ,//最初の質問
	'さいしょの質問');
define('ACCESS_NUMBER1' ,//アクセス数
	'このスレッドが参照されるのは');
define('ACCESS_NUMBER2' ,//アクセス数
	'回目です。');
define('NO_REPLY' ,//現在回答がありません。
	'現在回答がありません。');

/*contents.html コンテンツ投票順記事 */
define('CONTENT_TIME_TITLE2' ,//タイトル
	'の質問と回答');
define('CONTENT_POLL_ORDER' ,//投票の多い順番にみる
	'投票数が多い順にみる▼');

/*error.html エラー画面 */
define('ERROR_TITLE' ,//タイトル
	'エラーがあります。');

/*login.html ログイン画面 */
define('AUTO_LOGIN' ,//タイトル
	'次回から自動でログインする');

/*login.html パスワード発行画面 */
define('PASS_TITLE' ,//タイトル
	'パスワード再発行');

define('PASS_EXPLAIN' ,//タイトル
	'登録時に使用したメールアドレスを入力し、送信ボタンを押してください。<br>
新しいパスワードがそのメールアドレス宛てに送信されますので、新しいパスワードを使ってログインしてください。');

/*put_cat.html カテゴリ追加画面 */
define('PUT_CAT_TITLE' ,//タイトル
	'カテゴリー追加');

/*regisitUser.html カテゴリ追加画面 */
define('REGISTUSER_NOTICE' ,//注意がき
	'注意：ID（メールアドレス）、パスワード以外の情報はネット上で公開されます。');

/*res.html 返信画面 */
define('RES_TITLE1' ,//タイトル
	'');
define('RES_TITLE2' ,//タイトル
	'に返信します。');

/*search.html 検索結果画面 */
define('SEARCH_TITLE' ,//タイトル
	'検索結果');
define('NO_CONDITIONS',//検索条件を指定してください
	'検索条件を指定してください。');

/*thread.html 新着スレッド一覧画面 */
define('THREAD_TITLE' ,//タイトル
	'新着スレッド一覧');

/*u_info_update.html ユーザー情報更新画面 */
define('U_INFO_UPDATE_TITLE' ,//タイトル
	'さんのユーザー情報の変更');
define('MEMBERSHIP_NUMBER' ,//会員番号
	'会員番号');
define('OLD_INFORMATION' ,//現在の情報
	'現在の情報');
define('MAIL_SETTING' ,//メール送信設定
	'メール送信設定');
define('U_MAIL' ,//自分が投稿したスレッドに返信があった場合
	'自分が投稿したスレッドに返信があった場合');
define('U_MAIL_Y' ,//通知する
	'通知する');
define('U_MAIL_N' ,//通知しない
	'通知しない');
define('U_MAIL_MAGAZINE' ,//ラブマスターからの各種お知らせを
	'ラブマスターからの各種お知らせを');
define('U_MAGAZINE_Y' ,//受け取る
	'受け取る');
define('U_MAGAZINE_N' ,//受け取らない
	'受け取らない');
define('TO_CHANGE_PWD', //パスワードは変更したい時のみ入力してください
	'パスワードは変更したい時のみ入力してください');

/*u_info.html ユーザー情報画面 */
define('TURNOUT' ,//投票数
	'投票数（ポイント）');
define('U_INFO_AC' ,//投票数
	'ユーザー情報へのアクセス数');
define('U_INFO_EDIT' ,//ユーザー情報を編集する
	'グループ情報編集');
define('_NOTICE' ,//住所情報を編集の注意
	'※自社の情報・サブググループ情報を編集する場合は、必ずパソコンから操作してください。住所情報が正確に更新されません。<br>（新規登録時は携帯からでOKです。）');

define('U_CURRENT_POST' ,//今までの投稿
	'さんの今までの投稿');
define('U_INFO_OF' ,//さんの
	'さんの');

/*ログイン画面*/
define('LOGIN_SUCCESS','ログインしました。');

/* レスがないスレッド*/
 define('RES_ZERO_TITLE' ,//レスゼロスレッドタイトル
	'レスがないスレッド');

/* 会社ユーザー登録直後のメッセージ*/
define('PRE_CONFIRM_TITLE' ,//登録完了画面
	'メールアドレス確認完了');
define('PRE_CONFIRM_MESSAGE' ,//登録完了画面メッセージ
	'メールアドレス確認が完了しました。<br>
	登録には、この後弊社で審査を行ってから登録をいたします。<br>
	審査後は、登録されたメールアドレスにご連絡をします。<br>
	個人タクシーの方は事業者運転証をFaxで03-6862-5814までお送りください。');
define('PRE_CONFIRM_MESSAGE_ERROR' ,//登録完了画面メッセージ
	'このURLは無効です。');

/* 全登録会社リスト*/
define('ALL_COMPANY' ,//全登録会社リスト
	'全登録会社リスト');

/*サービス内容インデックス画面*/
define('SERVICE_DEFINITION',
	'タクシーサービス名の意味について');


/*サービス内容説明画面*/
define('NOTICE_SERVICE_INFO',
	'注：この説明は、「タクシー検索　たくる」の中での定義です。');

/* 管理画面*/
define('ADMIN_ACTIVATION' ,//仮登録データ承認
	'仮登録データ承認');

//最短経路検索API
//define('SEARCH_SHORTEST_ROOT_API', 'http://192.168.1.42:8080/tsp');
//define('SEARCH_SHORTEST_ROOT_API', 'http://153.120.74.240:8080/tsp');
define('SEARCH_SHORTEST_ROOT_API', 'http://tsp:8080/tsp'); //本番
//define('SEARCH_SHORTEST_ROOT_API', 'http://133.242.203.150:8080/tsp'); //本番
//define('SEARCH_SHORTEST_ROOT_API', 'http://192.168.1.101:8888/tsp');	// local

//ルート検索API
define('SEARCH_ROOT_API', 'http://graphhopper:8989/route?');	// 本番
//define('SEARCH_ROOT_API', 'http://192.168.1.101:8989/route?');	// local

//GEOコーディングのためのJavascript
define('GOOGLE_MAP','https://maps.google.com/maps/api/js?sensor=true');
define('OPEN_LAYERS','https://openlayers.org/api/OpenLayers.js');
//define('MAPBOX_JS','https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.js');
define('MAPBOX_JS', 'https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.js');
define('MAPBOX_CSS', 'https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.css');
define('MAPBOX_GL_JS', 'https://api.mapbox.com/mapbox-gl-js/v0.33.1/mapbox-gl.js');
define('MAPBOX_GL_CSS', 'https://api.mapbox.com/mapbox-gl-js/v0.33.1/mapbox-gl.css');
define('MAPBOX_GL_STYLE_ID', 'mapbox://styles/onlineconsultant/cj07xj7gd000p2spl15ijo2k8');
define('MAPBOX_GL_ACCESS_TOKEN','pk.eyJ1Ijoib25saW5lY29uc3VsdGFudCIsImEiOiJ0NXNSdE1VIn0.48aKT-tYUwPSibdAXP_NAQ');

define('GEOCODING_JS', TEMPLATE_URL.'/templates/js/geocoding_ver3.js');
define('MULTI_GEOCODING_JS', TEMPLATE_URL.'/templates/js/multi_geocoding_ver3.js');
define('SHORTEST_ROOT_JS', TEMPLATE_URL.'/templates/js/shortest_root.js');
define('ROOT_ANIMATION_JS', TEMPLATE_URL.'/templates/js/root_animation.js');
define('G_MAP_ROOT_JS', TEMPLATE_URL.'/templates/js/g_map.js');
define('ROOT_EDIT_GEOCODING_JS', TEMPLATE_URL.'/templates/js/root_edit_geocoding.js');
define('GEOCODING_JS_ADMIN', TEMPLATE_URL.'/templates/js/geocoding_ver3.js');
define('REVERSE_GEOCODING_JS',TEMPLATE_URL.'/templates/js/reverse_geocoding.js');
define('VACANCY_GEOCODING_JS',TEMPLATE_URL.'/templates/js/vacancy_geocoding.js'); //空車検索用
define('DESTINATION_EDIT_GEOCODING_JS', TEMPLATE_URL.'templates/js/destination_edit_geocoding_ver3.js'); //配送先変更用
define('DATA_TIME_INTERVAL','時間以内に更新されたデータが表示されています。
<br>約10Km県内での検索結果です。');

define('REVERSE_GEOCODING_NOTICE','現在地住所は大まかなもので、必ずしも正確なものではありません。');
define('PRESENT_LOCATION','現在地');
define('PRESENT_LOCATION_MAP','現在地地図へ');

/*ドライバーステータスアップデート画面*/
define('ACTION_1','作業中');
define('ACTION_2','休憩中');
define('ACTION_3','移動中');
define('ACTION_4','作業待ち');
define('ACTION_STATUS','作業ステータス');
define('DEFAULT_TIME_INTERVAL','120000');
define('DEFAULT_DISTANCE_INTERVAL','50');

define('VACANCY','空車');	//ステータス1
define('BOARD','積込済');	//ステータス2
define('DRIVER_OFF','到着'); //ステータス3
define('DRIVER_RESERVED','休憩'); //ステータス4
define('DRIVER_OTHER','その他'); //ステータス5
define('DRIVER_STATUS_UPDATE','ドライバー用メニュー画面');
define('CAR_ASSIGN_TEL','配車用電話番号');
define('COMMON_NEAR_VACANCY','現在地から近い空車一覧');
define('COMMON_NEAR_UPDATE','更新時刻');
define('ASSIGNED_BRANCH','所属サブグループ');
define('IS_BRANCH_MANAGER','サブグループの管理者であるか');
define('BRANCH_MANAGER','サブグループ管理者');
define('DRIVER_LOGIN','ドライバー専用');
define('DRIVER_ACCOUNT','ドライバー用ログインID');
define('DRIVER_PASSWD','ドライバー用パスワード');
define('DRIVER_MENU','ドライバー用メニュー画面');
define('DRIVER_SALES','売上');
define('DRIVER_RECORD','記録');
define('DRIVER_COMMENT','コメント');
define('NOTICE_INT','（半角整数で入力）');
define('ERROR_INT','は半角整数で入力してください。');
define('ERROR_FLOAT','は半角小数で入力してください。');
define('NOTICE_MEMO','（60文字以内）');
define('DRIVER_RECORDS','業務日誌');
define('DRIVER_RECORDS_MAP','業務日誌マップ');
define('ABBREVIATE_ALL_DRIVER_RECORD_MAP', 'DAV');
define('ALL_DRIVER_RECORD_MAP', 'Dynamic Analytics View');
define('YEN','円');
define('DRIVER_STATUS','ステータス');
define('DRIVER_MAP','ドライバーマップ');
define('REALTIME_MAP','リアルタイムマップ');
define('REALTIME_DRIVER_MAP','リアルタイムドライバーマップ');
define('ALL_JAPAN_DRIVER_MAP','全国空車ドライバーマップ');
define('PUBLIC_MAP','パブリックマップ');
define('NO_REGIST','登録しない');
define('SPEED','速度');
define('SHARE','共有');
define('ACCURACY', '精度');

/*ドライバー業務日誌閲覧画面*/
define('LIMIT_DRIVER_RECORDS','最新1000件の記録を表示しています。');
define('RECTANGLE','■');

define('DATA_VERI_ACCOUNT','そのアカウントは既に使用されています。');

/*新規グループ登録用ボタンタイトル*/
define('NEW_REGISTER_COMPANY','新規グループ登録');


/*ログイン画面の注意書き*/
define('DRIVER_LOGIN_LINK','<a href="index.php?action=driverLogin">ドライバー用ログイン</a>');
define('NOTICE_COMPANY_LOGIN','このログインは、グループ専用です。ドライバーログインは、
<a href="index.php?action=driverLogin">こちら</a>から。');

/*ドライバー一覧画面*/
define('DRIVER_INFO_EDIT' ,//ユーザー情報を編集する
	'ドライバー情報を編集する');

/*ドライバー確認カスタマイズ*/
define('NUMBER','番号');
define('DRIVER_CUSTIMIZE', 'アラーム新規作成');
define('DRIVER_CUSTIMIZE_EDIT', 'アラーム編集');
define('DRIVER_CHOICE', 'ドライバーを選択');
define('DRIVER', 'ドライバー');
define('CHOOSE', '指定');
define('SHOULD_ADDRESS', 'した住所にいるべき');
define('PLACE_IN_OR_NOT', '指定した住所に、「いたら送信/いなかったら送信」');
define('MUST_ADDRESS','にいるべき住所');
define('ACCURACY','誤差の範囲');
define('THIRTY_M','30M以内はアプリの');
define('NOT_DISIGNATION','なので、指定できません。');
define('SENDMAIL','メールを送る');
define('NOT_TIME_SENDMAIL','指定した時間にいなかったらメールを送る');
define('TIME_SENDMAIL','指定した時間にいたらメールを送る');
define('SEND_TIMING','(アラート自体は5分間隔で送信されます。)');
define('NOT_ADMIN_SENDMAIL','管理者メールアドレス以外に送信したいメールアドレス');
define('EMAIL_COUNT','(送信したいメールアドレスをカンマ区切りで最大10件まで登録できます。)');


/*ユーザー登録情報*/
define('COMMON_USER' ,//ユーザー情報を編集する
	'ユーザー情報');
define('U_ID','ユーザーID');
define('NICK_NAME','投稿用の名前');
define('COMMON_MAGAZINE', 'メールマガジン');
define('USER_INFO', '自己紹介文');
define('ALLOW_RECEIVE', '受け取る');
define('NOT_RECEIVE', '受け取らない');
define('PRE_CONFIRM_MESSAGE_USER' ,//ユーザー登録完了画面メッセージ
	'メールアドレス確認が完了しました。<br>
	お客様のユーザー登録が完了しました。<br>
	<br>
	クチコミの投稿には、<a href="index.php?action=prefecture">住所から検索</a>などでクチコミを投稿したいタクシー会社を見つけ、
	タクシー会社のページにある、「クチコミを投稿する」というリンクをクリックして行ってください。<br>
	');
define('FOR_USER','ユーザー用');
define('NOTICE_USER_LOGIN','こちらは一般ユーザー用ログインです。<br>
<a href="index.php?action=login">グループ専用ログイン</a>はこちら、ドライバーログインは、<a href="index.php?action=driverLogin">こちら</a>から。');
define('USER_INFO_EDIT','ユーザー情報編集');

/*クチコミ*/
define('WORD_OF_MOUTH','クチコミ');
define('PUT_WORD_OF_MOUTH','クチコミを投稿する');
define('PUT_WORD_OF_MOUTH_CONFIRM','クチコミ投稿　確認画面');
define('VIEW_WORD_OF_MOUTH','クチコミを見る');
define('WORD_OF_MOUTH_NOTICE','クチコミを書き込む前に、<a href="index.php?action=message_mobile&situation=kiyaku">
利用規約</a>をご確認ください。<br>
ルールに反する書き込みなどは、管理側で削除する場合がありますのでご了承ください。<br>
また、書き込み内容の利用に関して、<a href="index.php?action=message_mobile&situation=kiyaku">利用規約</a>に記載しておりますので、ご確認の上、ご投稿ください。');
define('MANNER','マナー');
define('SERVICE','サービス');
define('DRIVE_TECHNIQUE','運転');
define('TOTAL_POINT','総評');
define('COMMENT','コメント');
define('REFFERENCE','参考票');
define('KEISYO_CASUAL','さん');
define('WHO_POST','投稿者');
define('WORD_OF_MOUTH_COMPANY_REGIST','クチコミ用タクシー会社登録画面');
define('WORD_OF_MOUTH_REFFERENCE','参考になった');
define('ADMIN_WORD_OF_MOUTH','クチコミ管理');
define('WORD_OF_MOUTH_LIST','クチコミ一覧');
define('COMMON_FRGT_PW_USER'			//パスワードを忘れた方
		,'パスワードを忘れた方は<a href="index.php?action=user/password">こちら</a>.');
define('COMMON_YET_REG_USER'			//ユーザ登録がまだの方
		,'ユーザ登録がまだの方は <a href="index.php?action=user/putUser">こちら</a>.');
define('OF_WORD_OF_MOUTH','のクチコミ一覧。');

/*作業履歴*/
define('WORK_RECORDS','運転（履歴）日報');
define('START_TIME','開始時間');
define('END_TIME','終了時間');
define('TOTAL_TIME','作業時間');
define('TOTAL_TIME_SUM','作業時間合計');
define('EDIT','編集');
define('EDIT_ICON','<img src="'.TEMPLATE_URL.'/templates/image/icon_edit.gif" alt="編集" title="編集">');
define('ADD_ICON','<img src="'.TEMPLATE_URL.'/templates/image/icon_add.gif" alt="追加" title="追加">');
define('COPY_ICON','<img src="'.TEMPLATE_URL.'/templates/image/bt_copy.png" alt="コピー" title="コピー">');
define('DELETE','消去');
define('TIME','時間');
define('START','開始');
define('START_ADDRESS','開始住所');
define('END','終了');
define('END_ADDRESS','終了地点住所');
define('CIRCLE','○');
define('ALL_SELECT','全て');
define('THIS_MONTH_WORK_TIME','今月の勤務時間');
define('HUNDRED_WORK_TIME','最新の記録100件を表示しています。');
define('DUPLICATED_WORK_TIME','時間が重複する作業履歴があります。重複するものを先に削除してください。');
define('DISTANCE','距離');
define('SYABAN','車番');
define('PLATE_NUMBER','プレートナンバー');
define('REFINE','絞り込み');
define('WORKTIME_REFINE','業務時間の絞り込み');

/*日報*/
define('DAY_REPORT','日報');
define('DAY_REPORT_PDF','日報 PDF');
define('DAY_DRIVE_REPORT','運転日報');
define('DESTINATION_COMPANY_NAME','社名');
define('AMOUNT','積載量');
define('LOADAGE','積載数');
define('TOLL_FEE','通行料金');
define('DRIVE_DATE','運行日');
define('START_METER','出庫メーター');
define('ARRIVAL_METER','帰庫メーター');
define('DRIVE_DISTANCE','走行距離');
define('GARAGE_START','車庫出発');
define('GARAGE_ARRIVAL','車庫到着');
define('DRIVE_TIME','運行時間');
define('SUPPLIED_OIL','給油量');
define('DRIVER_BREAK','休憩');
define('JYOUMUIN','乗務員');
define('TOTAL_DISTANCE','走行距離');
define('TOTAL_DRIVE_TIME','運行時間');
define('ADMINISTRATOR','管理者');
define('OFFICER','事務');
define('ACCOUNTANT','担当者');
define('DAY_REPORT_DATA','運転日報用データ');

define('NOTICE_FLOAT','（半角小数で入力）');
define('DUPLICATED_DAY_REPORT','その日付の日報用データは既にあります。新規に作成ではなく、一覧画面、あるいは絞り込み結果画面から編集を選択してください。');

define('START_LATITUDE','開始地点緯度');
define('START_LONGITUDE','開始地点経度');
define('END_LATITUDE','終了地点緯度');
define('END_LONGITUDE','終了地点経度');

/*ターゲットマップ*/
define('TARGET','コンテナ');
define('TARGET_MAP','コンテナマップ');
define('TARGET_SET_DATE','コンテナ設置日');
define('TARGET_PICKED_DATE','コンテナ引き取り日');
define('TARGET_ID','コンテナ番号');
define('TARGET_SET_DRIVER','設置ドライバー');
define('REFINE_BY_SETTED','設置日で絞り込み');
define('REFINE_BY_PICKED','引き取り日で絞り込み');
define('FROM_TO','～');
define('TARGET_PICKED','引き取られたコンテナ履歴');
define('IS_PICKED','引き取り済み');
define('NOT_PICKED','未引き取り');
define('PICKED_DATE_NOTICE','※引き取り済みの場合のみ入力してください');
define('SELECT_BLANK','--');

/*CSV出力*/
define('CSV_REPORT','CSV出力');

/*ドライバーレコード　CSV出力*/
define('NUMBER_REFINE','件数調整');
define('LATITUDE','緯度');
define('LONGITUDE','経度');

/*メッセージ*/
define('MESSAGE','メッセージ');
define('MESSAGE_TO','メッセージ宛先');
define('WAS_SENT','を送信しました');
define('MESSAGE_HISTORY','メッセージ送信履歴');
define('MESSAGE_SUCCESS','メッセージは無事に送信されました');
define('MESSAGE_FAILED','メッセージ送信に失敗しました');
define('MESSAGE_SEND_SUCCEEDING','続けて新規メッセージを送信
');
define('ERROR_INFO','エラー情報');
define('COMMON_SENDER','送信者');
define('HAS_READ','既読');
define('HAS_NOT_READ','未読');
define('ROGER','了解');
define('THERE_IS_MESSAGE_NOT_READ','未読のメッセージがあります');
define('MAP_DETAIL_CHANGE','修正したい場合は、地図上をクリックしてください。');
define('MARKER_CHANGE','修正したい場合は、地図上のマーカーをドラッグ＆ドロップしてください。');
define('EMPTY_MESSAGE','空のメッセージ');

/*配送先*/
define('DESTINATION','配送先');
define('DESTINATION_NAME','配送先名称');
//define('DESTINATION_CATEGORY','種類');
define('CHANGE_ADDRESS_MANUALLY','自動出力の住所が間違っている場合は、修正してください。');
define('DESTINATION_DRAG_UPLOAD','CSVをドラッグすると自動で配送先を登録します');
define('ABORT_MARK','×');
define('FALSE_CSV_UPLOAD','　失敗');
define('DESTINATION_EASY_UPLOAD','CSV一括登録');
define('DESTINATION_SELF_UPLOAD','フォーム入力');
define('DESTINATION_MAP','配送先MAP');

/*ルート*/
define('ROOT','ルート');
define('ROOT_INDEX','順番');
define('ROOT_NAME','ルート名称');
define('ROOT_ADD','ルート追加');
define('DELIVER_DATE','配送日');
define('ROOT_DETAIL','ルート詳細');
define('DELIVER_TIME','配送時間');
define('COMMON_BIKOU','備考');
define('CHOOSE_FROM_DEST','配送先から指定');
define('ROOT_DETAIL_ADD','ルート詳細追加');
define('DUPLICATE_ROOT','同じドライバー、同じ日付のデータが既に存在しています。古いデータを消去するか、古いデータを編集してください。');
define('ROOT_DETAIL_OVER10','10件以上のデータがある場合は、ルートから新しく追加してください。');
define('NOT_DISPLAY_APPLI','(アプリには表示されません)');
define('ROOT_DISPLAY_APPLI','最新の日付のルートがアプリに表示されます。');

/*設定*/
define('SETTING','設定');
define('MOVING','移動');
define('WORK_CUSTOMIZE','作業内容カスタマイズ');
define('GPS_CUSTOMIZE','位置情報を取得する間隔を設定');
define('RESIDENCE','常駐して動作させるか');
define('ACCORD','許容する');
define('AMBIT','※500~3000mに範囲');
define('PUBLIC_MAP_SCOPE','リアルタイムマップの一般公開設定');
define('PUBLIC_DRIVERS','公開するドライバーの選択');
define('MAP_VIEWERS_SELECTION', VIEWER.'を選択');
define('PUBLIC_MAP_URL_TITLE','共有URL');
define('PUBLIC_MAP_IFRAME_CODE_TITLE','サイト内埋め込みコード');

/*画像*/
define('COMMON_IMAGE','画像');
define('IMAGE_NOTICE','※地図に表示されるアイコンの画像をアップロードできます。<br>
	※形式はGIF、JPEG、PNG、サイズは1点3Mまでアップロード可能です。画像は'.ICON_WIDTH.'ピクセルの幅と高さにリサイズされます。');
define('IMAGE_TITLE','画像タイトル');
define('COMMON_NO_IMAGE','現在画像はありません。');
define('CHANGE_IMAGE','違う画像にする');
define('ADD_IMAGE','画像を追加する');
define('IMAGE_TOO_BIG','画像のサイズが大きすぎます。3M以下にして再度アップロードしてください。');
define('IMAGE_ONLY','アップロードできるファイルはjpeg、png、gifのみです。');
define('MAP_ICON','地図用アイコン');

/* 配送先カテゴリ*/
define('DESTINATION_CATEGORY','配送先カテゴリー');
define('DESTINATION_CATEGORY_SAMPLE','（例）新規顧客、既存顧客、など');
define('CATRGORY_NAME','カテゴリー名');
define('COLOR','色');
define('MARKER_COLOR_EXPLANATION', '地図上で表示するマーカーの色を設定します。<br>下の図から選択したい色をクリックしてください。');
define('DESTINATION_CATEGORY_NOTICE','カテゴリーは複数選択できますが、地図上に表示するマーカーの色は一色です。');

/* ユーザー用画面*/
define('USER_MENU', 'ユーザーメニュー');

/*　動画　*/
define('FILE_NAME','ファイル名');
define('FILE_SIZE','ファイルサイズ');
define('COMMON_DOWNLOAD','ダウンロード');
define('UPLOAD_TIME','アップロードされた時間');

/* 積雪・渋滞の情報 */
//define('INFORMATION_TYPE','渋滞/積雪');
define('INFORMATION_TYPE','渋滞/ルート外れ');
define('SNOW','積雪');
define('TRAFFIC','渋滞');
define('OUT_OF_ROUTE','ルート外れ');
define('SUDDEN_BRAKING','急ブレーキ');

/* JV選択画面 */
define('JV_SELECT','JV選択画面');

/* 総合管理システム連携 */
// define('SELF_COMMUNICATION_URL', 'http://localhost/smart_location_admin_edison/index.php?action=test_communication_self');
define('SELF_COMMUNICATION_URL', 'http://edison.doutaikanri.com/smart_location_test/index.php?action=test_communication_self');
// define('EDISON_COMMUNICATION_URL', 'http://localhost/smart_location_admin_edison/index.php?action=test_communication');

/* 中継サーバー接続設定 */
define('IFT0120', 'IFT0120');
define('IFT0150', 'IFT0150');
define('RETRY_INTERVAL', 200000);
define('SEND_DRIVER_STATUS_LIMIT', 1000);
define('COMMUNICATE_TIMEOUT', 30);

/* 中継サーバー接続エラー時メール送信先 */
define('OC_MAILADDRESS','yuji-hamada@onlineconsultant.jp');
// define('OC_MAILADDRESS','smart_location_edison_error@onlineconsultant.jp');
// define('EDISON_MAILADDRESS', 'fukushima-sys@e-mall.co.jp');
define('EDISON_MAILADDRESS', 'vpp.user@onlineconsultant.jp');

/* 輸送ステータス送信 */
define('TRANSPORT_START_ERROR_MESSAGE', '輸送開始の登録に失敗しました');
define('TRANSPORT_END_ERROR_MESSAGE', '輸送終了の登録に失敗しました');

/* 急ブレーキ情報CSV出力用 */
define('TEMPORARY_STORAGE','temporary_storage');

/* エリア・ルート判定 */
define('AREA_MAP', 'エリアマップ');
define('MUTE', 'ミュート');
define('DELET_ALARM', 'アラームを消す');

/* 輸送ルート */
define('TRANSPOERT_ROUTE', '輸送ルート');
define('CONFIRM_NEW_REGISTER', '新規登録確認画面');
define('CONFIRM_DISPLAY', '確認画面');
define('CHOOSE_TRANSPOERT_ROUTE', '輸送ルート 選択');
define('TRANSPOERT_ROUTE_NAME', '輸送ルート 名称');
define('ROUTE_AND_TRANSPOERT_ROUTE_DETAIL', 'ルート及びエリア詳細');
define('JQUERY_UI_JS', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
define('JQUERY_UI_CSS', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css');
define('DATE_PICKER_JA_JS', TEMPLATE_URL.'templates/js/datepicker-ja.js');
define('ROOT_COPY','ルートのコピー');
define('CONFIRM_ROOT_COPY','ルートコピー 確認画面');
define('SELECT_ROOT_COPY','コピーしたいルートを選択してください。');
define('SELECT_ROOT','ルート選択');
define('EDIT_ROOT_OF_DRIVER','ドライバーに紐づくルート編集');
define('SELECT_ROOT_ON_SMARTDOUTAIKANRI','Smart動態管理上で作成したルートを選択してください。');
define('EMPTY_SELECT_ROOT','ルートの選択がされていません。');
define('DRIVERS_ROUTE_AREA', 'ドライバールート、エリア一覧');
define('ROUTE_INFOEMATION','ルートの備考');
define('ALL_ROUTE','ルート一覧');
define('EDIT_DELETE_DOWNLOAD','編集／削除／ダウンロード');
define('ROUTE_INFOEMATION','ルートの備考');

define('DRIVER_ROUTE_INFORMATION', 'ドライバールートの備考');
define('DATE_VALIDATION','8桁の半角数字を日付形式で入力して下さい。');



/* ナビエリア */
define('NAVI_AREA', 'ナビエリア');
define('NAVI_AREA_NAME', 'ナビエリア 名称');
define('RADIUS_FROM_CENTER', '中心点からの範囲');
define('NAVI_MESSAGE', 'エリア進入時メッセージ');
define('CLICK_AREA_CENTER_ON_MAP', '地図上でナビエリアの中心にしたい地点をクリックしてください。');
define('CATEGORY_TEMPORARY_STORAGE', '仮置場');

/* Mapbox関係 */
define('GEO_JSON_UPLOAD', 'GeoJSONアップロード');
define('GEO_JSON_ERROR', 'JSON形式のファイルをアップロードしてください');
define('GEO_JSON', 'GeoJSON');

/* ルート逸脱 */
define('ROUTE_DEVIATED','ルート逸脱');
?>
