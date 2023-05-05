<?php
// Setup variables
$output = "";
// DBASE info
$host = $_ENV['HOST'];
$port = $_ENV['PORT'];
$dbname = $_ENV['DBNAME'];
$user = $_ENV['USER'];
$pass = $_ENV['PASS'];

try {
  $DBH = new PDO("dblib:host=$host:$port;dbname=$dbname", $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

  // $DBH = new PDO("sqlsrv:server=$host,$port;Database=$dbname", $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
  //$DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
} catch (PDOException $e) {
  error_log("There was an error with PDO: " . $e->getMessage());
  die();
}
