<?php

include 'private.php';

$cook = explode("!",$_COOKIE['Ucam-WebAuth-Session-S']);
$crsid = mysqli_real_escape_string($link,$cook[7]);

$eventID = mysqli_real_escape_string($link,$_POST["eventID"]);

$quer = "SELECT * FROM events WHERE id=".$eventID;
$query = mysqli_query($link,$quer) or die("Could not check event exists.");

if (mysqli_num_rows($query) > 0) {

	$quer = "SELECT * FROM participants WHERE event_id=".$eventID." AND active=1";
	$query = mysqli_query($link,$quer) or die("Could not check event participants.");

	# If event is not full
	if (mysqli_num_rows($query) < $EVENT_MAX) {

		$quer = "SELECT * FROM participants WHERE event_id=".$eventID." AND crsid='".$crsid."'";
		$query = mysqli_query($link,$quer) or die("Could not check if already a participant.");

		if (mysqli_num_rows($query) > 0) { 
			$quer = "UPDATE participants SET active=1 WHERE event_id=".$eventID." AND crsid='".$crsid."'";
			$query = mysqli_query($link,$quer) or die("Could not reactivate participant.");
		} else {
			
			$quer="INSERT INTO participants(crsid,event_id) VALUES ('$crsid','$eventID')";
			$query = mysqli_query($link,$quer) or die("Could not insert event.");

		}

		
	}

}

header("Location: /finder/");

?>
