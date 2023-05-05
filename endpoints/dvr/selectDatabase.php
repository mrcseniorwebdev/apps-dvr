<?php

error_log("This is a log message from the PHP script.");


//   error_reporting(E_ALL);
//ini_set('display_errors', '1');


ini_set('memory_limit', '512M');

require_once("databaseConnection.php");

$error = null;

// Casting Variables //
$search = [];
if (!empty($_REQUEST)) {
  $draw = $_REQUEST["draw"];
  $start = $_REQUEST["start"];
  $length = $_REQUEST["length"];
  $filterID = $_REQUEST["custom"]["filterID"];
  if (!empty($_REQUEST["custom"]["search"])) {
    foreach ($_REQUEST["custom"]["search"] as $key => $value) {
      $search[$key] = $value;
    }
  }
  foreach ($_REQUEST["order"] as $index => $arr) {
    foreach ($arr as $key => $value) {
      $order[$index][$key] = $value;
    }
  }
  foreach ($_REQUEST["columns"] as $index => $arr) {
    foreach ($arr as $key => $value) {
      $columns[$index][$key] = $value;
    }
  }
} else {
  $error .= "<li>There was a problem.  The POST data was not set.</li>";
}
// Base SELECT //
$query = "select ID, DVDNumber, LongProgram, Network, ProgramTime, Duration, ShowDate, Notes, ShowTimePosition, Location from dvr.dbo.dvrRecordedShows";

$sWhere = " where 1=1";
$vals = [];

// Search //
/*if (!empty($search["airDate"])) {
  $search["airDate"] = date("Ymd", strtotime($search["airDate"]));
  $where .= " WHERE ShowDate = '" . $search["airDate"] . "'";
} else {
  $error .= "<li>Air Date search parameter is required.</li>";
}*/

//$sWhere .= " AND ShowDate >= '1/1/2005'";


if (!empty($search["airDate"])) {
  $vals['airdate'] = date("Y-m-d", strtotime($search["airDate"]));
  $sWhere .= " AND ShowDate = :airdate";
}

if (!empty($search["showName"])) {
  $vals['showname'] = '%' . str_replace('*', '%', $search["showName"]) . '%';
  $sWhere .= " AND LongProgram LIKE :showname";
}

if (!empty($search["network"])) {
  $vals['network'] = '%' . str_replace('*', '%', $search["network"]) . '%';
  $sWhere .= " AND Network LIKE :network";
}

if (!empty($search["DVDID"])) {
  $vals['dvdid'] = str_replace('*', '%', $search["DVDID"]);
  $sWhere .= " AND DVDNumber like :dvdid";
}

if (isset($filterID) && $filterID === "false") {
  $sWhere .= " AND DVDNumber != ''";
}

$query .= $sWhere;

$orderby = !empty($order[0]) ? (int)$order[0]["column"] : "";

// Ordering /
switch ($orderby) {
  case 0:
    $sort = "DVDNumber";
    break;
  case 1:
    $sort = "LongProgram ";
    break;
  case 2:
    $sort = "Network ";
    break;
  case 3:
    $sort = "ShowDate ";
    break;
  case 5:
    $sort = "";
    break;
  default:
    $sort = "";
}

if (!empty($sort)) {
  $query .= " ORDER BY " . $sort . " " . (!empty($order[0]["dir"]) ? strtoupper($order[0]["dir"]) : "asc") . ($orderby !== 0 ? ", DVDNumber asc" : "");
}

// LIMIT //
if (!empty($length)) {
  $query .= " OFFSET " . $start . " ROWS FETCH NEXT " . $length . " ROWS ONLY";
}

$stmt = $DBH->prepare($query);
$stmt->execute($vals);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Actual query and sorting into $data variable
$data = [];
foreach ($results as $row) {
  // Data Manipulation //
  $row["ShowDate"] = strtotime($row["ShowDate"]);
  $timestamp = strtotime(date('m/d/Y', $row["ShowDate"]) . ' ' . $row["ProgramTime"]);
  $row["ShowDate"] = date("m/d/Y - h:i A", $timestamp);
  $row["Duration"] = $row["Duration"] . " mins";
  $edit = '<a class="editButton" data-id="' . $row["ID"] . '" data-toggle="modal" data-target="#edit-record">Edit</a>';
  $delete = '<a class="deleteButton" data-id="' . $row["ID"] . '" data-toggle="modal" data-target="#delete-record">Delete</a>';
  if (!empty($row["Notes"])) {
    $notes = '<a class="recordComments" tabindex="0" role="button" data-container="body" data-toggle="popover" data-trigger="focus" data-content="' . $row["Notes"] . '" data-placement="left" rel="tooltip"><i class="fa fa-question-circle"></i></a>';
  } else {
    $notes = "";
  }

  // Set Data //
  $data[] = array(
    $row["DVDNumber"],
    $row["LongProgram"],
    $row["Network"],
    $row["ShowDate"],
    $row["Duration"],
    $row["ShowTimePosition"],
    $row["Location"],
    $edit . ' / ' . $delete . ' ' . $notes
  );
}

// Get Row Count //
$stmt = $DBH->prepare("SELECT COUNT(*) FROM dvr.dbo.dvrRecordedShows WITH (NOLOCK)");
$stmt->execute();
$recordCount = $stmt->fetchColumn();


$stmt = $DBH->prepare("SELECT COUNT(*) FROM dvr.dbo.dvrRecordedShows WITH (NOLOCK)" . $sWhere);
$stmt->execute($vals);
$filteredCount = $stmt->fetchColumn();

// Error creation //
if (!empty($error)) {
  $errors = "<ul>";
  $errors .= $error;
  $errors .= "</ul>";
}

// Output from database //
$output = [];
$output["draw"] = (int)$draw;
$output["recordsTotal"] = $recordCount;
$output["recordsFiltered"] = $filteredCount;
$output["data"] = $data;
if (!empty($errors)) {
  $output["error"] = $errors;
}
unset($DBH);
echo json_encode($output);
