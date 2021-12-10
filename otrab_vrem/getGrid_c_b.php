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
	
	$query_security = mysqli_query($db,"SELECT * FROM PERSONAL WHERE IP='".$_SERVER['REMOTE_USER']."' AND DATE_END='2100-01-01'");
	$list_security = mysqli_fetch_assoc($query_security);
	
	if($_GET['day']>9)$day = "0".$_GET['day']; else $day = $_GET['day'];
	if($_GET['month']>9)$month = "0".$_GET['month']; else $month = $_GET['month'];
	
?>
<rows>
<?php
	
	$query_in_out = mysqli_query($db,"SELECT * FROM IN_OUT WHERE TABN='".$_GET['tabn']."' AND ((YEAR(DATE_IN)='".$_GET['year']."' AND MONTH(DATE_IN)='".$_GET['month']."' AND DAY(DATE_IN)='".$_GET['day']."') OR (YEAR(DATE_OUT)='".$_GET['year']."' AND MONTH(DATE_OUT)='".$_GET['month']."' AND DAY(DATE_OUT)='".$_GET['day']."'))");
	
	/*
	$query_vxod = mysqli_query($db,"SELECT * FROM ARXPROX WHERE TABNO = '".$_GET['tabn']."' AND YEAR(LOGDATE)='".$_GET['year']."' AND MONTH(LOGDATE)='".$_GET['month']."' AND DAY(LOGDATE)='".$_GET['day']."' AND ACTION='1' AND ERROR=''");
	$query_vixod = mysqli_query($db,"SELECT * FROM ARXPROX WHERE TABNO = '".$_GET['tabn']."' AND YEAR(LOGDATE)='".$_GET['year']."' AND MONTH(LOGDATE)='".$_GET['month']."' AND DAY(LOGDATE)='".$_GET['day']."' AND ACTION='2' AND ERROR=''");
	*/
	$query_pers = mysqli_query($db, "SELECT * FROM PERSONAL WHERE TABN='".$_GET['tabn']."' AND DATE_END=(SELECT MAX(DATE_END) FROM PERSONAL WHERE TABN='".$_GET['tabn']."')");
	$list_pers = mysqli_fetch_assoc($query_pers);
	/*
	$query_rejim_in_day = mysqli_query($db,"SELECT * FROM SPR_GRAF_DNI WHERE ID_GRAF='".$list_pers['ID_GRAF']."' AND DATE='".$_GET['year']."-".$month."-".$day."' ");
	$list_rejim_in_day = mysqli_fetch_assoc($query_rejim_in_day);
	
	$query_rejim_in_mextday = mysqli_query($db,"SELECT * FROM SPR_GRAF_DNI WHERE ID_GRAF='".$list_pers['ID_GRAF']."' AND DATE=SUBDATE('".$_GET['year']."-".$month."-".$day."',INTERVAL -1 DAY) ");
	$list_rejim_in_mextday = mysqli_fetch_assoc($query_rejim_in_mextday);
	*/
	
	if($list_security['ID_PROFILE']=="4" || $list_security['ID_PROFILE']=="5") $type_cell="ed"; else $type_cell="ro";
	while ($list_in_out = mysqli_fetch_assoc($query_in_out))
		{
			if(strpos($list_in_out['COMMENTS'],"ERROR")!==false) $color="color:red;";
			else if(strpos($list_in_out['COMMENTS'],"EDIT_USER")!==false) $color="color:blue;";
			else $color="";
			
			print("<row id='".$list_in_out["ID"]."' style='".$color." font-family:".$list_font['GRID_FONT_FAMILY']."; font-size:".$list_font['GRID_FONT_SIZE']."px;' >");
			print("<cell type='".$type_cell."'>");
				print(Date_Mysql_for_View($list_in_out["DATE_IN"]));
            print("</cell>");
			print("<cell type='".$type_cell."'>");
				print($list_in_out["TIME_IN"]);
            print("</cell>");
			print("<cell type='ro'>");
				print($list_in_out["PROH_IN"]);
            print("</cell>");
			print("<cell type='".$type_cell."'>");
				print(Date_Mysql_for_View($list_in_out["DATE_OUT"]));
            print("</cell>");
			print("<cell type='".$type_cell."'>");
				print($list_in_out["TIME_OUT"]);
            print("</cell>");
			print("<cell type='ro'>");
				print($list_in_out["PROH_OUT"]);
            print("</cell>");
			print("</row>");
		}

?>
</rows>