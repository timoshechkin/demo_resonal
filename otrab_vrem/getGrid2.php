<?php
//session_start();
error_reporting(E_ALL ^ E_NOTICE);
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
  		header("Content-type: application/xhtml+xml"); } else {
  		header("Content-type: text/xml");
	}
	echo("<?xml version='1.0' encoding='UTF-8'?>\n");
//connect to database
require_once("../../mysql.php");

$query_font = mysqli_query($db,"SELECT * FROM USER_SETTINGS WHERE IP='".$_SERVER['REMOTE_USER']."'");
$list_font = mysqli_fetch_assoc($query_font);

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

function getStyle($type)
	{
		if($type=="RED_V") $style = " color:red; border-bottom: 2px solid #00CC00; ";
		if($type=="RED_R") $style = " color:red; ";
		if($type=="ORANGE_V") $style = " color:orange; border-bottom: 2px solid #00CC00; ";
		if($type=="ORANGE_R") $style = " color:orange; ";
		if($type=="BLUE_V") $style = " color:blue; border-bottom: 2px solid #00CC00; ";
		if($type=="BLUE_R") $style = " color:blue; ";
		if($type=="BLACK_V") $style = " color:black; border-bottom: 2px solid #00CC00; ";
		if($type=="BLACK_R") $style = " color:black; ";
		if($type=="GREEN_V") $style = " color:#009933; border-bottom: 2px solid #00CC00; ";
		if($type=="GREEN_R") $style = " color:#009933; ";
		return $style;
	}
	
	//Определяем доступ к подразделениям
	$query_pers_profile = mysqli_query($db, "SELECT * FROM PERSONAL WHERE IP='".$_SERVER['REMOTE_USER']."' AND DATE_END='2100-01-01'");
	$list_pers_profile = mysqli_fetch_assoc($query_pers_profile);
	
	if(mysqli_num_rows($query_pers_profile)>0 && ($list_pers_profile['ID_PROFILE']=="2" || $list_pers_profile['ID_PROFILE']=="3"))		$query_part_select = " AND ID_PODRAZD IN (".$list_pers_profile['LIST_PODR_DRIVE'].") OR TABN='".$list_pers_profile['TABN']."' ";
	else if(mysqli_num_rows($query_pers_profile)>0 && $list_pers_profile['ID_PROFILE']=="1")											$query_part_select = " AND TABN='".$list_pers_profile['TABN']."'";
	else if(mysqli_num_rows($query_pers_profile)>0 && ($list_pers_profile['ID_PROFILE']=="4" || $list_pers_profile['ID_PROFILE']=="5"))	$query_part_select = "";
	else 																																$query_part_select = " AND TABN=''";
	
	if ($_GET['listIdChild']!="") 
	{
		$queryPart = "ID_PODRAZD IN (".$_GET['idSel'].",".$_GET['listIdChild'].")";
	} 
	else 
	{
		$queryPart = "ID_PODRAZD = '".$_GET['idSel']."'";
	}
	
	if (strlen($_GET['month'])==1){$month1="0".$_GET['month']; $month2="0".($_GET['month']+1);} else {$month1=$_GET['month'];$month2=($_GET['month']+1);}
	$date1=$_GET['year']."-".$month1."-01";

	$query_date2 = mysqli_query($db,"SELECT DAYOFMONTH(SUBDATE('".$_GET['year']."-".$month2."-01',INTERVAL 1 DAY))"); //Получаем число последнего дня месяца
	$res_date2 = mysqli_fetch_array($query_date2);
	$date2=$_GET['year']."-".$month1."-".$res_date2[0];
	
