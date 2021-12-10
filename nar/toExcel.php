<?php
//connect to database
require_once("../../mysql.php");
require_once '../../PHPExcel/Classes/PHPExcel.php';

function Date_Mysql_for_View($date_in)//Входной формат MySql: гггг-мм-дд
	{
		$year = $date_in[0].$date_in[1].$date_in[2].$date_in[3];				//ГОД
		$month = $date_in[5].$date_in[6];										//МЕСЯЦ
		$day = $date_in[8].$date_in[9];											//ЧИСЛО
		$date_out = $day.".".$month.".".$year;
		return $date_out;
	}
	
function DateWiew_DateSQL($date_in)//Выходной формат MySql: гггг-мм-дд чч:мм:сс
	{
		$year = $date_in[6].$date_in[7].$date_in[8].$date_in[9];				//ГОД
		$month = $date_in[3].$date_in[4];										//МЕСЯЦ
		$day = $date_in[0].$date_in[1];											//ЧИСЛО
		
		$date_out = $year."-".$month."-".$day;
		return $date_out;
	}

if($_GET['action']=="to_excel")
{

		$file_name = "reports/nar_".$_GET['date'].".xls";

		//$objPHPExcel = PHPExcel_IOFactory::load("templates/form_report_isp.xls");
		$objReader = new PHPExcel_Reader_Excel5();
		$objPHPExcel = $objReader->load("templates/form_report_nar.xls");
		
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
		$aSheet->setCellValue('G1',$_GET['date']." г.");

		$rowId=8;//Первая строка для заполнения табличной части
		$i=1;//Начало нумерации

		$query = mysqli_query($db,"SELECT * FROM PERSONAL WHERE DATE_BEG<='".DateWiew_DateSQL($_GET['date'])."' AND DATE_END>='".DateWiew_DateSQL($_GET['date'])."' ORDER BY FAM ASC");
		$num_rows = mysqli_num_rows($query);
		//Записываем построчно
		while ($list = mysqli_fetch_assoc($query))
		{
			$query_prof = mysqli_query($db,"SELECT * FROM SPR_PROF WHERE ID = '".$list["ID_PROFES"]."'");
			$list_prof = mysqli_fetch_assoc($query_prof);
			
			$query_kateg = mysqli_query($db,"SELECT * FROM SPR_KATEG_PERS WHERE ID = '".$list["ID_KATEG_PERS"]."'");
			$list_kateg = mysqli_fetch_assoc($query_kateg);
			
			$query_graf = mysqli_query($db,"SELECT * FROM SPR_GRAF WHERE ID = '".$list["ID_GRAF"]."'");
			$list_graf = mysqli_fetch_assoc($query_graf);
			
			$query_podr = mysqli_query($db,"SELECT * FROM SPR_PODRAZD WHERE ID = '".$list["ID_PODRAZD"]."'");
			$list_podr = mysqli_fetch_assoc($query_podr);
			
			$query_prop = mysqli_query($db,"SELECT * FROM SPR_VID_PROP WHERE ID = '".$list["ID_PROPUSK"]."'");
			$list_prop = mysqli_fetch_assoc($query_prop);
			
			
			
			if($list["ID_SPR_OTR_BUX"]=="4")
			{
				$query_otr_bux = mysqli_query($db,"SELECT * FROM SPR_OTR_BUX WHERE ID IN(SELECT ID_SPR_OTR_BUX FROM SPR_PODRAZD WHERE ID='".$list['ID_PODRAZD']."')");
				$list_otr_bux = mysqli_fetch_assoc($query_otr_bux);
			}
			else
			{
				$query_otr_bux = mysqli_query($db,"SELECT * FROM SPR_OTR_BUX WHERE ID = '".$list["ID_SPR_OTR_BUX"]."'");
				$list_otr_bux = mysqli_fetch_assoc($query_otr_bux);
			}
			
			if($list['POL']=="m")$pol="Мужской"; else $pol="Женский";
			
			$query_obraz = mysqli_query($db,"SELECT * FROM SPR_VID_OBRAZ WHERE ID = '".$list["ID_SPR_VID_OBRAZ"]."'");
			$list_obraz = mysqli_fetch_assoc($query_obraz);
			
			$query_profile = mysqli_query($db,"SELECT * FROM SPR_PROFILES WHERE ID = '".$list["ID_PROFILE"]."'");
			$list_profile = mysqli_fetch_assoc($query_profile);
			
			$aSheet->setCellValue('A'.$rowId,$i);
			$aSheet->setCellValue('B'.$rowId,$list['TABN']);
			$aSheet->setCellValue('C'.$rowId,$list['FAM']);
			$aSheet->setCellValue('D'.$rowId,$list['NAME']);
			$aSheet->setCellValue('E'.$rowId,$list['OTCH']);
			$aSheet->setCellValue('F'.$rowId,$list_podr['KOD']);
			$aSheet->setCellValue('G'.$rowId,$list_podr['NAME']);
			$aSheet->setCellValue('H'.$rowId,$list_prof['NAME']);
			$aSheet->setCellValue('I'.$rowId,$list_kateg['NAME']);
			if($list['NENORM']=="1")$aSheet->setCellValue('J'.$rowId,"+");
			$aSheet->setCellValue('K'.$rowId,$list_prop['NAME']);
			$aSheet->setCellValue('L'.$rowId,$list_graf['KOD']);
			$aSheet->setCellValue('M'.$rowId,$list_graf['NAME']);
			$aSheet->setCellValue('N'.$rowId,$list['OKLAD']);
			$aSheet->setCellValue('O'.$rowId,$list['PROC_PREM']);
			$aSheet->setCellValue('P'.$rowId,$list['NADBAVKA']);
			
			$aSheet->setCellValue('Q'.$rowId,$list['DOPL_SOVM']);
			$aSheet->setCellValue('R'.$rowId,$list['PROC_DOPL_SECRET']);
			$aSheet->setCellValue('S'.$rowId,$list['PROC_DOPL_VRED']);
			$aSheet->setCellValue('T'.$rowId,$list['PROC_DOPL_KLASS']);
			$aSheet->setCellValue('U'.$rowId,$list['DOPL_MOLOD_SPEC']);
			$aSheet->setCellValue('V'.$rowId,$list['PROC_RK']);
			$aSheet->setCellValue('W'.$rowId,$list_otr_bux['KOD']);
			$aSheet->setCellValue('X'.$rowId,$list['STAVKA']);
			$aSheet->setCellValue('Y'.$rowId,$list['NUM_TD']);
			$aSheet->setCellValue('Z'.$rowId,Date_Mysql_for_View($list['DATE_TD']));
			$aSheet->setCellValue('AA'.$rowId,Date_Mysql_for_View($list['DATE_ROGD']));
			$aSheet->setCellValue('AB'.$rowId,$pol);
			$aSheet->setCellValue('AC'.$rowId,$list['SER_PASP']);
			$aSheet->setCellValue('AD'.$rowId,$list['NUM_PASP']);
			$aSheet->setCellValue('AE'.$rowId,$list['KEM_VID_PASP']);
			$aSheet->setCellValue('AF'.$rowId,Date_Mysql_for_View($list['DATE_VID_PASP']));
			$aSheet->setCellValue('AG'.$rowId,$list['KOD_PODR_PASP']);
			$aSheet->setCellValue('AH'.$rowId,$list['ADRES_PROPIS']);
			$aSheet->setCellValue('AI'.$rowId,$list_obraz['NAME']);
			$aSheet->setCellValue('AJ'.$rowId,Date_Mysql_for_View($list['DATE_END_OBRAZ']));
			$aSheet->setCellValue('AK'.$rowId,$list_profile['NAME']);
			
			if($list['DEKRET']=="1")$aSheet->setCellValue('AL'.$rowId,"+");
			if($list['UVOLEN']=="1")$aSheet->setCellValue('AM'.$rowId,"+");
			$aSheet->setCellValue('AN'.$rowId,Date_Mysql_for_View($list['DATE_BEG']));
			if($list['DATE_END']!="2100-01-01")$aSheet->setCellValue('AO'.$rowId,Date_Mysql_for_View($list['DATE_END']));

			if($i<$num_rows)$aSheet->insertNewRowBefore($rowId+1, 1);//Вставляем новую строку
			
			$i++;
			$rowId++;
		}

		$objPHPExcel->removeSheetByIndex(0);
		
		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		
		$objWriter->save($file_name);

}

?>