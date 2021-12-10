<?php
	header("Cache-Control: no-store, no-cache");//Запрет кэширования и сохранения истории
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


if ($_GET['action']=="copyListPers")
	{
		$arrayIdRows = explode(",",$_GET['list_id']);
		for($i=0; $i<count($arrayIdRows); $i++)
		{
			$query_pers = mysqli_query($db,"SELECT * FROM PERSONAL WHERE ID='".$arrayIdRows[$i]."'");
			$list_pers = mysqli_fetch_assoc($query_pers);
			
			if($list_pers['DATE_END_GRAF']=="")$DATE_END_GRAF="NULL"; else $DATE_END_GRAF="'".$list_pers['DATE_END_GRAF']."'";
			if($list_pers['DATE_TD']=="")$DATE_TD="NULL"; else $DATE_TD="'".$list_pers['DATE_TD']."'";
			if($list_pers['DATE_ROGD']=="")$DATE_ROGD="NULL"; else $DATE_ROGD="'".$list_pers['DATE_ROGD']."'";
			if($list_pers['DATE_VID_PASP']=="")$DATE_VID_PASP="NULL"; else $DATE_VID_PASP="'".$list_pers['DATE_VID_PASP']."'";
			if($list_pers['DATE_END_OBRAZ']=="")$DATE_END_OBRAZ="NULL"; else $DATE_END_OBRAZ="'".$list_pers['DATE_END_OBRAZ']."'";
			
			//Добавляем новую запись
			//echo "INSERT INTO PERSONAL VALUES (NULL,'".$list_pers['TABN']."','".Date_Convert_for_Mysql($_GET["date_beg"])."','2100-01-01','".$list_pers['FAM']."','".$list_pers['FAM_ROD']."','".$list_pers['NAME']."','".$list_pers['OTCH']."','".$list_pers['ID_PODRAZD']."','".$list_pers['ID_PROFES']."','".$list_pers['ID_KATEG_PERS']."','".$list_pers['ID_PROPUSK']."','".$list_pers['ID_GRAF']."',".$DATE_END_GRAF.",'".$list_pers['STAVKA']."','".$list_pers['NENORM']."','".$list_pers['OKLAD']."','".$list_pers['NADBAVKA']."','".$list_pers['PROC_PREM']."','".$list_pers['DOPL_SOVM']."','".$list_pers['PROC_DOPL_SECRET']."','".$list_pers['PROC_DOPL_VRED']."','".$list_pers['PROC_DOPL_KLASS']."','".$list_pers['DOPL_MOLOD_SPEC']."','".$list_pers['PROC_RK']."','".$list_pers['UVOLEN']."','".$list_pers['DEKRET']."','".$list_pers['IP']."','".$list_pers['ID_PROFILE']."','".$list_pers['LIST_PODR_DRIVE']."','".$list_pers['ID_SPR_OTR_BUX']."','".$list_pers['NUM_TD']."',".$DATE_TD.",'','','',NULL,'',".$DATE_ROGD.",'".$list_pers['POL']."','".$list_pers['SER_PASP']."','".$list_pers['NUM_PASP']."','".$list_pers['KEM_VID_PASP']."',".$DATE_VID_PASP.",'".$list_pers['KOD_PODR_PASP']."','".$list_pers['ADRES_PROPIS']."','".$list_pers['ID_SPR_VID_OBRAZ']."',".$DATE_END_OBRAZ.",'','',NULL,'',NULL,'',NULL)";
			$query_insert = mysqli_query($db,"INSERT INTO PERSONAL VALUES (NULL,'".$list_pers['TABN']."','".Date_Convert_for_Mysql($_GET["date_beg"])."','2100-01-01','".$list_pers['FAM']."','".$list_pers['FAM_ROD']."','".$list_pers['NAME']."','".$list_pers['OTCH']."','".$list_pers['ID_PODRAZD']."','".$list_pers['ID_PROFES']."','".$list_pers['ID_KATEG_PERS']."','".$list_pers['ID_PROPUSK']."','".$list_pers['ID_GRAF']."',".$DATE_END_GRAF.",'".$list_pers['STAVKA']."','".$list_pers['NENORM']."','".$list_pers['OKLAD']."','".$list_pers['NADBAVKA']."','".$list_pers['PROC_PREM']."','".$list_pers['DOPL_SOVM']."','".$list_pers['PROC_DOPL_SECRET']."','".$list_pers['PROC_DOPL_VRED']."','".$list_pers['PROC_DOPL_KLASS']."','".$list_pers['DOPL_MOLOD_SPEC']."','".$list_pers['PROC_RK']."','".$list_pers['UVOLEN']."','".$list_pers['DEKRET']."','".$list_pers['IP']."','".$list_pers['ID_PROFILE']."','".$list_pers['LIST_PODR_DRIVE']."','".$list_pers['ID_SPR_OTR_BUX']."','".$list_pers['NUM_TD']."',".$DATE_TD.",'','','',NULL,'',".$DATE_ROGD.",'".$list_pers['POL']."','".$list_pers['SER_PASP']."','".$list_pers['NUM_PASP']."','".$list_pers['KEM_VID_PASP']."',".$DATE_VID_PASP.",'".$list_pers['KOD_PODR_PASP']."','".$list_pers['ADRES_PROPIS']."','".$list_pers['ID_SPR_VID_OBRAZ']."',".$DATE_END_OBRAZ.",'','',NULL,'',NULL,'',NULL)");
			
			//Ограничиваем текущую запись
			//echo "UPDATE PERSONAL SET DATE_END = SUBDATE('".Date_Convert_for_Mysql($_GET["date_beg"])."',INTERVAL 1 DAY) WHERE ID='".$arrayIdRows[$i]."'";
			$query_update = mysqli_query($db,"UPDATE PERSONAL SET DATE_END = SUBDATE('".Date_Convert_for_Mysql($_GET["date_beg"])."',INTERVAL 1 DAY) WHERE ID='".$arrayIdRows[$i]."'");
		}
	}

