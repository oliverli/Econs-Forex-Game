<?php

    session_start();
    require_once("../include.php");
    global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
    if(!isIn() || !isTeacher())
    {
        header("Location: ../");
        exit();
    }
    if(!isset($_POST["numcurr"]) || !isset($_POST["name0"]))
    {
        header("Location: addcurrencies2.php");
        exit();
    }
    $numcurr = intval($_POST["numcurr"]);
    $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
    $query = "INSERT INTO currency (name, shortname, buyvalue, sellvalue) VALUES ('".$_POST["name0"]."','".$_POST["shortname0"]."', ".$_POST["buyvalue0"].", ".$_POST["sellvalue0"].")";
    for($i = 1; $i < $numcurr; $i++)
    {
        $query .= ",('".$_POST["name".$i]."','".$_POST["shortname".$i]."', ".$_POST["buyvalue".$i].", ".$_POST["sellvalue".$i].")";
    }
    if($db->query($query) === TRUE)
    {
        $query = "SELECT starttime FROM startendtime WHERE timeid=1 LIMIT 1";
        $result = $db->query($query);
        $row = $result->fetch_assoc();
        $starttime = $row["starttime"];
        $query = "SELECT currencyid FROM currency WHERE shortname=".$_POST["shortname0"]." LIMIT 1";
        $result = $db->query($query);
        $row = $result->fetch_assoc();
        $query = "INSERT INTO valuechanges (currencyid, newbuyvalue, newsellvalue, time, changegroup, yetcompleted) VALUES (".$row["currencyid"].", ".$_POST["buyvalue0"].", ".$_POST["sellvalue0"].", $starttime, 0, 0)";
        if($db->query($query) === TRUE)
        {
            header("index.php");
            exit();
        }
        else
        {
            echo "<script>alert(\"Currency addition failed. If problem persists, take a screenshot and contact Yicheng.\");</script>";
            die($db->error.$query);
        }
    }
    else
    {
        echo "<script>alert(\"Currency addition failed. If problem persists, take a screenshot and contact Yicheng.\");</script>";
        die($db->error.$query);
    }
?>