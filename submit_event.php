<?php

include 'private.php';

$cook = explode("!",$_COOKIE['Ucam-WebAuth-Session-S']);
$crsid = mysqli_real_escape_string($link,$cook[7]);

$name = mysqli_real_escape_string($link,$_POST["name"]);
$loc = mysqli_real_escape_string($link,$_POST["loc"]);

$sdate = mysqli_real_escape_string($link,$_POST["sdate"]);
$stime = mysqli_real_escape_string($link,$_POST["stime"]);
$sdatetime = $sdate . " " . $stime;

$quer="INSERT INTO events(name,location,creator,start_time) VALUES ('$name','$loc','$crsid','$sdatetime') ";
$query = mysqli_query($link,$quer) or die("Could not insert event.");

$MID = mysqli_insert_id($link);

# Admins don't automatically join events they create
if (!in_array($crsid,$ADMIN)) {
	$quer="INSERT INTO participants(crsid,event_id) VALUES ('$crsid','$MID') ";
	$query = mysqli_query($link,$quer) or die("Could not insert event.");
}

header("Location: /finder/");

?>