if ($_GET['action']=="delListPers")
	{
		$arrayIdRows = explode(",",$_GET['list_id']);
		for($i=0; $i<count($arrayIdRows); $i++)
		{
			$query_last = mysqli_query($db,"SELECT * FROM PERSONAL WHERE TABN=(SELECT TABN FROM PERSONAL WHERE ID='".$arrayIdRows[$i]."') AND DATE_END=(SELECT MAX(DATE_END) FROM PERSONAL WHERE TABN=(SELECT TABN FROM PERSONAL WHERE ID='".$arrayIdRows[$i]."') AND DATE_END<>'2100-01-01')");
			$list_last = mysqli_fetch_assoc($query_last);

			//Снимаем ограничение действия записи
			$query_update = mysqli_query($db,"UPDATE PERSONAL SET DATE_END='2100-01-01' WHERE ID='".$list_last['ID']."'");
			
			$query_delete = mysqli_query($db,"DELETE FROM PERSONAL WHERE ID='".$arrayIdRows[$i]."'");
		}
	}	

if ($_GET['action']=="saveListPers")
	{
		$arrayValues = explode("|",$_GET['list_new_values']);
		for($i=1; $i<count($arrayValues); $i++)
		{
			$arrayParValues = explode(",",$arrayValues[$i]);
			
			if($arrayParValues[1]=="8")//Для изменения признака доп. соглашения
			{
				if($arrayParValues[2]=="1")$value=$arrayParValues[2]; else $value="";
				
				//Определяем номер зарплатного доп. соглашения в текущем году по текущему трудовому договору
					$query_info = mysqli_query($db, "SELECT * FROM PERSONAL WHERE ID = '".$arrayParValues[0]."'");
					$list_info = mysqli_fetch_assoc($query_info);
					
					$query_last_num_z = mysqli_query($db, "SELECT NUM_DS FROM PERSONAL WHERE TABN='".$list_info['TABN']."' AND NUM_TD='".$list_info['NUM_TD']."' AND DATE_BEG=(SELECT MAX(DATE_BEG) FROM PERSONAL WHERE TABN='".$list_info['TABN']."' AND VID_DS='z' AND YEAR(DATE_BEG)=YEAR('".$list_info['DATE_BEG']."') AND DATE_BEG<'".$list_info['DATE_BEG']."') ");
					$list_last_num_z = mysqli_fetch_array($query_last_num_z);
					
					if(mysqli_num_rows($query_last_num_z)>0)//Если в текущем году уже есть доп.соглашение, то увеличиваем номер
					{
						$query_new_num_z = mysqli_query($db, "SELECT CONCAT(SUBSTRING('".$list_last_num_z['NUM_DS']."',1,3),(SUBSTRING('".$list_last_num_z['NUM_DS']."',4)+1)) ");
						$list_new_num_z = mysqli_fetch_array($query_new_num_z);
					}
					else
					{
						$query_new_num_z = mysqli_query($db, "SELECT CONCAT(SUBSTRING(YEAR('".$list_info['DATE_BEG']."'),3,2),'-1') ");
						$list_new_num_z = mysqli_fetch_array($query_new_num_z);
					}

				//echo "UPDATE PERSONAL SET VID_DS='z',NUM_DS='".$list_new_num_z[0]."',DATE_DS=DATE_BEG,OSN_DS='".$value."' WHERE ID='".$arrayParValues[0]."'<br>";
				$query_update_record = mysqli_query($db,"UPDATE PERSONAL SET VID_DS='z',NUM_DS='".$list_new_num_z[0]."',DATE_DS=DATE_BEG,OSN_DS='".$value."' WHERE ID='".$arrayParValues[0]."'");
			}
			else
			{
				if($arrayParValues[1]=="10")	$pole="OKLAD";
				if($arrayParValues[1]=="12")	$pole="PROC_PREM";
				if($arrayParValues[1]=="14")	$pole="NADBAVKA";
				if($arrayParValues[1]=="16")	$pole="DOPL_SOVM";
				if($arrayParValues[1]=="18")	$pole="PROC_DOPL_SECRET";
				if($arrayParValues[1]=="20")	$pole="PROC_DOPL_VRED";
				if($arrayParValues[1]=="22")	$pole="PROC_DOPL_KLASS";
				if($arrayParValues[1]=="24")	$pole="DOPL_MOLOD_SPEC";
				if($arrayParValues[1]=="26")	$pole="PROC_RK";
				
				//echo "UPDATE PERSONAL SET ".$pole."='".$arrayParValues[2]."', ID_SPR_PRICH_IZM='4', IZM_RAZD_7='1' WHERE ID='".$arrayParValues[0]."'<br>";
				$query_update_record = mysqli_query($db,"UPDATE PERSONAL SET ".$pole."='".$arrayParValues[2]."', ID_SPR_PRICH_IZM='4', IZM_RAZD_7='1' WHERE ID='".$arrayParValues[0]."'");
			}
		}

	}

	
