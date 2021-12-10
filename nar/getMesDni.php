<?php
//session_start();
        //connect to database
        require_once("../../mysql.php");
		$year = $_GET['year'];
		$month = $_GET['month'];
		if ($month==1){$month=12;$year=$year-1;}else{$month=$month-1;}
		$query = mysql_query("SELECT * FROM mesdni WHERE YEAR = '".$year."' AND MONTH = '".$month."'");
		$list = mysql_fetch_assoc($query);
		
		echo $list['DNI'];
		
		
    ?>
