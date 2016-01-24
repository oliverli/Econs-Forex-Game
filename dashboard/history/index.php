<?php
    session_start();
    require_once("../../include.php");
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
    $query = "SELECT shortname, name FROM currency WHERE currencyid = 1 LIMIT 1";
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
        <title>Forex Trading Simulator - History</title>
        <link rel="stylesheet" type="text/css" href="../../css/main.css" />
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/js/materialize.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/css/materialize.min.css" media="screen,projection" />
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
        <?php
            if(isset($remarks))
                echo "<script>".$remarks."</script>";
        ?>
    </head>

    <body>
        <div class="page-module">
            <div style="width:100%;">
                <div id="goRight"><p><a href="../">Home</a> <a href="./">History</a> <a href="../leaderboard/">Leaderboards</a> <?php if(isTeacher()) echo "<a href=\"../../admin/\">Admin Console</a>"; ?> <a href="../changepassword/">Change Password</a> <a href="../logout/">Logout</a></p></div>
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
            <div style="clear:both"></div>
            <div style="width: 100%;" align="center">
                <?php
                    $query = "SELECT name, currencyid FROM currency WHERE currencyid != 1 ORDER BY name ASC";
                    $curResults = $db->query($query);
                    $outTmp = "";
                    while($currencyRow = $curResults->fetch_assoc())
                    {
                        $currid = $currencyRow["currencyid"];
                        $data = array();
                        $labels = array();
                        $query = "SELECT newbuyvalue, newsellvalue, time FROM valuechanges WHERE currencyid=$currid AND yetcompleted=0 ORDER BY time ASC";
                        $result = $db->query($query) or die($query.$db->error);
                        if($result->num_rows > 0)
                        {
                            while($row = $result->fetch_assoc())
                            {
                                $avg = ($row["newbuyvalue"] + $row["newsellvalue"]) / 2;
                                array_push($data, $avg);
                                array_push($labels, date("D H:i:s", $row["time"]));
                            }
                        }
                        if(count($data) == 1)
                        {
                            array_push($data, $data[0]);
                            array_push($labels, $labels[0]);
                        }
                        echo "<h2>".$currencyRow["name"]." History</h2><canvas id=\"chart$currid\" width=\"950\" height=\"200\"></canvas>";
                        $outTmp .= "var ctx = $(\"#chart$currid\").get(0).getContext(\"2d\");\n";
                        $outTmp .= "var data = {labels: [";
                        for($i = 0; $i < count($labels) - 1; $i++)
                        {
                            $outTmp .= "\"".$labels[$i]."\", ";
                        }
                        if(count($labels) == 1)
                            $outTmp .= "\"".$labels[0]."\", ";
                        $outTmp .= "\"".$labels[count($labels) - 1]."\"], datasets: [{fillColor: \"rgba(204,0,0,0.2)\",strokeColor: \"rgba(204,0,0,1)\",pointColor: \"rgba(204,0,0,0.65)\",pointStrokeColor: \"#fff\",pointHighlightFill: \"rgba(210,0,0,1)\",pointHighlightStroke: \"#fff\",data: [";
                        for($i = 0; $i < count($data) - 1; $i++)
                        {
                            $outTmp .= $data[$i].", ";
                        }
                        if(count($data) == 1)
                            $outTmp .= "\"".$data[0]."\", ";
                        $outTmp .= $data[count($data) - 1];
                        $outTmp .= "]}]}; new Chart(ctx).LineAlt(data, optionss);";
                    }
                ?>
                <script>
                    //code below modified from https://stackoverflow.com/questions/31604040/show-label-in-tooltip-but-not-in-x-axis-for-chartjs-line-chart
                    Chart.types.Line.extend({
                        name: "LineAlt",
                        initialize: function (data)
                        {
                            Chart.types.Line.prototype.initialize.apply(this, arguments);
                            var xLabels = this.scale.xLabels;
                            var skipFrequency = xLabels.length / 10;
                            var skipCount = 0;
                            xLabels.forEach(function (label, i)
                            {
                                if(skipCount > 1)
                                {
                                    xLabels[i] = '';
                                    skipCount--;
                                }
                                else
                                {
                                    skipCount = skipFrequency;
                                }
                            })
                        }
                    });
                    var optionss = {
                        scaleShowGridLines: true,
                        scaleGridLineColor: "rgba(0,0,0,.05)",
                        scaleGridLineWidth: 1,
                        scaleShowHorizontalLines: true,
                        scaleShowVerticalLines: true,
                        bezierCurve: true,
                        bezierCurveTension: 0.4,
                        pointDot: true,
                        pointDotRadius: 4,
                        pointDotStrokeWidth: 1,
                        pointHitDetectionRadius: 2,
                        datasetStroke: true,
                        datasetStrokeWidth: 2,
                        datasetFill: true,
                        showXLabels: 10
                    };