if ($_GET['action']=="delete")
	{
		
		$query_info = mysqli_query($db,"SELECT * FROM PERSONAL WHERE ID = '".$_GET["id"]."'");
		$list_info = mysqli_fetch_assoc($query_info);
		
		//Находим предыдущую запись
		$query_last_record = mysqli_query($db,"SELECT * FROM PERSONAL WHERE TABN = '".$list_info["TABN"]."' AND DATE_BEG=(SELECT MAX(DATE_BEG) FROM PERSONAL WHERE TABN = '".$list_info["TABN"]."' AND DATE_BEG<'".$list_info["DATE_BEG"]."')");
		$list_last_record = mysqli_fetch_assoc($query_last_record);
		if(mysqli_num_rows($query_last_record)>0)//Если предыдущая запись найдена
		{
			$query_update_date_end_last_record = mysqli_query($db,"UPDATE PERSONAL SET DATE_END='".$list_info['DATE_END']."' WHERE ID = '".$list_last_record["ID"]."'");
			$query_delete = mysqli_query($db,"DELETE FROM PERSONAL WHERE ID = '".$_GET["id"]."'");
		}
		else//Если предыдущей записи не существует
		{
			$query_delete = mysqli_query($db,"DELETE FROM PERSONAL WHERE ID = '".$_GET["id"]."'");
		}
		
		$query_check_ip = mysqli_query($db,"SELECT * FROM PERSONAL WHERE IP='".$_GET['ip']."'");
		if(mysqli_num_rows($query_check_ip)==0)$query_delete = mysqli_query($db,"DELETE FROM USER_SETTINGS WHERE IP='".$_GET['ip']."'");
		
		
		//Удаляем событие из календаря
		$query_delete_schedule = mysqli_query($db,"DELETE FROM SCHEDULE WHERE TABLE_LINK='PERSONAL' AND ID_LINK='".$_GET["id"]."'");
	}
	
	
	
