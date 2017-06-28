<?php

try {
	
	
	// 未定義である・複数ファイルである・$_FILES Corruption 攻撃を受けた
	// どれかに該当していれば不正なパラメータとして処理する
	if (!isset($_FILES['file']['error']) || !is_int($_FILES['file']['error'])) {
		throw new RuntimeException('パラメータが不正です');
	}
	

	if ($_FILES ['file'] ['error'] == UPLOAD_ERR_NO_FILE) {
		
		throw new RuntimeException('ファイルが選択されていません');
		
	}else if ($_FILES ['file'] ['type'] != 'application/octet-stream'&& // checks for errors
						is_uploaded_file ( $_FILES ['file'] ['tmp_name'] )) {
							
		throw new RuntimeException('ファイル形式が正しくありません');
							
	}else if ($_FILES ['file'] ['size'] > 5242880&& // checks for errors
				is_uploaded_file ( $_FILES ['file'] ['tmp_name'] )) {
					
		throw new RuntimeException('ファイルサイズが5M以上です');
					
	}else if ($_FILES ['file'] ['error'] == UPLOAD_ERR_OK && // checks for errors
				is_uploaded_file ( $_FILES ['file'] ['tmp_name'] )) { // checks that file is uploaded
		
				echo file_get_contents ( $_FILES ['file'] ['tmp_name'] );
	
	}
	
	
} catch ( RuntimeException $e ) {
	
	echo '{"error":"'.$e->getMessage ().'"}';

}
?>