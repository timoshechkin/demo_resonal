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

	echo $_GET['action']."<br>";
	echo $_GET['year']."<br>";
	echo $_GET['month']."<br>";
	echo $_GET['day_beg']."<br>";
	echo $_GET['day_end']."<br>";
	echo $_GET['list_id_sel']."<br>";

$completed="off";
while ($completed=="off")
{
	
	//Проверяем блокировку таблиц
	$query_check_lock = mysqli_query($db,"	SELECT 
											IF(LOCK_WORK_OUT.LOCK_STATUS='0' OR (LOCK_WORK_OUT.LOCK_STATUS='1' AND LOCK_WORK_OUT.REMOTE_USER='".$_SERVER['REMOTE_USER']."'),0,1), 
											IF(LOCK_WORK_OUT_EDIT.LOCK_STATUS='0' OR (LOCK_WORK_OUT_EDIT.LOCK_STATUS='1' AND LOCK_WORK_OUT_EDIT.REMOTE_USER='".$_SERVER['REMOTE_USER']."'),0,1),
											IF(LOCK_OTRAB_VREM.LOCK_STATUS='0' OR (LOCK_OTRAB_VREM.LOCK_STATUS='1' AND LOCK_OTRAB_VREM.REMOTE_USER='".$_SERVER['REMOTE_USER']."'),0,1),
											IF(LOCK_ISPOLZ_VREM.LOCK_STATUS='0' OR (LOCK_ISPOLZ_VREM.LOCK_STATUS='1' AND LOCK_ISPOLZ_VREM.REMOTE_USER='".$_SERVER['REMOTE_USER']."'),0,1),
											IF(LOCK_WORK_SV_NORM.LOCK_STATUS='0' OR (LOCK_WORK_SV_NORM.LOCK_STATUS='1' AND LOCK_WORK_SV_NORM.REMOTE_USER='".$_SERVER['REMOTE_USER']."'),0,1)
											FROM LOCK_WORK_OUT, LOCK_WORK_OUT_EDIT, LOCK_OTRAB_VREM, LOCK_ISPOLZ_VREM, LOCK_WORK_SV_NORM");
	$list_check_lock = mysqli_fetch_array($query_check_lock);
	
	if($list_check_lock[0]=="0" && $list_check_lock[1]=="0" && $list_check_lock[2]=="0" && $list_check_lock[3]=="0" && $list_check_lock[4]=="0")//Если таблицы свободны, то выполняем запрос
	{
		
		$SET_LOCK_WORK_OUT = mysqli_query($db,"UPDATE LOCK_WORK_OUT SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
		$SET_LOCK_WORK_OUT_EDIT = mysqli_query($db,"UPDATE LOCK_WORK_OUT_EDIT SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
		$SET_LOCK_OTRAB_VREM = mysqli_query($db,"UPDATE LOCK_OTRAB_VREM SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
		$SET_LOCK_ISPOLZ_VREM = mysqli_query($db,"UPDATE LOCK_ISPOLZ_VREM SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
		$SET_LOCK_WORK_SV_NORM = mysqli_query($db,"UPDATE LOCK_WORK_SV_NORM SET LOCK_STATUS='1', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
				
			
				
				if($_GET['action']=="start_otrab")
				{
					
					//Выполняем импорт данных проходных из новых файлов
					//require_once("../vv/loadData.php");
					
					
					//echo $_GET['action']."<br>";
					//echo $_GET['year']."<br>";
					//echo $_GET['month']."<br>";
					//echo $_GET['day_beg']."<br>";
					//echo $_GET['day_end']."<br>";
					//echo $_GET['list_id_sel']."<br>";
					
					if ($_GET['month']>9)
					{
						$month1=$_GET['month']; 
					} 
					else 
					{
						$month1="0".$_GET['month'];
					}
					
					if($month1==12)
					{
						$month2="01";
						$year2=$_GET['year']+1;
					}
					else
					{
						if (($_GET['month']+1)>9) $month2=($_GET['month']+1); else $month2="0".($_GET['month']+1);
						$year2=$_GET['year'];
					}
					
					$date1=$_GET['year']."-".$month1."-01";
					
					//Получаем число последнего дня месяца
					$query_day_end = mysqli_query($db,"SELECT  DAYOFMONTH(IF( SUBDATE((MAX(IN_OUT.DATE_IN)),INTERVAL 1 DAY) >= '".$_GET['year']."-".$month1."-01' AND SUBDATE((MAX(IN_OUT.DATE_IN)),INTERVAL 1 DAY) <=SUBDATE('".$year2."-".$month2."-01',INTERVAL 1 DAY), SUBDATE((MAX(IN_OUT.DATE_IN)),INTERVAL 1 DAY), IF( SUBDATE((MAX(IN_OUT.DATE_IN)),INTERVAL 1 DAY) > SUBDATE('".$year2."-".$month2."-01',INTERVAL 1 DAY),SUBDATE('".$year2."-".$month2."-01',INTERVAL 1 DAY),'".$_GET['year']."-".$month1."-01')))  FROM IN_OUT "); //Получаем крайнее число месяца для расчета отработанного времени
					$res_day_end = mysqli_fetch_array($query_day_end);
					$date2=$_GET['year']."-".$month1."-".$res_day_end[0];


					//Чистим таблицу

					if($_GET['list_id_sel']!="")$clear = mysqli_query($db,"DELETE FROM WORK_OUT WHERE TABN IN(SELECT TABN FROM PERSONAL WHERE TABN IN(".$_GET['list_id_sel'].")) AND DATE(DATE_TIME_BEG) >= DATE(TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($_GET['day_beg']-1)." DAY),'00:00:00')) AND DATE(DATE_TIME_BEG) <= DATE(TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($_GET['day_end']-1)." DAY),'00:00:00')) ");
					if($_GET['list_id_sel']=="")$clear = mysqli_query($db,"DELETE FROM WORK_OUT WHERE DATE(DATE_TIME_BEG) >= DATE(TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($_GET['day_beg']-1)." DAY),'00:00:00')) AND DATE(DATE_TIME_BEG) <= DATE(TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($_GET['day_end']-1)." DAY),'00:00:00')) ");
					//$clear = mysqli_query($db,"DELETE FROM WORK_OUT WHERE DATE(DATE_TIME_BEG) >= DATE(TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($_GET['day_beg']-1)." DAY),'00:00:00')) AND DATE(DATE_TIME_BEG) <= DATE(TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($_GET['day_end']-1)." DAY),'00:00:00')) ");
					
					//Выбираем сотрудников
					if($_GET['list_id_sel']!="")
					{
						//echo "SELECT DISTINCT TABN FROM PERSONAL WHERE TABN IN(".$_GET['list_id_sel'].") AND ((DATE_BEG <= '".$date1."' AND DATE_END >= '".$date2."') OR (DATE_BEG <= '".$date1."' AND DATE_END >= '".$date1."' AND DATE_END <= '".$date2."' ) OR (DATE_BEG >= '".$date1."' AND DATE_BEG <= '".$date2."' AND DATE_END >= '".$date2."' )) ORDER BY TABN ASC";
						$query = mysqli_query($db,"SELECT DISTINCT TABN FROM PERSONAL WHERE TABN IN(".$_GET['list_id_sel'].") ORDER BY TABN ASC");
					}
					if($_GET['list_id_sel']=="")
					{
						$query = mysqli_query($db,"SELECT DISTINCT TABN FROM PERSONAL WHERE (DATE_BEG <= '".$date1."' AND DATE_END >= '".$date2."') OR (DATE_BEG >= '".$date1."' AND DATE_BEG <= '".$date2."') OR (DATE_END >= '".$date1."' AND DATE_END <= '".$date2."') ORDER BY TABN ASC");
					}
					//Обновляем статус процесса
					$query_set_status_process_beg = mysqli_query($db,"UPDATE STATUS_PROCESS SET IP='".$_SERVER['REMOTE_USER']."', STATUS='1', STATUS_GROUP='1', DATE_TIME_BEG=TIMESTAMP(CURDATE(),CURTIME()), DATE_TIME_END=NULL, TARGET='".mysqli_num_rows($query)."', FAKT='0' WHERE PROCESS='CREATE_OTRAB_VREM'");
					$fakt=0;
					
					//Устанавливаем период расчета
					$day_beg=$_GET['day_beg'];
					if($_GET['day_end']>$res_day_end[0])		$day_end=$res_day_end[0];
					else if($_GET['day_end']<$_GET['day_beg'])	$day_end=$_GET['day_beg'];
					else										$day_end=$_GET['day_end'];
					
					//echo "OK";
					
					//Перебираем сотрудников
					while ($list = mysqli_fetch_assoc($query))
						{
							//echo "OK";
							//echo $day_beg."_".$day_end."<br>";
							//Перебираем дни месяца
							for($d=$day_beg; $d<=$day_end; $d++)
							{
									//Обнуляем переменную продолжительности отсутствия в течении смены работника с режимом контроля "Свободный +"
									$total_time_out_vnsm = 0;

									//Выбираем данные по сотруднику
									$query_info = mysqli_query($db,"SELECT * FROM PERSONAL WHERE TABN='".$list['TABN']."' AND DATE_BEG <= SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY) AND DATE_END >= SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY)");
									$list_info = mysqli_fetch_assoc($query_info);
									
									//Берем лимит отсутствия работников с режимом Свободный+ в секундах
									$query_limit_out_vnsm = mysqli_query($db,"SELECT TIME_TO_SEC(LIMIT_VNS_OUT) FROM GLOBAL_DETAL_SET WHERE DATE_BEG <= SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY) AND DATE_END >= SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY)");
									$list_limit_out_vnsm = mysqli_fetch_array($query_limit_out_vnsm);
									
									//Проверяем на увольнение и декрет
									if($list_info['UVOLEN']!="1" && $list_info['DEKRET']!="1")
									{
										if($list_info['NENORM']=="1") $query_nenorm = "3"; else $query_nenorm = "2";
										
										//Выбираем режим работы у сотрудника в текущем дне
										$query_rejim_in_day = mysqli_query($db,"SELECT * FROM SPR_GRAF_DNI WHERE ID_GRAF='".$list_info['ID_GRAF']."' AND DATE=SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY)  ");
										$list_rejim_in_day = mysqli_fetch_assoc($query_rejim_in_day);
										
										
										//Переводим режим дня в формат DATETIME
										$query_rejim_in_day_datetime = mysqli_query($db,"SELECT TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNS']."') AS `NS`, TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNO']."') AS `NO`, TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKO']."') AS `KO`, TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."') AS `KS`");
										$list_rejim_in_day_datetime = mysqli_fetch_assoc($query_rejim_in_day_datetime);
										//$list_rejim_in_day_datetime['NS']
										//$list_rejim_in_day_datetime['NO']
										//$list_rejim_in_day_datetime['KO']
										//$list_rejim_in_day_datetime['KS']
										
										//Выбираем режим работы в текущем дне по основному графику (для гибкого режима)
										$query_rejim_in_day_base = mysqli_query($db,"SELECT * FROM SPR_GRAF_DNI WHERE ID_GRAF='1' AND DATE=SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY)  ");
										$list_rejim_in_day_base = mysqli_fetch_assoc($query_rejim_in_day_base);
										
										
										//Выбираем периоды присутствия в текущем дне по сотруднику
										//$query_in_out = mysqli_query($db,"SELECT * FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND INSTR(COMMENTS,'ERROR')=0 ");
										
										//Выбираем периоды по сотруднику попадающие на текущий день
										$query_in_out = mysqli_query($db,"SELECT * FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND INSTR(COMMENTS,'ERROR')=0  AND ((TIMESTAMP(DATE_IN,TIME_IN) <= TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00') AND TIMESTAMP(DATE_OUT,TIME_OUT) >= TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'24:00:00')) OR (TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00') < TIMESTAMP(DATE_OUT,TIME_OUT) AND TIMESTAMP(DATE_OUT,TIME_OUT) < TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'24:00:00')) OR (TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00') < TIMESTAMP(DATE_IN,TIME_IN) AND TIMESTAMP(DATE_IN,TIME_IN) < TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'24:00:00')) )");
										
										//Ищем неявки по табелю 1С
										$query_day_from_1c = mysqli_query($db,"SELECT * FROM TEMP_TABEL_1C WHERE TABN='".$list['TABN']."' AND YEAR='".$_GET['year']."' AND MONTH='".$_GET['month']."'");
										$list_day_from_1c = mysqli_fetch_assoc($query_day_from_1c);
										
										//----------------------------------------- РАБОЧИЕ дни КРОМЕ работников с гибким режимом учета --------------------------
											if(($list_rejim_in_day['STATUS']=="R" || $list_rejim_in_day['STATUS']=="PP") && $list_info['ID_PROPUSK']!="5")//Кроме работников с гибким режимом учета
											{
												if($list_info['ID_PROPUSK']=="4")//Если у работника административный пропуск, то выставляем время и причины по графику работы и табелю 1С
												{
													if		(strpos($list_day_from_1c[$d],"Б")!==false)		{$vid_isp="17";}
													else if	(strpos($list_day_from_1c[$d],"ОТ")!==false)	{$vid_isp="18";}
													else if	(strpos($list_day_from_1c[$d],"ДО")!==false)	{$vid_isp="12";}
													else if	(strpos($list_day_from_1c[$d],"У")!==false)		{$vid_isp="19";}
													else if	(strpos($list_day_from_1c[$d],"К")!==false)		{$vid_isp="21";}
													else if	(strpos($list_day_from_1c[$d],"ДУ")!==false)	{$vid_isp="20";}
													else if	(strpos($list_day_from_1c[$d],"НН")!==false)	{$vid_isp="22";}
													else $vid_isp="1";

													$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNS']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."'),SEC_TO_TIME(TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNS']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."'))) - SEC_TO_TIME(TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNO']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKO']."'))),'','".$vid_isp."','')");
												}
												else if(mysqli_num_rows($query_in_out)==0)//Если нет присутствия в текущем рабочем дне, то рассматриваем, как целодневное отсутствие и ищем причины отсутствия в табеле 1С
												{
													//Ищем день в корректировках
													$query_check_edit = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNS']."') AND DATE_TIME_END=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."') ");
													$list_check_edit = mysqli_fetch_assoc($query_check_edit);
													
													$id_work_out_edit = "";
													
													if		(strpos($list_day_from_1c[$d],"Б")!==false)		{$vid_isp="17";}
													else if	(strpos($list_day_from_1c[$d],"ОТ")!==false && $list_check_edit['ID_VID_ISP_VREM']!="4"){$vid_isp="18";}
													else if	(strpos($list_day_from_1c[$d],"ОТ")!==false && $list_check_edit['ID_VID_ISP_VREM']=="4"){$vid_isp="4"; $id_work_out_edit = $list_check_edit['ID'];} //Если в период отпуска по табелю 1С заведена корректировкой командировка, то оставляем признак командировки
													else if	(strpos($list_day_from_1c[$d],"ДО")!==false)	{$vid_isp="12";}
													else if	(strpos($list_day_from_1c[$d],"У")!==false)		{$vid_isp="19";}
													else if	(strpos($list_day_from_1c[$d],"К")!==false)		{$vid_isp="21";}
													else if	(strpos($list_day_from_1c[$d],"ДУ")!==false)	{$vid_isp="20";}
													else if	(strpos($list_day_from_1c[$d],"НН")!==false)	{$vid_isp="22";}
													else
													{
														if(mysqli_num_rows($query_check_edit)>0)
														{
															$vid_isp = $list_check_edit['ID_VID_ISP_VREM'];
															$id_work_out_edit = $list_check_edit['ID'];
															
															//Ищем периоды сверхурочной работой в таблице корректировок
															/*$query_sverhur = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."' AND DAY(DATE_TIME_BEG)='".$d."' AND ID_VID_ISP_VREM IN(7,48)");
															while($list_sverhur = mysqli_fetch_assoc($query_sverhur))
															{
																//Записываем периоды сверхурочной работы в командировке в основную таблицу
																$query_insert_work_out_edit = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list_sverhur['TABN']."','".$list_sverhur['DATE_TIME_BEG']."','".$list_sverhur['DATE_TIME_END']."','".$list_sverhur['PERIOD_TIME']."','".$list_sverhur['ID_PERIOD_DAY']."','".$list_sverhur['ID_VID_ISP_VREM']."','".$list_sverhur['ID']."')");
															}*/
														}
														else
														{
															$vid_isp="11";
														}
														
													}
													
													$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNS']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."'),SEC_TO_TIME(TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNS']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."'))) - SEC_TO_TIME(TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNO']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKO']."'))),'','".$vid_isp."','".$id_work_out_edit."')");
												}
												else //Если есть входы и выходы в текущем рабочем дне, то перебираем их
												{
													//Если по табелю 1С заведен ОТПУСК или БОЛЬНИЧНЫЙ, то записываем, как целосменное отсутствие по соответствующей причине и добавляем запись по работе в период отпуска и больничного
													if(strpos($list_day_from_1c[$d],"Б")!==false || strpos($list_day_from_1c[$d],"ОТ")!==false)
													{
														if		(strpos($list_day_from_1c[$d],"Б")!==false)		{$vid_isp="17";}
														else if	(strpos($list_day_from_1c[$d],"ОТ")!==false)	{$vid_isp="18";}
														//Записываем отпуск или больничный
														if(mysqli_num_rows($query_rejim_in_day)!=0)//Если установлен график работы
														{
															$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_rejim_in_day_datetime['NS']."','".$list_rejim_in_day_datetime['KS']."',SEC_TO_TIME(TIMESTAMPDIFF(SECOND,'".$list_rejim_in_day_datetime['NS']."','".$list_rejim_in_day_datetime['KS']."')) - SEC_TO_TIME(TIMESTAMPDIFF(SECOND,'".$list_rejim_in_day_datetime['NO']."','".$list_rejim_in_day_datetime['KO']."')),'','".$vid_isp."','')");
														}
														else//Для гибкого режима работы
														{
															$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),'00:00:00','','".$vid_isp."','')");
														}
														
														//Ищем запись работы в период отпуска или больничного в корректировках
														$query_check_edit_OB = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00') AND DATE_TIME_END=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00') AND ID_VID_ISP_VREM IN(44,45) ");
														$list_check_edit_OB = mysqli_fetch_assoc($query_check_edit_OB);
														
														if(mysqli_num_rows($query_check_edit_OB)==0)//Если не находим корректировку, то расчитываем
														{
															//Добавляем запись работы в период отпуска или больничного
															$otrab_vrem_summ = 0;
															$obed = 0;
															$otrab_vrem_itog = 0;
															while ($list_in_out = mysqli_fetch_assoc($query_in_out))//Перебираем периоды присутствия
															{
																//Определяем начало и окончание периода присутствия ограниченные текущим днем
																	$query_in_out_in_day = mysqli_query($db,"SELECT IF(TIMESTAMP('".$list_in_out['DATE_IN']."','".$list_in_out['TIME_IN']."') < TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),TIMESTAMP('".$list_in_out['DATE_IN']."','".$list_in_out['TIME_IN']."')) AS `IN`, IF(TIMESTAMP('".$list_in_out['DATE_OUT']."','".$list_in_out['TIME_OUT']."') > TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'24:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'24:00:00'),TIMESTAMP('".$list_in_out['DATE_OUT']."','".$list_in_out['TIME_OUT']."')) AS `OUT` ");
																	$list_in_out_in_day = mysqli_fetch_assoc($query_in_out_in_day);
																	
																//Вычисляем продолжительность между входом и выходом
																	$query_otrab_vrem = mysqli_query($db,"SELECT TIMESTAMPDIFF(SECOND,'".$list_in_out_in_day['IN']."','".$list_in_out_in_day['OUT']."')");
																	$list_otrab_vrem = mysqli_fetch_array($query_otrab_vrem);
																//------------------------------------------------------
															
																//Суммируем рабочее время по всем входам-выходам текущего дня
																	$otrab_vrem_summ = $otrab_vrem_summ + $list_otrab_vrem[0];
																//------------------------------------------------------
															}
															//Определяем продолжительность обеденного перерыва
																if($otrab_vrem_summ > 14400 && $otrab_vrem_summ <= 16200) {$obed = $otrab_vrem_summ - 14400;}		//если время присутствия больше 4 часов и не более 4.5 часа, то снимается время присутствия в диапазоне 4-4.5 часа
																else if($otrab_vrem_summ > 16200) {$obed = 30*60;}													//если отработанное время больше 4.5 часа, то снимается 30 мин.
															//----------------------------------------------------------
															
															//Уменьшаем отработанное время на обед
																$otrab_vrem_itog = $otrab_vrem_summ - $obed;
															//----------------------------------------------------------
															
															//Записываем результат
																if		(strpos($list_day_from_1c[$d],"Б")!==false)		{$vid_isp_2="28";}
																else if	(strpos($list_day_from_1c[$d],"ОТ")!==false)	{$vid_isp_2="27";}
																if($otrab_vrem_itog > 0)$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),SEC_TO_TIME(".$otrab_vrem_itog."),'','".$vid_isp_2."','')");
															//----------------------------------------------------------
														}
														else //Если находим корректировку, то восстанавливаем
														{
															$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_check_edit_OB['DATE_TIME_BEG']."','".$list_check_edit_OB['DATE_TIME_END']."','".$list_check_edit_OB['PERIOD_TIME']."','','".$list_check_edit_OB['ID_VID_ISP_VREM']."','".$list_check_edit_OB['ID']."')");
														}
														
													}
													//Если по табелю 1С заведен УЧЕБНЫЙ ОТПУСК, ОТСУТСТВИЕ С СОХРАНЕНИЕМ СРЕДНЕЙ З/П, ОТСУТСТВИЕ ПО НЕВЫЯСНЕННОЙ ПРИЧИНЕ, то записываем, как целосменное отсутствие по соответствующей причине
													else if(strpos($list_day_from_1c[$d],"У")!==false || strpos($list_day_from_1c[$d],"К")!==false || strpos($list_day_from_1c[$d],"ДУ")!==false || strpos($list_day_from_1c[$d],"НН")!==false)
													{
														if		(strpos($list_day_from_1c[$d],"У")!==false)		{$vid_isp="19";}
														else if	(strpos($list_day_from_1c[$d],"К")!==false)		{$vid_isp="21";}
														else if	(strpos($list_day_from_1c[$d],"ДУ")!==false)	{$vid_isp="20";}
														else if	(strpos($list_day_from_1c[$d],"НН")!==false)	{$vid_isp="22";}
														
														if(mysqli_num_rows($query_rejim_in_day)!=0)//Если установлен график работы
														{
															$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNS']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."'),SEC_TO_TIME(TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNS']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."'))) - SEC_TO_TIME(TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNO']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKO']."'))),'','".$vid_isp."','')");
														}
														else//Для гибкого режима работы
														{
															$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),'00:00:00','','".$vid_isp."','')");
														}
													}
													else
													{
														
														while ($list_in_out = mysqli_fetch_assoc($query_in_out))//Перебираем периоды присутствия
														{
																//Определяем начало и окончание периода присутствия ограниченные текущим днем
																	$query_in_out_in_day = mysqli_query($db,"SELECT IF(TIMESTAMP('".$list_in_out['DATE_IN']."','".$list_in_out['TIME_IN']."') < TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),TIMESTAMP('".$list_in_out['DATE_IN']."','".$list_in_out['TIME_IN']."')) AS `IN`, IF(TIMESTAMP('".$list_in_out['DATE_OUT']."','".$list_in_out['TIME_OUT']."') > TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'24:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'24:00:00'),TIMESTAMP('".$list_in_out['DATE_OUT']."','".$list_in_out['TIME_OUT']."')) AS `OUT` ");
																	$list_in_out_in_day = mysqli_fetch_assoc($query_in_out_in_day);
																	
																	//echo $list_in_out_in_day['IN']."_".$list_in_out_in_day['IN']."<br>";
																	//$list_in_out_in_day['IN']
																	//$list_in_out_in_day['OUT']
																	//$list_rejim_in_day_datetime['NS']
																	//$list_rejim_in_day_datetime['NO']
																	//$list_rejim_in_day_datetime['KO']
																	//$list_rejim_in_day_datetime['KS']
																	
																	
																//1 ОПРЕДЕЛЯЕМ ПЕРИОДЫ ПРИСУТСТВИЯ ДО НАЧАЛА СМЕНЫ
																	//Находим время конца временного периода (если вход не до начала смены, то 0)
																	$query_wds = mysqli_query($db,"SELECT IF ('".$list_in_out_in_day['IN']."'<'".$list_rejim_in_day_datetime['NS']."',IF('".$list_in_out_in_day['OUT']."'<'".$list_rejim_in_day_datetime['NS']."','".$list_in_out_in_day['OUT']."','".$list_rejim_in_day_datetime['NS']."'),'0')");
																	$list_wds = mysqli_fetch_array($query_wds);
																	
																	if($list_wds[0]!="0")
																	{
																		//Берем минимальную продолжительность работы до начала смены
																		$query_porog = mysqli_query($db,"SELECT * FROM GLOBAL_DETAL_SET WHERE DATE_BEG<='".$list_in_out['DATE_IN']."' AND DATE_END>='".$list_in_out['DATE_IN']."'");
																		$list_porog = mysqli_fetch_assoc($query_porog);
																		
																		
																		//Определяем продолжительность временного периода (если меньше порога, то 0)
																		$query_porog_wds = mysqli_query($db,"SELECT IF (TIMESTAMPDIFF(SECOND,'".$list_in_out_in_day['IN']."','".$list_wds[0]."') >= TIME_TO_SEC('".$list_porog['MIN_SN_DO']."'),SEC_TO_TIME(TIMESTAMPDIFF(SECOND,'".$list_in_out_in_day['IN']."','".$list_wds[0]."')),0)");
																		$list_porog_wds = mysqli_fetch_array($query_porog_wds);
																	
																		if($list_porog_wds[0]!="0")
																		{
																			//Ищем запись сверхурочной работы в корректировках
																			$query_check_edit_SU = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG='".$list_in_out_in_day['IN']."' AND DATE_TIME_END='".$list_wds[0]."' AND ID_VID_ISP_VREM = '42' ");
																			$list_check_edit_SU = mysqli_fetch_assoc($query_check_edit_SU);
																			if(mysqli_num_rows($query_check_edit_SU)==0)//Если не находим
																			{
																				$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_in_out_in_day['IN']."','".$list_wds[0]."','".$list_porog_wds[0]."','1','".$query_nenorm."','')");
																			}
																			else//Если находим, то восстанавливаем
																			{
																				$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_check_edit_SU['DATE_TIME_BEG']."','".$list_check_edit_SU['DATE_TIME_END']."','".$list_check_edit_SU['PERIOD_TIME']."','".$list_check_edit_SU['ID_PERIOD_DAY']."','".$list_check_edit_SU['ID_VID_ISP_VREM']."','".$list_check_edit_SU['ID']."')");
																			}
																		}
																	}

															//ОПРЕДЕЛЯЕМ НАЛИЧИЕ ОБЕДА ПО ГРАФИКУ РАБОТЫ СОТРУДНИКА
															if($list_rejim_in_day['WNO']!='00:00:00' && $list_rejim_in_day['WKO']!='00:00:00') //ЕСЛИ ОБЕД УСТАНОВЛЕН
																{
																
																//2 ОПРЕДЕЛЯЕМ ПЕРИОДЫ ПРИСУТСТВИЯ В РАБОЧЕЕ ВРЕМЯ ДО ОБЕДА
																
																	//Начало периода
																	$query_wrdo_beg = mysqli_query($db,"SELECT IF ('".$list_in_out_in_day['IN']."'<='".$list_rejim_in_day_datetime['NS']."' AND '".$list_in_out_in_day['OUT']."'>'".$list_rejim_in_day_datetime['NS']."','".$list_rejim_in_day_datetime['NS']."',IF('".$list_in_out_in_day['IN']."'>'".$list_rejim_in_day_datetime['NS']."' AND '".$list_in_out_in_day['IN']."'<'".$list_rejim_in_day_datetime['NO']."','".$list_in_out_in_day['IN']."','0'))");
																	$list_wrdo_beg = mysqli_fetch_array($query_wrdo_beg);
																	
																	//Конец периода
																	$query_wrdo_end = mysqli_query($db,"SELECT IF ('".$list_in_out_in_day['OUT']."'>='".$list_rejim_in_day_datetime['NO']."' AND '".$list_in_out_in_day['IN']."'<'".$list_rejim_in_day_datetime['NO']."','".$list_rejim_in_day_datetime['NO']."',IF('".$list_in_out_in_day['OUT']."'<='".$list_rejim_in_day_datetime['NO']."' AND '".$list_in_out_in_day['OUT']."'>'".$list_rejim_in_day_datetime['NS']."','".$list_in_out_in_day['OUT']."','0'))");
																	$list_wrdo_end = mysqli_fetch_array($query_wrdo_end);
																	
																	//echo $list_wrdo_beg[0]."_".$list_wrdo_end[0];
																	
																	//Если начало и окончание периода определено, то записываем период
																	if($list_wrdo_beg[0]!="0" && $list_wrdo_end[0]!="0")
																		{
																			$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_wrdo_beg[0]."','".$list_wrdo_end[0]."',SEC_TO_TIME(TIMESTAMPDIFF(SECOND,'".$list_wrdo_beg[0]."','".$list_wrdo_end[0]."')),'2','1','')");
																		}

																//3 ОПРЕДЕЛЯЕМ ПЕРИОДЫ ПРИСУТСТВИЯ В РАБОЧЕЕ ВРЕМЯ ПОСЛЕ ОБЕДА
																
																	//Начало периода
																	$query_wrpo_beg = mysqli_query($db,"SELECT IF ('".$list_in_out_in_day['IN']."'<'".$list_rejim_in_day_datetime['KO']."' AND '".$list_in_out_in_day['OUT']."'>'".$list_rejim_in_day_datetime['KO']."','".$list_rejim_in_day_datetime['KO']."',IF('".$list_in_out_in_day['IN']."'>'".$list_rejim_in_day_datetime['KO']."' AND '".$list_in_out_in_day['IN']."'<'".$list_rejim_in_day_datetime['KS']."','".$list_in_out_in_day['IN']."','0'))");
																	$list_wrpo_beg = mysqli_fetch_array($query_wrpo_beg);
																	
																	//Конец периода
																	$query_wrpo_end = mysqli_query($db,"SELECT IF ('".$list_in_out_in_day['OUT']."'>='".$list_rejim_in_day_datetime['KS']."' AND '".$list_in_out_in_day['IN']."'<'".$list_rejim_in_day_datetime['KS']."','".$list_rejim_in_day_datetime['KS']."',IF('".$list_in_out_in_day['OUT']."'<'".$list_rejim_in_day_datetime['KS']."' AND '".$list_in_out_in_day['OUT']."'>'".$list_rejim_in_day_datetime['KO']."','".$list_in_out_in_day['OUT']."','0'))");
																	$list_wrpo_end = mysqli_fetch_array($query_wrpo_end);
																	
																	//Если начало и окончание периода определено, то записываем период
																	if($list_wrpo_beg[0]!="0" && $list_wrpo_end[0]!="0")
																		{
																			$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_wrpo_beg[0]."','".$list_wrpo_end[0]."',SEC_TO_TIME(TIMESTAMPDIFF(SECOND,'".$list_wrpo_beg[0]."','".$list_wrpo_end[0]."')),'3','1','')");
																		}
																

																}
																else //ЕСЛИ ОБЕД НЕ УСТАНОВЛЕН
																{
																	//Начало периода
																	$query_wrvs_beg = mysqli_query($db,"SELECT IF ('".$list_in_out_in_day['IN']."'<'".$list_rejim_in_day_datetime['NS']."' AND '".$list_in_out_in_day['OUT']."'>'".$list_rejim_in_day_datetime['NS']."','".$list_rejim_in_day_datetime['NS']."',IF('".$list_in_out_in_day['IN']."'>'".$list_rejim_in_day_datetime['NS']."' AND '".$list_in_out_in_day['IN']."'<'".$list_rejim_in_day_datetime['KS']."','".$list_in_out_in_day['IN']."','0'))");
																	$list_wrvs_beg = mysqli_fetch_array($query_wrvs_beg);
																	
																	//Конец периода
																	$query_wrvs_end = mysqli_query($db,"SELECT IF ('".$list_in_out_in_day['OUT']."'>'".$list_rejim_in_day_datetime['KS']."' AND '".$list_in_out_in_day['IN']."'<'".$list_rejim_in_day_datetime['KS']."','".$list_rejim_in_day_datetime['KS']."',IF('".$list_in_out_in_day['OUT']."'<'".$list_rejim_in_day_datetime['KS']."' AND '".$list_in_out_in_day['OUT']."'>'".$list_rejim_in_day_datetime['NS']."','".$list_in_out_in_day['OUT']."','0'))");
																	$list_wrvs_end = mysqli_fetch_array($query_wrvs_end);
																	
																	//Если начало и окончание периода определено, то записываем период
																	if($list_wrvs_beg[0]!="0" && $list_wrvs_end[0]!="0")
																		{
																			$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_wrvs_beg[0]."','".$list_wrvs_end[0]."',SEC_TO_TIME(TIMESTAMPDIFF(SECOND,'".$list_wrvs_beg[0]."','".$list_wrvs_end[0]."')),'5','1','')");
																		}
																}

																//4 ОПРЕДЕЛЯЕМ ПЕРИОДЫ ПРИСУТСТВИЯ ПОСЛЕ ОКОНЧАНИЯ СМЕНЫ
																	//Находим время конца временного периода (если выход не после окончания смены, то 0)
																	$query_wps = mysqli_query($db,"SELECT IF ('".$list_in_out_in_day['OUT']."'>'".$list_rejim_in_day_datetime['KS']."',IF('".$list_in_out_in_day['IN']."'>'".$list_rejim_in_day_datetime['KS']."','".$list_in_out_in_day['IN']."','".$list_rejim_in_day_datetime['KS']."'),'0')");
																	$list_wps = mysqli_fetch_array($query_wps);
																	
																	if($list_wps[0]!="0")
																	{
																		//Берем минимальную продолжительность работы после окончания смены
																		$query_porog = mysqli_query($db,"SELECT * FROM GLOBAL_DETAL_SET WHERE DATE_BEG<='".$list_in_out['DATE_IN']."' AND DATE_END>='".$list_in_out['DATE_IN']."'");
																		$list_porog = mysqli_fetch_assoc($query_porog);
																		
																		//Определяем продолжительность временного периода (если меньше порога, то 0)
																		$query_porog_wps = mysqli_query($db,"SELECT IF (TIMESTAMPDIFF(SECOND,'".$list_wps[0]."','".$list_in_out_in_day['OUT']."') >= TIME_TO_SEC('".$list_porog['MIN_SN_POSLE']."'),SEC_TO_TIME(TIMESTAMPDIFF(SECOND,'".$list_wps[0]."','".$list_in_out_in_day['OUT']."')),0)");
																		$list_porog_wps = mysqli_fetch_array($query_porog_wps);
																	
																		if($list_porog_wps[0]!="0")
																		{
																			//Ищем запись сверхурочной работы в корректировках
																			$query_check_edit_SU = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG='".$list_wps[0]."' AND DATE_TIME_END='".$list_in_out_in_day['OUT']."' AND ID_VID_ISP_VREM = '42' ");
																			$list_check_edit_SU = mysqli_fetch_assoc($query_check_edit_SU);
																			if(mysqli_num_rows($query_check_edit_SU)==0)//Если не находим
																			{
																				$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_wps[0]."','".$list_in_out_in_day['OUT']."','".$list_porog_wps[0]."','4','".$query_nenorm."','')");
																			}
																			else //Если находим, то восстанавливаем
																			{
																				$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_check_edit_SU['DATE_TIME_BEG']."','".$list_check_edit_SU['DATE_TIME_END']."','".$list_check_edit_SU['PERIOD_TIME']."','".$list_check_edit_SU['ID_PERIOD_DAY']."','".$list_check_edit_SU['ID_VID_ISP_VREM']."','".$list_check_edit_SU['ID']."')");
																			}
																			
																		}
																	}

																//5 ОПРЕДЕЛЯЕМ ОПОЗДАНИЕ !!!
																	//Если текущий вход является первым входом после начала смены и он не позднее конца смены и до него после начала смены нет выходов и вход позже 1 минуты после начала смены, то это ОПОЗДАНИЕ
																	$query_opozd = mysqli_query($db,"SELECT IF((SELECT MIN(TIME_IN) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND TIME_IN>'".$list_rejim_in_day['WNS']."' AND INSTR(COMMENTS,'ERROR')=0 )='".$list_in_out['TIME_IN']."' AND '".$list_in_out['TIME_IN']."'<'".$list_rejim_in_day['WKS']."' AND (SELECT COUNT(ID) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND TIME_OUT>'".$list_rejim_in_day['WNS']."' AND TIME_OUT<'".$list_in_out['TIME_IN']."' AND INSTR(COMMENTS,'ERROR')=0 )=0,1,0)");

																	$list_opozd = mysqli_fetch_array($query_opozd);

																		if($list_opozd[0]=="1")
																		{
																			//Проверяем допустимое ли опоздание (допустимое -1, нет -0)
																			$query_check_dopust_opozd = mysqli_query($db,"SELECT IF(TIME_TO_SEC(TIMEDIFF('".$list_in_out['TIME_IN']."','".$list_rejim_in_day['WNS']."'))<=60,1,0)");
																			$list_check_dopust_opozd = mysqli_fetch_array($query_check_dopust_opozd);
																			
																			//Ищем наличие входа до начала смены с выходом до начала смены или без выхода
																			$query_in_out_IN_WITH_OUT = mysqli_query($db,"SELECT * FROM IN_OUT WHERE TABN='".$list['TABN']."' AND YEAR(DATE_IN)='".$_GET['year']."' AND MONTH(DATE_IN)='".$_GET['month']."' AND DAY(DATE_IN)='".$d."' AND TIME_IN<'".$list_rejim_in_day['WNS']."' ");
																			
																			$id_work_out_edit = "";
																			if($list_info['ID_PROPUSK']=="3") $vid_isp = "14";														//Если пропуск суперсвободный
																			else if($list_check_dopust_opozd[0]=="1")$vid_isp = "46";												//Если опоздание в пределах допуска
																			else if(mysqli_num_rows($query_in_out_IN_WITH_OUT)>0 && $list_info['ID_PROPUSK']=="2")$vid_isp = "1";	//Если есть вход до начала смены и режим учета СВОБОДНЫЙ, то "работа в течении смены"
																			else $vid_isp = "8";
																			
																			//ОПРЕДЕЛЯЕМ НАЛИЧИЕ ОБЕДА ПО ГРАФИКУ РАБОТЫ СОТРУДНИКА
																			if($list_rejim_in_day['WNO']!='00:00:00' && $list_rejim_in_day['WKO']!='00:00:00') //ЕСЛИ ОБЕД УСТАНОВЛЕН
																			{
																				//Период опоздания до обеда
																				if($list_info['ID_PROPUSK']=="6")
																				{
																					//Определяем итоговую продолжительность отсутствия с режимом Свободный +
																					$query_get_summ_out_vnsm = mysqli_query($db,"SELECT TIMESTAMPDIFF(SECOND,'".$list_rejim_in_day_datetime['NS']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),IF('".$list_in_out['TIME_IN']."'<'".$list_rejim_in_day['WNO']."','".$list_in_out['TIME_IN']."','".$list_rejim_in_day['WNO']."')))+".$total_time_out_vnsm."");
																					$list_get_summ_out_vnsm = mysqli_fetch_array($query_get_summ_out_vnsm);
																					
																					//echo "SELECT TIME_TO_SEC(TIMESTAMPDIFF(SECOND,'".$list_rejim_in_day_datetime['NS']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),IF('".$list_in_out['TIME_IN']."'<'".$list_rejim_in_day['WNO']."','".$list_in_out['TIME_IN']."','".$list_rejim_in_day['WNO']."'))))+".$total_time_out_vnsm."<br>";
																					//echo $total_time_out_vnsm."_".$list_get_summ_out_vnsm[0]."_".$list_limit_out_vnsm[0];
																					
																					$total_time_out_vnsm = $list_get_summ_out_vnsm[0];
																					if($total_time_out_vnsm<=$list_limit_out_vnsm[0])$vid_isp = "52"; //Если режим свободный+ и норма отсутствия не превышена
																					
																					
																				}
																				
																				//Ищем период в корректировках
																				$query_check_edit_1 = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG='".$list_rejim_in_day_datetime['NS']."' AND DATE_TIME_END=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),IF('".$list_in_out['TIME_IN']."'<'".$list_rejim_in_day['WNO']."','".$list_in_out['TIME_IN']."','".$list_rejim_in_day['WNO']."')) ");
																				$list_check_edit_1 = mysqli_fetch_assoc($query_check_edit_1);
																				if(mysqli_num_rows($query_check_edit_1)!=0 && $list_info['ID_PROPUSK']!="3")
																				{
																					$vid_isp = $list_check_edit_1['ID_VID_ISP_VREM'];
																					$id_work_out_edit = $list_check_edit_1['ID'];
																				}
																				
																				$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_rejim_in_day_datetime['NS']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),IF('".$list_in_out['TIME_IN']."'<'".$list_rejim_in_day['WNO']."','".$list_in_out['TIME_IN']."','".$list_rejim_in_day['WNO']."')),SEC_TO_TIME(TIMESTAMPDIFF(SECOND,'".$list_rejim_in_day_datetime['NS']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),IF('".$list_in_out['TIME_IN']."'<'".$list_rejim_in_day['WNO']."','".$list_in_out['TIME_IN']."','".$list_rejim_in_day['WNO']."')))),'2','".$vid_isp."','".$id_work_out_edit."')");

																				//Период опоздания после обеда
																				
																				$query_check = mysqli_query($db,"SELECT IF('".$list_in_out['TIME_IN']."'>'".$list_rejim_in_day['WKO']."',1,0)");
																				$list_check = mysqli_fetch_array($query_check);
																				if($list_check[0]=="1")
																				{
																					if($list_info['ID_PROPUSK']=="6")
																					{
																						//Определяем итоговую продолжительность отсутствия с режимом Свободный +
																						$query_get_summ_out_vnsm = mysqli_query($db,"SELECT TIMESTAMPDIFF(SECOND,'".$list_rejim_in_day_datetime['NS']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),IF('".$list_in_out['TIME_IN']."'<'".$list_rejim_in_day['WNO']."','".$list_in_out['TIME_IN']."','".$list_rejim_in_day['WNO']."')))+".$total_time_out_vnsm."");
																						$list_get_summ_out_vnsm = mysqli_fetch_array($query_get_summ_out_vnsm);
																						$total_time_out_vnsm = $list_get_summ_out_vnsm[0];
																					}
																					$id_work_out_edit = "";
																					if($list_info['ID_PROPUSK']=="3") $vid_isp = "14";														//Если пропуск суперсвободный
																					else if($list_info['ID_PROPUSK']!="2" && $list_info['ID_PROPUSK']!="3" && $list_info['ID_PROPUSK']!="6" && $list_check_dopust_opozd[0]=="1")$vid_isp = "46";												//Если опоздание в пределах допуска
																					else if(mysqli_num_rows($query_in_out_IN_WITH_OUT)>0 && $list_info['ID_PROPUSK']=="2")$vid_isp = "1";	//Если есть вход до начала смены без выхода и режим учета СВОБОДНЫЙ, то "работа в течении смены"
																					else if($list_info['ID_PROPUSK']=="6" && $total_time_out_vnsm<=$list_limit_out_vnsm[0])$vid_isp = "52"; //Если режим свободный+ и норма отсутствия не превышена
																					else $vid_isp = "8";
																					
																					//Ищем период в корректировках
																					$query_check_edit_2 = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG='".$list_rejim_in_day_datetime['KO']."' AND DATE_TIME_END='".$list_in_out_in_day['IN']."' ");
																					$list_check_edit_2 = mysqli_fetch_assoc($query_check_edit_2);
																					if(mysqli_num_rows($query_check_edit_2)!=0 && $list_info['ID_PROPUSK']!="3")
																					{
																						$vid_isp = $list_check_edit_2['ID_VID_ISP_VREM'];
																						$id_work_out_edit = $list_check_edit_2['ID'];
																					}
																					
																					$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_rejim_in_day_datetime['KO']."','".$list_in_out_in_day['IN']."',SEC_TO_TIME(TIMESTAMPDIFF(SECOND,'".$list_rejim_in_day_datetime['KO']."','".$list_in_out_in_day['IN']."')),'3','".$vid_isp."','".$id_work_out_edit."')");
																				}
																			}
																			else //ЕСЛИ ОБЕД НЕ УСТАНОВЛЕН
																			{
																				if($list_info['ID_PROPUSK']=="6")
																				{
																					//Определяем итоговую продолжительность отсутствия с режимом Свободный +
																					$query_get_summ_out_vnsm = mysqli_query($db,"SELECT TIMESTAMPDIFF(SECOND,'".$list_rejim_in_day_datetime['NS']."','".$list_in_out_in_day['IN']."')+".$total_time_out_vnsm."");
																					$list_get_summ_out_vnsm = mysqli_fetch_array($query_get_summ_out_vnsm);
																					$total_time_out_vnsm = $list_get_summ_out_vnsm[0];
																					if($total_time_out_vnsm<=$list_limit_out_vnsm[0])$vid_isp = "52"; //Если режим свободный+ и норма отсутствия не превышена
																				}
																				//Ищем период в корректировках
																				$query_check_edit_3 = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG='".$list_rejim_in_day_datetime['NS']."' AND DATE_TIME_END='".$list_in_out_in_day['IN']."' ");
																				$list_check_edit_3 = mysqli_fetch_assoc($query_check_edit_3);
																				if(mysqli_num_rows($query_check_edit_3)!=0 && $list_info['ID_PROPUSK']!="3")
																				{
																					$vid_isp = $list_check_edit_3['ID_VID_ISP_VREM'];
																					$id_work_out_edit = $list_check_edit_3['ID'];
																				}
																				
																				$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_rejim_in_day_datetime['NS']."','".$list_in_out_in_day['IN']."',SEC_TO_TIME(TIMESTAMPDIFF(SECOND,'".$list_rejim_in_day_datetime['NS']."','".$list_in_out_in_day['IN']."')),'5','".$vid_isp."','".$id_work_out_edit."')");

																			}
																			
																		}
				////Дальше не корректировал
																//6 ОПРЕДЕЛЯЕМ ПРЕЖДЕВРЕМЕННЫЙ ВЫХОД !!!
																	
																	//$query_prejdevr = mysqli_query($db,"SELECT IF((SELECT MAX(TIME_IN) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND INSTR(COMMENTS,'ERROR')=0 )='".$list_in_out['TIME_IN']."' AND '".$list_in_out['TIME_OUT']."'<'".$list_rejim_in_day['WKS']."' AND '".$list_in_out['TIME_OUT']."'>'".$list_rejim_in_day['WNS']."',1,0)");
																	$query_prejdevr = mysqli_query($db,"SELECT IF((SELECT MAX(TIMESTAMP(DATE_OUT,TIME_OUT)) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND TIMESTAMP(DATE_OUT,TIME_OUT)>'".$list_rejim_in_day_datetime['NS']."' AND TIMESTAMP(DATE_OUT,TIME_OUT)<'".$list_rejim_in_day_datetime['KS']."' AND INSTR(COMMENTS,'ERROR')=0 )='".$list_in_out_in_day['OUT']."' AND (SELECT COUNT(ID) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND TIMESTAMP(DATE_IN,TIME_IN)>'".$list_in_out_in_day['OUT']."' AND TIMESTAMP(DATE_IN,TIME_IN)<'".$list_rejim_in_day_datetime['KS']."' AND INSTR(COMMENTS,'ERROR')=0 )=0,1,0)");
																	$list_prejdevr = mysqli_fetch_array($query_prejdevr);
																		if($list_prejdevr[0]=="1")
																		{
																			//Проверяем в допуске ли преждевременный выход (в допуске 1, нет 0)
																			$query_check_dopust_prejd = mysqli_query($db,"SELECT IF(TIME_TO_SEC(TIMEDIFF('".$list_rejim_in_day['WKS']."','".$list_in_out['TIME_OUT']."'))<=60,1,0)");
																			$list_check_dopust_prejd = mysqli_fetch_array($query_check_dopust_prejd);
																			
																			//Ищем наличие входа до начала смены без выхода
																			$query_in_out_OUT_WITH_IN = mysqli_query($db,"SELECT * FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND INSTR(COMMENTS,'ERROR')=1 AND YEAR(DATE_IN)='".$_GET['year']."' AND MONTH(DATE_IN)='".$_GET['month']."' AND DAY(DATE_IN)='".$d."' AND TIME_OUT>'".$list_rejim_in_day['WKS']."' ");
																			
																			$id_work_out_edit = "";
																			if($list_info['ID_PROPUSK']=="3") $vid_isp="15";														//Если пропуск суперсвободный
																			else if($list_check_dopust_prejd[0]=="1")$vid_isp = "47";												//Если выход в пределах допуска
																			else if(mysqli_num_rows($query_in_out_OUT_WITH_IN)>0 && $list_info['ID_PROPUSK']=="2")$vid_isp = "1";	//Если есть выход после окончания смены без входа и режим учета СВОБОДНЫЙ, то "работа в течении смены"
																			else $vid_isp="9";
																			
																			//ОПРЕДЕЛЯЕМ НАЛИЧИЕ ОБЕДА ПО ГРАФИКУ РАБОТЫ СОТРУДНИКА
																			if($list_rejim_in_day['WNO']!='00:00:00' && $list_rejim_in_day['WKO']!='00:00:00') //ЕСЛИ ОБЕД УСТАНОВЛЕН
																			{
																				//Период преждевременного выхода после обеда
																				//Ищем период в корректировках
																				//echo "SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),IF('".$list_in_out['TIME_OUT']."'>'".$list_rejim_in_day['WKO']."','".$list_in_out['TIME_OUT']."','".$list_rejim_in_day['WKO']."')) AND DATE_TIME_END=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."') <br>";
																				
																				if($list_info['ID_PROPUSK']=="6")
																				{
																					//Определяем итоговую продолжительность отсутствия с режимом Свободный +
																					$query_get_summ_out_vnsm = mysqli_query($db,"SELECT TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),IF('".$list_in_out['TIME_OUT']."'>'".$list_rejim_in_day['WKO']."','".$list_in_out['TIME_OUT']."','".$list_rejim_in_day['WKO']."')),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."'))+".$total_time_out_vnsm."");
																					$list_get_summ_out_vnsm = mysqli_fetch_array($query_get_summ_out_vnsm);
																					$total_time_out_vnsm = $list_get_summ_out_vnsm[0];
																					if($total_time_out_vnsm<=$list_limit_out_vnsm[0])$vid_isp = "52"; //Если режим свободный+ и норма отсутствия не превышена
																				}
																				
																				$query_check_edit_1 = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),IF('".$list_in_out['TIME_OUT']."'>'".$list_rejim_in_day['WKO']."','".$list_in_out['TIME_OUT']."','".$list_rejim_in_day['WKO']."')) AND DATE_TIME_END=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."') ");
																				$list_check_edit_1 = mysqli_fetch_assoc($query_check_edit_1);
																				if(mysqli_num_rows($query_check_edit_1)!=0 && $list_info['ID_PROPUSK']!="3")
																				{
																					$vid_isp = $list_check_edit_1['ID_VID_ISP_VREM'];
																					$id_work_out_edit = $list_check_edit_1['ID'];
																				}
																				
																				$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),IF('".$list_in_out['TIME_OUT']."'>'".$list_rejim_in_day['WKO']."','".$list_in_out['TIME_OUT']."','".$list_rejim_in_day['WKO']."')),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."'),SEC_TO_TIME(TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),IF('".$list_in_out['TIME_OUT']."'>'".$list_rejim_in_day['WKO']."','".$list_in_out['TIME_OUT']."','".$list_rejim_in_day['WKO']."')),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."'))),'3','".$vid_isp."','".$id_work_out_edit."')");
																				
																				
																				
																				//Период преждевременного выхода до обеда
																				$query_check = mysqli_query($db,"SELECT IF('".$list_in_out['TIME_OUT']."'<'".$list_rejim_in_day['WNO']."',1,0)");
																				$list_check = mysqli_fetch_array($query_check);
																				if($list_check[0]=="1")
																				{
																					if($list_info['ID_PROPUSK']=="6")
																					{
																						//Определяем итоговую продолжительность отсутствия с режимом Свободный +
																						$query_get_summ_out_vnsm = mysqli_query($db,"SELECT TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNO']."'))+".$total_time_out_vnsm."");
																						$list_get_summ_out_vnsm = mysqli_fetch_array($query_get_summ_out_vnsm);
																						$total_time_out_vnsm = $list_get_summ_out_vnsm[0];
																					}
																					
																					$id_work_out_edit = "";
																					if($list_info['ID_PROPUSK']=="3") $vid_isp = "15";														//Если пропуск суперсвободный
																					else if($list_check_dopust_prejd[0]=="1")$vid_isp = "47";												//Если выход в пределах допуска
																					else if(mysqli_num_rows($query_in_out_OUT_WITH_IN)>0 && $list_info['ID_PROPUSK']=="2")$vid_isp = "1";	//Если есть выход после окончания смены без входа и режим учета СВОБОДНЫЙ, то "работа в течении смены"
																					else if($list_info['ID_PROPUSK']=="6" && $total_time_out_vnsm<=$list_limit_out_vnsm[0])$vid_isp = "52"; //Если режим свободный+ и норма отсутствия не превышена
																					else $vid_isp = "9";
																					
																					//Ищем период в корректировках
																					$query_check_edit_2 = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."') AND DATE_TIME_END=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNO']."') ");
																					$list_check_edit_2 = mysqli_fetch_assoc($query_check_edit_2);
																					if(mysqli_num_rows($query_check_edit_2)!=0 && $list_info['ID_PROPUSK']!="3")
																					{
																						$vid_isp = $list_check_edit_2['ID_VID_ISP_VREM'];
																						$id_work_out_edit = $list_check_edit_2['ID'];
																					}
																					
																					
																					$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNO']."'),SEC_TO_TIME(TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WNO']."'))),'2','".$vid_isp."','".$id_work_out_edit."')");
																				}
																			}
																			else //ЕСЛИ ОБЕД НЕ УСТАНОВЛЕН
																			{
																				if($list_info['ID_PROPUSK']=="6")
																				{
																					//Определяем итоговую продолжительность отсутствия с режимом Свободный +
																					$query_get_summ_out_vnsm = mysqli_query($db,"SELECT TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."'))+".$total_time_out_vnsm."");
																					$list_get_summ_out_vnsm = mysqli_fetch_array($query_get_summ_out_vnsm);
																					$total_time_out_vnsm = $list_get_summ_out_vnsm[0];
																					if($total_time_out_vnsm<=$list_limit_out_vnsm[0])$vid_isp = "52"; //Если режим свободный+ и норма отсутствия не превышена
																				}
																				
																				//Ищем период в корректировках
																				$query_check_edit_3 = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."') AND DATE_TIME_END=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."') ");
																				$list_check_edit_3 = mysqli_fetch_assoc($query_check_edit_3);
																				if(mysqli_num_rows($query_check_edit_3)!=0 && $list_info['ID_PROPUSK']!="3")
																				{
																					$vid_isp = $list_check_edit_3['ID_VID_ISP_VREM'];
																					$id_work_out_edit = $list_check_edit_3['ID'];
																				}
																				
																				$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."'),SEC_TO_TIME(TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_rejim_in_day['WKS']."'))),'5','".$vid_isp."','".$id_work_out_edit."')");
																				
																			}
																		}
																		
																//7 ОПРЕДЕЛЯЕМ ПЕРИОДЫ ВНУТРИСМЕННОГО ОТСУТСТВИЯ !!!
																	
																	$query_vnutrismen = mysqli_query($db,"SELECT IF((SELECT MIN(TIME_IN) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND TIME_IN > '".$list_in_out['TIME_IN']."' AND INSTR(COMMENTS,'ERROR')=0 ) < '".$list_rejim_in_day['WKS']."' AND '".$list_in_out['TIME_OUT']."'<'".$list_rejim_in_day['WKS']."' AND '".$list_in_out['TIME_OUT']."'>'".$list_rejim_in_day['WNS']."',1,0)");
																	$list_vnutrismen = mysqli_fetch_array($query_vnutrismen);
																	if($list_vnutrismen[0]=="1")
																		{
																			//ОПРЕДЕЛЯЕМ НАЛИЧИЕ ОБЕДА ПО ГРАФИКУ РАБОТЫ СОТРУДНИКА
																			if($list_rejim_in_day['WNO']!='00:00:00' && $list_rejim_in_day['WKO']!='00:00:00') //ЕСЛИ ОБЕД УСТАНОВЛЕН
																			{
																				//Период внутрисменного отсутствия до обеда
																				$query_check_1 = mysqli_query($db,"SELECT IF('".$list_in_out['TIME_OUT']."'<'".$list_rejim_in_day['WNO']."',1,0)");
																				$list_check_1 = mysqli_fetch_array($query_check_1);
																				
																				if($list_check_1[0]=="1")
																				{
																					$query_vnutrismen_end_1 = mysqli_query($db,"SELECT IF((SELECT MIN(TIME_IN) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND TIME_IN > '".$list_in_out['TIME_IN']."' AND INSTR(COMMENTS,'ERROR')=0 ) > '".$list_rejim_in_day['WNO']."','".$list_rejim_in_day['WNO']."',(SELECT MIN(TIME_IN) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND TIME_IN > '".$list_in_out['TIME_IN']."' AND INSTR(COMMENTS,'ERROR')=0 ))");
																					$list_vnutrismen_end_1 = mysqli_fetch_array($query_vnutrismen_end_1);
																					
																					//Проверяем допустимое ли отсутствие (допустимое -1, нет -0)
																					$query_check_dopust = mysqli_query($db,"SELECT IF(TIME_TO_SEC(TIMEDIFF('".$list_vnutrismen_end_1[0]."','".$list_in_out['TIME_OUT']."'))<=60,1,0)");
																					$list_check_dopust = mysqli_fetch_array($query_check_dopust);
																					
																					if($list_info['ID_PROPUSK']=="6")
																					{
																						//Определяем итоговую продолжительность отсутствия с режимом Свободный +
																						$query_get_summ_out_vnsm = mysqli_query($db,"SELECT TIME_TO_SEC(TIMEDIFF('".$list_vnutrismen_end_1[0]."','".$list_in_out['TIME_OUT']."'))+".$total_time_out_vnsm."");
																						$list_get_summ_out_vnsm = mysqli_fetch_array($query_get_summ_out_vnsm);
																						$total_time_out_vnsm = $list_get_summ_out_vnsm[0];
																					}
																					
																					$id_work_out_edit = "";
																					if($list_info['ID_PROPUSK']=="2" || $list_info['ID_PROPUSK']=="3")$vid_isp = "13"; //Если режим суперсвободный или свободный
																					else if($list_info['ID_PROPUSK']!="2" && $list_info['ID_PROPUSK']!="3" && $list_info['ID_PROPUSK']!="6" && $list_check_dopust[0]=="1")$vid_isp = "51";
																					else if($list_info['ID_PROPUSK']=="6" && $total_time_out_vnsm<=$list_limit_out_vnsm[0])$vid_isp = "52"; //Если режим свободный+ и норма отсутствия не превышена
																					else $vid_isp="10";
																					
																					//Ищем период в корректировках
																					$query_check_edit_1 = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."') AND DATE_TIME_END=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_vnutrismen_end_1[0]."') ");
																					$list_check_edit_1 = mysqli_fetch_assoc($query_check_edit_1);
																					if(mysqli_num_rows($query_check_edit_1)!=0 && $list_info['ID_PROPUSK']!="2" && $list_info['ID_PROPUSK']!="3")
																					{
																						$vid_isp = $list_check_edit_1['ID_VID_ISP_VREM'];
																						$id_work_out_edit = $list_check_edit_1['ID'];
																					}
																					
																					$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_vnutrismen_end_1[0]."'),SEC_TO_TIME(TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_vnutrismen_end_1[0]."'))),'2','".$vid_isp."','".$id_work_out_edit."')");
																				
																				}
																				
																				
																				//Период внутрисменного отсутствия после обеда
																				$query_check_2 = mysqli_query($db,"SELECT IF((SELECT MIN(TIME_IN) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND TIME_IN > '".$list_in_out['TIME_IN']."' AND INSTR(COMMENTS,'ERROR')=0 ) > '".$list_rejim_in_day['WKO']."' AND (SELECT MIN(TIME_IN) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND TIME_IN > '".$list_in_out['TIME_IN']."' AND INSTR(COMMENTS,'ERROR')=0 ) < '".$list_rejim_in_day['WKS']."',1,0)");
																				$list_check_2 = mysqli_fetch_array($query_check_2);
																				
																				if($list_check_2[0]=="1")
																				{
																					$query_vnutrismen_beg_2 = mysqli_query($db,"SELECT IF('".$list_in_out['TIME_OUT']."' > '".$list_rejim_in_day['WKO']."','".$list_in_out['TIME_OUT']."','".$list_rejim_in_day['WKO']."')");
																					$list_vnutrismen_beg_2 = mysqli_fetch_array($query_vnutrismen_beg_2);
																					
																					//Проверяем допустимое ли отсутствие (допустимое -1, нет -0)
																					$query_check_dopust = mysqli_query($db,"SELECT IF(TIME_TO_SEC(TIMEDIFF(    (SELECT MIN(TIME_IN) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND TIME_IN > '".$list_in_out['TIME_IN']."' AND INSTR(COMMENTS,'ERROR')=0 )    ,'".$list_vnutrismen_beg_2[0]."'))<=60,1,0)");
																					$list_check_dopust = mysqli_fetch_array($query_check_dopust);
																					
																					if($list_info['ID_PROPUSK']=="6")
																					{
																						//Определяем итоговую продолжительность отсутствия с режимом Свободный +
																						$query_get_summ_out_vnsm = mysqli_query($db,"SELECT TIME_TO_SEC(TIMEDIFF(    (SELECT MIN(TIME_IN) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND TIME_IN > '".$list_in_out['TIME_IN']."' AND INSTR(COMMENTS,'ERROR')=0 )    ,'".$list_vnutrismen_beg_2[0]."'))+".$total_time_out_vnsm."");
																						$list_get_summ_out_vnsm = mysqli_fetch_array($query_get_summ_out_vnsm);
																						$total_time_out_vnsm = $list_get_summ_out_vnsm[0];
																					}
																					
																					$id_work_out_edit = "";
																					if($list_info['ID_PROPUSK']=="2" || $list_info['ID_PROPUSK']=="3") $vid_isp="13"; //Если режим суперсвободный или свободный
																					else if($list_info['ID_PROPUSK']!="2" && $list_info['ID_PROPUSK']!="3" && $list_info['ID_PROPUSK']!="6" && $list_check_dopust[0]=="1")$vid_isp = "51";
																					else if($list_info['ID_PROPUSK']=="6" && $total_time_out_vnsm<=$list_limit_out_vnsm[0])$vid_isp = "52"; //Если режим свободный+ и норма отсутствия не превышена
																					else $vid_isp="10";

																					
																					//Ищем период в корректировках
																					$query_check_edit_2 = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_vnutrismen_beg_2[0]."') AND DATE_TIME_END=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),(SELECT MIN(TIME_IN) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND TIME_IN > '".$list_in_out['TIME_IN']."' AND INSTR(COMMENTS,'ERROR')=0 )) ");
																					$list_check_edit_2 = mysqli_fetch_assoc($query_check_edit_2);
																					if(mysqli_num_rows($query_check_edit_2)!=0 && $list_info['ID_PROPUSK']!="2" && $list_info['ID_PROPUSK']!="3")
																					{
																						$vid_isp = $list_check_edit_2['ID_VID_ISP_VREM'];
																						$id_work_out_edit = $list_check_edit_2['ID'];
																					}
																					
																					
																					$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_vnutrismen_beg_2[0]."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),(SELECT MIN(TIME_IN) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND TIME_IN > '".$list_in_out['TIME_IN']."' AND INSTR(COMMENTS,'ERROR')=0 )),SEC_TO_TIME(TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_vnutrismen_beg_2[0]."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),(SELECT MIN(TIME_IN) FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND TIME_IN > '".$list_in_out['TIME_IN']."' AND INSTR(COMMENTS,'ERROR')=0 )))),'3','".$vid_isp."','".$id_work_out_edit."')");
																				
																				}
																			}
																			else //ЕСЛИ ОБЕД НЕ УСТАНОВЛЕН
																			{
																				if($list_info['ID_PROPUSK']=="6")
																				{
																					//Определяем итоговую продолжительность отсутствия с режимом Свободный +
																					$query_get_summ_out_vnsm = mysqli_query($db,"SELECT TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_IN']."'))+".$total_time_out_vnsm."");
																					$list_get_summ_out_vnsm = mysqli_fetch_array($query_get_summ_out_vnsm);
																					$total_time_out_vnsm = $list_get_summ_out_vnsm[0];
																				}
																				
																					$id_work_out_edit = "";
																					if($list_info['ID_PROPUSK']=="2" || $list_info['ID_PROPUSK']=="3") $vid_isp="13"; //Если пропуск суперсвободный или свободный
																					else if($list_info['ID_PROPUSK']=="6" && $total_time_out_vnsm<=$list_limit_out_vnsm[0])$vid_isp = "52"; //Если режим свободный+ и норма отсутствия не превышена
																					else $vid_isp="10";
																					
																					//Ищем период в корректировках
																					$query_check_edit_3 = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."') AND DATE_TIME_END=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_IN']."') ");
																					$list_check_edit_3 = mysqli_fetch_assoc($query_check_edit_3);
																					if(mysqli_num_rows($query_check_edit_3)!=0 && $list_info['ID_PROPUSK']!="2" && $list_info['ID_PROPUSK']!="3")
																					{
																						$vid_isp = $list_check_edit_3['ID_VID_ISP_VREM'];
																						$id_work_out_edit = $list_check_edit_3['ID'];
																					}
																					
																					$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_IN']."'),SEC_TO_TIME(TIMESTAMPDIFF(SECOND,TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_OUT']."'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'".$list_in_out['TIME_IN']."'))),'5','".$vid_isp."','".$id_work_out_edit."')");
																				
																			}
																		}
																
															
														}
				//Дальше корректировал
														//Определяем целосменное отсутствие в случае если работник присутствовал вне смены
															//$query_celosmen = mysqli_query($db,"SELECT * FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND INSTR(COMMENTS,'ERROR')=0  AND YEAR(DATE_IN) = '".$_GET['year']."' AND MONTH(DATE_IN) = '".$_GET['month']."' AND DAY(DATE_IN) = '".$d."' AND ((TIME_IN >= '".$list_rejim_in_day['WNS']."' AND TIME_IN <= '".$list_rejim_in_day['WNO']."') OR (TIME_IN >= '".$list_rejim_in_day['WKO']."' AND TIME_IN <= '".$list_rejim_in_day['WKS']."') OR (TIME_OUT >= '".$list_rejim_in_day['WNS']."' AND TIME_OUT <= '".$list_rejim_in_day['WNO']."') OR (TIME_OUT >= '".$list_rejim_in_day['WKO']."' AND TIME_OUT <= '".$list_rejim_in_day['WKS']."') OR (TIME_IN <= '".$list_rejim_in_day['WNS']."' AND TIME_OUT >= '".$list_rejim_in_day['WNO']."') OR (TIME_IN <= '".$list_rejim_in_day['WKO']."' AND TIME_OUT >= '".$list_rejim_in_day['WKS']."')) ");
															//$list_celosmen = mysqli_fetch_array($query_celosmen);
															$query_celosmen = mysqli_query($db,"SELECT * FROM IN_OUT WHERE TABN = '".$list['TABN']."' AND INSTR(COMMENTS,'ERROR')=0 AND ((TIMESTAMP(DATE_IN,TIME_IN) <= '".$list_rejim_in_day_datetime['NS']."' AND TIMESTAMP(DATE_OUT,TIME_OUT) >= '".$list_rejim_in_day_datetime['KS']."') OR ('".$list_rejim_in_day_datetime['NS']."' < TIMESTAMP(DATE_OUT,TIME_OUT) AND TIMESTAMP(DATE_OUT,TIME_OUT) < '".$list_rejim_in_day_datetime['KS']."') OR ('".$list_rejim_in_day_datetime['NS']."' < TIMESTAMP(DATE_IN,TIME_IN) AND TIMESTAMP(DATE_IN,TIME_IN) < '".$list_rejim_in_day_datetime['KS']."') )");
															$list_celosmen = mysqli_fetch_array($query_celosmen);
															
															if(mysqli_num_rows($query_celosmen)==0)//Если периоды не попадают на смену считаем целосменным отсутствием
															{
																$id_work_out_edit = "";
																$vid_isp = "11";
																
																//Ищем период в корректировках
																$query_check_edit = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG='".$list_rejim_in_day_datetime['NS']."' AND DATE_TIME_END='".$list_rejim_in_day_datetime['KS']."' ");
																$list_check_edit = mysqli_fetch_assoc($query_check_edit);
																if(mysqli_num_rows($query_check_edit)!=0)
																{
																	$vid_isp = $list_check_edit['ID_VID_ISP_VREM'];
																	$id_work_out_edit = $list_check_edit['ID'];
																}
																
																$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_rejim_in_day_datetime['NS']."','".$list_rejim_in_day_datetime['KS']."',SEC_TO_TIME(TIMESTAMPDIFF(SECOND,'".$list_rejim_in_day_datetime['NS']."','".$list_rejim_in_day_datetime['KS']."')) - SEC_TO_TIME(TIMESTAMPDIFF(SECOND,'".$list_rejim_in_day_datetime['NO']."','".$list_rejim_in_day_datetime['KO']."')),'','".$vid_isp."','".$id_work_out_edit."')");
															}
														
														//Определяем сверхурочку без фиксации в СКУД в корректировках
														//$query_SV = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."' AND DAY(DATE_TIME_BEG)='".$d."' AND ID_VID_ISP_VREM IN(7,48)");
														//while($list_SV = mysqli_fetch_assoc($query_SV))
														//{
															//Записываем периоды сверхурочной работы
															//$query_insert_work_out_edit = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list_SV['TABN']."','".$list_SV['DATE_TIME_BEG']."','".$list_SV['DATE_TIME_END']."','".$list_SV['PERIOD_TIME']."','".$list_SV['ID_PERIOD_DAY']."','".$list_SV['ID_VID_ISP_VREM']."','".$list_SV['ID']."')");
														//}
													}
													
												}
												
											
												
											}
										//--------------------------------------------------------------------------------

										//----------------------------------------- ВЫХОДНЫЕ дни (кроме работников с Административным пропуском) и РАБОЧИЕ дни (для работников с Гибким пропуском) -------------------------
											if(($list_info['ID_PROPUSK']!="4" && ($list_rejim_in_day['STATUS']=="V" || $list_rejim_in_day['STATUS']=="P")) || $list_info['ID_PROPUSK']=="5")//Кроме работников с административным пропуском
											{
												$vid_isp_1c="?";
												if		(strpos($list_day_from_1c[$d],"Б")!==false)		{$vid_isp_1c="17";}
												else if	(strpos($list_day_from_1c[$d],"ОТ")!==false)	{$vid_isp_1c="18";}
												else if	(strpos($list_day_from_1c[$d],"ДО")!==false)	{$vid_isp_1c="12";}
												else if	(strpos($list_day_from_1c[$d],"У")!==false)		{$vid_isp_1c="19";}
												else if	(strpos($list_day_from_1c[$d],"К")!==false)		{$vid_isp_1c="21";}
												else if	(strpos($list_day_from_1c[$d],"ДУ")!==false)	{$vid_isp_1c="20";}
												else if	(strpos($list_day_from_1c[$d],"НН")!==false)	{$vid_isp_1c="22";}
												
												if($vid_isp_1c!="?")$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),'00:00:00','','".$vid_isp_1c."','')");
												
												$otrab_vrem_summ = 0;
												$obed = 0;
												$otrab_vrem_itog = 0;
												while ($list_in_out = mysqli_fetch_assoc($query_in_out))//Перебираем периоды присутствия
													{
														echo $list_in_out['TABN']."_".$list_in_out['DATE_IN']."_".$list_in_out['DATE_OUT']."<br>";
														//Определяем начало и окончание периода присутствия ограниченные текущим днем
														$query_in_out_in_day = mysqli_query($db,"SELECT IF(TIMESTAMP('".$list_in_out['DATE_IN']."','".$list_in_out['TIME_IN']."') < TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),TIMESTAMP('".$list_in_out['DATE_IN']."','".$list_in_out['TIME_IN']."')) AS `IN`, IF(TIMESTAMP('".$list_in_out['DATE_OUT']."','".$list_in_out['TIME_OUT']."') > TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'24:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'24:00:00'),TIMESTAMP('".$list_in_out['DATE_OUT']."','".$list_in_out['TIME_OUT']."')) AS `OUT` ");
														//$query_in_out_in_day = mysqli_query($db,"SELECT IF(TIMESTAMP('".$list_in_out['DATE_IN']."','".$list_in_out['TIME_IN']."') < TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00')) AS IN, IF(TIMESTAMP('".$list_in_out['DATE_OUT']."','".$list_in_out['TIME_OUT']."') > TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'24:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'24:00:00')) AS OUT ");
														$list_in_out_in_day = mysqli_fetch_assoc($query_in_out_in_day);
														
														//Вычисляем продолжительность между входом и выходом
															$query_otrab_vrem = mysqli_query($db,"SELECT TIMESTAMPDIFF(SECOND,'".$list_in_out_in_day['IN']."','".$list_in_out_in_day['OUT']."')");
															$list_otrab_vrem = mysqli_fetch_array($query_otrab_vrem);
														//------------------------------------------------------
														
														//Суммируем рабочее время по всем входам-выходам текущего дня
															$otrab_vrem_summ = $otrab_vrem_summ + $list_otrab_vrem[0];
														//------------------------------------------------------
													}
												//Определяем продолжительность обеденного перерыва
													if($otrab_vrem_summ > 14400 && $otrab_vrem_summ <= 16200) {$obed = $otrab_vrem_summ - 14400;}		//если время присутствия больше 4 часов и не более 4.5 часа, то снимается время присутствия в диапазоне 4-4.5 часа
													else if($otrab_vrem_summ > 16200) {$obed = 30*60;}													//если отработанное время больше 4.5 часа, то снимается 30 мин.
												//----------------------------------------------------------
												
												//Уменьшаем отработанное время на обед
													$otrab_vrem_itog = $otrab_vrem_summ - $obed;
												//----------------------------------------------------------
												
												//Записываем результат
													if($list_info['ID_PROPUSK']=="5" && ($list_rejim_in_day_base['STATUS']=="R" || $list_rejim_in_day_base['STATUS']=="PP") && $otrab_vrem_itog > 0)//Если гибкий режим, рабочий день по основному графику, есть явки и отработанное время больше 0
													{
														echo "INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),SEC_TO_TIME(".$otrab_vrem_itog."),'','29','')<br>";
														$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),SEC_TO_TIME(".$otrab_vrem_itog."),'','29','')");
													}
													else if($otrab_vrem_itog > 0)//
													{
														//Ищем запись работы в выходные и праздничные дни в корректировках
														$query_check_edit_VP = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00') AND DATE_TIME_END=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00') AND ID_VID_ISP_VREM IN(43,24,49) ");
														$list_check_edit_VP = mysqli_fetch_assoc($query_check_edit_VP);

														if(mysqli_num_rows($query_check_edit_VP)>0)//Если находим, то добавляем записть из корректировки
														{
															$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_check_edit_VP['DATE_TIME_BEG']."','".$list_check_edit_VP['DATE_TIME_END']."','".$list_check_edit_VP['PERIOD_TIME']."','".$list_check_edit_VP['ID_PERIOD_DAY']."','".$list_check_edit_VP['ID_VID_ISP_VREM']."','".$list_check_edit_VP['ID']."')");
														}
														else//Если не находим, то записываем как присутствие
														{
															$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."',TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00'),SEC_TO_TIME(".$otrab_vrem_itog."),'','16','')");
														}
													}
												//----------------------------------------------------------
											}
										
										
										//Восстанавливаем из WORK_OUT_EDIT присутствие и работу без фиксации в СКУД в сверхурочное время, в выходные и нерабочие праздничные дни, на гибком режиме учета
											$query_bez_SKUD = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."' AND DAY(DATE_TIME_BEG)='".$d."' AND ID_VID_ISP_VREM IN(7,48,24,49,32)");
											while($list_bez_SKUD = mysqli_fetch_assoc($query_bez_SKUD))
											{
												//Записываем периоды
												$query_insert_work_out_edit = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list_bez_SKUD['TABN']."','".$list_bez_SKUD['DATE_TIME_BEG']."','".$list_bez_SKUD['DATE_TIME_END']."','".$list_bez_SKUD['PERIOD_TIME']."','".$list_bez_SKUD['ID_PERIOD_DAY']."','".$list_bez_SKUD['ID_VID_ISP_VREM']."','".$list_bez_SKUD['ID']."')");
											}
											//Определяем периоды присутствия или работы в сверхурочное время без фиксации в СКУД в корректировках
											/*$query_SV = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."' AND DAY(DATE_TIME_BEG)='".$d."' AND ID_VID_ISP_VREM IN(7,48)");
											while($list_SV = mysqli_fetch_assoc($query_SV))
											{
												//Записываем периоды сверхурочной работы
												$query_insert_work_out_edit = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list_SV['TABN']."','".$list_SV['DATE_TIME_BEG']."','".$list_SV['DATE_TIME_END']."','".$list_SV['PERIOD_TIME']."','".$list_SV['ID_PERIOD_DAY']."','".$list_SV['ID_VID_ISP_VREM']."','".$list_SV['ID']."')");
											}
											
											//Определяем периоды присутствия или работы в выходные и праздничные дни без фиксации в СКУД в корректировках
											$query_check_edit_VP = mysqli_query($db,"SELECT * FROM WORK_OUT_EDIT WHERE TABN='".$list['TABN']."' AND DATE_TIME_BEG=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00') AND DATE_TIME_END=TIMESTAMP(SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY),'00:00:00') AND ID_VID_ISP_VREM IN(24,49) ");
											$list_check_edit_VP = mysqli_fetch_assoc($query_check_edit_VP);
											if(mysqli_num_rows($query_check_edit_VP)>0)//Если находим, то добавляем записть из корректировки
											{
												$query_insert_work_out = mysqli_query($db,"INSERT INTO WORK_OUT VALUES(NULL,'".$list['TABN']."','".$list_check_edit_VP['DATE_TIME_BEG']."','".$list_check_edit_VP['DATE_TIME_END']."','".$list_check_edit_VP['PERIOD_TIME']."','".$list_check_edit_VP['ID_PERIOD_DAY']."','".$list_check_edit_VP['ID_VID_ISP_VREM']."','".$list_check_edit_VP['ID']."')");
											}*/
										//--------------------------------------------------------------------------------
									}
							}
							$fakt++;
							$query_set_status_process_fakt = mysqli_query($db,"UPDATE STATUS_PROCESS SET FAKT='".$fakt."' WHERE PROCESS='CREATE_OTRAB_VREM'");
						}
					//УДАЛЯЕМ ЛИШНИЕ ЗАПИСИ КОРРЕКТИРОВОК ТЕКУЩЕГО МЕСЯЦА
					$clear_work_out_edit = mysqli_query($db,"DELETE FROM WORK_OUT_EDIT WHERE YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND ID NOT IN(SELECT ID_WORK_OUT_EDIT FROM WORK_OUT WHERE ID_WORK_OUT_EDIT <>'') ");

					//echo "Расчет ".$_GET['month']. " месяца, ".$_GET['year']." года завершен!";
					
					$query_set_status_process_end = mysqli_query($db,"UPDATE STATUS_PROCESS SET STATUS='0', DATE_TIME_END=TIMESTAMP(CURDATE(),CURTIME()) WHERE PROCESS='CREATE_OTRAB_VREM'");
					//}

					//Конец процесса расчета отработанного времени




					//Запускаем процесс сохранения сводной таблицы
					require_once("saveSvod.php");
					

					//Запускаем расчет использования рабочего времени
					$_GET['action']="start_otrab";
					
					if($_GET['list_id_sel']!="")
					{
						$_GET['list_tabn'] = $_GET['list_id_sel'];
					}
					else
					{
						$_GET['list_tabn']="";
					}

					require_once("../ispolz_vrem/save.php");
					$query_set_status_process_end_group = mysqli_query($db,"UPDATE STATUS_PROCESS SET STATUS_GROUP='0' WHERE PROCESS='CREATE_OTRAB_VREM'");

				}



				if($_GET['action']=="clear")
				{
					$clear = mysqli_query($db,"DELETE FROM WORK_OUT WHERE YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' ");
					//Чистим таблицу
					$clear_otrab_vrem = mysqli_query($db,"DELETE FROM OTRAB_VREM WHERE YEAR = '".$_GET['year']."' AND MONTH = '".$_GET['month']."' ");

					echo "Текущие данные ".$_GET['month']. " месяца, ".$_GET['year']." года удалены!\rИстория корректировок сохранена!";
				}


				if($_GET['action']=="clear_all")
				{
					$clear = mysqli_query($db,"DELETE FROM WORK_OUT WHERE YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' ");
					$clear_edit = mysqli_query($db,"DELETE FROM WORK_OUT_EDIT WHERE YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' ");
					//Чистим таблицу
					$clear_otrab_vrem = mysqli_query($db,"DELETE FROM OTRAB_VREM WHERE YEAR = '".$_GET['year']."' AND MONTH = '".$_GET['month']."' ");

					echo "Текущие данные и корректировки ".$_GET['month']. " месяца, ".$_GET['year']." года удалены!";
				}


				if($_GET['action']=="edit_vid_isp_vrem")
				{
					//Определяем текущий вид использования рабочего времени
					$query_get_first_vid_isp_vrem = mysqli_query($db,"SELECT ID_VID_ISP_VREM FROM WORK_OUT WHERE ID='".$_GET['id_work_out']."' ");
					$list_get_first_vid_isp_vrem = mysqli_fetch_array($query_get_first_vid_isp_vrem);
					
					//Обновление вида использования рабочего времени
					$update = mysqli_query($db,"UPDATE WORK_OUT SET ID_VID_ISP_VREM='".$_GET['id_vid_isp_vrem']."' WHERE ID='".$_GET['id_work_out']."' ");
					
					//Проверяем на наличие записи корректировки
					$query_check_edit = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE ID='".$_GET['id_work_out']."' ");
					$list_check_edit = mysqli_fetch_assoc($query_check_edit);
					
					//Если записи корректировки нет, то записываем. Если есть, то обновляем
					if($list_check_edit['ID_WORK_OUT_EDIT']=="")
					{
						$query_insert_work_out_edit = mysqli_query($db,"INSERT INTO WORK_OUT_EDIT VALUES(NULL,'".$list_check_edit['TABN']."','".$list_check_edit['DATE_TIME_BEG']."','".$list_check_edit['DATE_TIME_END']."','".$list_check_edit['PERIOD_TIME']."','".$list_check_edit['PERIOD_TIME']."','".$list_check_edit['ID_PERIOD_DAY']."','".$list_check_edit['ID_VID_ISP_VREM']."','".$list_get_first_vid_isp_vrem[0]."',TIMESTAMP(CURDATE(),CURTIME()),'".$_SERVER['REMOTE_USER']."','0')");
						
						$save_id_work_out_edit = mysqli_query($db,"UPDATE WORK_OUT SET ID_WORK_OUT_EDIT=LAST_INSERT_ID() WHERE ID='".$_GET['id_work_out']."' ");
						
						
					}
					else
					{
						$query_update_work_out_edit = mysqli_query($db,"UPDATE WORK_OUT_EDIT SET TABN='".$list_check_edit['TABN']."', DATE_TIME_BEG='".$list_check_edit['DATE_TIME_BEG']."', DATE_TIME_END='".$list_check_edit['DATE_TIME_END']."', PERIOD_TIME='".$list_check_edit['PERIOD_TIME']."', ID_PERIOD_DAY='".$list_check_edit['ID_PERIOD_DAY']."', ID_VID_ISP_VREM='".$list_check_edit['ID_VID_ISP_VREM']."', DATE_TIME_EDIT=TIMESTAMP(CURDATE(),CURTIME()), IP='".$_SERVER['REMOTE_USER']."' WHERE ID='".$list_check_edit['ID_WORK_OUT_EDIT']."' ");
					}

					//Добавляем или обновляем запись в WORKS_SV_NORM для подтвержденного сверхнормативного времени
					if($_GET['id_vid_isp_vrem']=="42" || $_GET['id_vid_isp_vrem']=="43" || $_GET['id_vid_isp_vrem']=="44" || $_GET['id_vid_isp_vrem']=="45" || $_GET['id_vid_isp_vrem']=="31")
					{
						$query_select_WORKS_SV_NORM = mysqli_query($db,"SELECT * FROM WORKS_SV_NORM WHERE TABN='".$list_check_edit['TABN']."' AND DATE_TIME_BEG='".$list_check_edit['DATE_TIME_BEG']."' AND DATE_TIME_END='".$list_check_edit['DATE_TIME_END']."' ");
						$list_select_WORKS_SV_NORM = mysqli_fetch_assoc($query_select_WORKS_SV_NORM);
						
						if(mysqli_num_rows($query_select_WORKS_SV_NORM)>0)$query_update_WORKS_SV_NORM = mysqli_query($db,"UPDATE WORKS_SV_NORM SET ID_WORK_OUT_EDIT=LAST_INSERT_ID(), STATUS_SET='1', ID_SPR_TEMS_WORKS='1', TEXT_WORKS='', IP_EDIT='".$_SERVER['REMOTE_USER']."', DATE_TIME_EDIT=TIMESTAMP(CURDATE(),CURTIME()), RES_ACCEPT='1', IP_ACCEPT='".$_SERVER['REMOTE_USER']."', DATE_TIME_ACCEPT=TIMESTAMP(CURDATE(),CURTIME()) WHERE ID='".$list_select_WORKS_SV_NORM['ID']."'");
						else $query_insert_WORKS_SV_NORM = mysqli_query($db,"INSERT INTO WORKS_SV_NORM VALUES(NULL,'".$list_check_edit['TABN']."','".$list_check_edit['DATE_TIME_BEG']."','".$list_check_edit['DATE_TIME_END']."',LAST_INSERT_ID(),'1','1','','".$_SERVER['REMOTE_USER']."',TIMESTAMP(CURDATE(),CURTIME()),'1','".$_SERVER['REMOTE_USER']."',TIMESTAMP(CURDATE(),CURTIME()) )");
					}
				}


				if($_GET['action']=="edit_period_time")
				{
					$update_work_out = mysqli_query($db,"UPDATE WORK_OUT SET PERIOD_TIME='".$_GET['period_time']."' WHERE ID='".$_GET['id']."' ");
					$update_work_out_edit = mysqli_query($db,"UPDATE WORK_OUT_EDIT SET PERIOD_TIME_FIRST='".$_GET['period_time']."', PERIOD_TIME='".$_GET['period_time']."' WHERE ID=(SELECT ID_WORK_OUT_EDIT FROM WORK_OUT WHERE ID='".$_GET['id']."') ");
					
					$query_select_LIST_SV_NORM = mysqli_query($db,"SELECT * FROM LIST_SV_NORM WHERE CONCAT(TABN,DATE_TIME_BEG)=(SELECT CONCAT(TABN,DATE_TIME_BEG) FROM WORK_OUT WHERE ID='".$_GET['id']."') ");
					$list_select_LIST_SV_NORM = mysqli_fetch_assoc($query_select_LIST_SV_NORM);
					
					if(mysqli_num_rows($query_select_LIST_SV_NORM)>0)
					{
						$query_update_LIST_SV_NORM = mysqli_query($db,"UPDATE LIST_SV_NORM SET TIME_FIRST='".$_GET['period_time']."',TIME='".$_GET['period_time']."' WHERE ID='".$list_select_LIST_SV_NORM['ID']."' ");
					
						//Обновляем время в WORKS_SV_NORM
						$query_update_WORKS_SV_NORM = mysqli_query($db,"UPDATE WORKS_SV_NORM_NEW SET TIME_SUMM=(SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(TIME))) FROM LIST_SV_NORM WHERE ID_WORKS_SV_NORM='".$list_select_LIST_SV_NORM['ID_WORKS_SV_NORM']."') WHERE ID='".$list_select_LIST_SV_NORM['ID_WORKS_SV_NORM']."' ");
					}
				}
			
			$SET_LOCK_WORK_OUT = mysqli_query($db,"UPDATE LOCK_WORK_OUT SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
			$SET_LOCK_WORK_OUT_EDIT = mysqli_query($db,"UPDATE LOCK_WORK_OUT_EDIT SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
			$SET_LOCK_OTRAB_VREM = mysqli_query($db,"UPDATE LOCK_OTRAB_VREM SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
			$SET_LOCK_ISPOLZ_VREM = mysqli_query($db,"UPDATE LOCK_ISPOLZ_VREM SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
			$SET_LOCK_WORK_SV_NORM = mysqli_query($db,"UPDATE LOCK_WORK_SV_NORM SET LOCK_STATUS='0', REMOTE_USER='".$_SERVER['REMOTE_USER']."'");
			
			$completed="on";
	}
	else
	{
		$completed="off";
	}
}

//echo $completed;

?>