<?php echo $outTmp ?>
                </script>
                <div id="news" style="position:relative; width:34.5%;float:right;">
                    <h2>News History</h2>
                    <table style="width:100%">
                        <tr class="red">
                            <th colspan="2" class="red" >BBT News Archive</th>
                        </tr>
                        <?php
                            $query = "SELECT newstext, time FROM news WHERE time <= ".time()." ORDER BY time DESC";
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
            <div style="float:left;width:63.5%;">
                <h2>Transaction History</h2>
                <table style="width:100%">
                    <tr class="red">
                        <th class="red">No.</th>
                        <th class="red">Time</th>
                        <th class="red">Transaction Type</th>
                        <th class="red">Rate</th>
                        <th class="red">Amount Sold</th>
                        <th class="red">Amount Received</th>
                    </tr>
                    <?php
                        $query = "SELECT transactions.transtype, currency.shortname, transactions.amount, transactions.rate, transactions.receiveamt, transactions.time FROM transactions INNER JOIN currency ON currency.currencyid = transactions.currencyid WHERE transactions.userkey = $userkey ORDER BY time DESC";
                        $result = $db->query($query) or die($db->error);
                        $count = 1;
                        if($result->num_rows <= 0)
                        {
                            echo "<td class=\"table-bordered\" colspan=\"6\">No previous transactions found.</td>";
                        }
                        else
                        {
                            while($row = $result->fetch_assoc())
                            {
                                echo "<tr>";
                                if($row["transtype"] == 0)
                                {
                                    $newcurrname = $row["shortname"];
                                    echo "<td class=\"table-bordered\">$count</td>";
                                    echo "<td class=\"table-bordered\">".nicetime($row["time"])."</td>";
                                    echo "<td class=\"table-bordered\">$basecurrabbv to $newcurrname (Buy)</td>";
                                    echo "<td class=\"table-bordered\">".$row["rate"]."</td>";
                                    echo "<td class=\"table-bordered\">$basecurrabbv".number_format($row["amount"])."</td>";
                                    echo "<td class=\"table-bordered\">$newcurrname".number_format($row["receiveamt"])."</td>";
                                }
                                else
                                {
                                    $newcurrname = $row["shortname"];
                                    echo "<td class=\"table-bordered\">$count</td>";
                                    echo "<td class=\"table-bordered\">".nicetime($row["time"])."</td>";
                                    echo "<td class=\"table-bordered\">$newcurrname to $basecurrabbv (Sell)</td>";
                                    echo "<td class=\"table-bordered\">".$row["rate"]."</td>";
                                    echo "<td class=\"table-bordered\">$newcurrname".number_format($row["amount"])."</td>";
                                    echo "<td class=\"table-bordered\">$basecurrabbv".number_format($row["receiveamt"])."</td>";
                                }
                                $count++;
                                echo "</tr>";
                            }
                        }
                    ?>
                </table>
            </div>
            <div style="clear:both"></div>
        </div>
    </body>
</html>
