<?php
//session_start();
        //connect to database
        require_once("../../mysql.php");
function Date_Mysql_for_View($date_in)//Входной формат MySql: гггг-мм-дд
	{
		if($date_in=="")
		{
			return "";
		}
		else
		{
			$year = $date_in[0].$date_in[1].$date_in[2].$date_in[3];				//ГОД
			$month = $date_in[5].$date_in[6];										//МЕСЯЦ
			$day = $date_in[8].$date_in[9];											//ЧИСЛО
			$date_out = $day.".".$month.".".$year;
			return $date_out;
		}
		
	}
function Date_Convert_for_Mysql($date_in)//Входной формат MySql: дд.мм.гггг
	{
		$year = $date_in[6].$date_in[7].$date_in[8].$date_in[9];				//ГОД
		$month = $date_in[3].$date_in[4];										//МЕСЯЦ
		$day = $date_in[0].$date_in[1];											//ЧИСЛО
		$date_out = $year."-".$month."-".$day;
		return $date_out;
	}

if($_GET['action']=="get_podrazd")
{
	$query = mysqli_query($db, "SELECT * FROM SPR_PODRAZD WHERE ID = '".$_GET['id']."'");
	$list = mysqli_fetch_array($query);
	echo "|".$list['KOD']."|".$list['NAME'];
}	

