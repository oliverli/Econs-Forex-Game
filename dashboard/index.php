<?php
    session_start();
    require_once("../include.php");
    if(!isIn())
    {
        header("Location: ../");
        exit();
    }
    date_default_timezone_set('Asia/Singapore');
    purgeDatabase();
    $gameEnded = gameEnded();
    global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
    $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
    $basecurrabbv = "";
    $query = "SELECT shortname, name FROM currency WHERE currencyid = 1";
    $result = $db->query($query) or die($db->error);
    while($row = $result->fetch_assoc())
    {
        $basecurrabbv = $row["shortname"];
        $basecurrname = $row["name"];
    }
    if(isset($_SESSION["remarks"]))
    {
        $remarks = $_SESSION["remarks"];
        unset($_SESSION["remarks"]);
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Forex Trading Simulator - Dashboard</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="../css/main.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/js/materialize.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/css/materialize.min.css" media="screen,projection" />
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
        <link href="../css/main.css" rel="stylesheet" />
        <script>
            $(document).ready(function ()
            {
                $.ajaxSetup({cache: false});
                setInterval(function ()
                {
                    $("#board").load('../ajax/boardhandler.php');
                    $("#news").load('../ajax/newshandler.php');
                    $("#goLeft").load('../ajax/headerhandler.php');
                }, 30000);
            });
        </script>
        <?php
            if(isset($remarks))
                echo $remarks;
        ?>
    </head>
    <body class="indigo lighten-5">
        <nav>
            <div id="nav-wrapper" class="indigo row">
                <div class="col left">Forex Trading Simulator</div>
                <ul id="nav-mobile" class="col right hide-on-small-and-down">
                    <li><a href="./">Home</a></li> 
                    <li><a href="./history/">History</a></li>
                    <li><a href="./leaderboard/">Leaderboards</a></li> <?php if(isTeacher()) echo "<li><a href=\"../admin/\">Admin Console</a></li>"; ?>
                    <li><a href="./changepassword/">Change Password</a></li>
                    <li><a href="./logout/">Logout</a></li>
                </ul>
            </div>
            <!--<div id="goLeft">
                <?php
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
                ?>
            </div>-->
        </nav>
        <div class="container">
            <div id="board" style="">
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
            </div>
            <div id="news" style="position:relative; top:10px;">
                <table style="width:100%">
                    <tr class="red">
                        <th colspan="2" class="red">BBT News Headlines</th>
                    </tr>
                    <?php
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
                    ?>
                </table>
            </div>
        </div>
    </body>
</html>
