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
	
	
	if($_GET['day']>9)$day = "0".$_GET['day']; else $day = $_GET['day'];
	if($_GET['month']>9)$month = "0".$_GET['month']; else $month = $_GET['month'];
	
?>
<rows>
<?php

	$query_work_out = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$_GET['day']."' ORDER BY DATE_TIME_BEG ASC");

	$query_pers = mysqli_query($db, "SELECT * FROM PERSONAL WHERE TABN='".$_GET['tabn']."' AND DATE_END=(SELECT MAX(DATE_END) FROM PERSONAL WHERE TABN='".$_GET['tabn']."')");
	$list_pers = mysqli_fetch_assoc($query_pers);
		
	while ($list_work_out = mysqli_fetch_assoc($query_work_out))
		{		
			$query_period_day = mysqli_query($db, "SELECT * FROM SPR_PERIOD_DAY WHERE ID='".$list_work_out['ID_PERIOD_DAY']."' ");
			$list_period_day = mysqli_fetch_assoc($query_period_day);
			
			$query_vid_isp_vrem = mysqli_query($db, "SELECT * FROM SPR_VID_ISP_VREM WHERE ID='".$list_work_out['ID_VID_ISP_VREM']."' ");
			$list_vid_isp_vrem = mysqli_fetch_assoc($query_vid_isp_vrem);
			
			$query_hours = mysqli_query($db, "SELECT ROUND(TIME_TO_SEC('".$list_work_out['PERIOD_TIME']."')/60/60,2)");
			$list_hours = mysqli_fetch_array($query_hours);
			
			
			if($list_work_out['ID_VID_ISP_VREM']=="8" || $list_work_out['ID_VID_ISP_VREM']=="9" || $list_work_out['ID_VID_ISP_VREM']=="10" || $list_work_out['ID_VID_ISP_VREM']=="11") $font = " color:red;";
			else if($list_work_out['ID_VID_ISP_VREM']=="2" || $list_work_out['ID_VID_ISP_VREM']=="16" || $list_work_out['ID_VID_ISP_VREM']=="27" || $list_work_out['ID_VID_ISP_VREM']=="28" || $list_work_out['ID_VID_ISP_VREM']=="7" || $list_work_out['ID_VID_ISP_VREM']=="24") $font = " color:orange;";
			else if($list_work_out['ID_WORK_OUT_EDIT']<>"") $font = " color:blue;";
			else $font = " color:black;";
			
			print("<row id='".$list_work_out['ID']."' style=' ".$font." font-family:".$list_font['GRID_FONT_FAMILY']."; font-size:".$list_font['GRID_FONT_SIZE']."px;' >");
				print("<cell type='img'>");
					if($list_work_out['ID_WORK_OUT_EDIT']<>"")print("dhtmlxSuite/dhtmlxGrid/codebase/imgs/ar_right.gif"); else print("dhtmlxSuite/dhtmlxGrid/codebase/imgs/blank.gif");
				print("</cell>");
				print("<cell type='ro'>");
					print(Date_Mysql_for_View(substr($list_work_out['DATE_TIME_BEG'],0,10)));
				print("</cell>");
				print("<cell type='ro'>");
					print(substr($list_work_out['DATE_TIME_BEG'],11,8));
				print("</cell>");
				print("<cell type='ro'>");
					print(Date_Mysql_for_View(substr($list_work_out['DATE_TIME_END'],0,10)));
				print("</cell>");
				print("<cell type='ro'>");
					print(substr($list_work_out['DATE_TIME_END'],11,8));
				print("</cell>");
				
				if($list_work_out['ID_VID_ISP_VREM']=="7" || $list_work_out['ID_VID_ISP_VREM']=="24" || $list_work_out['ID_VID_ISP_VREM']=="32") print("<cell type='ed'>");
				else print("<cell type='ro'>");

					print($list_work_out['PERIOD_TIME']);
				print("</cell>");
				print("<cell type='ro'>");
					print($list_hours[0]);
				print("</cell>");
				print("<cell type='ro'>");
					print($list_period_day['NAME']);
				print("</cell>");
				print("<cell type='ro'>");
					print($list_vid_isp_vrem['NAME']);
				print("</cell>");
			print("</row>");
		}

?>
</rows>