if($_GET['action']=="person")
{
		//$query = mysqli_query($db, "SELECT * FROM PERSONAL WHERE ID = '".$_GET['id']."' AND DATE_BEG <='".Date_Convert_for_Mysql($_GET['date'])."' AND DATE_END >='".Date_Convert_for_Mysql($_GET['date'])."' ");
		$query = mysqli_query($db, "SELECT * FROM PERSONAL WHERE ID = '".$_GET['id']."' ");
		$list = mysqli_fetch_assoc($query);
		
		$query_podr = mysqli_query($db, "SELECT * FROM SPR_PODRAZD WHERE ID = '".$list['ID_PODRAZD']."' ");
		$list_podr = mysqli_fetch_assoc($query_podr);
		
		$query_prof = mysqli_query($db,"SELECT * FROM SPR_PROF WHERE ID = '".$list["ID_PROFES"]."'");
		$list_prof = mysqli_fetch_assoc($query_prof);
			
		$query_kateg = mysqli_query($db,"SELECT * FROM SPR_KATEG_PERS WHERE ID = '".$list["ID_KATEG_PERS"]."'");
		$list_kateg = mysqli_fetch_assoc($query_kateg);
			
		$query_graf = mysqli_query($db,"SELECT * FROM SPR_GRAF WHERE ID = '".$list["ID_GRAF"]."'");
		$list_graf = mysqli_fetch_assoc($query_graf);
		
		$query_prop = mysqli_query($db,"SELECT * FROM SPR_VID_PROP WHERE ID = '".$list["ID_PROPUSK"]."'");
		$list_prop = mysqli_fetch_assoc($query_prop);
		
		$query_profile = mysqli_query($db,"SELECT * FROM SPR_PROFILES WHERE ID = '".$list["ID_PROFILE"]."'");
		$list_profile = mysqli_fetch_assoc($query_profile);
		
		$query_otr_bux = mysqli_query($db,"SELECT * FROM SPR_OTR_BUX WHERE ID = '".$list["ID_SPR_OTR_BUX"]."'");
		$list_otr_bux = mysqli_fetch_assoc($query_otr_bux);
		if (mysqli_num_rows($query_otr_bux)>0 && $list_otr_bux['ID']<>"4") $str_otr_bux=$list_otr_bux['KOD']." ".$list_otr_bux['NAME']." (".$list_otr_bux['KATEG_PERS'].")";
		else if (mysqli_num_rows($query_otr_bux)>0 && $list_otr_bux['ID']=="4") $str_otr_bux="В соответствие с учетом по структурному подразделению";
		else $str_otr_bux="";
		
		$query_vid_obraz = mysqli_query($db,"SELECT * FROM SPR_VID_OBRAZ WHERE ID = '".$list["ID_SPR_VID_OBRAZ"]."'");
		$list_vid_obraz = mysqli_fetch_assoc($query_vid_obraz);
		if (mysqli_num_rows($query_vid_obraz)>0)$str_vid_obraz = $list_vid_obraz['NAME'];
		else $str_vid_obraz = "";
		
		/*
		$list_podr_drive = explode("|",$list['LIST_PODR_DRIVE']);
		if(count($list_podr_drive)>0)
		{
			if($list_podr_drive[0]!="")
			{
				$query_podr_drive_1 = mysqli_query($db, "SELECT * FROM SPR_PODRAZD WHERE ID = '".$list_podr_drive[0]."' ");
				$list_podr_drive_1 = mysqli_fetch_assoc($query_podr_drive_1);
				$id_podr_drive_1 = $list_podr_drive[0];
				$podr_drive_1=$list_podr_drive_1['NAME'];
			}
			else
			{
				$id_podr_drive_1 = "";
				$podr_drive_1 = "";
			}
			if($list_podr_drive[1]!="")
			{
				$query_podr_drive_2 = mysqli_query($db, "SELECT * FROM SPR_PODRAZD WHERE ID = '".$list_podr_drive[1]."' ");
				$list_podr_drive_2 = mysqli_fetch_assoc($query_podr_drive_2);
				$id_podr_drive_2 = $list_podr_drive[1];
				$podr_drive_2=$list_podr_drive_2['NAME'];
			}
			else
			{
				$id_podr_drive_2 = "";
				$podr_drive_2 = "";
			}
			if($list_podr_drive[2]!="")
			{
				$query_podr_drive_3 = mysqli_query($db, "SELECT * FROM SPR_PODRAZD WHERE ID = '".$list_podr_drive[2]."' ");
				$list_podr_drive_3 = mysqli_fetch_assoc($query_podr_drive_3);
				$id_podr_drive_3 = $list_podr_drive[2];
				$podr_drive_3=$list_podr_drive_3['NAME'];
			}
			else
			{
				$id_podr_drive_3 = "";
				$podr_drive_3 = "";
			}
			if($list_podr_drive[3]!="")
			{
				$query_podr_drive_4 = mysqli_query($db, "SELECT * FROM SPR_PODRAZD WHERE ID = '".$list_podr_drive[3]."' ");
				$list_podr_drive_4 = mysqli_fetch_assoc($query_podr_drive_4);
				$id_podr_drive_4 = $list_podr_drive[3];
				$podr_drive_4=$list_podr_drive_4['NAME'];
			}
			else
			{
				$id_podr_drive_4 = "";
				$podr_drive_4 = "";
			}
		}
		else
		{
			$id_podr_drive_1="";
			$id_podr_drive_2="";
			$id_podr_drive_3="";
			$id_podr_drive_4="";
			$podr_drive_1="";
			$podr_drive_2="";
			$podr_drive_3="";
			$podr_drive_4="";
		}	
		*/
		
		
		$graf_name = "";
		if (mysqli_num_rows($query_graf)>0) $graf_name = "(".$list_graf["KOD"].") ".$list_graf["NAME"];
		if($list['DATE_END_IZM_5']=="NULL")$DATE_END_IZM_5=""; else $DATE_END_IZM_5=Date_Mysql_for_View($list['DATE_END_IZM_5']);
		if($list['DATE_END_IZM_6']=="NULL")$DATE_END_IZM_6=""; else $DATE_END_IZM_6=Date_Mysql_for_View($list['DATE_END_IZM_6']);
		if($list['DATE_END_IZM_7']=="NULL")$DATE_END_IZM_7=""; else $DATE_END_IZM_7=Date_Mysql_for_View($list['DATE_END_IZM_7']);
		
		echo ("|".$list['TABN']."|".Date_Mysql_for_View($list['DATE_BEG'])."|".Date_Mysql_for_View($list['DATE_END'])."|".$list['FAM']."|".$list['NAME']."|".$list['OTCH']."|".$list['ID_PODRAZD']."|[".$list_podr['SOKR_NAME_RU']."] ".$list_podr['NAME']."|".$list['ID_PROFES']."|".$list_prof['NAME']."|".$list['ID_KATEG_PERS']."|".$list_kateg['NAME']."|".$list['ID_PROPUSK']."|".$list_prop['NAME']."|".$list['ID_GRAF']."|".$graf_name."|".Date_Mysql_for_View($list['DATE_END_GRAF'])."|".$list['NENORM']."|".$list['OKLAD']."|".$list['NADBAVKA']."|".$list['PROC_PREM']."|".$list['UVOLEN']."|".$list['DEKRET']."|".$list['IP']."|".$list['ID_PROFILE']."|".$list['FAM_ROD']."|".$list['DOPL_SOVM']."|".$list['PROC_DOPL_SECRET']."|".$list['PROC_DOPL_VRED']."|".$list['PROC_DOPL_KLASS']."|".$list['DOPL_MOLOD_SPEC']."|".$list['ID_SPR_OTR_BUX']."|".$str_otr_bux."|".$list['NUM_TD']."|".Date_Mysql_for_View($list['DATE_TD'])."|".$list['VID_DS']."|".$list['NUM_DS']."|".Date_Mysql_for_View($list['DATE_DS'])."|".Date_Mysql_for_View($list['DATE_ROGD'])."|".$list['POL']."|".$list['SER_PASP']."|".$list['NUM_PASP']."|".$list['KEM_VID_PASP']."|".Date_Mysql_for_View($list['DATE_VID_PASP'])."|".$list['KOD_PODR_PASP']."|".$list['ADRES_PROPIS']."|".$list['ID_SPR_VID_OBRAZ']."|".$str_vid_obraz."|".Date_Mysql_for_View($list['DATE_END_OBRAZ'])."|".$list['OSN_TD']."|".$list['OSN_DS']."|".$list['ID_SPR_PRICH_IZM']."|".$list['STAVKA']."|".$list['IZM_RAZD_5']."|".$list['IZM_RAZD_6']."|".$list['IZM_RAZD_7']."|".$DATE_END_IZM_5."|".$DATE_END_IZM_6."|".$DATE_END_IZM_7."|".$list['PROC_RK']);
	
}


