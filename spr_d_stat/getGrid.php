<?php
	error_reporting(E_ALL ^ E_NOTICE);
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
  		header("Content-type: application/xhtml+xml"); } else {
  		header("Content-type: text/xml");
	}


	echo("<?xml version='1.0' encoding='UTF-8'?>\n");
//connect to database
require_once("../../../mysql.php");
$query_font = mysqli_query($db,"SELECT * FROM USER_SETTINGS WHERE IP='".$_SERVER['REMOTE_USER']."'");
$list_font = mysqli_fetch_assoc($query_font);
?>
<rows>
<?php
		$query_podr = mysqli_query($db,"SELECT * FROM SPR_D_STAT");
		while ($list = mysqli_fetch_assoc($query_podr))
			{
            print("<row id='".$list["ID"]."' style='font-family:".$list_font['GRID_FONT_FAMILY']."; font-size:".$list_font['GRID_FONT_SIZE']."px;' >");
				print("<cell>");
                    print($list["KOD"]);
                print("</cell>");
				print("<cell>");
                    print($list["TAB"]);
                print("</cell>");
				print("<cell>");
                    print($list["NAME"]);
                print("</cell>");
             print("</row>");
			}
?>
</rows>
