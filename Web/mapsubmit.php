<?php
include "../WebInclude/funcs.php";
$cmrID = getCMRID();
session_start();
if (isset($_SESSION['loggedIn'])) {
	$temp = explode(".", $_FILES["file"]["name"]);
	$extension = end($temp);

	if ($extension == "html" || $extension == "php"){
		echo '<p style="color: red;"><b>Your account is hereby banned from all future CMR\'s and your racedata deleted.</b></p>Just kidding. Don\'t try to exploit my server though. ♥';
	} else if (count($temp) == 1) { //no file extensions.
		echo "Thanks " . $_SESSION['usernameCase'] . "!<br>";
		if ($_FILES["file"]["error"] > 0) {
			echo "ERROR UPLOADING. Return Code: " . $_FILES["file"]["error"] . "<br>";
			echo "screenshot this to EklipZ in #DFcmr";
		} else {
			$maps = getMaps();

			$filename = $_SESSION['usernameCase'] . "-" . $_POST["mapname"];
			$safefile = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $filename); //REGEX's OUT CONTROL CODES AND WHATNOT.
			
			$split = explode("-", $safefile, 2);
			$mapname = $split[1];
			$mapper = $split[0];

			$mappath = "C:/CMR/maps/" . $cmrID . "/pending/" . $safefile;

			if (isset($maps[$safefile])) {
				$mapdata = $maps[$safefile];
			} else {
				$mapdata = array();
				$mapdata['url'] = "";
				$mapdata['approvedby'] = "";
			}
			
			$mapdata['name'] = $mapname;
			$mapdata['filepath'] = $mappath;
			$mapdata['authorname'] = $mapper;

			if (file_exists()) {
				unlink($mappath);
				move_uploaded_file($_FILES["file"]["tmp_name"], $mappath);
				echo "Replaced: " . $safefile . " successfully.<br>";
			} else {
				move_uploaded_file($_FILES["file"]["tmp_name"], $mappath);
				echo "Uploaded: " . $safefile . " successfully.<br>";
			}
			array_push($maps, $mapdata);
			writeMaps($maps);
		}
	} else {
		echo "uh oh you had a \".\" in the filename. Please, no files with extensions or \".\"'s";
		echo "<br><a href=\"map.php\">Resubmit</a>";
	}
} else {
	$_SESSION['redirect'] = "http://eklipz.us.to/cmr/map.php";
	$_SESSION['warning'] = "You need to log in before uploading maps.";
	session_write_close();
	header( 'Location: http://eklipz.us.to/cmr/login.php' );
}
?>