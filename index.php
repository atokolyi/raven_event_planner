<?php

include 'private.php';

echo '<html><head><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" /><link rel="stylesheet" href="main.css"><script src="custom.js"></script><title>'.$TITLE.'</title><link rel="apple-touch-icon" sizes="180x180" href="/finder/apple-touch-icon.png"><link rel="icon" type="image/png" sizes="32x32" href="/finder/favicon-32x32.png"><link rel="icon" type="image/png" sizes="16x16" href="/finder/favicon-16x16.png"><link rel="manifest" href="/finder/site.webmanifest"><link rel="shortcut icon" href="/finder/favicon.ico" /></head><body>';

$cook = explode("!",$_COOKIE['Ucam-WebAuth-Session-S']);

$crsid = mysqli_real_escape_string($link,$cook[7]);

echo "<h1>".$TITLE."</h1>";

if (in_array($crsid,$ADMIN)) {
	echo "<b>Admin mode enabled.</b></br></br>";
}

$quer = "SELECT nickname FROM nicks WHERE crsid='$crsid'";
$query = mysqli_query($link, $quer);
if (mysqli_num_rows($query) < 1) {
	echo "Your CRSID: ".$crsid."</br></br>";
	echo "</b>Welcome new user!</b> Start by setting your nickname and mobile:</br>";
	echo "<form method='post' action='set_nick.php'><input type='text' id='nick' name='nick' value='' placeholder='Nickname' required><input type='text' id='mobi' name='mobi' value='' placeholder='Mobile number' required><input type='submit' value='Set'></form>";
	goto finish; # And no I will not apologise for it
}

# Print events that you're a part of
events_table(True,$link,$crsid,$ADMIN,$MAP_KEY,$EVENT_MAX);

# Print events that you haven't joined
events_table(False,$link,$crsid,$ADMIN,$MAP_KEY,$EVENT_MAX);

echo '</br></br><b>Create an event</b></br>';
echo "<form method='post' action='submit_event.php'>";
echo "<input type='text' id='name' name='name' value='' required placeholder='Short description'></br>";
echo "<input type='text' id='loc' name='loc' value='' required placeholder='Location'></br>";
echo "<input style='width:50%' type='date' id='sdate' name='sdate' value='".date('Y-m-d')."' required>";
echo "<input style='width:30%' type='time' id='stime' name='stime' value='".date('H:i')."' required></br>";
echo "<input type='submit' value='Submit'></form>";

# Admins can see registered users details
if (in_array($crsid,$ADMIN)) { 
	echo "</br></br><b>Registered users</b></br>";
	echo "<table border='1'><tr style='font-weight: bold;'><td>CRSID</td><td>Nickname</td><td>Number</td><td>Registered</td></tr>";
	$query = mysqli_query($link, "SELECT * FROM nicks ORDER BY first_set_dt ASC");
	# Fetch nickname if present!
	if (mysqli_num_rows($query) > 0) {
	  // output data of each row
	  while($row = mysqli_fetch_assoc($query)) {
		echo "<tr><td>";
		echo $row["crsid"];
		echo "</td><td>";
		echo $row["nickname"];
		echo "</td><td>";
		echo "<a href='tel:" . $row['mobi'] . "'>" . $row['mobi'] . "</a>";
		echo "</td><td>";
		echo $row["first_set_dt"];
		echo "</td></tr>";
	  }
	} else {
	  echo "<tr><td>No results.</td><td></td><td></td></tr>";
	}
	echo "</table>";
}

# Change nickname and mobile
$quer = "SELECT nickname,mobi FROM nicks WHERE crsid='$crsid'";
$query = mysqli_query($link, $quer);
if (mysqli_num_rows($query) > 0) {
	$row = mysqli_fetch_assoc($query); 
	echo "</br><b>Update details</b></br>";	
	echo "<form method='post' action='set_nick.php'><input type='text' id='nick' name='nick' value='".$row['nickname']."' required><input type='text' id='mobi' name='mobi' value='".$row['mobi']."' required><input type='submit' value='Change'></form>";
}

finish:
echo "</br></body><footer>Made with 👽 by <a href='https://alextokolyi.com/'>Alex Tokolyi</a>.</br>Source code on <a href='https://github.com/atokolyi/raven_event_planner'>GitHub</a>.</footer></html>";
$query = mysqli_query($link,"INSERT INTO visit(crsid) VALUES ('$crsid')") or die("Could not insert CRSID");


