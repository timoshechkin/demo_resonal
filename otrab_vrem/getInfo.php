<?php
//session_start();
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
function getStyle($type)
	{
		if($type=="RED_V") $style = " color:red; border-bottom: 2px solid #00CC00; ";
		if($type=="RED_R") $style = " color:red; ";
		if($type=="BLUE_V") $style = " color:blue; border-bottom: 2px solid #00CC00; ";
		if($type=="BLUE_R") $style = " color:blue; ";
		if($type=="BLACK_V") $style = " color:black; border-bottom: 2px solid #00CC00; ";
		if($type=="BLACK_R") $style = " color:black; ";
		if($type=="GREEN_V") $style = " color:#009933; font-weight:bold; border-bottom: 2px solid #00CC00; ";
		if($type=="GREEN_R") $style = " color:#009933; font-weight:bold; ";
		return $style;
	}

$query_font = mysqli_query($db,"SELECT * FROM USER_SETTINGS WHERE IP='".$_SERVER['REMOTE_USER']."'");
$list_font = mysqli_fetch_assoc($query_font);


if($_GET['action']=="get_day_end")
{
	$query_info = mysqli_query($db,"SELECT DAY(MAX(DATE_IN)) FROM IN_OUT WHERE YEAR(DATE_IN)='".$_GET['year']."' AND MONTH(DATE_IN)='".$_GET['month']."' ");
	$list_info = mysqli_fetch_array($query_info);
	echo "|".$list_info[0];
}

if($_GET['action']=="check_modul_status")
{
	$query_info = mysqli_query($db,"SELECT (SELECT STATUS FROM STATUS_PROCESS WHERE PROCESS='CREATE_OTRAB_VREM') AS OTRAB_VREM,(SELECT STATUS FROM STATUS_PROCESS WHERE PROCESS='UPDATE_SVOD_OTRAB_VREM') AS SVOD ");
	$list_info = mysqli_fetch_assoc($query_info);
	echo "|".$list_info['OTRAB_VREM']."|".$list_info['SVOD'];
}
	
