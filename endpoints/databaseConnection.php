<?php
// Setup variables
$output = "";
// DBASE info
$host = $_ENV['HOST'];
$port = $_ENV['PORT'];
$dbname = $_ENV['DBNAME'];
$user = $_ENV['USER'];
$pass = $_ENV['PASS'];
// $host = "dvr-mssql2.cy9fa96f4hrh.us-east-1.rds.amazonaws.com";
// $port = 1433;
// $dbname = "dvr";
// $user = "jyeager";
// $pass = "Laminar22";

try {
  $DBH = new PDO("dblib:host=$host:$port;dbname=$dbname", $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

  // $DBH = new PDO("sqlsrv:server=$host,$port;Database=$dbname", $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
  //$DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
} catch (PDOException $e) {
  error_log("There was an error with PDO: " . $e->getMessage());
  die();
}
