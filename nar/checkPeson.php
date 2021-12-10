<?php
//session_start();
//connect to database
require_once("../../mysql.php");
require_once ("../../PHPExcel/Classes/PHPExcel.php");

function Date_Mysql_for_View($date_in)//Входной формат MySql: гггг-мм-дд
	{
		$year = $date_in[0].$date_in[1].$date_in[2].$date_in[3];				//ГОД
		$month = $date_in[5].$date_in[6];										//МЕСЯЦ
		$day = $date_in[8].$date_in[9];											//ЧИСЛО
		$date_out = $day.".".$month.".".$year;
		return $date_out;
	}
function Date_Convert_for_Mysql($date_in)//Входной формат MySql: дд.мм.гггг
	{
		$year = $date_in[6].$date_in[7].$date_in[8].$date_in[9];				//ГОД
		$month = $date_in[3].$date_in[4];										//МЕСЯЦ
		$day = $date_in[0].$date_in[1];											//ЧИСЛО
		$date_out = $year."-".$month."-".$day;
		return $date_out;
	}

		//$query = mysqli_query($db,"SELECT * FROM PERSONAL WHERE DATE_BEG <='".Date_Convert_for_Mysql($_GET['date'])."' AND DATE_END >='".Date_Convert_for_Mysql($_GET['date'])."' AND UVOLEN<>'1' AND DEKRET<>'1' ORDER BY FAM ASC ");
		//$list = mysqli_fetch_assoc($query);
			
$dir = "/var/www/html/";											//Путь для временной загрузки файла

$file_load = $dir."temp_".basename($_FILES['file']['name']);		//Новое полное имя загруженного файла


if(file_exists($file_load))unlink($file_load);						//Проверяем на существование файла с таким же именем и если есть, то удаляем
move_uploaded_file($_FILES['file']['tmp_name'],$file_load); 		//Сохраняем файл в каталоге $dir
	

$path_info = pathinfo($_FILES['file']['name']);						//Определяем расширение файла

