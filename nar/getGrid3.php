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

			
	if(isset($_GET['id'])) $query = mysqli_query($db,"SELECT * FROM PERSONAL WHERE TABN=(SELECT TABN FROM PERSONAL WHERE ID='".$_GET['id']."') ORDER BY DATE_BEG");
	if(isset($_GET['tabn'])) $query = mysqli_query($db,"SELECT * FROM PERSONAL WHERE TABN='".$_GET['tabn']."' ORDER BY DATE_BEG");
	
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
			
			/*if($list_graf['SUMM_UCH']=="0")
			{
				$query_stavka = mysqli_query($db,"SELECT ROUND((SELECT (SUM(TIME_TO_SEC(TIMEDIFF(WKS,WNS))-TIME_TO_SEC(TIMEDIFF(WKO,WNO)))/60/60) FROM SPR_GRAF_DNI WHERE ID_GRAF='".$list["ID_GRAF"]."' AND STATUS IN('R','PP') AND YEAR(DATE)=YEAR('".Date_Convert_for_Mysql($_GET['date'])."'))/(SUM(TIME_TO_SEC(TIMEDIFF(WKS,WNS))-TIME_TO_SEC(TIMEDIFF(WKO,WNO)))/60/60),2) FROM SPR_GRAF_DNI WHERE ID_GRAF='1' AND STATUS IN('R','PP') AND YEAR(DATE)=YEAR('".Date_Convert_for_Mysql($_GET['date'])."') ");
				$list_stavka = mysqli_fetch_array($query_stavka);
			}*/
			
			if($list["ID"]==$_GET['id']) $selected="1"; else $selected="";
			
			print("<row id='".$list["ID"]."' selected='".$selected."' style='font-family:".$list_font['GRID_FONT_FAMILY']."; font-size:".$list_font['GRID_FONT_SIZE']."px;' >");
				print("<cell>");
                    print($list["TABN"]);
                print("</cell>");
				print("<cell>");
                    print($list["FAM"]);
                print("</cell>");
				print("<cell>");
                    print($list["NAME"]);
                print("</cell>");
				print("<cell>");
                    print($list["OTCH"]);
                print("</cell>");
				print("<cell>");
                    print($list_podr["SOKR_NAME_RU"]);
                print("</cell>");
				print("<cell>");
                    print($list_prof["NAME"]);
                print("</cell>");
				print("<cell>");
                    //if($list_graf['SUMM_UCH']=="1") print($list_graf['K_NORM']); else print($list_stavka[0]);
					print($list["STAVKA"]);
                print("</cell>");
				print("<cell>");
                    print($list_kateg["NAME"]);
                print("</cell>");
				print("<cell>");
                    print($list_prop["NAME"]);
                print("</cell>");
				print("<cell>");
					if(mysqli_num_rows($query_graf)>0)print("(".$list_graf['KOD'].") ".$list_graf['NAME']); else print("");
                print("</cell>");
				print("<cell>");
                    if($list["UVOLEN"]=="1") print("dhtmlxSuite/dhtmlxGrid/codebase/imgs/results.gif"); else print("dhtmlxSuite/dhtmlxGrid/codebase/imgs/blank.gif");
                print("</cell>");
				print("<cell>");
                    if($list["DEKRET"]=="1") print("dhtmlxSuite/dhtmlxGrid/codebase/imgs/results.gif"); else print("dhtmlxSuite/dhtmlxGrid/codebase/imgs/blank.gif");
                print("</cell>");
				print("<cell>");
                    print(Date_Mysql_for_View($list["DATE_BEG"]));
                print("</cell>");
				print("<cell>");
                    if($list["DATE_END"]=="2100-01-01") print(""); else print(Date_Mysql_for_View($list["DATE_END"]));
                print("</cell>");
             print("</row>");
			}

?>
</rows>