<?php
//connect to database
require_once("../../mysql.php");
require_once '../../PHPExcel/Classes/PHPExcel.php';


if($_GET['action']=="to_excel_otkl")
{
		$zip = new ZipArchive();
		$zip_name = "reports/otkl_".$_GET['month']."_".$_GET['year'].".zip";
		if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==TRUE)
		{
			$error_zip = 1;
			echo "Error!";
		}
		else
		{
			$error_zip = 0;
		}

		$ind_file = 0;

	$query_podrazd = mysqli_query($db,"SELECT * FROM SPR_PODRAZD WHERE ID IN(SELECT ID_PODRAZD FROM PERSONAL WHERE DATE_END='2100-01-01' AND TABN IN(SELECT TABN FROM WORK_OUT WHERE YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."' AND ID_VID_ISP_VREM IN(8,9,10,11)))");
	if (mysqli_num_rows($query_podrazd)==0)echo "Отклонений за данный период нет!";
	while ($list_podrazd = mysqli_fetch_assoc($query_podrazd))
	{
		$file_name = "reports/otkl_".$list_podrazd['SOKR_NAME']."_".$_GET['month']."_".$_GET['year'].".xls";

		//$objPHPExcel = PHPExcel_IOFactory::load("templates/form_report_otkl.xls");
		$objReader = new PHPExcel_Reader_Excel5();
		$objPHPExcel = $objReader->load("templates/form_report_otkl.xls");

		
		
		//Копируем шаблон на новый лист
		$objPHPExcel->setActiveSheetIndex(0);
		$sheet = $objPHPExcel->getActiveSheet();
		$copiedSheet = $sheet->copy();
		$copiedSheet->setTitle(mb_convert_encoding(date("d_m_Y"),'utf-8','windows-1251'));
		$objPHPExcel->addSheet($copiedSheet);
		
		//Делаем активным созданный лист
		$objPHPExcel->setActiveSheetIndex(1);
		$aSheet = $objPHPExcel->getActiveSheet();
		
		//Заполняем шапку
		$aSheet->setCellValue('I2',$list_podrazd['NAME']);
		$aSheet->setCellValue('G2',$_GET['year']);
		$aSheet->setCellValue('H2',$_GET['month']);

		$rowId=7;//Первая строка для заполнения табличной части
		$i=1;

		$query = mysqli_query($db,"SELECT ID, TABN, DATE_TIME_BEG, DATE_TIME_END, ROUND(TIME_TO_SEC(PERIOD_TIME)/60/60,2) AS PERIOD_TIME_HOURS, ID_PERIOD_DAY, ID_VID_ISP_VREM, ID_WORK_OUT_EDIT FROM WORK_OUT WHERE TABN IN(SELECT TABN FROM PERSONAL WHERE ID_PODRAZD='".$list_podrazd['ID']."' AND DATE_END='2100-01-01') AND YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."' AND ID_VID_ISP_VREM IN(8,9,10,11)");
		$num_rows = mysqli_num_rows($query);
		//Записываем построчно
		while ($list = mysqli_fetch_assoc($query))
		{
			$query_pers = mysqli_query($db,"SELECT * FROM PERSONAL WHERE TABN='".$list['TABN']."' AND DATE_END='2100-01-01'");
			$list_pers = mysqli_fetch_assoc($query_pers);
			
			$query_vid_isp_vrem = mysqli_query($db,"SELECT * FROM SPR_VID_ISP_VREM WHERE ID='".$list['ID_VID_ISP_VREM']."'");
			$list_vid_isp_vrem = mysqli_fetch_assoc($query_vid_isp_vrem);
			
			$fam = $list_pers["FAM"];
			$name = $list_pers["NAME"];
			$otch = $list_pers["OTCH"];
			$fio = mb_convert_case($fam,MB_CASE_TITLE,"UTF-8")." ".substr($name,0,2).".".substr($otch,0,2).".";

			$aSheet->setCellValue('A'.$rowId,$i);
			$aSheet->setCellValue('B'.$rowId,$list["TABN"]);
			$aSheet->setCellValue('C'.$rowId,$fio);
			
			$aSheet->setCellValue('D'.$rowId,substr($list["DATE_TIME_BEG"],8,2).".".substr($list["DATE_TIME_BEG"],5,2).".".substr($list["DATE_TIME_BEG"],0,4));
			$aSheet->setCellValue('E'.$rowId,substr($list["DATE_TIME_BEG"],11,8));
			$aSheet->setCellValue('F'.$rowId,substr($list["DATE_TIME_END"],8,2).".".substr($list["DATE_TIME_END"],5,2).".".substr($list["DATE_TIME_END"],0,4));
			$aSheet->setCellValue('G'.$rowId,substr($list["DATE_TIME_END"],11,8));
			$aSheet->setCellValue('H'.$rowId,$list["PERIOD_TIME_HOURS"]);

			if(mysqli_num_rows($query_vid_isp_vrem)!=0)$aSheet->setCellValue('I'.$rowId,$list_vid_isp_vrem["NAME"]);
			
			
			
			$i++;
			$rowId++;
			if($i-1<$num_rows)$aSheet->insertNewRowBefore($rowId, 1);
			
		}

		$objPHPExcel->removeSheetByIndex(0);
		
		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		
		$objWriter->save($file_name);
		
		if($error_zip==0)$zip->addFile($file_name);
		
		$list_file[$ind_file] = $file_name;
		$ind_file++;

	}
	$zip->close();

	//Удаляем файлы
	for($n=0; $n<count($list_file); $n++)
	{
		unlink($list_file[$n]);
	}
}

if($_GET['action']=="to_excel_su")
{
		$zip = new ZipArchive();
		$zip_name = "reports/su_".$_GET['month']."_".$_GET['year'].".zip";
		if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==TRUE)
		{
			$error_zip = 1;
			echo "Error!";
		}
		else
		{
			$error_zip = 0;
		}

		$ind_file = 0;

	$query_podrazd = mysqli_query($db,"SELECT * FROM SPR_PODRAZD WHERE ID IN(SELECT ID_PODRAZD FROM PERSONAL WHERE DATE_END='2100-01-01' AND TABN IN(SELECT TABN FROM WORK_OUT WHERE YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."' AND ID_VID_ISP_VREM IN(2,7,16,24,27,28)))");
	if (mysqli_num_rows($query_podrazd)==0)echo "Отклонений за данный период нет!";
	while ($list_podrazd = mysqli_fetch_assoc($query_podrazd))
	{
		$file_name = "reports/su_".$list_podrazd['SOKR_NAME']."_".$_GET['month']."_".$_GET['year'].".xls";

		//$objPHPExcel = PHPExcel_IOFactory::load("templates/form_report_su.xls");
		$objReader = new PHPExcel_Reader_Excel5();
		$objPHPExcel = $objReader->load("templates/form_report_su.xls");
		
		//Копируем шаблон на новый лист
		$objPHPExcel->setActiveSheetIndex(0);
		$sheet = $objPHPExcel->getActiveSheet();
		$copiedSheet = $sheet->copy();
		$copiedSheet->setTitle(mb_convert_encoding(date("d_m_Y"),'utf-8','windows-1251'));
		$objPHPExcel->addSheet($copiedSheet);
		
		//Делаем активным созданный лист
		$objPHPExcel->setActiveSheetIndex(1);
		$aSheet = $objPHPExcel->getActiveSheet();
		
		//Заполняем шапку
		$aSheet->setCellValue('I2',$list_podrazd['NAME']);
		$aSheet->setCellValue('G2',$_GET['year']);
		$aSheet->setCellValue('H2',$_GET['month']);

		$rowId=7;//Первая строка для заполнения табличной части
		$i=1;

		$query = mysqli_query($db,"SELECT ID, TABN, DATE_TIME_BEG, DATE_TIME_END, ROUND(TIME_TO_SEC(PERIOD_TIME)/60/60,2) AS PERIOD_TIME_HOURS, ID_PERIOD_DAY, ID_VID_ISP_VREM, ID_WORK_OUT_EDIT FROM WORK_OUT WHERE TABN IN(SELECT TABN FROM PERSONAL WHERE ID_PODRAZD='".$list_podrazd['ID']."' AND DATE_END='2100-01-01') AND YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."' AND ID_VID_ISP_VREM IN(2,7,16,24,27,28)");
		$num_rows = mysqli_num_rows($query);
		//Записываем построчно
		while ($list = mysqli_fetch_assoc($query))
		{
			$query_pers = mysqli_query($db,"SELECT * FROM PERSONAL WHERE TABN='".$list['TABN']."' AND DATE_END='2100-01-01'");
			$list_pers = mysqli_fetch_assoc($query_pers);
			
			$query_vid_isp_vrem = mysqli_query($db,"SELECT * FROM SPR_VID_ISP_VREM WHERE ID='".$list['ID_VID_ISP_VREM']."'");
			$list_vid_isp_vrem = mysqli_fetch_assoc($query_vid_isp_vrem);
			
			$fam = $list_pers["FAM"];
			$name = $list_pers["NAME"];
			$otch = $list_pers["OTCH"];
			$fio = mb_convert_case($fam,MB_CASE_TITLE,"UTF-8")." ".substr($name,0,2).".".substr($otch,0,2).".";

			$aSheet->setCellValue('A'.$rowId,$i);
			$aSheet->setCellValue('B'.$rowId,$list["TABN"]);
			$aSheet->setCellValue('C'.$rowId,$fio);
			
			$aSheet->setCellValue('D'.$rowId,substr($list["DATE_TIME_BEG"],8,2).".".substr($list["DATE_TIME_BEG"],5,2).".".substr($list["DATE_TIME_BEG"],0,4));
			$aSheet->setCellValue('E'.$rowId,substr($list["DATE_TIME_BEG"],11,8));
			$aSheet->setCellValue('F'.$rowId,substr($list["DATE_TIME_END"],8,2).".".substr($list["DATE_TIME_END"],5,2).".".substr($list["DATE_TIME_END"],0,4));
			$aSheet->setCellValue('G'.$rowId,substr($list["DATE_TIME_END"],11,8));
			$aSheet->setCellValue('H'.$rowId,$list["PERIOD_TIME_HOURS"]);

			if(mysqli_num_rows($query_vid_isp_vrem)!=0)$aSheet->setCellValue('I'.$rowId,$list_vid_isp_vrem["NAME"]);
			
			
			
			$i++;
			$rowId++;
			if($i-1<$num_rows)$aSheet->insertNewRowBefore($rowId, 1);
			
		}

		$objPHPExcel->removeSheetByIndex(0);
		
		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);

		$objWriter->save($file_name);
		
		if($error_zip==0)$zip->addFile($file_name);
		
		$list_file[$ind_file] = $file_name;
		$ind_file++;

	}
	$zip->close();

	//Удаляем файлы
	for($n=0; $n<count($list_file); $n++)
	{
		unlink($list_file[$n]);
	}
}

?>