if($path_info['extension']=="xls")									//Проверка для списка в формате XLS
	{
		$objPHPExcel = PHPExcel_IOFactory::load($file_load);
		$objPHPExcel->setActiveSheetIndex(0);
		$aSheet = $objPHPExcel->getActiveSheet();
		
		//Перебираем строки файла начиная с 5-ой строки
		
		for($row_ind=5; $row_ind<400; $row_ind++)
		{
			//Если ячейка пустая, то прекращаем перебор
			if($aSheet->getCellByColumnAndRow(0,$row_ind)->getValue()=="")break;
			
			$tabn =				$aSheet->getCellByColumnAndRow(1,$row_ind)->getValue();
			$fio =				$aSheet->getCellByColumnAndRow(2,$row_ind)->getValue();
			$kod_podr =			$aSheet->getCellByColumnAndRow(3,$row_ind)->getValue();
			$kod_prof =			$aSheet->getCellByColumnAndRow(4,$row_ind)->getValue();
			$kateg =			$aSheet->getCellByColumnAndRow(5,$row_ind)->getValue();
			$trud_dog =			$aSheet->getCellByColumnAndRow(6,$row_ind)->getValue();
			$date_trud_dog =	$aSheet->getCellByColumnAndRow(7,$row_ind)->getValue();
			$date_rogd =		$aSheet->getCellByColumnAndRow(8,$row_ind)->getValue();
			$pol =				$aSheet->getCellByColumnAndRow(9,$row_ind)->getValue();
			$ser_pasp =			$aSheet->getCellByColumnAndRow(10,$row_ind)->getValue();
			$num_pasp =			$aSheet->getCellByColumnAndRow(11,$row_ind)->getValue();
			$kem_vid_pasp =		$aSheet->getCellByColumnAndRow(12,$row_ind)->getValue();
			$date_vid_pasp =	$aSheet->getCellByColumnAndRow(13,$row_ind)->getValue();
			$kod_podr_pasp =	$aSheet->getCellByColumnAndRow(14,$row_ind)->getValue();
			$adres =			$aSheet->getCellByColumnAndRow(15,$row_ind)->getValue();
			$stavka =			$aSheet->getCellByColumnAndRow(16,$row_ind)->getValue();
			$oklad =			$aSheet->getCellByColumnAndRow(17,$row_ind)->getValue();
			
			//echo $row_ind."_".$tabn."_".$fio."_".$kod_podr."_".$kod_prof."_".$kateg."_".$oklad."<br>";
			
			$query_pers = mysqli_query($db,"SELECT * FROM PERSONAL WHERE TABN='0".str_replace(" ","",$tabn)."' AND DATE_BEG <='".Date_Convert_for_Mysql($_POST['date'])."' AND DATE_END >='".Date_Convert_for_Mysql($_POST['date'])."' AND UVOLEN<>'1' AND DEKRET<>'1' ORDER BY FAM ASC ");
			$list_pers = mysqli_fetch_assoc($query_pers);
			
			$query_prof = mysqli_query($db,"SELECT * FROM SPR_PROF WHERE ID = '".$list_pers["ID_PROFES"]."'");
			$list_prof = mysqli_fetch_assoc($query_prof);
			
			$query_kateg = mysqli_query($db,"SELECT * FROM SPR_KATEG_PERS WHERE ID = '".$list_pers["ID_KATEG_PERS"]."'");
			$list_kateg = mysqli_fetch_assoc($query_kateg);
			
			$query_graf = mysqli_query($db,"SELECT * FROM SPR_GRAF WHERE ID = '".$list_pers["ID_GRAF"]."'");
			$list_graf = mysqli_fetch_assoc($query_graf);
			
			$query_podr = mysqli_query($db,"SELECT * FROM SPR_PODRAZD WHERE ID = '".$list_pers["ID_PODRAZD"]."'");
			$list_podr = mysqli_fetch_assoc($query_podr);
			
			$query_prop = mysqli_query($db,"SELECT * FROM SPR_VID_PROP WHERE ID = '".$list_pers["ID_PROPUSK"]."'");
			$list_prop = mysqli_fetch_assoc($query_prop);
			
			if($pol=="Мужской") $pol="m"; else if($pol=="Женский") $pol="w";
			
			
			if(mysqli_num_rows($query_pers)==0)
			{
				echo "<font size='2'>Сотрудник с таб. №0".str_replace(" ","",$tabn)." не найден в СУРВ!</font><br>";
			}
			else
			{
				if(str_replace(" ","",$fio) != $list_pers['FAM'].$list_pers['NAME'].$list_pers['OTCH'])	echo "<font size='2'>Ошибка в ФИО сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if(number_format($kod_podr,0,"","") != $list_podr['KOD'])								echo "<font size='2'>Ошибка в коде подразделения сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if(str_replace(" ","",$kod_prof) != $list_prof['KOD'])									echo "<font size='2'>Ошибка в должности сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if(str_replace(" ","",$kateg) != $list_kateg['NAME'])									echo "<font size='2'>Ошибка в категории сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if(str_replace(" ","",$trud_dog) != $list_pers['NUM_TD'])								echo "<font size='2'>Ошибка в № трудового договора сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if($date_trud_dog != Date_Mysql_for_View($list_pers['DATE_TD']))						echo "<font size='2'>Ошибка в дате трудового договора сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if($date_rogd != Date_Mysql_for_View($list_pers['DATE_ROGD']))							echo "<font size='2'>Ошибка в дате рождения сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if($pol != $list_pers['POL'])															echo "<font size='2'>Ошибка в поле сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if(str_replace(" ","",$ser_pasp) != str_replace(" ","",$list_pers['SER_PASP']))			echo "<font size='2'>Ошибка в серии паспорта сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if(str_replace(" ","",$num_pasp) != $list_pers['NUM_PASP'])								echo "<font size='2'>Ошибка в номере паспорта сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if($kem_vid_pasp != $list_pers['KEM_VID_PASP'])											echo "<font size='2'>Ошибка в информации о выдаче паспорта сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if($date_vid_pasp != Date_Mysql_for_View($list_pers['DATE_VID_PASP']))					echo "<font size='2'>Ошибка в дате выдачи паспорта сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if($kod_podr_pasp != $list_pers['KOD_PODR_PASP'])										echo "<font size='2'>Ошибка в коде подразделения паспорта сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if($adres != $list_pers['ADRES_PROPIS'])												echo "<font size='2'>Ошибка в адресе сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if(number_format(str_replace(",",".",$stavka),2,".","") != $list_pers['STAVKA'])		echo "<font size='2'>Ошибка в ставке сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
				if(number_format($oklad,0,"","") != $list_pers['OKLAD'])								echo "<font size='2'>Ошибка в окладе сотрудника с таб. №0".str_replace(" ","",$tabn)."!</font><br>";
			}
			
		}
		echo "<font size='2'>Проверка завершена!</font><br>";
		unlink($file_load);											//Удаляем файл

	}


?>