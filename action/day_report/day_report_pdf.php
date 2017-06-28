<?php
/**
 * PDF出力に利用しているライブラリ
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Default Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */
/**
 * 日報をPDFで出力するアクション
 */

	//PDF出力ライブラリを読みこみ
	require_once('../tcpdf/tcpdf.php');
		
	//ドライバーIDが来ているか
	if($_POST['driver_id']){
		$driver_id = htmlentities($_POST["driver_id"], ENT_QUOTES, mb_internal_encoding());
	}
	//ドライバーIDが来ているか
	if($_POST['company_id']){
		$company_id = htmlentities($_POST["company_id"], ENT_QUOTES, mb_internal_encoding());
	}
	
	if(!empty($branch_manager_id)){
			
		//営業所のマネージャーがその会社の情報にアクセスしてよいか
		branch_manager_auth($branch_manager_id, $company_id);
	
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $driver_id);
	
	}else{
		//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
		driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);

	}
	
	$driver_name = Driver::getNameById($driver_id);

	$status = htmlentities($_POST['status'], ENT_QUOTES, mb_internal_encoding());
    
    $time_from_year = htmlentities($_POST["time_from_year"], ENT_QUOTES, mb_internal_encoding());
    $time_from_month = htmlentities($_POST["time_from_month"], ENT_QUOTES, mb_internal_encoding());
    $time_from_day = htmlentities($_POST["time_from_day"], ENT_QUOTES, mb_internal_encoding());
    
    $time_to_year = htmlentities($_POST["time_to_year"], ENT_QUOTES, mb_internal_encoding());
    $time_to_month = htmlentities($_POST["time_to_month"], ENT_QUOTES, mb_internal_encoding());
    $time_to_day = htmlentities($_POST["time_to_day"], ENT_QUOTES, mb_internal_encoding());
    
    //記録の取得
	$dataList = Work::getWorktimeByDate($driver_id, $status, $time_from_year, $time_from_month, $time_from_day, 
	 					$time_to_year, $time_to_month, $time_to_day);

	 					
	//日報データの取得　出庫メータなど
	$dayReportList = DayReport::getDayReportByDate($driver_id, $time_from_year, $time_from_month, 
						$time_from_day, $time_to_year, $time_to_month, $time_to_day);


	//ステータス名の取得
	$workingStatuses = Data::getStatusByCompanyId($company_id);
	$workingStatus = $workingStatuses[0];						
						
	//記録を日付ごとにグループ分け
	$i=0;
	
	if($dataList != NULL && $dataList[0]['start'] != NULL){
		foreach ($dataList as $each_data){
			$each_start_date[$i] = substr($each_data['start'], 0, 10);
			if($i != 0){
				
				if($each_start_date[$i]==$each_start_date[$i-1]){
					$data_by_date[$j][]=$each_data;
				}else{ 
					$j++ ;		
					$data_by_date[$j][]=$each_data;	
				}
				
			}else{
				
				$j = 0;	
				$data_by_date[$i][]=$each_data;		
			
			}
			
			//給油量などの運転日報用データを結合
/*			foreach($dayReportList as $each_dayReportList){
				if($each_dayReportList['drive_date']== $each_start_date[$i]){
					array_merge($data_by_date[$j], $each_dayReportList); 
				}
			}
*/			
			$i++ ;  
		
		}
	
		//$test_print = var_dump($dataList);
		
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Smart動態管理');
		$pdf->SetTitle(DAY_DRIVE_REPORT);
		
		//ヘッダーを消す
		$pdf->SetPrintHeader(false);
		
		// set default header data
		//$pdf->SetHeaderData('', '', DAY_DRIVE_REPORT, '', array(0,0,0), array(0,0,0));
		$pdf->setFooterData($tc=array(0,64,0), $lc=array(0,64,128));
		
		// set header and footer fonts
		//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		//set some language-dependent strings
		$pdf->setLanguageArray($l);
		
		// ---------------------------------------------------------
		
		// set default font subsetting mode
		$pdf->setFontSubsetting(true);
		
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('kozminproregular', '', 9, '', true);
		
		// Add a page
		// This method has several options, check the source code documentation for more information.
		//$pdf->AddPage();
		
		// set text shadow effect
		$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
		
		// Set some content to print
		/*$html = <<<EOD
		<h1>Welcome to <a href="http://www.tcpdf.org" style="text-decoration:none;background-color:#CC0000;color:black;">&nbsp;<span style="color:black;">TC</span><span style="color:white;">PDF</span>&nbsp;</a>!</h1>
		<i>This is the first example of TCPDF library.</i>
		<p>This text is printed using the <i>writeHTMLCell()</i> method but you can also use: <i>Multicell(), writeHTML(), Write(), Cell() and Text()</i>.</p>
		<p>Please check the source code documentation and other examples for further information.</p>
		<p style="color:#CC0000;">TO IMPROVE AND EXPAND TCPDF I NEED YOUR SUPPORT, PLEASE <a href="http://sourceforge.net/donate/index.php?group_id=128076">MAKE A DONATION!</a></p>
		EOD;
		*/
		
		foreach ( $data_by_date as $each_date){
		
			if($dayReportList !=null ){
				
				foreach($dayReportList as $each_day_report_list){

					$each_drive_date = substr($each_date[0]['start'],0,10);
					$each_drive_date_report = substr($each_day_report_list['drive_date'],0,10);
					
					if($each_drive_date == $each_drive_date_report){
						$start_meter = $each_day_report_list['start_meter'];
						$arrival_meter = $each_day_report_list['arrival_meter'];
						$supplied_oil = $each_day_report_list['supplied_oil'];

					}
				
				}
			}
			
			
			$total_distance = 0;
			$total_time = 0;
			$break_time = 0;
		
			//走行距離と時間の合計と休憩時間を計算
			foreach($each_date as $data_calculate){
				$total_distance = $total_distance + $data_calculate['distance'];
				$each_data_seconds = h2s($data_calculate['total_time']);
				$total_time = + $each_data_seconds;
				
				//休憩時間
				if($data_calculate['status']==4){
					$break_time = $each_data_seconds;
				}
			}
			
			$total_time = s2h($total_time);
			$break_time = s2h($break_time);
			
			//車庫到着時間を計算
			$temporary_array = $each_date;
			$final_data = array_pop($temporary_array);
			$arrival_garage_time = $final_data['end'];
		
			
			$pdf->AddPage();
			
			/*表題部分*/
			$pdf->SetFont('kozminproregular', '', 18, '', true);
			$pdf->Cell(200, 6, DAY_DRIVE_REPORT, 0, 0, 'C');
		
			$pdf->SetFont('kozminproregular', '', 9, '', true);
			/*印鑑部分*/
			$w_sine_zone = array(20, 20, 20);
			$pdf->Cell($w_sine_zone[0], 6, ADMINISTRATOR, 1, 0, 'C');
			$pdf->Cell($w_sine_zone[1], 6, OFFICER, 1, 0, 'C');
			$pdf->Cell($w_sine_zone[2], 6, ACCOUNTANT, 1, 0, 'C');
			$pdf->Ln();
		
			$pdf->Cell(200, 12, '', 0, 0, 'C');
			$pdf->Cell($w_sine_zone[0], 12, '', 1, 0, 'L');
			$pdf->Cell($w_sine_zone[1], 12, '', 1, 0, 'L');
			$pdf->Cell($w_sine_zone[2], 12, '', 1, 0, 'L');
			
			$pdf->Ln();
			/*印鑑部分　終わり*/
			
			
			$html = "<p>&nbsp;</p>";
			$pdf->writeHTML($html, true, false, true, false, 'J');
			
			// Color and font restoration
			$pdf->SetFillColor(224, 235, 255);
				
			/*上の表部分*/
			// set default header data
			$w_header = array(18, 35, 25, 35, 18, 35, 15, 15, 15,20);
				
			$pdf->Cell($w_header[0], 6, DRIVE_DATE, 1, 0, 'L');
			$pdf->Cell($w_header[1], 6, substr($each_date[0]['start'],0,10), 1, 0, 'L');
			$pdf->Cell($w_header[2], 6, START_METER, 1, 0, 'L');
			$pdf->Cell($w_header[3], 6, $start_meter, 1, 0, 'L' );
			$pdf->Cell($w_header[4], 6, GARAGE_START, 1, 0, 'L');
			$pdf->Cell($w_header[5], 6, $each_date[0]['start'], 1, 0, 'L');
			$pdf->Cell($w_header[6], 6, SUPPLIED_OIL, '1', 0, 'L');
			$pdf->Cell($w_header[7], 6, $supplied_oil, '1', 0, 'L');
			$pdf->Cell($w_header[8], 6, TOTAL_DISTANCE, '1', 0, 'L');
			$pdf->Cell($w_header[9], 6, $total_distance, '1', 0, 'L');
			
			$pdf->Ln();
			
			$pdf->Cell($w_header[0], 6, JYOUMUIN, '1', 0, 'L',$fill,'',1);
			$pdf->Cell($w_header[1], 6, $driver_name[0]['last_name'].' '.$driver_name[0]['first_name'], '1', 0, 'L',
						$fill,'',1);
			$pdf->Cell($w_header[2], 6, ARRIVAL_METER, '1', 0, 'L',$fill,'',1);
			$pdf->Cell($w_header[3], 6, $arrival_meter, '1', 0, 'L',$fill,'',1);
			$pdf->Cell($w_header[4], 6, GARAGE_ARRIVAL, '1', 0, 'L',$fill,'',1);
			$pdf->Cell($w_header[5], 6, $arrival_garage_time, '1', 0, 'L',$fill,'',1);
			$pdf->Cell($w_header[6], 6, DRIVER_BREAK, '1', 0, 'L',$fill,'',1);
			$pdf->Cell($w_header[7], 6, $break_time, '1', 0, 'L',$fill,'',1);
			$pdf->Cell($w_header[8], 6, TOTAL_DRIVE_TIME, '1', 0, 'L',$fill,'',1);
			$pdf->Cell($w_header[9], 6, $total_time, '1', 0, 'L',$fill,'',1);
			
			$pdf->Ln();
			
			$html = "<p>&nbsp;</p>";
			$pdf->writeHTML($html, true, false, true, false, 'J');
			/*上の表部分 ここまで*/
			
			if($company_id==10072){
				$header = array(DRIVER_STATUS, SYABAN, DESTINATION_COMPANY_NAME, COMMON_JYUSYO, TIME,
					DISTANCE, LOADAGE, TOLL_FEE, COMMON_COMMENT);
			}else{
				$header = array(DRIVER_STATUS, SYABAN, DESTINATION_COMPANY_NAME, COMMON_JYUSYO, TIME,
				 					DISTANCE, AMOUNT, TOLL_FEE, COMMON_COMMENT);
			}
			
			
			$num_headers = count($header);
		
			$w = array(18, 23, 30, 85, 35, 12, 12, 12, 30);		
			for($i = 0; $i < $num_headers; ++$i) {
				$pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1, '', 1);
			}
			
			$pdf->Ln();
		
			// Data
			
			$fill = 0;
			foreach($each_date as $data) {
		
				if($data['status']==1){
					$driver_status = $workingStatus->action_1;
				}elseif($data['status']==2){
					$driver_status = $workingStatus->action_2;
				}elseif($data['status']==3){
					$driver_status = $workingStatus->action_3;
				}elseif($data['status']==4){
					$driver_status = $workingStatus->action_4;
				}
				
				
				$data['start_address'] = preg_replace("/&minus;/", '-', $data['start_address']);
				
				$pdf->Cell($w[0], 6, $driver_status, 'LR', 0, 'L', $fill);
				$pdf->Cell($w[1], 6, $data['plate_number'], 'LR', 0, 'L', $fill, '', 1);
				$pdf->Cell($w[2], 6, $data['destination_company_name'], 'LR', 0, 'L', $fill);
				$pdf->Cell($w[3], 6, trim($data['start_address']), 'LR', 0, 'L', $fill);
				$pdf->Cell($w[4], 6, $data['start'], 'LR', 0, 'L', $fill);
				$pdf->Cell($w[5], 6, $data['distance'], 'LR', 0, 'L', $fill);
				$pdf->Cell($w[6], 6, $data['amount'], 'LR', 0, 'R', $fill);
				$pdf->Cell($w[7], 6, $data['toll_fee'], 'LR', 0, 'R', $fill);
				$pdf->Cell($w[8], 6, $data['comment'], 'LR', 0, 'L', $fill);
				$pdf->Ln();
				$fill=!$fill;
			}
			$pdf->Cell(array_sum($w), 0, '', 'T');		
		
		
		}
		
		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('day_report.pdf', 'I');

	}else{
		
		$smarty->assign("message","該当するデータがありません。");				
		
		$smarty->assign("driver_id",$driver_id);
		$smarty->assign("company_id",$company_id);
		
		
		$smarty->assign("filename","worktime.html");
		$smarty->display('template.html');
		exit();		
	}
    
