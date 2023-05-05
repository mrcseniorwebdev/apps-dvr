<?php
require_once("databaseConnection.php");

// Build Data for query //
foreach ($DBH->query("SELECT TOP 1 ID FROM dvr.dbo.dvrRecordedShows ORDER BY ID DESC;") as $row) {
  //Get last ID and add one to it
  $ID = $row[0] + 1;
}

$query = "SELECT DVDNumber, LongProgram, Network, ProgramTime, Duration, ShowDate, ShowTimePosition, Location, Notes FROM dvr.dbo.dvrRecordedShows WHERE ID = '" . $_POST["recordID"] . "'";

foreach ($DBH->query($query) as $row) {
  $row["ShowDate"] = str_replace("12:00:00:000AM", "", $row["ShowDate"]);
  $row["ShowDate"] = date("m/d/Y", strtotime($row["ShowDate"]));
  $data[] = array(
    $row["DVDNumber"],
    $row["LongProgram"],
    $row["Network"],
    $row["ProgramTime"],
    $row["Duration"],
    $row["ShowDate"],
    $row["ShowTimePosition"],
    $row["Location"],
    $row["Notes"]);
}

echo json_encode($data);