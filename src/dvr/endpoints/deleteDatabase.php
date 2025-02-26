<?php
require_once("databaseConnection.php");

if (!empty($_POST)) {
  $id = $_POST["recordID"];
} else {
  die("Failed to read record ID.");
}

$query = "DELETE FROM dvr.dbo.dvrRecordedShows WHERE ID = :id;";

$run = $DBH->prepare($query);
$run->bindValue(':id',$id, PDO::PARAM_STR);
try {
  $output = $run->execute();
} catch (Exception $e) {
  die("Database connection failed: ".$e);
}

echo json_encode($output);