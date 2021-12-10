<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
//connect to database
require_once("../../mysql.php");


$query = mysqli_query($db,"SELECT * FROM STATUS_PROCESS WHERE STATUS='1' AND ID IN(5,6,7)");
$list = mysqli_fetch_assoc($query);
//Смотрим статус выполнения всех процессов группы
$query_group = mysqli_query($db,"SELECT STATUS_GROUP FROM STATUS_PROCESS WHERE ID='5'");
$list_group = mysqli_fetch_array($query_group);
	
if(mysqli_num_rows($query)>0)
{
	if($list['TARGET']=="0") $procent=0; else $procent=round($list['FAKT']/$list['TARGET']*100,0);
	echo "|".$list['ID']."|".$list['STATUS']."|".$list_group[0]."|".$procent;
}
else echo "|0|0|0|0";//|ID|STATUS|STATUS_GROUP|PROCENT

?>