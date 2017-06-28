<?php
//モバイル版のみの設定ファイル



$sid=SID;

	//キャリア判別
	$carrier=get_carrier();
	
	//CSS割り当て
	if($carrier=='softbank'||$carrier=='au'){

		$smarty->assign("css",'<LINK rel="stylesheet" href="templates/css/mobile.css" 
			type="text/css" media="handheld">');		
		//モバイル版のUTF-8からShift_JIS変換
		$smarty->register_outputfilter("Encode_utf8tosjis");
		
	}elseif($carrier=='iPhone'){
		
		//iPhone用に、CSSは携帯用、メタタグでiPhone用にする
		$smarty->assign("css",'<LINK rel="stylesheet" href="templates/css/smart_phone.css" 
			type="text/css">
			<meta name="viewport" content="initial-scale=1.0, user-scalable=yes ,maximum-scale=3.0 width=device-width" />');	
		
	}elseif($carrier=='Android'){
		
		//iPhone用に、CSSは携帯用、メタタグでiPhone用にする
		$smarty->assign("css",'<LINK rel="stylesheet" href="templates/css/smart_phone.css" 
			type="text/css">
			<meta name="viewport" content="initial-scale=1.0, user-scalable=yes ,maximum-scale=3.0 width=device-width" />');	
		
		
	}elseif($carrier=='docomo'){

		//モバイル版のUTF-8からShift_JIS変換
		$smarty->register_outputfilter("Encode_utf8tosjis");
		
		
	}elseif($carrier==''){

		$smarty->assign("css",'<LINK rel="stylesheet" href="templates/css/pc.css" 
			type="text/css" media="screen">');
	
	}
	
	$smarty->assign("carrier",$carrier);

?>