<?php

    require_once("include.php");
    global $mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase, $numTables;
    $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
    $query = "SELECT studentID1, studentID2 FROM pairsList ORDER BY pairID";
    $result = $db->query($query) or die($db->error);
    while($row = $result->fetch_assoc())
    {
        $query = "SELECT name FROM studentList WHERE studentID = ".$row["studentID1"];
        $result2 = $db->query($query) or die($db->error);
        $row2 = $result2->fetch_assoc();
        $query = "SELECT name FROM studentList WHERE studentID = ".$row["studentID2"];
        $result3 = $db->query($query) or die($db->error);
        $row3 = $result3->fetch_assoc();
        echo $row2["name"]." (".$row["studentID1"]."),".$row3["name"]." (".$row["studentID2"].")\n";
    }
    /*
      $pairs = array();
      for($i=1;$i<=25;$i++)
      {
      for($j=$i+1;$j<=25;$j++)
      {
      array_push($pairs,array($i,$j));
      }
      }
      $query = "INSERT INTO pairsList (studentID1,studentID2) VALUES ";
      $hascomma = false;
      foreach($pairs as $pair)
      {
      if($hascomma) $query .= ",";
      $query .= "(".$pair[0].",".$pair[1].")";
      $hascomma = true;
      }
      $db->query($query) or die($db->error); */
?>