# Print the table of events
function events_table($part,$link,$crsid,$ADMIN,$MAP_KEY,$EVENT_MAX) {

	if ($part) {
		$quer = "SELECT * FROM events WHERE events.id IN (SELECT participants.event_id from participants where participants.crsid='".$crsid."' AND participants.active=1) AND events.active=1 ORDER BY events.start_time ASC";
	} else {
		$quer = "SELECT * FROM events WHERE events.id NOT IN (SELECT participants.event_id from participants where participants.crsid='".$crsid."' AND participants.active=1) AND events.active=1 ORDER BY events.start_time ASC";
	}

	$query = mysqli_query($link, $quer);
	if (!$part) { echo "<b>Active events</b></br>"; }
	if (mysqli_num_rows($query) > 0) {

		if ($part) { 
			echo "<b>Joined events</b></br>";
			echo "<table border='1'><tr style='font-weight: bold;'><td>Name</td><td>Location</td></tr>";
		} else {
			echo "<table border='1'><tr style='font-weight: bold;'><td>Name</td><td>Approx. location</td></tr>";
		}
	  	while($row = mysqli_fetch_assoc($query)) {
		
		$querP = "SELECT participants.crsid,nicks.nickname from participants LEFT JOIN nicks ON participants.crsid=nicks.crsid WHERE participants.event_id=".$row["id"]." AND participants.active=1 ORDER BY participants.time_joined ASC";
		$queryP = mysqli_query($link, $querP);

		if ($part || mysqli_num_rows($queryP) < $EVENT_MAX || in_array($crsid,$ADMIN)) {

			# Print event details
			echo "<tr><td>";
			echo "<b>".$row["name"]."</b>";
			$st_dt = strtotime($row["start_time"]);
			echo "</br>".date('D jS \of M, g:ia',$st_dt)."</b>";

			# Join or event full button
			if (!$part) {
				if (mysqli_num_rows($queryP) < $EVENT_MAX) {
					echo "</br></br><form method='post' action='join_event.php'><input type='hidden' id='eventID' name='eventID' value='".$row["id"]."'><input type='submit' value='Join! (".($EVENT_MAX-mysqli_num_rows($queryP))." left)'></form>";
				} else {
					echo "</br></br><button type='button' disabled>Event full.</button></br></br>";
				}
			}
			
			# Leave event button
			if ($part) {
				echo "</br></br><form method='post' action='leave_event.php'><input type='hidden' id='eventID' name='eventID' value='".$row["id"]."'><input type='submit' value='Leave event'></form>";
			} else {
				echo "";
			}

			# Print event participants
			if (mysqli_num_rows($queryP) > 0) {
				#echo "</br>Current participants:";
				while($rowP = mysqli_fetch_assoc($queryP)) {
					echo "– ".$rowP["nickname"]." (".$rowP["crsid"].")</br>";
				}
				echo "</br>";
			}
			
			# If you created the event (or are an admin), you can delete it
			if ($row["creator"]==$crsid || in_array($crsid,$ADMIN)) {
				echo "<form method='post' action='delete_event.php'><input type='hidden' id='eventID' name='eventID' value='".$row["id"]."'><input type='submit' value='Delete event'></form>";
			}
			
			# If a map exists for event ID get that, else download one then use
			echo "</td><td>";
			$LOCAL_MAP = "maps/".$row["id"].".png";
			$FULL_LOC = urlencode($row["location"].", Cambridge, UK");
			$MAP_LINK = "https://maps.google.com/?q=" . $FULL_LOC;
			if (!file_exists($LOCAL_MAP)) {
				$IMG_SRC = "https://maps.googleapis.com/maps/api/staticmap?markers=color:red|size:tiny|" . $FULL_LOC . "&zoom=13&size=200x200&scale=2&key=" . $MAP_KEY;
				file_put_contents($LOCAL_MAP,file_get_contents($IMG_SRC));
			}
			
			# If you've joined the event, print the creators contact details and the event address
			if ($part || in_array($crsid,$ADMIN)) {
				echo '<a href="'.$MAP_LINK.'"><img src="'.$LOCAL_MAP.'" height=200 width=200></a><hr>'. $row["location"].'</br>Organiser: ';
				$querN = "SELECT crsid,nickname,mobi FROM nicks WHERE crsid='".$row["creator"]."'";
				$queryN = mysqli_query($link, $querN);
				if (mysqli_num_rows($queryN) > 0) {
					$rowN = mysqli_fetch_assoc($queryN);
					echo $rowN['nickname'] . " (" . $rowN['crsid'] . ") </br><a href='tel:" . $rowN['mobi'] . "'>" . $rowN['mobi'] . "</a>";
				} else {
					echo $row["creator"];
				}
			} else { 
				echo '<img src="'.$LOCAL_MAP.'" height=200 width=200>';
			}
			echo "</td></tr>";

		}
	  }
	echo "</table>";
	} else {
	  if (!$part) { echo "None found. Create one below!</br>"; }
	}
	if ($part && mysqli_num_rows($query) > 0) { 
		echo "</br></br>";}
}

?>
