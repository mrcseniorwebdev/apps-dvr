<?php
require_once("databaseConnection.php");

if (isset($_POST)) {
  $list = [];
  $query = "SELECT DISTINCT " . $_POST["field"] . " FROM dvr.dbo.dvrRecordedShows WHERE " . $_POST["field"] . " LIKE '%". $_POST["request"]. "%'";

  foreach ($DBH->query($query) as $row) {
    $list[] = $row[0];
  };
} else {
  die("ERROR!  POST data not set.");
}

unset($DBH);
echo json_encode($list);