if($_GET['action']=="layout_size")
{
	$query = mysqli_query($db, "SELECT * FROM USER_SETTINGS WHERE IP = '".$_SERVER['REMOTE_USER']."'");
	$list = mysqli_fetch_array($query);
	echo "|".$list['PERS_A_W']."|".$list['PERS_C_H'];
}

if($_GET['action']=="get_id_profile")
{
	//Определяем пользователя
	require_once("../../Application.php");
	$objApp = new Application();
	$objApp->getCurrentUserInfo();

	$query_pers = mysqli_query($db, "SELECT * FROM PERSONAL WHERE TABN='".$_SESSION["Gate_currentUser"]["tabno"]."' AND DATE_END='2100-01-01'");
	$list_pers = mysqli_fetch_assoc($query_pers);
	
	$query_profile = mysqli_query($db, "SELECT * FROM SPR_PROFILES WHERE ID='".$list_pers['ID_PROFILE']."' ");
	$list_profile = mysqli_fetch_assoc($query_profile);
	
	$query_propusk = mysqli_query($db, "SELECT * FROM SPR_VID_PROP WHERE ID='".$list_pers['ID_PROPUSK']."' ");
	$list_propusk = mysqli_fetch_assoc($query_propusk);
	
	$query_fam_lower = mysqli_query($db,"SELECT CONCAT(LEFT('".$list_pers['FAM']."',1),LOWER(RIGHT('".$list_pers['FAM']."',LENGTH('".$list_pers['FAM']."')/2-1)))");
	$list_fam_lower = mysqli_fetch_array($query_fam_lower);
	
	if(mysqli_num_rows($query_pers)>0) echo "|".$list_pers['ID_PROFILE']."|".$list_pers['IP']."|".$list_profile['NAME']."|".$list_fam_lower[0]." ".substr($list_pers['NAME'],0,2).".".substr($list_pers['OTCH'],0,2).".|".$_SERVER['REMOTE_USER']."|".$list_propusk['NAME']; else echo "|0|0|0|0|".$_SERVER['REMOTE_USER']."|0";
}

