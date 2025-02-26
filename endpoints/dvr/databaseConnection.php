<?php
// Setup variables
$output = "";
// DBASE info
$host = $_ENV['MSSQLHOST'];
$port = $_ENV['MSSQLPORT'];
$dbname = $_ENV['MSSQLDBNAME'];
$user = $_ENV['MSSQLUSER'];
$pass = $_ENV['MSSQLPASS'];
error_log(print_r("dblib:host=$host:$port;dbname=$dbname", true));
error_log(print_r($user, true));
error_log(print_r($pass, true));

try {
  $DBH = new PDO("dblib:version=8.0;charset=UTF-8;host=$host:$port;dbname=$dbname", $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
  // $DBH = new PDO("sqlsrv:Server=$host,$port;Database=$dbname", $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

  // $DBH = new PDO("sqlsrv:server=$host,$port;Database=$dbname", $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
  //$DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
} catch (PDOException $e) {
  error_log("There was an error with PDO: " . $e->getMessage());
  die();
}