if ($_GET['action']=="edit")
	{

		$query_info = mysqli_query($db,"SELECT * FROM PERSONAL WHERE ID = '".$_GET["id"]."'");
		$list_info = mysqli_fetch_assoc($query_info);
		
		if($_GET['date_td']=="")$DATE_TD="NULL"; else $DATE_TD="'".Date_Convert_for_Mysql($_GET['date_td'])."'";
		if($_GET['date_ds']=="")$DATE_DS="NULL"; else $DATE_DS="'".Date_Convert_for_Mysql($_GET['date_ds'])."'";
		if($_GET['date_rogd']=="")$DATE_ROGD="NULL"; else $DATE_ROGD="'".Date_Convert_for_Mysql($_GET['date_rogd'])."'";
		if($_GET['date_vid_pasp']=="")$DATE_VID_PASP="NULL"; else $DATE_VID_PASP="'".Date_Convert_for_Mysql($_GET['date_vid_pasp'])."'";
		if($_GET['date_end_obraz']=="")$DATE_END_OBRAZ="NULL"; else $DATE_END_OBRAZ="'".Date_Convert_for_Mysql($_GET['date_end_obraz'])."'";
		if($_GET['date_end_izm_5']=="")$DATE_END_IZM_5="NULL"; else $DATE_END_IZM_5="'".Date_Convert_for_Mysql($_GET['date_end_izm_5'])."'";
		if($_GET['date_end_izm_6']=="")$DATE_END_IZM_6="NULL"; else $DATE_END_IZM_6="'".Date_Convert_for_Mysql($_GET['date_end_izm_6'])."'";
		if($_GET['date_end_izm_7']=="")$DATE_END_IZM_7="NULL"; else $DATE_END_IZM_7="'".Date_Convert_for_Mysql($_GET['date_end_izm_7'])."'";
		
		//Находим предыдущую запись
		$query_last_record = mysqli_query($db,"SELECT * FROM PERSONAL WHERE TABN = '".$list_info["TABN"]."' AND DATE_BEG=(SELECT MAX(DATE_BEG) FROM PERSONAL WHERE TABN = '".$list_info["TABN"]."' AND DATE_BEG<'".$list_info["DATE_BEG"]."')");
		$list_last_record = mysqli_fetch_assoc($query_last_record);
		
		if(mysqli_num_rows($query_last_record)>0)//Если предыдущая запись найдена
		{
			$query_check = mysqli_query($db,"SELECT IF ('".Date_Convert_for_Mysql($_GET["date_beg"])."' < '".Date_Convert_for_Mysql($_GET["date_end"])."' AND '".Date_Convert_for_Mysql($_GET["date_beg"])."'>'".$list_last_record['DATE_BEG']."','1','0')");
			$list_check = mysqli_fetch_array($query_check);
			
			if ($list_check[0]=='1')
			{
				//Обновляем дату окончания действия предыдущей записи
				$query_update_date_end_last_record = mysqli_query($db,"UPDATE PERSONAL SET DATE_END = SUBDATE('".Date_Convert_for_Mysql($_GET["date_beg"])."', INTERVAL 1 DAY) WHERE ID='".$list_last_record["ID"]."' ");
				//Обновляем данные редактируемой записи
				$query_update = mysqli_query($db,"UPDATE PERSONAL SET DATE_BEG='".Date_Convert_for_Mysql($_GET['date_beg'])."',FAM='".trim($_GET['fam'])."',FAM_ROD='".trim($_GET['fam_rod'])."',NAME='".trim($_GET['name'])."',OTCH='".trim($_GET['otch'])."',ID_PODRAZD='".$_GET['id_podrazd']."',ID_PROFES='".$_GET['id_profes']."',ID_KATEG_PERS='".$_GET['id_kateg']."',ID_PROPUSK='".$_GET['id_prop']."',ID_GRAF='".$_GET['id_graf']."',DATE_END_GRAF='".Date_Convert_for_Mysql($_GET['date_end_graf'])."', STAVKA='".$_GET['stavka']."', NENORM='".$_GET['nenorm']."', OKLAD='".$_GET['oklad']."',NADBAVKA='".$_GET['nadbavka']."',PROC_PREM='".$_GET['proc_prem']."',DOPL_SOVM='".$_GET['dopl_sovm']."',PROC_DOPL_SECRET='".$_GET['proc_dopl_secret']."',PROC_DOPL_VRED='".$_GET['proc_dopl_vred']."',PROC_DOPL_KLASS='".$_GET['proc_dopl_klass']."',DOPL_MOLOD_SPEC='".$_GET['dopl_molod_spec']."',PROC_RK='".$_GET['proc_rk']."',UVOLEN='".$_GET['uvolen']."',DEKRET='".$_GET['dekret']."', IP='".$_GET['ip']."', ID_PROFILE='".$_GET['profile']."', LIST_PODR_DRIVE='".$_GET['list_id_podr_drive']."', ID_SPR_OTR_BUX='".$_GET['id_otr_bux']."', NUM_TD='".$_GET['num_td']."', DATE_TD=".$DATE_TD.", OSN_TD='".$_GET['osn_td']."', VID_DS='".$_GET['vid_ds']."', NUM_DS='".$_GET['num_ds']."', DATE_DS=".$DATE_DS.", OSN_DS='".$_GET['osn_ds']."', DATE_ROGD=".$DATE_ROGD.", POL='".$_GET['pol']."', SER_PASP='".$_GET['ser_pasp']."', NUM_PASP='".$_GET['num_pasp']."', KEM_VID_PASP='".$_GET['kem_vid_pasp']."', DATE_VID_PASP=".$DATE_VID_PASP.", KOD_PODR_PASP='".$_GET['kod_podr_pasp']."', ADRES_PROPIS='".$_GET['adres_propis']."', ID_SPR_VID_OBRAZ='".$_GET['id_vid_obraz']."', DATE_END_OBRAZ=".$DATE_END_OBRAZ.", ID_SPR_PRICH_IZM='".$_GET['id_prich_izm']."', IZM_RAZD_5='".$_GET['izm_razd_5']."', DATE_END_IZM_5=".$DATE_END_IZM_5.", IZM_RAZD_6='".$_GET['izm_razd_6']."', DATE_END_IZM_6=".$DATE_END_IZM_6.", IZM_RAZD_7='".$_GET['izm_razd_7']."', DATE_END_IZM_7=".$DATE_END_IZM_7." WHERE ID='".$_GET['id']."' ");
				
				echo "Данные сохранены!";
			}
			else
			{
				echo "Неверно указан период действия записи!";
			}
		}
		else
		{
			$query_check = mysqli_query($db,"SELECT IF ('".Date_Convert_for_Mysql($_GET["date_beg"])."' < '".Date_Convert_for_Mysql($_GET["date_end"])."','1','0')");
			$list_check = mysqli_fetch_array($query_check);
			if ($list_check[0]=='1')
			{
				//Обновляем данные редактируемой записи
				$query_update = mysqli_query($db,"UPDATE PERSONAL SET DATE_BEG='".Date_Convert_for_Mysql($_GET["date_beg"])."',FAM='".trim($_GET["fam"])."',FAM_ROD='".trim($_GET["fam_rod"])."',NAME='".trim($_GET["name"])."',OTCH='".trim($_GET["otch"])."',ID_PODRAZD='".$_GET["id_podrazd"]."',ID_PROFES='".$_GET["id_profes"]."',ID_KATEG_PERS='".$_GET["id_kateg"]."',ID_PROPUSK='".$_GET["id_prop"]."',ID_GRAF='".$_GET["id_graf"]."',DATE_END_GRAF='".Date_Convert_for_Mysql($_GET["date_end_graf"])."', STAVKA='".$_GET['stavka']."', NENORM='".$_GET["nenorm"]."', OKLAD='".$_GET["oklad"]."',NADBAVKA='".$_GET["nadbavka"]."',PROC_PREM='".$_GET["proc_prem"]."',DOPL_SOVM='".$_GET['dopl_sovm']."',PROC_DOPL_SECRET='".$_GET['proc_dopl_secret']."',PROC_DOPL_VRED='".$_GET['proc_dopl_vred']."',PROC_DOPL_KLASS='".$_GET['proc_dopl_klass']."',DOPL_MOLOD_SPEC='".$_GET['dopl_molod_spec']."',PROC_RK='".$_GET['proc_rk']."',UVOLEN='".$_GET["uvolen"]."',DEKRET='".$_GET["dekret"]."', IP='".$_GET['ip']."', ID_PROFILE='".$_GET['profile']."', LIST_PODR_DRIVE='".$_GET['list_id_podr_drive']."', ID_SPR_OTR_BUX='".$_GET['id_otr_bux']."', NUM_TD='".$_GET['num_td']."', DATE_TD=".$DATE_TD.", OSN_TD='".$_GET['osn_td']."', VID_DS='".$_GET['vid_ds']."', NUM_DS='".$_GET['num_ds']."', DATE_DS=".$DATE_DS.", OSN_DS='".$_GET['osn_ds']."', DATE_ROGD=".$DATE_ROGD.", POL='".$_GET['pol']."', SER_PASP='".$_GET['ser_pasp']."', NUM_PASP='".$_GET['num_pasp']."', KEM_VID_PASP='".$_GET['kem_vid_pasp']."', DATE_VID_PASP=".$DATE_VID_PASP.", KOD_PODR_PASP='".$_GET['kod_podr_pasp']."', ADRES_PROPIS='".$_GET['adres_propis']."', ID_SPR_VID_OBRAZ='".$_GET['id_vid_obraz']."', DATE_END_OBRAZ=".$DATE_END_OBRAZ.", ID_SPR_PRICH_IZM='".$_GET['id_prich_izm']."', IZM_RAZD_5='".$_GET['izm_razd_5']."', DATE_END_IZM_5=".$DATE_END_IZM_5.", IZM_RAZD_6='".$_GET['izm_razd_6']."', DATE_END_IZM_6=".$DATE_END_IZM_6.", IZM_RAZD_7='".$_GET['izm_razd_7']."', DATE_END_IZM_7=".$DATE_END_IZM_7." WHERE ID='".$_GET["id"]."' ");
				echo "Данные сохранены!";
			}
			else
			{
				echo "Неверно указан период действия записи!";
			}
		}

				
		if($_GET['ip']!="")
		{
			$query_check_ip = mysqli_query($db,"SELECT * FROM USER_SETTINGS WHERE IP='".$_GET['ip']."'");
			if(mysqli_num_rows($query_check_ip)==0)$query_insert = mysqli_query($db,"INSERT INTO USER_SETTINGS VALUES (NULL,'".$_GET['ip']."',DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT)");
		}
		else
		{
			$query_check_ip = mysqli_query($db,"SELECT * FROM PERSONAL WHERE IP='".$_GET['ip']."'");
			if(mysqli_num_rows($query_check_ip)==0)$query_delete = mysqli_query($db,"DELETE FROM USER_SETTINGS WHERE IP='".$_GET['ip']."'");
		}
		
	
		
	}
	
