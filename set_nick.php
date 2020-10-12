<?php

include 'private.php';

$cook = explode("!",$_COOKIE['Ucam-WebAuth-Session-S']);
$crsid = mysqli_real_escape_string($link,$cook[7]);

$nick = mysqli_real_escape_string($link,$_POST["nick"]);
$mobi = mysqli_real_escape_string($link,$_POST["mobi"]);

$quer="INSERT INTO nicks(crsid,nickname,mobi) VALUES ('$crsid','$nick','$mobi') ON DUPLICATE KEY UPDATE nickname='$nick', mobi='$mobi'";
$query = mysqli_query($link,$quer) or die("Could not insert CRSID");

header("Location: /finder/");

?>
