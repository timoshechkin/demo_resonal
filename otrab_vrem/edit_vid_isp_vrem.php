<?php
//session_start();
error_reporting(E_ALL ^ E_NOTICE);


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

function Time_Minute_for_Mysql($minute)//Входной формат MySql: минуты числом
	{
		
		$h = intval($minute/60);
		$m = $minute-($h*60);
		if(strlen($h)==1) $h_str="0".$h; else $h_str=$h;
		if(strlen($m)==1) $m_str="0".$m; else $m_str=$m;
		$time = $h_str.":".$m_str.":00";
		return $time; //Выходной формат: hh:mm:ss
	}
/*	
if($_GET['action']=="add_vid_isp_vrem")
{
	if($_GET['month']>9) $month="0".$_GET['month']; else $month=$_GET['month'];
	if($_GET['day']>9) $day="0".$_GET['day']; else $day=$_GET['day'];
	
	//Ищем время окончания последнего периода в текущем дне
	$query_time_end_last_period = mysqli_query($db,"SELECT TIME(MAX(DATE_TIME_END)) FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_END)='".$_GET['year']."' AND MONTH(DATE_TIME_END)='".$_GET['month']."' AND DAY(DATE_TIME_END)='".$_GET['day']."' ");
	$list_time_end_last_period = mysqli_fetch_array($query_time_end_last_period);
	
	//Проверяем является ли текущий день командировкой
	$query_check = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_END)='".$_GET['year']."' AND MONTH(DATE_TIME_END)='".$_GET['month']."' AND DAY(DATE_TIME_END)='".$_GET['day']."' AND ID_VID_ISP_VREM='4'");
	
	if(mysqli_num_rows($query_check)>0)//Если текущий день командировка, то добавляем период использования
	{
		$add_vid_isp_vrem_edit = mysqli_query($db,"INSERT INTO WORK_OUT_EDIT VALUES(NULL,'".$_GET['tabn']."','".$_GET['year']."-".$month."-".$day." ".$list_time_end_last_period[0]."','".$_GET['year']."-".$month."-".$day." ".$list_time_end_last_period[0]."','','','4','7','',TIMESTAMP(CURDATE(),CURTIME()))");
		$add_vid_isp_vrem = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$_GET['tabn']."','".$_GET['year']."-".$month."-".$day." ".$list_time_end_last_period[0]."','".$_GET['year']."-".$month."-".$day." ".$list_time_end_last_period[0]."','','4','7',LAST_INSERT_ID())");
	}
}
*/

