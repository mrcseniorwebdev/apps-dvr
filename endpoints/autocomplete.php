<?php
   try {
            
      if (empty($_GET['col'])) {
         return;
      }
      
      require_once("databaseConnection.php");

      $col = $_GET['col'];
      $vals['query'] = '%' . (!empty($_GET['q']) ? $_GET['q'] : '') . '%';
      $list = [];
      //printf("select %s from dvr.dbo.dvrRecordedShows where %s like '%%:query%%' group by %s", $_GET['col'], $_GET['col'], $_GET['col']);
      $stmt = $DBH->prepare(sprintf("select %s from dvr.dbo.dvrRecordedShows where %s like :query group by %s", $col, $col, $col));
      $stmt->execute($vals);
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
      $ret = [];
      foreach ($results as $result) {
         $ret[] = [
            'id' => $result[$col],
            'value' => $result[$col],
         ];
      }
      
      //$ret = array_map(function($item) use($col) { return $item[$col]; }, $results);
      print json_encode($ret);
      
   } catch (Exception $e) {
      print $e->getMessage();
   }

   die();