if($_GET['action']=="update_cell")
{
				if($_GET['month']<10) $month="0".$_GET['month']; else $month=$_GET['month'];
				if($_GET['day']<10) $day="0".$_GET['day']; else $day=$_GET['day'];
	
				$query_info = mysqli_query($db,"SELECT * FROM PERSONAL WHERE TABN='".$_GET['tabn']."' AND DATE_END>='".$_GET['year']."-".$month."-".$day."' AND DATE_BEG<='".$_GET['year']."-".$month."-".$day."' ");
				$list_info = mysqli_fetch_assoc($query_info);
				
				
				//Режим дня у сотрудника
				$query_rejim_in_day = mysqli_query($db,"SELECT * FROM SPR_GRAF_DNI WHERE ID_GRAF='".$list_info['ID_GRAF']."' AND YEAR(DATE)='".$_GET['year']."' AND MONTH(DATE)='".$_GET['month']."' AND DAY(DATE)='".$_GET['day']."' ");
				$list_rejim_in_day = mysqli_fetch_assoc($query_rejim_in_day);				
				//if($list_rejim_in_day['STATUS']=="V" || $list_rejim_in_day['STATUS']=="P") $background="V"; else $background="R";
				if($list_rejim_in_day['STATUS']=="V") 									{$background="V"; $value_type_day="В";}
				if($list_rejim_in_day['STATUS']=="P") 									{$background="V"; $value_type_day="П";}
				if($list_rejim_in_day['STATUS']=="PP") 									{$background="R"; $value_type_day="ПП";}
				if($list_rejim_in_day['STATUS']=="R" && $list_info['ID_PROPUSK']!="5") 	{$background="R"; $value_type_day="Р";}
				if($list_rejim_in_day['STATUS']=="R" && $list_info['ID_PROPUSK']=="5")	{$background="R"; $value_type_day="ГР";}
				

				$query_check_celosmen = mysqli_query($db,"SELECT ID_VID_ISP_VREM FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$_GET['day']."' AND ID_VID_ISP_VREM IN (4,11,12,17,18,19,20,21,22)");
				$list_check_celosmen = mysqli_fetch_array($query_check_celosmen);
				
				$query_otrab_vrem_ur = mysqli_query($db,"SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(PERIOD_TIME))) FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$_GET['day']."' AND (SELECT WORKTIME FROM SPR_VID_ISP_VREM WHERE SPR_VID_ISP_VREM.ID=WORK_OUT.ID_VID_ISP_VREM)='1' AND ID_VID_ISP_VREM NOT IN(42,43,44,45,7,24)");
				$list_otrab_vrem_ur = mysqli_fetch_array($query_otrab_vrem_ur);
				
				$query_otrab_vrem_svnorm = mysqli_query($db,"SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(PERIOD_TIME))) FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$_GET['day']."' AND (SELECT WORKTIME FROM SPR_VID_ISP_VREM WHERE SPR_VID_ISP_VREM.ID=WORK_OUT.ID_VID_ISP_VREM)='1' AND ID_VID_ISP_VREM IN(42,43,44,45,7,24)");
				$list_otrab_vrem_svnorm = mysqli_fetch_array($query_otrab_vrem_svnorm);
				
				$value_time_ur = substr($list_otrab_vrem_ur[0],0,5);
				$value_time_svnorm = substr($list_otrab_vrem_svnorm[0],0,5);
				
				
				
				
					if(mysqli_num_rows($query_check_celosmen)==0)//Если НЕцелосменные отсутствия
					{
						$query_check_narush = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$_GET['day']."' AND ID_VID_ISP_VREM IN (8,9,10)");
						$query_check_edit = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$_GET['day']."' AND ID_WORK_OUT_EDIT<>''");
						
						//Проверяем на соответствие табелю 1С
							$query_1C = mysqli_query($db,"SELECT ROUND(SUBSTRING(`".$_GET['day']."`,LOCATE('ДО',`".$_GET['day']."`)+3,LOCATE('|',`".$_GET['day']."`,LOCATE('ДО',`".$_GET['day']."`))-LOCATE('ДО',`".$_GET['day']."`)-3),2) FROM TEMP_TABEL_1C WHERE YEAR='".$_GET['year']."' AND MONTH='".$_GET['month']."' AND TABN='".$_GET['tabn']."'");
							$list_1C = mysqli_fetch_array($query_1C);
							
							$query_work_out = mysqli_query($db,"SELECT ROUND(SUM(TIME_TO_SEC(PERIOD_TIME))/60/60,2) FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$_GET['day']."' AND ID_VID_ISP_VREM IN(12,23) ");
							$list_work_out = mysqli_fetch_array($query_work_out);
							if($list_work_out[0]=="") $work_out="0.00"; else $work_out=$list_work_out[0];
							
						//конец проверки
						
						if(mysqli_num_rows($query_check_narush)!=0) $font = "RED_";
						else if(mysqli_num_rows($query_check_edit)!=0) $font = "BLUE_";
						else $font = "BLACK_";

					}
					else if(mysqli_num_rows($query_check_celosmen)>0)//Если целосменные отсутствия
					{
						$query_check_edit = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$_GET['day']."' AND ID_WORK_OUT_EDIT<>''");
						//Проверяем на соответствие табелю 1С
							$query_1C = mysqli_query($db,"SELECT ROUND(SUBSTRING(`".$_GET['day']."`,LOCATE('ДО',`".$_GET['day']."`)+3,LOCATE('|',`".$_GET['day']."`,LOCATE('ДО',`".$_GET['day']."`))-LOCATE('ДО',`".$_GET['day']."`)-3),2) FROM TEMP_TABEL_1C WHERE YEAR='".$_GET['year']."' AND MONTH='".$_GET['month']."' AND TABN='".$_GET['tabn']."'");
							$list_1C = mysqli_fetch_array($query_1C);
							
							$query_work_out = mysqli_query($db,"SELECT ROUND(SUM(TIME_TO_SEC(PERIOD_TIME))/60/60,2) FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$_GET['day']."' AND ID_VID_ISP_VREM IN(12,23) ");
							$list_work_out = mysqli_fetch_array($query_work_out);
							if($list_work_out[0]=="") $work_out="0.00"; else $work_out=$list_work_out[0];
							
							if(($list_1C[0]-$work_out)==0) $font="BLACK_"; else $font="RED_";
						//конец проверки
				
						if($list_check_celosmen[0]=="4")
						{
							if(mysqli_num_rows($query_check_edit)!=0)$font = "BLUE_"; else $font = "BLACK_";
							$value_type_day = "К";
						}
						if($list_check_celosmen[0]=="11")
						{
							$font="RED_";
							$value_type_day = "??";
						}
						if($list_check_celosmen[0]=="12")
						{
							$font = "BLACK_";
							$value_type_day = "БС";
						}
						if($list_check_celosmen[0]=="17")
						{
							if(mysqli_num_rows($query_check_edit)!=0)$font = "BLUE_"; else $font = "BLACK_";
							$value_type_day = "БЛ";
						}
						if($list_check_celosmen[0]=="18")
						{
							if(mysqli_num_rows($query_check_edit)!=0)$font = "BLUE_"; else $font = "BLACK_";
							$value_type_day = "ОТ";
						}
						if($list_check_celosmen[0]=="19")
						{
							$font = "BLACK_";
							$value_type_day = "УО";
						}
						if($list_check_celosmen[0]=="20")
						{
							$font = "BLACK_";
							$value_type_day = "УБ";
						}
						if($list_check_celosmen[0]=="21")
						{
							$font = "BLACK_";
							$value_type_day = "СР";
						}
						if($list_check_celosmen[0]=="22")
						{
							$font = "BLACK_";
							$value_type_day = "НН";
						}
						
					}
	
	$query_otrab_vrem_summ = mysqli_query($db,"SELECT ROUND(SUM(TIME_TO_SEC(PERIOD_TIME))/3600,2) FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND (SELECT WORKTIME FROM SPR_VID_ISP_VREM WHERE SPR_VID_ISP_VREM.ID=WORK_OUT.ID_VID_ISP_VREM)='1'");
	$list_otrab_vrem_summ = mysqli_fetch_array($query_otrab_vrem_summ);
	
	//$query_otrab_vrem_summ = mysqli_query($db,"SELECT ROUND(SUM(TIME_TO_SEC(PERIOD_TIME))/3600,2) FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND (SELECT WORKTIME FROM SPR_VID_ISP_VREM WHERE SPR_VID_ISP_VREM.ID=WORK_OUT.ID_VID_ISP_VREM)='1'");
	//$list_otrab_vrem_summ = mysqli_fetch_array($query_otrab_vrem_summ);
	
	$query_otrab_vrem_summ_ur = mysqli_query($db,"SELECT ROUND(SUM(TIME_TO_SEC(PERIOD_TIME))/3600,2) FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND (SELECT WORKTIME FROM SPR_VID_ISP_VREM WHERE SPR_VID_ISP_VREM.ID=WORK_OUT.ID_VID_ISP_VREM)='1' AND ID_VID_ISP_VREM NOT IN(42,43,44,45,7,24,49)");
	$list_otrab_vrem_summ_ur = mysqli_fetch_array($query_otrab_vrem_summ_ur);
	
	$query_otrab_vrem_summ_svnorm = mysqli_query($db,"SELECT ROUND(SUM(TIME_TO_SEC(PERIOD_TIME))/3600,2) FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND (SELECT WORKTIME FROM SPR_VID_ISP_VREM WHERE SPR_VID_ISP_VREM.ID=WORK_OUT.ID_VID_ISP_VREM)='1' AND ID_VID_ISP_VREM IN(42,43,44,45,7,24,49)");
	$list_otrab_vrem_summ_svnorm = mysqli_fetch_array($query_otrab_vrem_summ_svnorm);
	

	
	if($_GET['month']<10) $month="0".$_GET['month']; else $month=$_GET['month'];
	if($_GET['day']<10) $day="0".$_GET['day']; else $day=$_GET['day'];
	$query_update_otrab_vrem = mysqli_query($db,"UPDATE `OTRAB_VREM` SET `TOTAL`='".$list_otrab_vrem_summ[0]."|".$list_otrab_vrem_summ_ur[0]."|".$list_otrab_vrem_summ_svnorm[0]."', `".$_GET['day']."`='".$value_type_day."|".$value_time_ur."|".$value_time_svnorm."|".$font.$background."' WHERE `TABN`='".$_GET['tabn']."' AND YEAR='".$_GET['year']."' AND MONTH='".$_GET['month']."' ");
	
	if($value_time_ur=="")$value_print_ur="-"; else $value_print_ur=$value_time_ur;
	if($value_time_svnorm=="")$value_print_svnorm="-"; else $value_print_svnorm=$value_time_svnorm;
	
	
	if($list_otrab_vrem_summ[0]=="") $print_otrab_vrem_summ = "-"; else $print_otrab_vrem_summ = $list_otrab_vrem_summ[0];
	if($list_otrab_vrem_summ_ur[0]=="") $print_otrab_vrem_summ_ur = "-"; else $print_otrab_vrem_summ_ur = $list_otrab_vrem_summ_ur[0];
	if($list_otrab_vrem_summ_svnorm[0]=="") $print_otrab_vrem_summ_svnorm = "-"; else $print_otrab_vrem_summ_svnorm = $list_otrab_vrem_summ_svnorm[0];
	
	echo $value_type_day."<br>".$value_print_ur."<br>".$value_print_svnorm."|".getStyle($font.$background)."font-family:".$list_font['GRID_FONT_FAMILY']."; font-size:".$list_font['GRID_FONT_SIZE']."px;|".$print_otrab_vrem_summ."<br>".$print_otrab_vrem_summ_ur."<br>".$print_otrab_vrem_summ_svnorm;
}

if($_GET['action']=="vid_isp_vrem")
{
	$query = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE ID='".$_GET['id']."'");
	$list = mysqli_fetch_assoc($query);
	echo $list['ID_VID_ISP_VREM'];
}

if($_GET['action']=="check_komand")
{
	$query = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."' AND DAY(DATE_TIME_BEG)='".$_GET['day']."' AND ID_VID_ISP_VREM ='4'");
	echo mysqli_num_rows($query);
}

if($_GET['action']=="check_records_for_enable_button")
{
	if($_GET['month']<10) $month="0".$_GET['month']; else $month=$_GET['month'];
	if($_GET['day']<10) $day="0".$_GET['day']; else $day=$_GET['day'];
	
	//Определяем статус текущего дня
	$query_status = mysqli_query($db,"SELECT STATUS FROM SPR_GRAF_DNI WHERE ID_GRAF=(SELECT ID_GRAF FROM PERSONAL WHERE TABN='".$_GET['tabn']."' AND DATE_BEG<='".$_GET['year']."-".$month."-".$day."' AND DATE_END>='".$_GET['year']."-".$month."-".$day."') AND YEAR(DATE)='".$_GET['year']."' AND MONTH(DATE)='".$_GET['month']."' AND DAY(DATE)='".$_GET['day']."'");
	$list_status = mysqli_fetch_array($query_status);
	
	//Определяем категорию пропуска сотрудника
	$query_propusk = mysqli_query($db,"SELECT ID_PROPUSK FROM PERSONAL WHERE TABN='".$_GET['tabn']."' AND DATE_BEG<='".$_GET['year']."-".$month."-".$day."' AND DATE_END>='".$_GET['year']."-".$month."-".$day."'");
	$list_propusk = mysqli_fetch_array($query_propusk);
	
	//Проверяем наличие записей в текущем дне
	$query_check = mysqli_query($db,"SELECT IF(".$_GET['day']." <= MAX(DAY(DATE_TIME_BEG)),'on','off') FROM WORK_OUT WHERE YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."' ");
	$list_check = mysqli_fetch_array($query_check);
	
	if($list_check[0]=="on" && ($list_status[0]=="V" || $list_status[0]=="P"))
	{
		echo "1";
	}
	else if($list_check[0]=="on" && $list_propusk[0]=="5")
	{
		echo "2";
	}
	else if($list_check[0]=="on")
	{
		echo "3";
	}
	else
	{
		echo "0";
	}
}

if($_GET['action']=="get_isp_vrem_edit")
{
	$query = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE ID='".$_GET['id']."'");
	$list = mysqli_fetch_assoc($query);
	if($list['ID_VID_ISP_VREM']=='7' || $list['ID_VID_ISP_VREM']=='24' || $list['ID_VID_ISP_VREM']=='32') echo ""; 
	else echo $list['ID_WORK_OUT_EDIT'];
}

if($_GET['action']=="check_edit_isp_vrem")
{
	$query = mysqli_query($db,"SELECT FOR_EDIT FROM SPR_VID_ISP_VREM WHERE ID=(SELECT ID_VID_ISP_VREM FROM WORK_OUT WHERE ID='".$_GET['id']."')");
	$list = mysqli_fetch_array($query);
	echo $list[0];
}

if($_GET['action']=="check_tabel_1c")
{
	$query = mysqli_query($db,"SELECT * FROM TEMP_TABEL_1C WHERE YEAR='".$_GET['year']."' AND MONTH='".$_GET['month']."' ");
	echo mysqli_num_rows($query);
}

if($_GET['action']=="check_file")
{
	if(file_exists("reports/".$_GET['file_name'])) echo "1"; else echo "0";
}

if($_GET['action']=="get_DOVS_in_1C")//Время внутрисменного отсутствия без содержания в 1С
{	
	$query_1C = mysqli_query($db,"SELECT ROUND(REPLACE(SUBSTRING(`".$_GET['day']."`,LOCATE('ДО',`".$_GET['day']."`)+3,LOCATE('|',`".$_GET['day']."`,LOCATE('ДО',`".$_GET['day']."`))-LOCATE('ДО',`".$_GET['day']."`)-2),',','.'),2) FROM TEMP_TABEL_1C WHERE YEAR='".$_GET['year']."' AND MONTH='".$_GET['month']."' AND TABN='".$_GET['tabn']."'");
	$list_1C = mysqli_fetch_array($query_1C);
	
	$query_work_out = mysqli_query($db,"SELECT ROUND(SUM(TIME_TO_SEC(PERIOD_TIME))/60/60,2) FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$_GET['day']."' AND ID_VID_ISP_VREM IN(12,23) ");
	$list_work_out = mysqli_fetch_array($query_work_out);
	if($list_work_out[0]=="") $work_out="0.00"; else $work_out=$list_work_out[0];
	
	echo "|".$list_1C[0]."|".$work_out."|";
	
}


if($_GET['action']=="layout_size")
{
	$query = mysqli_query($db, "SELECT * FROM USER_SETTINGS WHERE IP = '".$_SERVER['REMOTE_USER']."'");
	$list = mysqli_fetch_array($query);
	echo "|".$list['OTRAB_A_W']."|".$list['OTRAB_C_H']."|".$list['OTRAB_CB_W'];
}

if($_GET['action']=="get_id_profile")
{
	$query_pers = mysqli_query($db, "SELECT * FROM PERSONAL WHERE IP='".$_SERVER['REMOTE_USER']."' AND DATE_END='2100-01-01'");
	$list_pers = mysqli_fetch_assoc($query_pers);
	
	if(mysqli_num_rows($query_pers)>0) echo "|".$list_pers['ID_PROFILE']; else echo "|0";
}

//SELECT ROUND(SUBSTRING(`4`,LOCATE('ДО',`4`)+3,LOCATE('|',`4`,LOCATE('ДО',`4`))-LOCATE('ДО',`4`)-3),2) FROM TEMP_TABEL_1C WHERE YEAR='2017' AND MONTH='8' AND TABN='07375'


?>