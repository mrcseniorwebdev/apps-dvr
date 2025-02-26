<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("databaseConnection.php");

$num_shows = is_array($_REQUEST['air_date']) ? count($_REQUEST['air_date']) : 1;

$cols = [];
$vals = [];

if (!empty($_REQUEST["dvdid"])) {
   for ($i = 0; $i < $num_shows; $i++) {
       if(!empty($_REQUEST["air_date"][$i]) && !empty($_REQUEST["show_name"][$i]) && !empty($_REQUEST["network"][$i])) {
           // Build Data for query //
           foreach ($DBH->query("SELECT TOP 1 ID FROM dvr.dbo.dvrRecordedShows ORDER BY ID DESC;") as $row) {
               //Get last ID and add one to it + show index
               $ID = $row[0] + $i + 1;
           }

           $cols[$i] = [];
           $vals[$i] = [];

           if (empty($ID)) {
               die("Failed to properly read database.");
           }

           $cols[$i][] = "ID";
           $vals[$i][':ID_' . $i] = $ID;

           //DVD ID
           $cols[$i][] = "DVDNumber";
           $vals[$i][':dvdID_' . $i] = $_REQUEST["dvdid"];

           //Air Date
           $airDateVal = strtotime($_REQUEST["air_date"][$i]);
           if($airDateVal == false) {
               die("There is an invalid air date provided.");
           }
           $airDate = date("Ymd", $airDateVal);
           $cols[$i][] = "ShowDate";
           $vals[$i][':air_date_' . $i] = $airDate;

           //Network
           $network = $_REQUEST["network"][$i];
           $cols[$i][] = "Network";
           $vals[$i][':network_' . $i] = $network;

           //Show
           $cols[$i][] = "LongProgram";
           $vals[$i][':show_name_' . $i] = $_REQUEST["show_name"][$i];
       } else {
           break;
       }

       //Duration
        $cols[$i][] = "Duration";
        $vals[$i][':duration_' . $i] = !empty($_REQUEST["duration"][$i]) ? $_REQUEST["duration"][$i] : null;

        //Program Time
        $cols[$i][] = "ProgramTime";
        $vals[$i][':program_time_' . $i] = !empty($_REQUEST["program_time"][$i]) ? $_REQUEST["program_time"][$i] : null;

        //Show Time Position
        $cols[$i][] = "ShowTimePosition";
        $vals[$i][':time_position_' . $i] = !empty($_REQUEST["time_position"][$i]) ? $_REQUEST["time_position"][$i] : null;

        //Location
        $cols[$i][] = "Location";
        $vals[$i][':location_' . $i] = !empty($_REQUEST["location"][$i]) ? $_REQUEST["location"][$i] : null;

        //Comments
        $cols[$i][] = "Notes";
        $vals[$i][':comments_' . $i] = !empty($_REQUEST["comments"][$i]) ? $_REQUEST["comments"][$i] : null;
   }
} else {
   die("The DVD/VHS ID is essential to everything here");
}

if (empty($cols)) {
	die("No data passed, be sure you have the airdate, network and show name filled in");
}

$insert_cols = array_map(function($item) { return "(" . implode(', ', $item) . ")"; }, $cols);
$insert_vals = array_map(function($item) { return "(" . implode(', ', array_keys($item)) . ")"; }, $vals);

$query = "INSERT INTO dvr.dbo.dvrRecordedShows " . $insert_cols[0] . " values " . implode(", ", $insert_vals);

$query_vals = [];
foreach($vals as $k => $v) {
   $query_vals = array_merge($query_vals, $v);
}

// Run query //
try {
   $run = $DBH->prepare($query);
   $output = $run->execute($query_vals);
   echo json_encode($output);
} catch (Exception $e) {
   die("Error: " . $e->getMessage());
};
unset($DBH);