if ($_GET['action']=="add_copy")
	{
		
		if($_GET['date_td']=="")$DATE_TD="NULL"; else $DATE_TD="'".Date_Convert_for_Mysql($_GET['date_td'])."'";
		if($_GET['date_ds']=="")$DATE_DS="NULL"; else $DATE_DS="'".Date_Convert_for_Mysql($_GET['date_ds'])."'";
		if($_GET['date_rogd']=="")$DATE_ROGD="NULL"; else $DATE_ROGD="'".Date_Convert_for_Mysql($_GET['date_rogd'])."'";
		if($_GET['date_vid_pasp']=="")$DATE_VID_PASP="NULL"; else $DATE_VID_PASP="'".Date_Convert_for_Mysql($_GET['date_vid_pasp'])."'";
		if($_GET['date_end_obraz']=="")$DATE_END_OBRAZ="NULL"; else $DATE_END_OBRAZ="'".Date_Convert_for_Mysql($_GET['date_end_obraz'])."'";
		if($_GET['date_end_izm_5']=="")$DATE_END_IZM_5="NULL"; else $DATE_END_IZM_5="'".Date_Convert_for_Mysql($_GET['date_end_izm_5'])."'";
		if($_GET['date_end_izm_6']=="")$DATE_END_IZM_6="NULL"; else $DATE_END_IZM_6="'".Date_Convert_for_Mysql($_GET['date_end_izm_6'])."'";
		if($_GET['date_end_izm_7']=="")$DATE_END_IZM_7="NULL"; else $DATE_END_IZM_7="'".Date_Convert_for_Mysql($_GET['date_end_izm_7'])."'";
		
		//Проверка коректности указанной даты начала действия записи
		$query_check = mysqli_query($db,"SELECT IF ('".Date_Convert_for_Mysql($_GET["date_beg"])."' > (SELECT DATE_BEG FROM PERSONAL WHERE ID = '".$_GET["id"]."') AND '".Date_Convert_for_Mysql($_GET["date_beg"])."' <= '".Date_Convert_for_Mysql($_GET["date_end"])."','1','0')");
		$list_check = mysqli_fetch_array($query_check);
		if($list_check[0]=='1')
		{
			$query_update = mysqli_query($db,"UPDATE PERSONAL SET DATE_END = SUBDATE('".Date_Convert_for_Mysql($_GET["date_beg"])."', INTERVAL 1 DAY) WHERE ID='".$_GET["id"]."' ");
			//Создаем новую запись
			if($_GET["date_end_graf"]=="") $date_end_graf=Date_Convert_for_Mysql("01.01.2100"); else $date_end_graf=Date_Convert_for_Mysql($_GET["date_end_graf"]);
			$query_new = mysqli_query($db,"INSERT INTO PERSONAL VALUES (NULL,'".$_GET["tabn"]."','".Date_Convert_for_Mysql($_GET["date_beg"])."','".Date_Convert_for_Mysql($_GET["date_end"])."','".trim($_GET["fam"])."','".trim($_GET["fam_rod"])."','".trim($_GET["name"])."','".trim($_GET["otch"])."','".$_GET["id_podrazd"]."','".$_GET["id_profes"]."','".$_GET["id_kateg"]."','".$_GET["id_prop"]."','".$_GET["id_graf"]."','".$date_end_graf."','".$_GET['stavka']."','".$_GET["nenorm"]."','".$_GET["oklad"]."','".$_GET["nadbavka"]."','".$_GET["proc_prem"]."','".$_GET['dopl_sovm']."','".$_GET['proc_dopl_secret']."','".$_GET['proc_dopl_vred']."','".$_GET['proc_dopl_klass']."','".$_GET['dopl_molod_spec']."','".$_GET['proc_rk']."','".$_GET["uvolen"]."','".$_GET["dekret"]."','".$_GET["ip"]."','".$_GET["profile"]."','".$_GET['list_id_podr_drive']."','".$_GET['id_otr_bux']."','".$_GET['num_td']."',".$DATE_TD.",'".$_GET['osn_td']."','".$_GET['vid_ds']."','".$_GET['num_ds']."',".$DATE_DS.",'".$_GET['osn_ds']."',".$DATE_ROGD.",'".$_GET['pol']."','".$_GET['ser_pasp']."','".$_GET['num_pasp']."','".$_GET['kem_vid_pasp']."',".$DATE_VID_PASP.",'".$_GET['kod_podr_pasp']."','".$_GET['adres_propis']."','".$_GET['id_vid_obraz']."',".$DATE_END_OBRAZ.",'".$_GET['id_prich_izm']."', '".$_GET['izm_razd_5']."', ".$DATE_END_IZM_5.", '".$_GET['izm_razd_6']."', ".$DATE_END_IZM_6.", '".$_GET['izm_razd_7']."', ".$DATE_END_IZM_7.")");
			echo "Данные сохранены!";
		}
		else
		{
			echo "Неверно указан период действия записи!";
		}
		
		if($_GET['ip']!="")
		{
			$query_check_ip = mysqli_query($db,"SELECT * FROM USER_SETTINGS WHERE IP='".$_GET['ip']."'");
			if(mysqli_num_rows($query_check_ip)==0)$query_insert = mysqli_query($db,"INSERT INTO USER_SETTINGS VALUES (NULL,'".$_GET['ip']."',DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT)");
		}
		else
		{
			$query_check_ip = mysqli_query($db,"SELECT * FROM PERSONAL WHERE IP='".$_GET['ip']."'");
			if(mysqli_num_rows($query_check_ip)==0)$query_delete = mysqli_query($db,"DELETE FROM USER_SETTINGS WHERE IP='".$_GET['ip']."'");
		}
	}
