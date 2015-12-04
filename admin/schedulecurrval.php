<?php
    session_start();
    require_once("../include.php");
    if(!isIn() || !isTeacher())
    {
        header("Location: ../");
        exit();
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Forex Trading Simulator - View Currency Change Values</title>
        <style>
            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
            }
            th, td {
                padding: 5px;
                text-align:center;
            }
            p
            {
                text-align:center;
            }
        </style>
        <?php
            global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
            $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
            if(!isset($_GET["curr"]))
            {
                die("<script>window.close()</script>");
            }
            $curr = $db->escape_string($_GET["curr"]);
            $query = "SELECT currency.shortname, valuechanges.newbuyvalue, valuechanges.newsellvalue, currency.name FROM valuechanges INNER JOIN currency ON currency.currencyid=valuechanges.currencyid WHERE valuechanges.changegroup=$curr ORDER BY currency.name ASC";
            $result = $db->query($query) or die($db->error);
        ?>
    </head>

    <body>
        <table style="width:100%">
            <tr>
                <th>Currency</th>
                <th>New Buying Value</th>
                <th>New Selling Value</th>
            </tr>
            <?php
                while($row = $result->fetch_assoc())
                {
                    echo "<tr>";
                    echo "<td>".$row["name"]." (";
                    echo $row["shortname"].")</td>";
                    echo "<td>".$row["newbuyvalue"]."</td>";
                    echo "<td>".$row["newsellvalue"]."</td>";
                    echo "</tr>";
                }
            ?>
        </table>
        <p><a href="addschedule.php" onclick="return window.close()">Close Window</a></p>
    </body>
</html>