?>
<rows>
<?php

	$query = mysqli_query($db,"SELECT * FROM OTRAB_VREM WHERE YEAR='".$_GET['year']."' AND MONTH='".$_GET['month']."' AND TABN IN(SELECT TABN FROM PERSONAL WHERE ".$queryPart." ".$query_part_select." AND ((DATE_BEG <= '".$date1."' AND DATE_END >= '".$date2."') OR (DATE_BEG >= '".$date1."' AND DATE_BEG <= '".$date2."') OR (DATE_END >= '".$date1."' AND DATE_END <= '".$date2."')) AND UVOLEN <> '1' AND DEKRET <> '1' ORDER BY TABN ASC)");

	while ($list = mysqli_fetch_assoc($query))
		{
			if($list_font['GRID_SKIN']=="gray") $style_col_fix = "background-color:#d8d8d8;"; else $style_col_fix = "";
			
			$query_pers = mysqli_query($db,"SELECT * FROM PERSONAL WHERE TABN='".$list['TABN']."' AND ((DATE_BEG <= '".$date1."' AND DATE_END >= '".$date2."') OR (DATE_BEG <= '".$date1."' AND DATE_END >= '".$date1."' AND DATE_END <= '".$date2."' ) OR (DATE_BEG >= '".$date1."' AND DATE_BEG <= '".$date2."' AND DATE_END >= '".$date2."' ))");
			$list_pers = mysqli_fetch_assoc($query_pers);
			
			$query_fam_lower = mysqli_query($db,"SELECT CONCAT(LEFT('".$list_pers['FAM']."',1),LOWER(RIGHT('".$list_pers['FAM']."',LENGTH('".$list_pers['FAM']."')/2-1)))");
			$list_fam_lower = mysqli_fetch_array($query_fam_lower);
			
			$query_prof = mysqli_query($db,"SELECT * FROM SPR_PROF WHERE ID = '".$list_pers['ID_PROFES']."'");
			$list_prof = mysqli_fetch_assoc($query_prof);
			
			$query_podr = mysqli_query($db,"SELECT * FROM SPR_PODRAZD WHERE ID = '".$list_pers['ID_PODRAZD']."'");
			$list_podr = mysqli_fetch_assoc($query_podr);
			
			$total_time = explode("|",$list['TOTAL']);
			
			print("<row id='".$list["TABN"]."'>");
				print("<cell type='ch' style='".$style_col_fix." font-family:".$list_font['GRID_FONT_FAMILY']."; font-size:".$list_font['GRID_FONT_SIZE']."px;' >");
					print("");
				print("</cell>");
				print("<cell type='ro' style='".$style_col_fix." font-family:".$list_font['GRID_FONT_FAMILY']."; font-size:".$list_font['GRID_FONT_SIZE']."px;' >");
					print($list_pers['TABN']);
				print("</cell>");
				print("<cell type='ro' style='".$style_col_fix." font-family:".$list_font['GRID_FONT_FAMILY']."; font-size:".$list_font['GRID_FONT_SIZE']."px;' >");
					print($list_fam_lower[0]." ".substr($list_pers['NAME'],0,2).".".substr($list_pers['OTCH'],0,2).".");
				print("</cell>");
				print("<cell type='ro' style='".$style_col_fix." font-family:".$list_font['GRID_FONT_FAMILY']."; font-size:".$list_font['GRID_FONT_SIZE']."px;' >");
					print($list_podr['SOKR_NAME_RU']);
				print("</cell>");
				print("<cell type='ro' style='".$style_col_fix." font-family:".$list_font['GRID_FONT_FAMILY']."; font-size:".$list_font['GRID_FONT_SIZE']."px;' >");
					print("<![CDATA[");
						if($total_time[0]!="")print($total_time[0]); else print("-");
					print("<br>");//Перенос строки
						if($total_time[1]!="")print($total_time[1]); else print("-");
					print("<br>");//Перенос строки
						if($total_time[2]!="")print($total_time[2]); else print("-");
					print("]]>");
				print("</cell>");
			
				for($i=1; $i<32; $i++)	
				{
					//$query_work_out = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE TABN='".$list_pers['TABN']."' AND YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."' AND DAY(DATE_TIME_BEG)='".$i."'");
					//if(mysqli_num_rows($query_work_out)>0)$background=" background-color:#d8d8d8; "; else $background="";
					
					$cell = explode("|",$list[$i]);
					print("<cell type='ro' style=' ".getStyle($cell[3])." font-family:".$list_font['GRID_FONT_FAMILY']."; font-size:".$list_font['GRID_FONT_SIZE']."px;'  >");
						print("<![CDATA[");
							if($cell[0]!="")print($cell[0]); else print("-");//Признак или статус дня
						print("<br>");//Перенос строки
							if($cell[1]!="")print($cell[1]); else print("-");//Урочно отработанное время
						print("<br>");//Перенос строки
							if($cell[2]!="")print($cell[2]); else print("-");//Время отработанное сверхурочно или в выходные и нерабочие праздничные дни
						print("]]>");
					print("</cell>");
				}
			print("</row>");
		}

?>
</rows>