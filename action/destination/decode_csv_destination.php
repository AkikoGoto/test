<?php
if(!empty($u_id)){
	$company_id = $u_id;
}elseif(!empty($branch_manager_id)){
	$driver_info_array = Driver::getById($branch_manager_id, $from_web);
	$company_id = $driver_info_array[0]->company_id;
//$company_id=9841;
}else{
	//不正なアクセスです、というメッセージ					
	//header("Location:index.php?action=message_mobile&situation=wrong_access");	
	//exit(0);
}

/* HTML特殊文字をエスケープする関数 */
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// パラメータを正しい構造で受け取った時のみ実行
$status = "FALSE";

if (isset($_FILES['file']['error']) && is_int($_FILES['file']['error'])) {

    try {

    	//ファイルがCSVかどうかチェック
		$mimes = array(
			'application/vnd.ms-excel',
			'text/plain', 'text/csv','text/tsv',
			'text/comma-separated-values', 'application/csv',
			'application/excel');
		
		if(in_array($_FILES['file']['type'],$mimes)){
		
			
	        // ファイルアップロードエラーチェック
	        switch ($_FILES['file']['error']) {
	            case UPLOAD_ERR_OK:
	                // エラー無し
	                break;
	            case UPLOAD_ERR_NO_FILE:
	                // ファイル未選択
	                throw new RuntimeException( $FILES_STR . 'CSVファイルが選択されていません。');
	            case UPLOAD_ERR_INI_SIZE:
	            case UPLOAD_ERR_FORM_SIZE:
	                // 許可サイズを超過
	                throw new RuntimeException('ファイルのサイズが大きすぎて、アップロードできませんでした。');
	            default:
	                throw new RuntimeException('予期せぬエラーが発生しました。再度アップロードしてください。');
	        }
	
	        $tmp_name = $_FILES['file']['tmp_name'];
	        $detect_order = 'ASCII,JIS,UTF-8,CP51932,SJIS-win';
	        setlocale(LC_ALL, 'ja_JP.UTF-8');
	
	        // 文字コードを変換してファイルを置換
	        $buffer = file_get_contents($tmp_name);
	        if (!$encoding = mb_detect_encoding($buffer, $detect_order, true)) {
	            // 文字コードの自動判定に失敗
	            unset($buffer);
	            throw new RuntimeException('Character set detection failed');
	        }
	        file_put_contents($tmp_name, mb_convert_encoding($buffer, 'UTF-8', $encoding));
	        unset($buffer);
			
	        try {
	        	
	            $fp = fopen($tmp_name, 'rb');
	            
	            //CSVから出コードして取得した配送先を配列に格納
	            $destinations = Destination::analyzeDesctinationCSV( $company_id, $fp );
	            
	            //配列でなかったら、エラーコードが返ってきているため、それをステータスにする
	            if (!is_array($destinations)) {
	            	$status = $destinations;
	            	$destinations = null;
	            } else {
		            $status = "SUCCESS";
	            }
				
	        	if (!feof($fp)) {
	                // ファイルポインタが終端に達していなければエラー
	                throw new RuntimeException('CSV parsing error');
	            }
				
				fclose($fp);
	            
	        } catch (Exception $e) {
	            
	        	fclose($fp);
	        	if ($status != "INVALID_CSV_FORMAT")
		        	$status = "FALSE";
	            
	        }
	        
		} else {
			
			$status = "INVALID_TYPE";
			
		}
    	

    } catch (Exception $e) {

        /* エラーメッセージをセット */
        $msg = array('red', $e->getMessage());

    }
    
    $result = array("status" => $status, "destinations" => $destinations);
    $json_result = json_encode($result);
	header('Content-Type: application/json');
    echo $json_result;

}