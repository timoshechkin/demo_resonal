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

	if($_GET['action']=="edit_in_out")
	{
		//Обновляем значения в IN_OUT
		if($_GET['type']=="in_time")	{$pole = "TIME_IN";		$value=$_GET['value'];}
		if($_GET['type']=="out_time")	{$pole = "TIME_OUT";	$value=$_GET['value'];}
		if($_GET['type']=="in_date")	{$pole = "DATE_IN";		$value=Date_Convert_for_Mysql($_GET['value']);}
		if($_GET['type']=="out_date")	{$pole = "DATE_OUT";	$value=Date_Convert_for_Mysql($_GET['value']);}
		
		$UPDATE_IN_OUT = mysqli_query($db,"UPDATE IN_OUT SET ".$pole."='".$value."',COMMENTS='EDIT_USER_".$_SERVER['REMOTE_USER']."' WHERE ID='".$_GET['id']."'");
	}
	
	if($_GET['action']=="add_in_out")
	{
		if($_GET['month']<10)$month="0".$_GET['month']; else $month=$_GET['month'];
		if($_GET['day']<10)$day="0".$_GET['day']; else $day=$_GET['day'];
		//Добавить запись в IN_OUT
		$insert_IN_OUT = mysqli_query($db,"INSERT INTO IN_OUT VALUES(NULL,'".$_GET['tabn']."','".$_GET['year']."-".$month."-".$day."','00:00:00','','".$_GET['year']."-".$month."-".$day."','00:00:00','','','','EDIT_USER_".$_SERVER['REMOTE_USER']."')");
	}
	
	if($_GET['action']=="del_in_out")
	{
		//Удалить запись в IN_OUT
		$UPDATE_IN_OUT = mysqli_query($db,"DELETE FROM IN_OUT WHERE ID='".$_GET['id']."'");
	}


?>