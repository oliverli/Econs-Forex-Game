<?php
    session_start();
    require_once("../../include.php");
    if(!isIn())
    {
        header("Location: ../../");
        exit();
    }
    global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
    $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
    purgeDatabase();
    $gameEnded = gameEnded();
    $basecurrabbv = "";
    $query = "SELECT shortname FROM currency WHERE currencyid = 1 LIMIT 1";
    $result = $db->query($query) or die($db->error);
    while($row = $result->fetch_assoc())
    {
        $basecurrabbv = $row["shortname"];
    }
    $query = "SELECT name FROM users WHERE userkey=".$_SESSION["userkey"]." LIMIT 1";
    $result = $db->query($query) or die();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Forex Trading Simulator - Leaderboards</title>
        <link rel="stylesheet" type="text/css" href="../../css/main.css" />
        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
        <script>
            $(document).ready(function ()
            {
                $.ajaxSetup({cache: false});
                setInterval(function ()
                {
                    $("#news").load('../../ajax/newshandler.php');
<?php
    if(!isset($_GET["teachermode"]))
    {
        ?>$("#goLeft").load('../ajax/headerhandler.php');<?php } ?>
                }, 60000);
            });
        </script>
    </head>
    <body>
        <div class="page-module">
            <?php
                if(!isset($_GET["teachermode"]))
                {
                    ?>
                    <div style="width:100%;">
                        <div id="goRight"><p><a href="../">Home</a> <a href="../history/">History</a> <a href="../leaderboard/">Leaderboards</a> <?php if(isTeacher()) echo "<a href=\"../../admin/\">Admin Console</a>"; ?> <a href="../changepassword/">Change Password</a> <a href="../logout/">Logout</a></p></div>
                        <div id="goLeft">
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
                        </div>
                    </div>
                <?php } ?>
            <div style="clear:both"></div>
            <h1<?php if(isset($_GET["teachermode"])) echo " style='position:relative; top:10px;'"; ?>>Leaderboards</h1>
            <div id="leaderboard">
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
            </div>
        </div>
    </body>
</html>
