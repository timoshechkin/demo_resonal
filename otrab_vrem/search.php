<?php
header("Cache-Control: no-store, no-cache");//Запрет кэширования и сохранения истории
        //connect to database
        require_once("../../mysql.php");
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
	
		
		if ($_GET['listIdChild']!="") 
		{
			$queryPart = "ID_PODRAZD IN (".$_GET['idSel'].",".$_GET['listIdChild'].")";
		} 
		else 
		{
			$queryPart = "ID_PODRAZD = '".$_GET['idSel']."'";
		}
		
		$query = mysqli_query($db, "SELECT * FROM PERSONAL WHERE ".$queryPart." AND LOCATE('".$_GET['str_fam']."',FAM)='1' AND DATE_END='2100-01-01'");
		$list = mysqli_fetch_assoc($query);
		echo ("|".$list['ID']);
?>