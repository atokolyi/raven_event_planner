<?php

include 'private.php';

$cook = explode("!",$_COOKIE['Ucam-WebAuth-Session-S']);
$crsid = mysqli_real_escape_string($link,$cook[7]);

$eventID = mysqli_real_escape_string($link,$_POST["eventID"]);

$quer="UPDATE participants SET active=0, time_left=NOW() WHERE event_id='$eventID'";
$query = mysqli_query($link,$quer) or die("Could not remove participants.");

$quer="UPDATE events SET active=0 WHERE id='$eventID'";
$query = mysqli_query($link,$quer) or die("Could not remove event.");

header("Location: /finder/");

?>
