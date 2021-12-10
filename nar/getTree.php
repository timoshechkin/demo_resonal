<?php 
	error_reporting(E_ALL ^ E_NOTICE);
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
  		header("Content-type: application/xhtml+xml"); } else {
  		header("Content-type: text/xml");
	}
echo("<?xml version='1.0' encoding='UTF-8'?>\n");
require_once("../../mysql.php");

$query_font = mysqli_query($db,"SELECT * FROM USER_SETTINGS WHERE IP='".$_SERVER['REMOTE_USER']."'");
$list_font = mysqli_fetch_assoc($query_font);

//output data in XML format  

	if (isset($_GET["id"])) {$url_var=$_GET["id"];}

	
print("<tree id='".$url_var."' >");
	
	$query = mysqli_query($db,"SELECT * FROM SPR_PODRAZD WHERE ID_ROOT = '".$url_var."'");
         
		 while($list = mysqli_fetch_assoc($query))
			{
			$child=1;
			$query_check = mysqli_query($db,"SELECT * FROM SPR_PODRAZD WHERE  ID_ROOT = '".$list['ID']."'");
			$list_check = mysqli_num_rows($query_check);
			if($list_check==0) $child=0;

			//text='[".$list['KOD']."] ".$list['NAME']."'
			print("
					<item child='".$child."' id='".$list['ID']."' text='[".$list['SOKR_NAME_RU']."] ".$list['NAME']."'>
						<itemtext><![CDATA[<font color='red'>Label</font>]]></itemtext>
						<userdata name='kod'>".$list['KOD']."</userdata>
					</item>
				");
			}
			
print("</tree>");

?> 
