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

?>
<rows>
<?php

			
		$query = mysqli_query($db,"SELECT * FROM PERSONAL WHERE DATE_BEG <='".Date_Convert_for_Mysql($_GET['date'])."' AND DATE_END >='".Date_Convert_for_Mysql($_GET['date'])."' AND UVOLEN<>'1' ORDER BY FAM ASC ");
		while ($list = mysqli_fetch_assoc($query))
		{
			$query_fam_lower = mysqli_query($db,"SELECT CONCAT(LEFT('".$list['FAM']."',1),LOWER(RIGHT('".$list['FAM']."',LENGTH('".$list['FAM']."')/2-1)))");
			$list_fam_lower = mysqli_fetch_array($query_fam_lower);
			
			$query_prof = mysqli_query($db,"SELECT * FROM SPR_PROF WHERE ID = '".$list["ID_PROFES"]."'");
			$list_prof = mysqli_fetch_assoc($query_prof);
			
			$query_podr = mysqli_query($db,"SELECT * FROM SPR_PODRAZD WHERE ID = '".$list["ID_PODRAZD"]."'");
			$list_podr = mysqli_fetch_assoc($query_podr);
			
			if($list['DATE_END']=="2100-01-01")
			{
				$font_color="";
				$type="";
			}
			else
			{
				$font_color=" color:red; ";
				$type="type='ro'";
			}
			
			print("<row id='".$list['ID']."' style='".$font_color."font-family:".$list_font['GRID_FONT_FAMILY']."; font-size:".$list_font['GRID_FONT_SIZE']."px;' >");
				print("<cell ".$type.">");
                    print("");
                print("</cell>");
				print("<cell>");
                    print($list['TABN']);
                print("</cell>");
				print("<cell>");
                    print($list_fam_lower[0]." ".substr($list['NAME'],0,2).".".substr($list['OTCH'],0,2).".");
                print("</cell>");
				print("<cell>");
                    print($list_podr['SOKR_NAME_RU']);
                print("</cell>");
				print("<cell>");
                    print($list_prof['NAME']);
                print("</cell>");
				print("<cell>");
                    print(Date_Mysql_for_View($list['DATE_BEG']));
                print("</cell>");
				print("<cell>");
                    if($list['DATE_END']=="2100-01-01") print(""); else print(Date_Mysql_for_View($list['DATE_END']));
                print("</cell>");
				
				if($list['DATE_END']=="2100-01-01")
				{
					print("<cell>");
						print($list['OSN_TD']);
					print("</cell>");
					print("<cell>");
						print($list['OSN_TD']);
					print("</cell>");
				}
				else
				{
					print("<cell type='img'>");
						if($list['OSN_TD']=="1")print("../../dhtmlxSuite/dhtmlxGrid/codebase/imgs/item_chk1_dis.gif"); else print("../../dhtmlxSuite/dhtmlxGrid/codebase/imgs/item_chk0_dis.gif");
					print("</cell>");
					print("<cell type='ro'>");
						print("");
					print("</cell>");
				}
				
				print("<cell>");
                    print($list['OKLAD']);
                print("</cell>");
				print("<cell ".$type.">");
                    if($list['DATE_END']=="2100-01-01")print($list['OKLAD']);
                print("</cell>");
				print("<cell>");
                    print($list['PROC_PREM']);
                print("</cell>");
				print("<cell ".$type.">");
                    if($list['DATE_END']=="2100-01-01")print($list['PROC_PREM']);
                print("</cell>");
				print("<cell>");
                    print($list['NADBAVKA']);
                print("</cell>");
				print("<cell ".$type.">");
                    if($list['DATE_END']=="2100-01-01")print($list['NADBAVKA']);
                print("</cell>");
				print("<cell>");
                    print($list['DOPL_SOVM']);
                print("</cell>");
				print("<cell ".$type.">");
                    if($list['DATE_END']=="2100-01-01")print($list['DOPL_SOVM']);
                print("</cell>");
				print("<cell>");
                    print($list['PROC_DOPL_SECRET']);
                print("</cell>");
				print("<cell ".$type.">");
                    if($list['DATE_END']=="2100-01-01")print($list['PROC_DOPL_SECRET']);
                print("</cell>");
				print("<cell>");
                    print($list['PROC_DOPL_VRED']);
                print("</cell>");
				print("<cell ".$type.">");
                    if($list['DATE_END']=="2100-01-01")print($list['PROC_DOPL_VRED']);
                print("</cell>");
				print("<cell>");
                    print($list['PROC_DOPL_KLASS']);
                print("</cell>");
				print("<cell ".$type.">");
                    if($list['DATE_END']=="2100-01-01")print($list['PROC_DOPL_KLASS']);
                print("</cell>");
				print("<cell>");
                    print($list['DOPL_MOLOD_SPEC']);
                print("</cell>");
				print("<cell ".$type.">");
                    if($list['DATE_END']=="2100-01-01")print($list['DOPL_MOLOD_SPEC']);
                print("</cell>");
				print("<cell>");
                    print($list['PROC_RK']);
                print("</cell>");
				print("<cell ".$type.">");
                    if($list['DATE_END']=="2100-01-01")print($list['PROC_RK']);
                print("</cell>");
             print("</row>");
		}

?>
</rows>