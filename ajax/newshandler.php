<?php

    session_start();
    require_once("../include.php");
    if(!isIn())
        die();
    global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
    $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
    date_default_timezone_set('Asia/Singapore');
    echo "<table style=\"width:100%\"><tr class=\"red\"><th colspan=\"2\" class=\"red\">BBT News Headlines</th></tr>";
    $query = "SELECT newstext, time FROM news WHERE time <= ".time()." ORDER BY time DESC LIMIT 30";
    $result = $db->query($query) or die($db->error);
    if($result->num_rows <= 0)
        echo "<tr><td colspan=\"2\" class=\"table-bordered\">There are no news reports at the moment.</td></tr>";
    while($row = $result->fetch_assoc())
    {
        echo "<tr>";
        echo "<td class=\"table-bordered\">".$row["newstext"]."</td>";
        echo "<td class=\"table-bordered\" style=\"width:25%\">".nicetime($row["time"])."</td>";
        echo "</tr>";
    }
    echo "</table>";
?>