if ($_GET['action']=="new")
	{
		if($_GET['date_td']=="")$DATE_TD="NULL"; else $DATE_TD="'".Date_Convert_for_Mysql($_GET['date_td'])."'";
		if($_GET['date_ds']=="")$DATE_DS="NULL"; else $DATE_DS="'".Date_Convert_for_Mysql($_GET['date_ds'])."'";
		if($_GET['date_rogd']=="")$DATE_ROGD="NULL"; else $DATE_ROGD="'".Date_Convert_for_Mysql($_GET['date_rogd'])."'";
		if($_GET['date_vid_pasp']=="")$DATE_VID_PASP="NULL"; else $DATE_VID_PASP="'".Date_Convert_for_Mysql($_GET['date_vid_pasp'])."'";
		if($_GET['date_end_obraz']=="")$DATE_END_OBRAZ="NULL"; else $DATE_END_OBRAZ="'".Date_Convert_for_Mysql($_GET['date_end_obraz'])."'";
		if($_GET['date_end_izm_5']=="")$DATE_END_IZM_5="NULL"; else $DATE_END_IZM_5="'".Date_Convert_for_Mysql($_GET['date_end_izm_5'])."'";
		if($_GET['date_end_izm_6']=="")$DATE_END_IZM_6="NULL"; else $DATE_END_IZM_6="'".Date_Convert_for_Mysql($_GET['date_end_izm_6'])."'";
		if($_GET['date_end_izm_7']=="")$DATE_END_IZM_7="NULL"; else $DATE_END_IZM_7="'".Date_Convert_for_Mysql($_GET['date_end_izm_7'])."'";
		
		$query_check = mysqli_query($db,"SELECT * FROM PERSONAL WHERE TABN = '".$_GET["tabn"]."'"); //проверяем на существование табельного номера в БД
		//Создаем новую запись
		if (mysqli_num_rows($query_check)==0)
		{
			if($_GET["date_end_graf"]=="") $date_end_graf=Date_Convert_for_Mysql("01.01.2100"); else $date_end_graf=Date_Convert_for_Mysql($_GET["date_end_graf"]);
			$query_new = mysqli_query($db,"INSERT INTO PERSONAL VALUES (NULL,'".$_GET["tabn"]."','".Date_Convert_for_Mysql($_GET["date_beg"])."','".Date_Convert_for_Mysql($_GET["date_end"])."','".trim($_GET["fam"])."','".trim($_GET["fam_rod"])."','".trim($_GET["name"])."','".trim($_GET["otch"])."','".$_GET["id_podrazd"]."','".$_GET["id_profes"]."','".$_GET["id_kateg"]."','".$_GET["id_prop"]."','".$_GET["id_graf"]."','".$date_end_graf."','".$_GET['stavka']."','".$_GET["nenorm"]."','".$_GET["oklad"]."','".$_GET["nadbavka"]."','".$_GET["proc_prem"]."','".$_GET['dopl_sovm']."','".$_GET['proc_dopl_secret']."','".$_GET['proc_dopl_vred']."','".$_GET['proc_dopl_klass']."','".$_GET['dopl_molod_spec']."','".$_GET['proc_rk']."','".$_GET["uvolen"]."','".$_GET["dekret"]."','".$_GET["ip"]."','".$_GET["profile"]."','".$_GET['list_id_podr_drive']."','".$_GET['id_otr_bux']."','".$_GET['num_td']."',".$DATE_TD.",'".$_GET['osn_td']."','".$_GET['vid_ds']."','".$_GET['num_ds']."',".$DATE_DS.",'".$_GET['osn_ds']."',".$DATE_ROGD.",'".$_GET['pol']."','".$_GET['ser_pasp']."','".$_GET['num_pasp']."','".$_GET['kem_vid_pasp']."',".$DATE_VID_PASP.",'".$_GET['kod_podr_pasp']."','".$_GET['adres_propis']."','".$_GET['id_vid_obraz']."',".$DATE_END_OBRAZ.",'".$_GET['id_prich_izm']."', '".$_GET['izm_razd_5']."', ".$DATE_END_IZM_5.", '".$_GET['izm_razd_6']."', ".$DATE_END_IZM_6.", '".$_GET['izm_razd_7']."', ".$DATE_END_IZM_7.")");
			echo "Данные сохранены!";
		}
		else echo "Запись с таким табельным номером уже существует!";
		
		if($_GET['ip']!="")
		{
			$query_check_ip = mysqli_query($db,"SELECT * FROM USER_SETTINGS WHERE IP='".$_GET['ip']."'");
			if(mysqli_num_rows($query_check_ip)==0)$query_insert = mysqli_query($db,"INSERT INTO USER_SETTINGS VALUES (NULL,'".$_GET['ip']."',DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT)");
		}
		else
		{
			$query_check_ip = mysqli_query($db,"SELECT * FROM PERSONAL WHERE IP='".$_GET['ip']."'");
			if(mysqli_num_rows($query_check_ip)==0)$query_delete = mysqli_query($db,"DELETE FROM USER_SETTINGS WHERE IP='".$_GET['ip']."'");
		}
	}

	