if($_GET['action']=="get_last_ds")
{
	$query_info = mysqli_query($db, "SELECT * FROM PERSONAL WHERE ID = '".$_GET['id']."'");
	$list_info = mysqli_fetch_assoc($query_info);
	
	$query_last_rec = mysqli_query($db, "SELECT * FROM PERSONAL WHERE TABN='".$list_info['TABN']."' AND DATE_BEG=(SELECT MAX(DATE_BEG) FROM PERSONAL WHERE TABN='".$list_info['TABN']."' AND DATE_BEG<'".$list_info['DATE_BEG']."') ");
	$list_last_rec = mysqli_fetch_assoc($query_last_rec);
	
	if($list_last_rec['DATE_DS']=="NULL")$DATE_DS=""; else $DATE_DS=Date_Mysql_for_View($list_last_rec['DATE_DS']);
	
	echo "|".$list_last_rec['VID_DS']."|".$list_last_rec['NUM_DS']."|".$DATE_DS;
}

if($_GET['action']=="get_last_td")
{
	$query_info = mysqli_query($db, "SELECT * FROM PERSONAL WHERE ID = '".$_GET['id']."'");
	$list_info = mysqli_fetch_assoc($query_info);
	
	$query_last_rec = mysqli_query($db, "SELECT * FROM PERSONAL WHERE TABN='".$list_info['TABN']."' AND DATE_BEG=(SELECT MAX(DATE_BEG) FROM PERSONAL WHERE TABN='".$list_info['TABN']."' AND DATE_BEG<'".$list_info['DATE_BEG']."') ");
	$list_last_rec = mysqli_fetch_assoc($query_last_rec);
	
	if($list_last_rec['DATE_TD']=="NULL")$DATE_TD=""; else $DATE_TD=Date_Mysql_for_View($list_last_rec['DATE_TD']);
	
	echo "|".$list_last_rec['NUM_TD']."|".$DATE_TD;
}

if($_GET['action']=="get_num_ds_z")
{
	//$query_info = mysqli_query($db, "SELECT * FROM PERSONAL WHERE ID = '".$_GET['id']."'");
	//$list_info = mysqli_fetch_assoc($query_info);
	//Ищем последний номер зарплатного доп. соглашения в текущем году по текущему трудовому договору
	$query_last_num_z = mysqli_query($db, "SELECT NUM_DS FROM PERSONAL WHERE TABN='".$_GET['tabn']."' AND NUM_TD='".$_GET['num_td']."' AND DATE_BEG=(SELECT MAX(DATE_BEG) FROM PERSONAL WHERE TABN='".$_GET['tabn']."' AND VID_DS='z' AND YEAR(DATE_BEG)=YEAR('".Date_Convert_for_Mysql($_GET['date_beg'])."') AND DATE_BEG<'".Date_Convert_for_Mysql($_GET['date_beg'])."') ");
	$list_last_num_z = mysqli_fetch_array($query_last_num_z);
	
	if(mysqli_num_rows($query_last_num_z)>0)//Если в текущем году уже есть доп.соглашение, то увеличиваем номер
	{
		$query_new_num_z = mysqli_query($db, "SELECT CONCAT(SUBSTRING('".$list_last_num_z['NUM_DS']."',1,3),(SUBSTRING('".$list_last_num_z['NUM_DS']."',4)+1)) ");
		$list_new_num_z = mysqli_fetch_array($query_new_num_z);
	}
	else
	{
		$query_new_num_z = mysqli_query($db, "SELECT CONCAT(SUBSTRING(YEAR('".Date_Convert_for_Mysql($_GET['date_beg'])."'),3,2),'-1') ");
		$list_new_num_z = mysqli_fetch_array($query_new_num_z);
	}
	
	echo "|".$list_new_num_z[0];
}



?>
