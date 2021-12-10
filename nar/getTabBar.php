<?php
//session_start();
//connect to database
require_once("../../../mysql.php");
$query = mysql_query("SELECT * FROM nar_uch WHERE CEX = '".$_GET['cex']."'");

	print("<?xml version=\"1.0\"?>");
	print("<tabbar>");
	print("<row>");
		while ($list = mysql_fetch_assoc($query))
			{
            	print("<tab id='".$list['ID']."' width='100px'>".$list['NAME']."</tab>");
			 	$query2 = mysql_query("SELECT * FROM nar_brig WHERE ID_UCH = '".$list["ID"]."'");
			 	while ($list2 = mysql_fetch_assoc($query2))
				{
					print("<tab id='".$list2['ID']."' width='100px'>".$list2['NAME']."</tab>");
				}
			}
	print("</row>");
	print("</tabbar>");
?>
