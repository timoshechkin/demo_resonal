<?php
	error_reporting(E_ALL ^ E_NOTICE);
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
  		header("Content-type: application/xhtml+xml"); } else {
  		header("Content-type: text/xml");
	}
	echo("<?xml version='1.0' encoding='UTF-8'?>\n");
//connect to database
require_once("../../mysql.php");
$query_font = mysqli_query($db,"SELECT * FROM USER_SETTINGS WHERE IP='".$_SERVER['REMOTE_USER']."'");
$list_font = mysqli_fetch_assoc($query_font);

$query_profile = mysqli_query($db,"SELECT * FROM PERSONAL WHERE ID='".$_GET['id']."' AND DATE_END='2100-01-01'");
$list_profile = mysqli_fetch_assoc($query_profile);

?>
<rows>
<?php
		$query_podr = mysqli_query($db,"SELECT * FROM SPR_PODRAZD WHERE ID IN(".$list_profile['LIST_PODR_DRIVE'].") ORDER BY NAME ASC");
		while ($list = mysqli_fetch_assoc($query_podr))
		{
			print("<row id='".$list["ID"]."' style='font-family:".$list_font['GRID_FONT_FAMILY']."; font-size:".$list_font['GRID_FONT_SIZE']."px;' >");
				print("<cell>");
					print("[".$list["SOKR_NAME_RU"]."] ".$list["NAME"]);
				print("</cell>");
			print("</row>");
		}
?>
</rows>
