<?php
    session_start();
    require_once("../include.php");
    if(!isIn())
        die();
    global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
    $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
    purgeDatabase();
    $userkey = intval($_SESSION["userkey"]);
    if($userkey <= 0)
        die();
    $query = "SELECT shortname, name FROM currency WHERE currencyid = 1 LIMIT 1";
    $result = $db->query($query) or die($db->error);
    while($row = $result->fetch_assoc())
    {
        $basecurrabbv = $row["shortname"];
        $basecurrname = $row["name"];
    }
?>
<table style="width:100%">
    <tr class="red">
        <th class="red">Currency</th>
        <th class="red">USD Selling Value</th>
        <th class="red">USD Buying Value</th>
        <th class="red">Amount Owned</th>
        <th class="red">Buy/Sell</th>
    </tr>
    <?php
        $query = "SELECT amount FROM wallet WHERE currencyid=1 AND userkey = $userkey LIMIT 1";
        $result = $db->query($query) or die($db->error);
        while($row = $result->fetch_assoc())
        {
            echo "<tr>";
            echo "<td class=\"table-bordered\">$basecurrname ($basecurrabbv)</td>";
            echo "<td class=\"table-bordered\">N.A.</td>";
            echo "<td class=\"table-bordered\">N.A.</td>";
            echo "<td class=\"table-bordered\">".number_format($row["amount"], 2)."</td>";
            echo "<td class=\"table-bordered\">N.A.</td>";
            echo "</tr>";
            break;
        }
        $query = "SELECT currency.name, currency.shortname, currency.sellvalue, currency.buyvalue, currency.currencyid, wallet.amount FROM currency INNER JOIN wallet ON currency.currencyid = wallet.currencyid WHERE currency.currencyid != 1 AND wallet.userkey = $userkey ORDER BY currency.name ASC";
        $result = $db->query($query) or die($db->error);
        while($row = $result->fetch_assoc())
        {
            echo "<tr>";
            echo "<td class=\"table-bordered\">".$row["name"]." (";
            echo $row["shortname"].")</td>";
            echo "<td class=\"table-bordered\">".$row["buyvalue"]."</td>";
            echo "<td class=\"table-bordered\">".$row["sellvalue"]."</td>";
            echo "<td class=\"table-bordered\">".number_format($row["amount"], 2)."</td>";
            echo "<td class=\"table-bordered\"><a href='./buysell/?currid=".$row["currencyid"]."' target=\"_top\" data-ftrans=\"slide\" id=\"buysell\">Buy/Sell</a></td>";
            echo "</tr>";
        }
    ?>
</table>