if($_GET['action']=="add_vid_isp_vrem_1")//Добавляем запись сверхурочной работы без фиксации в СКУД
{
	$SET_LOCK_WORK_OUT = mysqli_query($db,"UPDATE LOCK_WORK_OUT SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	$SET_LOCK_WORK_OUT_EDIT = mysqli_query($db,"UPDATE LOCK_WORK_OUT_EDIT SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	
	if($_GET['month']>9) $month="0".$_GET['month']; else $month=$_GET['month'];
	if($_GET['day']>9) $day="0".$_GET['day']; else $day=$_GET['day'];
	
	//Проверяем есть ли записи сверхурочки за пределами организации в текущем дне
	$query_check = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE ID_VID_ISP_VREM IN(7,48) AND TABN='".$_GET['tabn']."' AND YEAR(DATE_TIME_END)='".$_GET['year']."' AND MONTH(DATE_TIME_END)='".$_GET['month']."' AND DAY(DATE_TIME_END)='".$_GET['day']."'");
	
	if(mysqli_num_rows($query_check)==0)//Если в текущем дне нет записей, то добавляем 
	{
		$add_vid_isp_vrem_edit = mysqli_query($db,"INSERT INTO WORK_OUT_EDIT VALUES(NULL,'".$_GET['tabn']."','".$_GET['year']."-".$month."-".$day."  00:00:00','".$_GET['year']."-".$month."-".$day."  00:00:00','','','','7','7',TIMESTAMP(CURDATE(),CURTIME()),'".$_SERVER['REMOTE_USER']."','0')");
		$add_vid_isp_vrem = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$_GET['tabn']."','".$_GET['year']."-".$month."-".$day."  00:00:00','".$_GET['year']."-".$month."-".$day."  00:00:00','','','7',LAST_INSERT_ID())");
	/*
		//Проверяем на существование записи в WORKS_SV_NORM
		$query_check_ID_WORKS_SV_NORM = mysqli_query($db,"SELECT ID FROM WORKS_SV_NORM_NEW WHERE TABN='".$_GET['tabn']."' AND DATE='".$_GET['year']."-".$month."-".$day."' ");
		$list_check_ID_WORKS_SV_NORM = mysqli_fetch_array($query_check_ID_WORKS_SV_NORM);
		if(mysqli_num_rows($query_check_ID_WORKS_SV_NORM)==0)//Если записи в WORKS_SV_NORM нет, то добавляем новую запись
		{
			$query_select_DATA_FROM_TZ = mysqli_query($db,"SELECT GROUP_CONCAT(CONCAT('Тема №:',THEM,' Изделие:',IZDEL,' Этап:',ETAP,' Работа:',NAME_WORK) SEPARATOR '|') AS INFO, SEC_TO_TIME(SUM(TIME_HOURS)*60*60) AS TIME FROM DATA_FROM_TZ WHERE TABN='".$_GET['tabn']."' AND DATE='".$_GET['year']."-".$month."-".$day."' ");
			$list_select_DATA_FROM_TZ = mysqli_fetch_array($query_select_DATA_FROM_TZ);
			
			//Добавляем запись работы в сверхнормативное время
			$query_insert_WORKS_SV_NORM = mysqli_query($db,"INSERT INTO WORKS_SV_NORM_NEW VALUES(NULL,'".$_GET['tabn']."','".$_GET['year']."-".$month."-".$day."','".$list_select_DATA_FROM_TZ['INFO']."','','','".$list_select_DATA_FROM_TZ['TIME']."','',0,'',NULL,0,'',NULL)");
			
			//Берем ID из WORKS_SV_NORM добавленной записи
			$query_select_ID_WORKS_SV_NORM = mysqli_query($db,"SELECT ID FROM WORKS_SV_NORM_NEW WHERE TABN='".$_GET['tabn']."' AND DATE='".$_GET['year']."-".$month."-".$day."' ");
			$list_select_ID_WORKS_SV_NORM = mysqli_fetch_array($query_select_ID_WORKS_SV_NORM);
			
			//Добавляем записи периодов работы в сверхнормативное время
			$query_insert_LIST_SV_NORM = mysqli_query($db,"INSERT INTO LIST_SV_NORM VALUES(NULL,'".$list_select_ID_WORKS_SV_NORM[0]."','".$_GET['tabn']."','".$_GET['year']."-".$month."-".$day."  00:00:00','".$_GET['year']."-".$month."-".$day."  00:00:00','','')");
		}
		else
		{
			//Добавляем пустую запись периода работы в сверхнормативное время
			$query_insert_LIST_SV_NORM = mysqli_query($db,"INSERT INTO LIST_SV_NORM VALUES(NULL,'".$list_check_ID_WORKS_SV_NORM[0]."','".$_GET['tabn']."','".$_GET['year']."-".$month."-".$day."  00:00:00','".$_GET['year']."-".$month."-".$day."  00:00:00','','')");
		}
		*/
	}
	
	$SET_LOCK_WORK_OUT = mysqli_query($db,"UPDATE LOCK_WORK_OUT SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	$SET_LOCK_WORK_OUT_EDIT = mysqli_query($db,"UPDATE LOCK_WORK_OUT_EDIT SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
}






if($_GET['action']=="add_vid_isp_vrem_2")//Добавляем период присутствия в выходной или праздничный день без фиксации в СКУД
{
	$SET_LOCK_WORK_OUT = mysqli_query($db,"UPDATE LOCK_WORK_OUT SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	$SET_LOCK_WORK_OUT_EDIT = mysqli_query($db,"UPDATE LOCK_WORK_OUT_EDIT SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	
	if($_GET['month']>9) $month="0".$_GET['month']; else $month=$_GET['month'];
	if($_GET['day']>9) $day="0".$_GET['day']; else $day=$_GET['day'];
	
	
	//Проверяем есть ли записи в текущем дне
	$query_check = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE TABN='".$_GET['tabn']."' AND ID_VID_ISP_VREM='24' AND YEAR(DATE_TIME_END)='".$_GET['year']."' AND MONTH(DATE_TIME_END)='".$_GET['month']."' AND DAY(DATE_TIME_END)='".$_GET['day']."'");
	
	if(mysqli_num_rows($query_check)==0)//Если в текущем дне нет записей, то добавляем период использования
	{
		$add_vid_isp_vrem_edit = mysqli_query($db,"INSERT INTO WORK_OUT_EDIT VALUES(NULL,'".$_GET['tabn']."','".$_GET['year']."-".$month."-".$day." 00:00:00','".$_GET['year']."-".$month."-".$day." 00:00:00','','','','24','24',TIMESTAMP(CURDATE(),CURTIME()),'".$_SERVER['REMOTE_USER']."','0')");
		$add_vid_isp_vrem = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$_GET['tabn']."','".$_GET['year']."-".$month."-".$day." 00:00:00','".$_GET['year']."-".$month."-".$day." 00:00:00','','','24',LAST_INSERT_ID())");
	}
	
	$SET_LOCK_WORK_OUT = mysqli_query($db,"UPDATE LOCK_WORK_OUT SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	$SET_LOCK_WORK_OUT_EDIT = mysqli_query($db,"UPDATE LOCK_WORK_OUT_EDIT SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
}



if($_GET['action']=="add_vid_isp_vrem_3")
{
	$SET_LOCK_WORK_OUT = mysqli_query($db,"UPDATE LOCK_WORK_OUT SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	$SET_LOCK_WORK_OUT_EDIT = mysqli_query($db,"UPDATE LOCK_WORK_OUT_EDIT SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	
	if($_GET['month']>9) $month="0".$_GET['month']; else $month=$_GET['month'];
	if($_GET['day']>9) $day="0".$_GET['day']; else $day=$_GET['day'];

	$add_vid_isp_vrem_edit = mysqli_query($db,"INSERT INTO WORK_OUT_EDIT VALUES(NULL,'".$_GET['tabn']."','".$_GET['year']."-".$month."-".$day."  00:00:00','".$_GET['year']."-".$month."-".$day."  00:00:00','','','','32','',TIMESTAMP(CURDATE(),CURTIME()),'".$_SERVER['REMOTE_USER']."','0')");
	$add_vid_isp_vrem = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$_GET['tabn']."','".$_GET['year']."-".$month."-".$day."  00:00:00','".$_GET['year']."-".$month."-".$day."  00:00:00','','','32',LAST_INSERT_ID())");
	
	$SET_LOCK_WORK_OUT = mysqli_query($db,"UPDATE LOCK_WORK_OUT SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	$SET_LOCK_WORK_OUT_EDIT = mysqli_query($db,"UPDATE LOCK_WORK_OUT_EDIT SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
}



if($_GET['action']=="del_vid_isp_vrem")
{
	$SET_LOCK_WORK_OUT = mysqli_query($db,"UPDATE LOCK_WORK_OUT SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	$SET_LOCK_WORK_OUT_EDIT = mysqli_query($db,"UPDATE LOCK_WORK_OUT_EDIT SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	
	//Проверяем является ли текущий период сверхурочной работой
	$query_check = mysqli_query($db,"SELECT ID_VID_ISP_VREM FROM WORK_OUT WHERE ID='".$_GET['id']."' ");
	$list_check = mysqli_fetch_array($query_check);
	
	if($list_check[0]=="7" || $list_check[0]=="24" || $list_check[0]=="32")//Если текущий период сверхурочная работа или работа в выходной день в командировке или работа на гибком режиме работы вне организации, то удаляем период использования
	{
		$del_vid_isp_vrem_edit = mysqli_query($db,"DELETE FROM WORK_OUT_EDIT WHERE ID=(SELECT ID_WORK_OUT_EDIT FROM WORK_OUT WHERE ID='".$_GET['id']."') ");
		$del_vid_isp_vrem = mysqli_query($db,"DELETE FROM WORK_OUT WHERE ID='".$_GET['id']."'");
		
		
		$query_ID_WORKS_SV_NORM = mysqli_query($db,"SELECT ID FROM WORKS_SV_NORM_NEW WHERE ID=(SELECT DISTINCT ID_WORKS_SV_NORM FROM LIST_SV_NORM WHERE CONCAT(TABN,DATE_TIME_BEG)=(SELECT CONCAT(TABN,DATE_TIME_BEG) FROM WORK_OUT WHERE ID='".$_GET['id']."') )");
		$list_ID_WORKS_SV_NORM = mysqli_fetch_array($query_ID_WORKS_SV_NORM);
		
		//Удаляем записи из WORKS_SV_NORM_NEW и LIST_SV_NORM
		$query_delete_LIST_SV_NORM = mysqli_query($db,"DELETE FROM LIST_SV_NORM WHERE ID_WORKS_SV_NORM='".$list_ID_WORKS_SV_NORM[0]."' ");
		$query_delete_WORKS_SV_NORM = mysqli_query($db,"DELETE FROM WORKS_SV_NORM_NEW WHERE ID='".$list_ID_WORKS_SV_NORM[0]."' ");
	}
	
	$SET_LOCK_WORK_OUT = mysqli_query($db,"UPDATE LOCK_WORK_OUT SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	$SET_LOCK_WORK_OUT_EDIT = mysqli_query($db,"UPDATE LOCK_WORK_OUT_EDIT SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
}


if($_GET['action']=="del_edit_vid_isp_vrem")
{
	$SET_LOCK_WORK_OUT = mysqli_query($db,"UPDATE LOCK_WORK_OUT SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	$SET_LOCK_WORK_OUT_EDIT = mysqli_query($db,"UPDATE LOCK_WORK_OUT_EDIT SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	$SET_LOCK_WORKS_SV_NORM = mysqli_query($db,"UPDATE LOCK_WORKS_SV_NORM SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	
	//Определяем первоначальный вид использования рабочего времени
	$get_vid_isp_vrem_first = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE ID=(SELECT ID_WORK_OUT_EDIT FROM WORK_OUT WHERE ID='".$_GET['id']."')");
	$list_vid_isp_vrem_first = mysqli_fetch_assoc($get_vid_isp_vrem_first);

	//Удаляем запись корректировки
	$del_vid_isp_vrem_edit = mysqli_query($db,"DELETE FROM WORK_OUT_EDIT WHERE ID=(SELECT ID_WORK_OUT_EDIT FROM WORK_OUT WHERE ID='".$_GET['id']."') ");
	
	//Восстанавливаем первоначальный вид использования рабочего времени
	$update_vid_isp_vrem = mysqli_query($db,"UPDATE WORK_OUT SET ID_VID_ISP_VREM='".$list_vid_isp_vrem_first['ID_VID_ISP_VREM_FIRST']."', PERIOD_TIME='".$list_vid_isp_vrem_first['PERIOD_TIME_FIRST']."', ID_WORK_OUT_EDIT='' WHERE ID='".$_GET['id']."'");
	//Удаляем запись из WORKS_SV_NORM
	$query_delete_WORKS_SV_NORM = mysqli_query($db,"DELETE FROM WORKS_SV_NORM WHERE TABN='".$list_vid_isp_vrem_first['TABN']."' AND DATE_TIME_BEG='".$list_vid_isp_vrem_first['DATE_TIME_BEG']."' AND DATE_TIME_END='".$list_vid_isp_vrem_first['DATE_TIME_END']."' ");
	
	$SET_LOCK_WORK_OUT = mysqli_query($db,"UPDATE LOCK_WORK_OUT SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	$SET_LOCK_WORK_OUT_EDIT = mysqli_query($db,"UPDATE LOCK_WORK_OUT_EDIT SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
	$SET_LOCK_WORKS_SV_NORM = mysqli_query($db,"UPDATE LOCK_WORKS_SV_NORM SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
}


?>