if($_GET['action']=="edit" || $_GET['action']=="add_copy" || $_GET['action']=="new")
	{
		//Обновляем события в календаре
			if($_GET['date_end_izm_5']=="")
			{
				//Удаляем событие
				$query_delete_schedule = mysqli_query($db,"DELETE FROM SCHEDULE WHERE TABLE_LINK='PERSONAL' AND ID_LINK='".$_GET["id"]."' AND TYPE='PERS_END_IZM_RAZD_5' ");
			}
			else if($_GET['date_end_izm_5']!="")
			{
				$query_check = mysqli_query($db,"SELECT * FROM SCHEDULE WHERE TABLE_LINK='PERSONAL' AND ID_LINK='".$_GET["id"]."' AND TYPE='PERS_END_IZM_RAZD_5'");
				if(mysqli_num_rows($query_check)>0)
				{
					//Обновляем событие
					$query_update_schedule = mysqli_query($db,"UPDATE SCHEDULE SET DATETIME_BEG='".Date_Convert_for_Mysql($_GET['date_end_izm_5'])." 00:00:00', DATETIME_END='".Date_Convert_for_Mysql($_GET['date_end_izm_5'])." 23:55:00', TEXT='Окончание срока действия изменения условий работы', DETAILS='".trim($_GET['fam'])." ".trim($_GET['name'])." ".trim($_GET['otch'])."' WHERE TABLE_LINK='PERSONAL' AND ID_LINK='".$_GET["id"]."' AND TYPE='PERS_END_IZM_RAZD_5' ");
				}
				else
				{
					//Добавляем событие
					$query_insert_schedule = mysqli_query($db,"INSERT INTO SCHEDULE VALUES(NULL,'PERS_END_IZM_RAZD_5','PERSONAL','".$_GET["id"]."','','1','".Date_Convert_for_Mysql($_GET['date_end_izm_5'])." 00:00:00','".Date_Convert_for_Mysql($_GET['date_end_izm_5'])." 23:55:00','Окончание срока действия изменения условий работы','".trim($_GET['fam'])." ".trim($_GET['name'])." ".trim($_GET['otch'])."')");
				}
			}
			
			if($_GET['date_end_izm_6']=="")
			{
				//Удаляем событие
				$query_delete_schedule = mysqli_query($db,"DELETE FROM SCHEDULE WHERE TABLE_LINK='PERSONAL' AND ID_LINK='".$_GET["id"]."' AND TYPE='PERS_END_IZM_RAZD_6' ");
			}
			else if($_GET['date_end_izm_6']!="")
			{
				$query_check = mysqli_query($db,"SELECT * FROM SCHEDULE WHERE TABLE_LINK='PERSONAL' AND ID_LINK='".$_GET["id"]."' AND TYPE='PERS_END_IZM_RAZD_6'");
				if(mysqli_num_rows($query_check)>0)
				{
					//Обновляем событие
					$query_update_schedule = mysqli_query($db,"UPDATE SCHEDULE SET DATETIME_BEG='".Date_Convert_for_Mysql($_GET['date_end_izm_6'])." 00:00:00', DATETIME_END='".Date_Convert_for_Mysql($_GET['date_end_izm_6'])." 23:55:00', TEXT='Окончание срока действия изменения режима работы', DETAILS='".trim($_GET['fam'])." ".trim($_GET['name'])." ".trim($_GET['otch'])."' WHERE TABLE_LINK='PERSONAL' AND ID_LINK='".$_GET["id"]."' AND TYPE='PERS_END_IZM_RAZD_6' ");
				}
				else
				{
					//Добавляем событие
					$query_insert_schedule = mysqli_query($db,"INSERT INTO SCHEDULE VALUES(NULL,'PERS_END_IZM_RAZD_6','PERSONAL','".$_GET["id"]."','','1','".Date_Convert_for_Mysql($_GET['date_end_izm_6'])." 00:00:00','".Date_Convert_for_Mysql($_GET['date_end_izm_6'])." 23:55:00','Окончание срока действия изменения режима работы','".trim($_GET['fam'])." ".trim($_GET['name'])." ".trim($_GET['otch'])."')");
				}		
			}
			
			if($_GET['date_end_izm_7']=="")
			{
				//Удаляем событие
				$query_delete_schedule = mysqli_query($db,"DELETE FROM SCHEDULE WHERE TABLE_LINK='PERSONAL' AND ID_LINK='".$_GET["id"]."' AND TYPE='PERS_END_IZM_RAZD_7' ");
			}
			else if($_GET['date_end_izm_7']!="")
			{
				$query_check = mysqli_query($db,"SELECT * FROM SCHEDULE WHERE TABLE_LINK='PERSONAL' AND ID_LINK='".$_GET["id"]."' AND TYPE='PERS_END_IZM_RAZD_7'");
				if(mysqli_num_rows($query_check)>0)
				{
					//Обновляем событие
					$query_update_schedule = mysqli_query($db,"UPDATE SCHEDULE SET DATETIME_BEG='".Date_Convert_for_Mysql($_GET['date_end_izm_7'])." 00:00:00', DATETIME_END='".Date_Convert_for_Mysql($_GET['date_end_izm_7'])." 23:55:00', TEXT='Окончание срока действия изменения условий оплаты', DETAILS='".trim($_GET['fam'])." ".trim($_GET['name'])." ".trim($_GET['otch'])."' WHERE TABLE_LINK='PERSONAL' AND ID_LINK='".$_GET["id"]."' AND TYPE='PERS_END_IZM_RAZD_7' ");
				}
				else
				{
					//Добавляем событие
					$query_insert_schedule = mysqli_query($db,"INSERT INTO SCHEDULE VALUES(NULL,'PERS_END_IZM_RAZD_7','PERSONAL','".$_GET["id"]."','','1','".Date_Convert_for_Mysql($_GET['date_end_izm_7'])." 00:00:00','".Date_Convert_for_Mysql($_GET['date_end_izm_7'])." 23:55:00','Окончание срока действия изменения условий оплаты','".trim($_GET['fam'])." ".trim($_GET['name'])." ".trim($_GET['otch'])."')");
				}	
			}
	}
?> 
