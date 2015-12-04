<?php

//random initialization stuff
    require_once("include.php");
    global $mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase, $numTables;
    $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
    $vValue = 0.0;
    $threshold = 20;

//for each table
    class table
    {

        public $students = array(), $pairs = array();
        public $tablevalue = 0.0;

        public function evaluatePairs()
        {
            $tablecount = count($this->students);
            for($i = 0; $i < $tablecount; $i++)
            {
                for($j = $i + 1; $j < $tablecount; $j++)
                {
                    $one = $this->students[$i];
                    $two = $this->students[$j];
                    if($one > $two)
                    {
                        $tmp = $two;
                        $two = $one;
                        $one = $tmp;
                    }
                    array_push($this->pairs, array($one, $two));
                }
            }
        }

    }

    /* $query = "SELECT * FROM planList WHERE expiry > CURDATE()";
      $result = $db->query($query) or die($db->error);
      if($result->num_rows > 0)
      {
      while($row = $result->fetch_assoc())
      {
      $planID = $row["planID"];
      }
      } */
//else
    {
        do
        {
            //generate a new seating plan
            $query = "SELECT planID FROM planList ORDER BY planID DESC LIMIT 1";
            $result = $db->query($query) or die($db->error);
            if($result->num_rows == 0)
                $planID = 1;
            else
            {
                while($row = $result->fetch_assoc())
                {
                    $planID = $row["planID"] + 1;
                }
            }

            //planID in tow time to put everything into an array
            $tables = array();
            for($i = 0; $i < $numTables; $i++)
                array_push($tables, new table());
            $query = "SELECT * FROM studentList";
            $people = array();
            $result = $db->query($query) or die($db->error);
            $counter = 0;
            while($row = $result->fetch_assoc())
            {
                $people[$counter] = $row["studentID"];
                $counter++;
            }
            //WARNING - COOL AS FUCK PHP FUNCTION INCOMING
            shuffle($people); //does exactly what it says on the tin
            $counter = 0;
            foreach($people as $person)
            {
                array_push($tables[$counter]->students, $person);
                $counter++;
                $counter%=$numTables;
            }
            /* $query = "SELECT * FROM studentList WHERE gender=1"; //now the guys
              $people = array();
              $result = $db->query($query) or die($db->error);
              $counter = 0;
              while($row = $result->fetch_assoc())
              {
              $people[$counter] = $row["studentID"];
              $counter++;
              }
              //WARNING - COOL AS FUCK PHP FUNCTION INCOMING
              shuffle($people); //does exactly what it says on the tin
              $counter = $numTables-1;
              foreach($people as $person)
              {
              array_push($tables[$counter]->students,$person);
              $counter--;
              if($counter < 0) $counter = $numTables-1;
              } */

            //now we tabulate the table values
            for($i = 0; $i < $numTables; $i++)
            {
                $tables[$i]->evaluatePairs();
                //get the balance value
                foreach($tables[$i]->pairs as $pair)
                {
                    $one = $pair[0];
                    $two = $pair[1];
                    $query = "SELECT fIndex, n FROM pairsList WHERE studentID1=$one AND studentID2=$two";
                    $result = $db->query($query) or die($db->error);
                    $row = $result->fetch_assoc();
                    $tables[$i]->tablevalue += $row["fIndex"] + ($row["n"] * $row["n"] * $row["n"]);
                }
            }

            //find the difference values
            $sum = 0.0;
            for($i = 0; $i < $numTables; $i++)
            {
                for($j = $i + 1; $j < $numTables; $j++)
                {
                    $sum += abs($tables[$i]->tablevalue - $tables[$j]->tablevalue);
                }
            }
            $vValue = $sum / combination($numTables, 2);
            $threshold += ($planID * $planID * $planID) / 700000;
        }
        while($vValue > $threshold);

        //insert data into database
        $query = "INSERT INTO planList (expiry, vValue) VALUES (DATE_ADD(CURDATE(),INTERVAL 7 DAY),$vValue)"; //horrible code here, don't kill me pls
        $db->query($query) or die($db->error);
        $query = "INSERT INTO studentPlan (studentID, planID, tableNum) VALUES ";
        $havecomma = false;
        for($i = 0; $i < $numTables; $i++)
        {
            foreach($tables[$i]->students as $person)
            {
                if($havecomma)
                    $query .= ",";
                $query .= "($person, $planID, $i)";
                $havecomma = true;
            }
        }
        $db->query($query) or die($db->error);
        //update pairsList with new values
        foreach($tables as $table)
        {
            foreach($table->pairs as $pair)
            {
                $query = "UPDATE pairsList SET n=n+1.15 WHERE studentID1=".$pair[0]." AND studentID2=".$pair[1];
                $db->query($query) or die($db->error);
                $query = "UPDATE pairsList SET nodecay=nodecay+1 WHERE studentID1=".$pair[0]." AND studentID2=".$pair[1];
                $db->query($query) or die($db->error);
            }
        }
        $query = "UPDATE pairsList SET n=n-0.15";
        $db->query($query) or die($db->error);
    }
//now we have a seating arrangement, let's display it
    $tables = array();
    for($i = 0; $i < $numTables; $i++)
        array_push($tables, array());
    $query = "SELECT studentList.name, studentPlan.tableNum FROM studentPlan INNER JOIN studentList ON studentList.studentID = studentPlan.studentID WHERE studentPlan.planID = $planID";
    $result = $db->query($query) or die($db->error);
    while($row = $result->fetch_assoc())
    {
        array_push($tables[$row["tableNum"]], $row["name"]);
    }
    $outstr = "";
    for($i = 0; $i < $numTables; $i++)
    {
        $outstr .= "<div class='tableBox'>";
        foreach($tables[$i] as $person)
        {
            $outstr .= "<span>".$person."</span>";
        }
        $outstr .= "</div>";
    }
    $haveTable = true;
    echo $outstr;
    $db->close();

    function factorial($num)
    {
        if($num == 2)
            return $num;
        return $num * factorial($num - 1);
    }

    function combination($a, $b)
    {
        return (factorial($a) / (factorial($b) * factorial($a - $b)));
    }

?>