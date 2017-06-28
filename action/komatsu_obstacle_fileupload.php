<?php
var_dump($_SESSION);
	if($_GET['company_id']){	
	$id=sanitizeGet($_GET['company_id']);
	}
	user_auth($u_id,$id);
	$uploaddir = Komatsu_obstacle::get_uploaded_directory($u_id);
	//csvファイル読み込みとバリデーション
	if(!empty($_FILES['uploadedfile']['tmp_name'])){
		$uploadedfile = $uploaddir."/".basename($_FILES['uploadedfile']['name']);		
		$fp = fopen($_FILES['uploadedfile']['tmp_name'],"r");
		$is_num = true;
		while($data = fgetcsv($fp)){
	        for($i=0;$i<count($data);$i++){
	            if (!is_numeric($data[$i])){
	            	$is_num = false;
					$_SESSION['komatsu_obstacle']['msg']="CSVファイルは半角英数字で入力してください。";
	            }
	        }
	    }
	    fclose($fp);
	    //読み込みとバリデーションのコードここまで
	    
	    //バリデーションに成功したらアップロード
	    print  $uploadedfile;
	  	if($is_num){
	  		
	  		if ( file_exists( $uploadedfile )) {
					$_SESSION['komatsu_obstacle']['msg'] = $_FILES['uploadedfile']['name']."を上書きしました。";
				}else{
			    	$_SESSION['komatsu_obstacle']['msg'] = $_FILES['uploadedfile']['name']."のアップロードが完了しました。";
				}		
	  		
			if (!move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $uploadedfile)) {
				$_SESSION['komatsu_obstacle']['msg'] = $_FILES['uploadedfile']['name']."をアップロードできませんでした。";
			}
		}
		//アップロードのコードここまで
		
	}

	header("Location:index.php?action=komatsu_obstacle&id={$u_id}");