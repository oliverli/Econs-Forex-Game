<?php

    session_start();
    require_once("../include.php");
    if(!isIn())
        die();
    $gameEnded = gameEnded();
    global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
    $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
    purgeDatabase();
    $query = "SELECT shortname, name FROM currency WHERE currencyid = 1 LIMIT 1";
    $result = $db->query($query) or die($db->error);
    while($row = $result->fetch_assoc())
    {
        $basecurrabbv = $row["shortname"];
        $basecurrname = $row["name"];
    }
    $userkey = intval($_SESSION["userkey"]);
    if($userkey <= 0)
        return;
    $query = "SELECT name, networth FROM users WHERE userkey=$userkey LIMIT 1";
    $result = $db->query($query) or die();
    $totalvalue = 0;
    if($row = $result->fetch_assoc())
    {
        echo "<p>Hello, ".$row["name"].".";
        $totalvalue = $row["networth"];
    }
    if(!$gameEnded)
        echo " Your net worth is ".$basecurrabbv.number_format($totalvalue, 2).".</p>";
    else
    {
        echo " The game has ended.</p><p>";
        if($totalvalue == 10000000)
        {
            echo " You did not make or lose any money.</p>";
        }
        else if($totalvalue > 10000000)
        {
            echo " You made a profit of ".$basecurrabbv.number_format($totalvalue - 10000000, 2)."!</p>";
        }
        else
        {
            echo " You lost ".$basecurrabbv.number_format(0 - ($totalvalue - 10000000), 2).".</p>";
        }
    }
    $db->close();
?>
