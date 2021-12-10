<?php 
	header("Cache-Control: no-store, no-cache");//Запрет кэширования и сохранения истории
	require_once("../../mysql.php");
	$idUch=$_GET["idUch"];
	$cex=$_GET["cex"];
	$year=$_GET["year"];
	$month=$_GET["month"];
	if ($month<10)
		{$month2="0".$month;}//добавляем ноль к значению
	else
		{$month2=$month;}
		
	$year2=substr($year,2,2);


	//Перечень всех нарядов выбранного цеха
	$query1 = mysql_query("SELECT * FROM nar_sum WHERE ID_UCH IN (SELECT ID FROM nar_uch WHERE ID_ROOT = '".$idUch."') AND YEAR = '".$year."' AND MONTH = '".$month."' AND DATE_RASPR <> '0000-00-00'");
	//
	$query_create_temp_table = mysql_query("CREATE TEMPORARY TABLE temp(TABN varchar(25),SHNU varchar(25),SUMM double(25,2))");
	while($list1 = mysql_fetch_assoc($query1))
	{
		
		$query2 = mysql_query("SELECT * FROM nar_brig WHERE ID_NAR_SUM='".$list1["ID"]."'");
		while($list2 = mysql_fetch_assoc($query2))
		{
			if($list2['SUMM_TAR']!=0)$query_insert_temp_tarif = mysql_query("INSERT INTO temp VALUES ('".$list2['TABN']."','".iconv('windows-1251','cp866',$list1['SHNU_TAR'])."','".$list2['SUMM_TAR']."')");
			if($list2['SUMM_PR']!=0)$query_insert_temp_prem = mysql_query("INSERT INTO temp VALUES ('".$list2['TABN']."','".iconv('windows-1251','cp866',$list1['SHNU_PR'])."','".$list2['SUMM_PR']."')");
			if($list2['SUMM_PR_2']!=0)$query_insert_temp_prem = mysql_query("INSERT INTO temp VALUES ('".$list2['TABN']."','".iconv('windows-1251','cp866',$list1['SHNU_PR_2'])."','".$list2['SUMM_PR_2']."')");
			if($list2['SUMM_300']!=0)$query_insert_temp_prem = mysql_query("INSERT INTO temp VALUES ('".$list2['TABN']."','".iconv('windows-1251','cp866','001г')."','".$list2['SUMM_300']."')");
			if($list2['SUMM_303']!=0)$query_insert_temp_prem = mysql_query("INSERT INTO temp VALUES ('".$list2['TABN']."','".iconv('windows-1251','cp866','001б')."','".$list2['SUMM_303']."')");
			if($list2['SUMM_324']!=0)$query_insert_temp_prem = mysql_query("INSERT INTO temp VALUES ('".$list2['TABN']."','".iconv('windows-1251','cp866','008')."','".$list2['SUMM_324']."')");
			if($list2['SUMM_SV_1']!=0)$query_insert_temp_sv1 = mysql_query("INSERT INTO temp VALUES ('".$list2['TABN']."','".iconv('windows-1251','cp866','520')."','".$list2['SUMM_SV_1']."')");
			if($list2['SUMM_SV_2']!=0)$query_insert_temp_sv2 = mysql_query("INSERT INTO temp VALUES ('".$list2['TABN']."','".iconv('windows-1251','cp866','520')."','".$list2['SUMM_SV_2']."')");
			if($list2['SUMM_VIX']!=0)$query_insert_temp_vix = mysql_query("INSERT INTO temp VALUES ('".$list2['TABN']."','".iconv('windows-1251','cp866','034')."','".$list2['SUMM_VIX']."')");
			
		}
		
	}

	$query_upload = mysql_query("SELECT TABN,SHNU,SUM(SUMM) AS SUMM2 FROM temp GROUP BY TABN,SHNU");
	
	$f=fopen("c:\PARUS\\ZARPLATA_".$cex.".TXT","w+");
	fputs($f,"00100002".$cex." ".$month2.$year2."0000 00000000000.00 0000 0000 00\r\n");
	$count=0;
	
	while($list_upload = mysql_fetch_assoc($query_upload))
		{
			$summ = $list_upload['SUMM2']*100;
			$lensumm = strlen($summ);
			for($i=8-$lensumm; $i>0; $i--){$summ="0".$summ;}
		
			$begm = $month;
			$lenbegm = strlen($begm);
			for($i=2-$lenbegm; $i>0; $i--){$begm="0".$begm;}
			if(strlen($list_upload['SHNU'])== 3)
				{$shnu = $list_upload['SHNU']." ";}
			else
				{$shnu = $list_upload['SHNU'];}
			fputs($f,"200".$list_upload['TABN'].$shnu.$begm."000000 ".$summ."000.00 0000 0000 00\r\n");
			$count++;
		}
	
	$len = strlen($count);
	for($i=8-$len; $i>0; $i--)
		{
			$count="0".$count;
		}
	
	fputs($f,"29900000900 00000000 ".$count."000.00 0000 0000 00\r\n");
	fclose($f);

	if(mysql_error()==false)echo "Данные выгружены! Кол-во строк:".$count;

?> 
