<?php
/* 
//fake input
$this_msg['service'] = "Discord"; 
$this_msg['host'] = "1234";
$this_msg['channel'] = "5678";
$this_msg['id'] = "875";
$this_msg['time'] = time();
$this_msg['user'] = "Defender833";
$this_msg['message'] = "!vote 2";

$mysqli_host = "127.0.0.1";
$mysqli_user = "root";
$mysqli_pass = "";
$mysqli_base	= "votes";
*/
$mysqli_host = $mysql['host'];
$mysqli_user = $mysql['user'];
$mysqli_pass = $mysql['pass'];
$mysqli_base = $mysql['base'];

$link_db = mysqli_connect($mysqli_host,$mysqli_user,$mysqli_pass, $mysqli_base);

$result = mysqli_query($link_db, "select * from Umfragen");

while($line = mysqli_fetch_assoc($result))
{
	echo $line["Umfragetext"]."<br>";
	$result_2 = mysqli_query($link_db, "select * from Ergebnisse where Umfrage_ID = ".$line["Vote_ID"]);
	
	while($line_2 = mysqli_fetch_assoc($result_2))
	{
		echo $line_2["Auswahl_ID"].":".$line_2["Text"]."<br>";	
	}

	$voted = mysqli_query($link_db, "select * from user_voted where Umfrage_ID = ".$line["Vote_ID"]." and User_ID ='".$this_msg['user']."'");
	
	if(mysqli_num_rows($voted)== 0)
	{
		$vote_array = explode(" ",$this_msg['message']);
	// input string zerlegen
	if(isset($vote_array[1]))
	{
		//echo "ich komm zum schreiben!";
		$write = mysqli_query($link_db, "update Ergebnisse set Anzahl_Votes = Anzahl_Votes +1 where Umfrage_ID = ".$line["Vote_ID"]." and Auswahl_ID = ".$vote_array[1]);		
		// der jeweiligen Auswahl_ID einen Punkt gutschreiben
		$write = mysqli_query($link_db, "insert into user_voted set Umfrage_ID = ".$line["Vote_ID"]." , User_ID ='".$this_msg['user']."'");		
		// setze user in base auf die wählerliste
	}
	
	}
	
	
}
?>