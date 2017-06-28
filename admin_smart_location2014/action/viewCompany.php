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
		$dataList=Data::getCompany_admin($id);
	
		$prefecture_name=$dataList['prefecture_name'];
		foreach($dataList as $key => $value){
			$smarty->assign( "$key" , $value );
		}
	}
	
}catch(Exception $e){

	$message=$e->getMessage();
}

$smarty->assign("js",'<script src="'.GOOGLE_MAP.'"type="text/javascript"></script>
								<script type="text/javascript" src="'.GEOCODING_JS.'"></script>');

//バリデーションでエラーがない場合のみ、確認画面表示
//geocodingする住所
$geocode_address_city=$dataList['city'].$dataList['ward'].$dataList['town'].$dataList['address'];

//県名のみ、一度Shift_JISデータに変換する
if(is_garapagos()){
	$geo_prefecture_name=mb_convert_encoding($prefecture_name, "SJIS", "UTF-8");
}else{
	$geo_prefecture_name=$prefecture_name;
}
$geocode_address=$geo_prefecture_name.$geocode_address_city;

//geocodingする住所
//ガラケーの場合UTF-8からShift_JISに
if(is_garapagos()){
	$geocode_address_convert=mb_convert_encoding($geocode_address, "UTF-8", "SJIS");
}else{
	$geocode_address_convert=$geocode_address;
}
$geocode_address='<div id="geocode_address">'.$geocode_address_convert.'</div>';

$smarty->assign("onload_js","onload=\"doGeocode()\"");

$smarty->assign("prefecture_name",$prefecture_name);
$smarty->assign("google_map_js",$google_map_js);
$smarty->assign("geocoding_js",$geocoding_js);
$smarty->assign("geocode_address",$geocode_address);


$smarty->assign('id',$id);

$smarty->assign("prefecturesList",$prefecturesList);

//画像
$smarty->assign('image_info',$image_info);

//仮登録情報かどうかのフラグ
$smarty->assign('activation',$activation);

$smarty->assign("filename","viewCompany.html");
$smarty->display("template.html");

?>