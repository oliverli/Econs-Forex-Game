<?php
    session_start();
    require_once("../include.php");
    if(!isIn())
        die();
    $gameEnded = gameEnded();
    global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
    $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
    $basecurrabbv = "";
    $query = "SELECT shortname FROM currency WHERE currencyid = 1 LIMIT 1";
    $result = $db->query($query) or die($db->error);
    while($row = $result->fetch_assoc())
    {
        $basecurrabbv = $row["shortname"];
    }
?>
<table style="width:100%">
    <tr class="red">
        <th class="red">No.</th>
        <th class="red">Name</th>
        <th class="red"><?php
                if($gameEnded)
                    echo "Net Profit";
                else
                    echo "Net Worth";
            ?> (<?php echo $basecurrabbv ?>)</th>
    </tr>
    <?php
        $query = "SELECT name, networth FROM users WHERE usertype=1 ORDER BY networth DESC";
        $result = $db->query($query) or die($db->error);
        $number = 1;
        $tietally = 0;
        $previousNetWorth = -PHP_INT_MAX;
        while($row = $result->fetch_assoc())
        {
            echo "<tr>";
            if($row["networth"] == $previousNetWorth)
            {
                $number--;
                echo "<td class=\"table-bordered\">".$number."</td>";
                $tietally++;
                echo "<td class=\"table-bordered\">".$row["name"]."</td>";
                if($gameEnded)
                    echo "<td class=\"table-bordered\">".number_format($row["networth"] - 10000000, 2)."</td>";
                else
                    echo "<td class=\"table-bordered\">".number_format($row["networth"], 2)."</td>";
            }
            else if($tietally != 0)
            {
                $number += $tietally;
                echo "<td class=\"table-bordered\">".$number."</td>";
                $tietally = 0;
                echo "<td class=\"table-bordered\">".$row["name"]."</td>";
                if($gameEnded)
                    echo "<td class=\"table-bordered\">".number_format($row["networth"] - 10000000, 2)."</td>";
                else
                    echo "<td class=\"table-bordered\">".number_format($row["networth"], 2)."</td>";
            }
            else
            {
                echo "<td class=\"table-bordered\">".$number."</td>";
                echo "<td class=\"table-bordered\">".$row["name"]."</td>";
                if($gameEnded)
                    echo "<td class=\"table-bordered\">".number_format($row["networth"] - 10000000, 2)."</td>";
                else
                    echo "<td class=\"table-bordered\">".number_format($row["networth"], 2)."</td>";
            }
            $number++;
            $previousNetWorth = $row["networth"];
            echo "</tr>";
        }
    ?>
</table>
