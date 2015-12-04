<?php

    session_start();
    require_once("../include.php");
    if(!isIn() || !isTeacher())
    {
        header("Location: ../");
        exit();
    }
    if(!isset($_GET["deletekey"]) || !isset($_GET["type"]))
    {
        header("Location: addschedule.php");
        exit();
    }
    $id = intval($_GET["deletekey"]);
    $type = intval($_GET["type"]);
    if($id < -1)
    {
        header("Location: addschedule.php");
        exit();
    }
    global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
    $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
    //TODO: implement multiple tables changes
    switch($type)
    {
        case 1:
        case 4:
            $query = "TRUNCATE startendtime";
            $db->query($query) or die($db->error);
            header("Location: addschedule4.php");
            exit();
            break;
        case 2:
            if($id == -1)
            {
                $query = "TRUNCATE valuechanges";
                $db->query($query) or die($db->error);
                $query = "TRUNCATE news";
                $db->query($query) or die($db->error);
                header("Location: addschedule.php");
                exit();
                break;
            }
            else
            {
                $query = "DELETE FROM valuechanges WHERE changegroup = $id";
                $db->query($query) or die($db->error);
                header("Location: addschedule.php");
                exit();
                break;
            }
        case 3:
            $query = "DELETE FROM news WHERE newsid = $id";
            $db->query($query) or die($db->error);
            header("Location: addschedule.php");
            exit();
            break;
    }
?>
