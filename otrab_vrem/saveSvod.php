<?php
//session_start();
error_reporting(E_ALL ^ E_NOTICE);

if($_GET['action']=="save_svod")
{
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
}


//Начало процесса сохранения сводной таблицы

					//Обновляем статус процесса
					if($_GET['list_id_sel']!="") $query_set_status_process_beg = mysqli_query($db,"UPDATE STATUS_PROCESS SET IP='".$_SERVER['REMOTE_USER']."', STATUS='1', DATE_TIME_BEG=TIMESTAMP(CURDATE(),CURTIME()), DATE_TIME_END=NULL, TARGET=(SELECT COUNT(DISTINCT TABN) FROM WORK_OUT WHERE YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."' AND TABN IN(".$_GET['list_id_sel'].")), FAKT='0' WHERE PROCESS='UPDATE_SVOD_OTRAB_VREM'");
					else if($_GET['list_id_sel']=="") $query_set_status_process_beg = mysqli_query($db,"UPDATE STATUS_PROCESS SET IP='".$_SERVER['REMOTE_USER']."', STATUS='1', DATE_TIME_BEG=TIMESTAMP(CURDATE(),CURTIME()), DATE_TIME_END=NULL, TARGET=(SELECT COUNT(DISTINCT TABN) FROM WORK_OUT WHERE YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."'), FAKT='0' WHERE PROCESS='UPDATE_SVOD_OTRAB_VREM'");

					//СОХРАНЯЕМ СВОДНУЮ ТАБЛИЦУ
					if (strlen($_GET['month'])==1){$month1="0".$_GET['month']; $month2="0".($_GET['month']+1);} else {$month1=$_GET['month'];$month2=($_GET['month']+1);}
					//Дата начала месяца
					$date1 = $_GET['year']."-".$month1."-01";

					$query_date2 = mysqli_query($db,"SELECT DAYOFMONTH(SUBDATE('".$_GET['year']."-".$month2."-01',INTERVAL 1 DAY))");
					$res_date2 = mysqli_fetch_array($query_date2);
					//Дата конца месяца
					$date2 = $_GET['year']."-".$month1."-".$res_date2[0];
					
					if($_GET['list_id_sel']!="")
					{
						$query = mysqli_query($db,"SELECT DISTINCT TABN FROM WORK_OUT WHERE TABN IN(".$_GET['list_id_sel'].") AND YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."'");
					}
					else if($_GET['list_id_sel']=="")
					{
						//Удаляем записи в сводной таблице по которым отсутствуют данные в WORK_OUT
						$query_delete = mysqli_query($db,"DELETE FROM OTRAB_VREM WHERE YEAR='".$_GET['year']."' AND MONTH='".$_GET['month']."' AND TABN NOT IN(SELECT DISTINCT TABN FROM WORK_OUT WHERE YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."')");
						$query = mysqli_query($db,"SELECT DISTINCT TABN FROM WORK_OUT WHERE YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."'");
					}
				
					$fakt=0;
					while ($list = mysqli_fetch_assoc($query))
						{
							//echo "[".$list['TABN']."] ";
							///*
							$query_info_1 = mysqli_query($db,"SELECT * FROM PERSONAL WHERE TABN='".$list['TABN']."' AND DATE_BEG=(SELECT MAX(DATE_BEG) FROM PERSONAL WHERE TABN='".$list["TABN"]."' AND DATE_BEG<='".$date2."' AND UVOLEN<>'1' AND DEKRET<>'1')");
							$list_info_1 = mysqli_fetch_assoc($query_info_1);
							
							
							$query_prof = mysqli_query($db,"SELECT * FROM SPR_PROF WHERE ID = '".$list_info_1["ID_PROFES"]."'");
							$list_prof = mysqli_fetch_assoc($query_prof);
							
							$query_podr = mysqli_query($db,"SELECT * FROM SPR_PODRAZD WHERE ID = '".$list_info_1["ID_PODRAZD"]."'");
							$list_podr = mysqli_fetch_assoc($query_podr);

							$query_otrab_vrem_summ_total = mysqli_query($db,"SELECT ROUND(SUM(TIME_TO_SEC(PERIOD_TIME))/3600,2) FROM WORK_OUT WHERE TABN='".$list["TABN"]."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND (SELECT WORKTIME FROM SPR_VID_ISP_VREM WHERE SPR_VID_ISP_VREM.ID=WORK_OUT.ID_VID_ISP_VREM)='1'");
							$list_otrab_vrem_summ_total = mysqli_fetch_array($query_otrab_vrem_summ_total);
							
							$query_otrab_vrem_summ_ur = mysqli_query($db,"SELECT ROUND(SUM(TIME_TO_SEC(PERIOD_TIME))/3600,2) FROM WORK_OUT WHERE TABN='".$list["TABN"]."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND (SELECT WORKTIME FROM SPR_VID_ISP_VREM WHERE SPR_VID_ISP_VREM.ID=WORK_OUT.ID_VID_ISP_VREM)='1' AND ID_VID_ISP_VREM NOT IN(42,43,44,45,48,49)");
							$list_otrab_vrem_summ_ur = mysqli_fetch_array($query_otrab_vrem_summ_ur);
							
							$query_otrab_vrem_summ_svnorm = mysqli_query($db,"SELECT ROUND(SUM(TIME_TO_SEC(PERIOD_TIME))/3600,2) FROM WORK_OUT WHERE TABN='".$list["TABN"]."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND (SELECT WORKTIME FROM SPR_VID_ISP_VREM WHERE SPR_VID_ISP_VREM.ID=WORK_OUT.ID_VID_ISP_VREM)='1' AND ID_VID_ISP_VREM IN(42,43,44,45,48,49)");
							$list_otrab_vrem_summ_svnorm = mysqli_fetch_array($query_otrab_vrem_summ_svnorm);

							$query_max_day = mysqli_query($db,"SELECT DAY(MAX(DATE_TIME_BEG)) FROM WORK_OUT WHERE TABN='".$list['TABN']."' AND YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."' ");
							$list_max_day = mysqli_fetch_array($query_max_day);
							
							$volues_volue = "";
							
							for($d=1; $d<=31; $d++)
							{
								if($d <= $list_max_day[0])
								{
									//Определяем необходимость пересчета текущего дня
									if(isset($list_days))
									{
										$array_days = explode(',',$list_days);
										for($i_d=0;$i_d<count($array_days);$i_d++)
										{
											if($d==$array_days[$i_d])
											{
												$update_day="on";
												break;
											}
											else
											{
												$update_day="off";
											}
										}
									}
									else
									{
										$update_day="on";
									}
									
									
									//Если переменная списка дней не определена или день из списка равен текущему дню, то производим обновление значения по текущему дню
									if($update_day=="on")
									{
										//Определяем параметры сотрудника, действующие на текущую дату
										$query_info = mysqli_query($db,"SELECT * FROM PERSONAL WHERE TABN='".$list["TABN"]."' AND DATE_BEG <= SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY) AND DATE_END >= SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY)");
										$list_info = mysqli_fetch_assoc($query_info);
										
										//Режим дня у сотрудника
										$query_rejim_in_day = mysqli_query($db,"SELECT * FROM SPR_GRAF_DNI WHERE ID_GRAF='".$list_info['ID_GRAF']."' AND DATE=SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY)  ");
										$list_rejim_in_day = mysqli_fetch_assoc($query_rejim_in_day);				
										
										//Режим дня по основному графику
										$query_rejim_in_day_base = mysqli_query($db,"SELECT * FROM SPR_GRAF_DNI WHERE ID_GRAF='1' AND DATE=SUBDATE('".$date1."',INTERVAL -".($d-1)." DAY)  ");
										$list_rejim_in_day_base = mysqli_fetch_assoc($query_rejim_in_day_base);
										
										if($list_info['ID_PROPUSK']!="5")
										{
											if($list_rejim_in_day['STATUS']=="V") 		{$background="V"; $type_day="В";}
											if($list_rejim_in_day['STATUS']=="P") 		{$background="V"; $type_day="П";}
											if($list_rejim_in_day['STATUS']=="PP") 		{$background="R"; $type_day="ПП";}
											if($list_rejim_in_day['STATUS']=="R") 		{$background="R"; $type_day="Р";}
										}
										else
										{
											if($list_rejim_in_day_base['STATUS']=="V") 												{$background="V"; $type_day="В";}
											if($list_rejim_in_day_base['STATUS']=="P") 												{$background="V"; $type_day="П";}
											if($list_rejim_in_day_base['STATUS']=="PP" || $list_rejim_in_day_base['STATUS']=="R") 	{$background="R"; $type_day="ГР";}
										}
										

										$query_check_celosmen = mysqli_query($db,"SELECT ID_VID_ISP_VREM FROM WORK_OUT WHERE TABN='".$list["TABN"]."' AND YEAR(DATE_TIME_BEG)='".$_GET['year']."' AND MONTH(DATE_TIME_BEG)='".$_GET['month']."' AND DAY(DATE_TIME_BEG)='".$d."' AND ID_VID_ISP_VREM IN (4,11,12,17,18,19,20,21,22)");
										$list_check_celosmen = mysqli_fetch_array($query_check_celosmen);
										
										$query_otrab_vrem_ur = mysqli_query($db,"SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(PERIOD_TIME))) FROM WORK_OUT WHERE TABN='".$list["TABN"]."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$d."' AND (SELECT WORKTIME FROM SPR_VID_ISP_VREM WHERE SPR_VID_ISP_VREM.ID=WORK_OUT.ID_VID_ISP_VREM)='1' AND ID_VID_ISP_VREM NOT IN(42,43,44,45,48,49)");
										$list_otrab_vrem_ur = mysqli_fetch_array($query_otrab_vrem_ur);
										
										$query_otrab_vrem_svnorm = mysqli_query($db,"SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(PERIOD_TIME))) FROM WORK_OUT WHERE TABN='".$list["TABN"]."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$d."' AND (SELECT WORKTIME FROM SPR_VID_ISP_VREM WHERE SPR_VID_ISP_VREM.ID=WORK_OUT.ID_VID_ISP_VREM)='1' AND ID_VID_ISP_VREM IN(42,43,44,45,48,49)");
										$list_otrab_vrem_svnorm = mysqli_fetch_array($query_otrab_vrem_svnorm);
										
											if(mysqli_num_rows($query_info)==0)
											{
												$volues_volue .= "'|||BLACK_".$background."'";
											}
											else if($list_info['DEKRET']=="1")
											{
												//$volues_style .= "'BLACK_".$background."'";
												$volues_volue .= "'ОЖ|||BLACK_".$background."'";
											}
											else if($list_info['UVOLEN']=="1")
											{
												//$volues_style .= "'BLACK_".$background."'";
												$volues_volue .= "'УВ|||BLACK_".$background."'";
											}
											else if($list_info['UVOLEN']!="1" && $list_info['DEKRET']!="1" && mysqli_num_rows($query_check_celosmen)==0)//Если не целосменное отсутствие
											{
													$query_check_narush = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE TABN='".$list["TABN"]."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$d."' AND ID_VID_ISP_VREM IN (8,9,10)");
													$query_check_edit = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE TABN='".$list["TABN"]."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$d."' AND ID_WORK_OUT_EDIT<>''");
													
													//Проверяем на соответствие табелю 1С целосменных отпусков без содержания
													$query_1C_BS = mysqli_query($db,"SELECT ROUND(SUBSTRING(`".$d."`,LOCATE('ДО',`".$d."`)+3,LOCATE('|',`".$d."`,LOCATE('ДО',`".$d."`))-LOCATE('ДО',`".$d."`)-3),2) FROM TEMP_TABEL_1C WHERE YEAR='".$_GET['year']."' AND MONTH='".$_GET['month']."' AND TABN='".$list["TABN"]."'");
													$list_1C_BS = mysqli_fetch_array($query_1C_BS);
													
													$query_work_out_BS = mysqli_query($db,"SELECT ROUND(SUM(TIME_TO_SEC(PERIOD_TIME))/60/60,2) FROM WORK_OUT WHERE TABN='".$list["TABN"]."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$d."' AND ID_VID_ISP_VREM IN(12,23) ");
													$list_work_out_BS = mysqli_fetch_array($query_work_out_BS);
													if($list_work_out_BS[0]=="") $work_out_BS="0.00"; else $work_out_BS=$list_work_out_BS[0];

													//конец проверки
													
													
													if(mysqli_num_rows($query_check_narush)!=0) $font = "RED_";
													else if(mysqli_num_rows($query_check_edit)!=0) $font = "BLUE_";
													else $font = "BLACK_";
													//$volues_style .= "'".$font.$background."'";

													$volues_volue .= "'".$type_day."|".substr($list_otrab_vrem_ur[0],0,5)."|".substr($list_otrab_vrem_svnorm[0],0,5)."|".$font.$background."'";

											}
											else if($list_info['UVOLEN']!="1" && $list_info['DEKRET']!="1" && mysqli_num_rows($query_check_celosmen)>0)//Если целосменное отсутствие 1С
											{
												//$query_check_prisutv = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE TABN='".$list["TABN"]."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$d."' AND ID_VID_ISP_VREM IN (27,28)");
												$query_check_edit = mysqli_query($db,"SELECT * FROM WORK_OUT WHERE TABN='".$list['TABN']."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$d."' AND ID_WORK_OUT_EDIT<>''");
												//Проверяем на соответствие табелю 1С целосменных отпусков без содержания
													$query_1C_BS = mysqli_query($db,"SELECT ROUND(SUBSTRING(`".$d."`,LOCATE('ДО',`".$d."`)+3,LOCATE('|',`".$d."`,LOCATE('ДО',`".$d."`))-LOCATE('ДО',`".$d."`)-3),2) FROM TEMP_TABEL_1C WHERE YEAR='".$_GET['year']."' AND MONTH='".$_GET['month']."' AND TABN='".$list["TABN"]."'");
													$list_1C_BS = mysqli_fetch_array($query_1C_BS);
													
													$query_work_out_BS = mysqli_query($db,"SELECT ROUND(SUM(TIME_TO_SEC(PERIOD_TIME))/60/60,2) FROM WORK_OUT WHERE TABN='".$list["TABN"]."' AND YEAR(DATE_TIME_BEG) = '".$_GET['year']."' AND MONTH(DATE_TIME_BEG) = '".$_GET['month']."' AND DAY(DATE_TIME_BEG) = '".$d."' AND ID_VID_ISP_VREM IN(12,23) ");
													$list_work_out_BS = mysqli_fetch_array($query_work_out_BS);
													if($list_work_out_BS[0]=="") $work_out_BS="0.00"; else $work_out_BS=$list_work_out_BS[0];
													
													if(($list_1C_BS[0]-$work_out_BS)==0) $font="BLACK_"; else $font="RED_";
												//конец проверки
												
												if($list_check_celosmen[0]=="4")
												{
													if(mysqli_num_rows($query_check_edit)!=0)$font = "BLUE_"; else $font = "BLACK_";
													$vid_celosmen = "К";
												}
												if($list_check_celosmen[0]=="11")
												{
													$font="RED_";
													$vid_celosmen = "??";
												}
												if($list_check_celosmen[0]=="12")//Если день без содержания
												{
													$font="BLACK_";
													$vid_celosmen = "БС";
												}
												if($list_check_celosmen[0]=="17")
												{
													if(mysqli_num_rows($query_check_edit)!=0)$font = "BLUE_"; else $font = "BLACK_";
													$vid_celosmen = "БЛ";
												}
												if($list_check_celosmen[0]=="18")
												{
													if(mysqli_num_rows($query_check_edit)!=0)$font = "BLUE_"; else $font = "BLACK_";
													$vid_celosmen = "ОТ";
												}
												if($list_check_celosmen[0]=="19")
												{
													$font="BLACK_";
													$vid_celosmen = "УО";
												}
												if($list_check_celosmen[0]=="20")
												{
													$font="BLACK_";
													$vid_celosmen = "УБ";
												}
												if($list_check_celosmen[0]=="21")
												{
													$font="BLACK_";
													$vid_celosmen = "СР";
												}
												if($list_check_celosmen[0]=="22")
												{
													$font="BLACK_";
													$vid_celosmen = "НН";
												}
												
												//$volues_style .= "'".$font.$background."'";
												$volues_volue .= "'".$vid_celosmen."|".substr($list_otrab_vrem_ur[0],0,5)."|".substr($list_otrab_vrem_svnorm[0],0,5)."|".$font.$background."'";
												
												
												
											}
											else
											{
												$volues_volue .="'|||'";
											}
									}//Иначе оставляем значение по текущему дню без изменений
									else
									{
										//Берем существующее значение по текущему дню
										$query_volue_day = mysqli_query($db,"SELECT `".$d."` FROM OTRAB_VREM WHERE YEAR='".$_GET['year']."' AND MONTH='".$_GET['month']."' AND TABN='".$list_info_1["TABN"]."' ");
										$list_volue_day = mysqli_fetch_array($query_volue_day);
										$volues_volue .="'".$list_volue_day[0]."'";
									}
								}
								else
								{
									$volues_volue .="'|||'";
								}

									if($d!=31)$volues_volue .=",";
							}
							//echo $volues_volue;
							//Проверяем наличие записи в сводной таблице
							$query_find_row = mysqli_query($db,"SELECT * FROM OTRAB_VREM WHERE YEAR='".$_GET['year']."' AND MONTH='".$_GET['month']."' AND TABN='".$list_info_1["TABN"]."' ");
							$list_find_row = mysqli_fetch_assoc($query_find_row);
							
							if(mysqli_num_rows($query_find_row)>0)$query_id_row = "'".$list_find_row['ID']."'";			//Если запись существует, то заменяем запись
							else $query_id_row = "NULL";																//Если запись не находим, то добавляем новую
							
							//ЗАПИСЫВАЕМ
							$query_replace_volue = mysqli_query($db,"REPLACE INTO OTRAB_VREM VALUES(".$query_id_row.",'".$_GET['year']."','".$_GET['month']."','".$list_info_1["TABN"]."','".$list_otrab_vrem_summ_total[0]."|".$list_otrab_vrem_summ_ur[0]."|".$list_otrab_vrem_summ_svnorm[0]."',".$volues_volue.")");
							
							//Обновляем статус процесса
							$fakt++;
							$query_set_status_process_fakt = mysqli_query($db,"UPDATE STATUS_PROCESS SET FAKT='".$fakt."' WHERE PROCESS='UPDATE_SVOD_OTRAB_VREM'");
						//*/
						}
					$query_set_status_process_end = mysqli_query($db,"UPDATE STATUS_PROCESS SET STATUS='0', DATE_TIME_END=TIMESTAMP(CURDATE(),CURTIME()) WHERE PROCESS='UPDATE_SVOD_OTRAB_VREM'");
?>