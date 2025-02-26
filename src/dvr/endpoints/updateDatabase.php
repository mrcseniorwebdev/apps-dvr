<?php
require_once("databaseConnection.php");

$queryData = "";

if (!empty($_POST)) {
  if (!empty($_POST["dvdID"])) {
    $dvdID = $_POST["dvdID"];
    $queryData .= "DVDNumber='".$dvdID."', ";
  }
  if (!empty($_POST["showName"])) {
    $showName = $_POST["showName"];
    $queryData .= "LongProgram='".$showName."', ";
  }
  if (!empty($_POST["network"])) {
    $network = $_POST["network"];
    $queryData .= "Network='".$network."', ";
  }
  if (!empty($_POST["duration"])) {
    $duration = $_POST["duration"];
    $queryData .= "Duration='".$duration."', ";
  }
  if (!empty($_POST["comments"])) {
    $comments = $_POST["comments"];
    $queryData .= "Notes='".$comments."', ";
  }
  if (!empty($_POST["programTime"])) {
    $programTime = $_POST["programTime"];
    $queryData .= "ProgramTime='".$programTime."', ";
  }
  if (!empty($_POST["timePosition"])) {
    $timePosition = $_POST["timePosition"];
    $queryData .= "ShowTimePosition='".$timePosition."', ";
  }
  if (!empty($_POST["location"])) {
    $location = $_POST["location"];
    $queryData .= "Location='".$location."', ";
  }
  if (!empty($_POST["airDate"])) {
    $airDateVal = strtotime($_POST["airDate"]);
    if($airDateVal == false) {
      die("There is an invalid air date provided.");
    }
    $_POST["airDate"] = date("Ymd", $airDateVal);
    $airDate = $_POST["airDate"];
    $queryData .= "ShowDate='".$airDate."'";
  } else {
    die("Cannot complete action without show date.");
  }
  if (!empty($_POST["recordID"])) {
    $recordID = $_POST["recordID"];
  } else {
    die("Failed to get record ID.");
  }
}

// Build UPDATE //
$query = "UPDATE dvr.dbo.dvrRecordedShows SET ". $queryData ." WHERE ID='" . $recordID . "'";

// Run query //
try {
  $run = $DBH->prepare($query);
  $output = $run->execute();
  echo json_encode($output);
} catch (Exception $e) {
   die("Error: " . $e->getMessage());
};
unset($DBH);