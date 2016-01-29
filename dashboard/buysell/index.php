<?php 
    
    /*
     * The MIT License
     *
     * Copyright 2016 Li Yicheng, Walter Kong, and Sun Yudong.
     *
     * Permission is hereby granted, free of charge, to any person obtaining a copy
     * of this software and associated documentation files (the "Software"), to deal
     * in the Software without restriction, including without limitation the rights
     * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
     * copies of the Software, and to permit persons to whom the Software is
     * furnished to do so, subject to the following conditions:
     *
     * The above copyright notice and this permission notice shall be included in
     * all copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
     * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
     * THE SOFTWARE.
     */

    set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__)."/../../classes/");
    require_once("pages/BuySell.php");

    $worker = new BuySell();
    
    /*
    session_start();
    require_once("../../include.php");
    if(!isIn())
    {
        header("Location: ../../");
        exit();
    }
    date_default_timezone_set('Asia/Singapore');
    purgeDatabase();
    $gameEnded = gameEnded();
    global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
    $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
    $basecurrabbv = $basecurrname = "";
    $userkey = $_SESSION["userkey"];
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
    if(isset($_GET["currid"]))
    {
        $currid = intval($_GET["currid"]);
        if($currid <= 1)
        {
            header("Location: ../");
            exit();
        }
        global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
        $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
        $query = "SELECT name, shortname, buyvalue, sellvalue FROM currency WHERE currencyid = $currid LIMIT 1";
        $result = $db->query($query) or die();
        if($result->num_rows <= 0)
        {
            header("Location: ../");
            exit();
        }
        $row = $result->fetch_assoc();
        $currname = $row["name"];
        $currsname = $row["shortname"];
        $buyvalue = $row["buyvalue"];
        $sellvalue = $row["sellvalue"];
        $query = "SELECT amount, currencyid FROM wallet WHERE userkey = $userkey AND (currencyid=$currid OR currencyid=1) LIMIT 2";
        $result = $db->query($query) or die();
        $basecurramt = $seccurramt = 0;
        if($result->num_rows <= 0)
        {
            header("Location: ../");
            exit();
        }
        while($row = $result->fetch_assoc())
        {
            if($row["currencyid"] == 1)
                $basecurramt = $row["amount"];
            else
                $seccurramt = $row["amount"];
        }
    }
    if(isset($_POST["buyamt"]) && isset($_POST["buybtn"]) && isset($_POST["currid"]) && !$gameEnded)
    {
        $buyamt = round(floatval($_POST["buyamt"] * 1000000), 2);
        if($buyamt <= 0)
        {
            //naughty naughty
            header("Location: ../");
            exit();
        }
        $currid = intval($_POST["currid"]);
        if($currid <= 1)
        {
            header("Location: ../");
            exit();
        }
        global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
        $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
        $query = "SELECT amount FROM wallet WHERE currencyid=1 AND userkey=$userkey LIMIT 1";
        $result = $db->query($query) or die($db->error);
        $row = $result->fetch_assoc();
        if($buyamt > $row["amount"])
            $buyamt = $row["amount"];
        //die($buyamt);
        $query = "SELECT buyvalue, shortname FROM currency WHERE currencyid=$currid LIMIT 1";
        $result = $db->query($query) or die();
        if($result->num_rows <= 0)
        {
            header("Location: ../");
            exit();
        }
        $row = $result->fetch_assoc();
        $buyvalue = $row["buyvalue"];
        $shortname = $row["shortname"];
        $newamt = round($buyamt * $buyvalue, 2);
        $query = "START TRANSACTION;";
        $db->query($query) or die($db->error);
        $query = "UPDATE wallet SET amount=amount+$newamt WHERE currencyid=$currid AND userkey=$userkey;";
        $db->query($query) or die($db->error);
        $query = "UPDATE wallet SET amount=amount-$buyamt WHERE currencyid=1 AND userkey=$userkey;";
        $db->query($query) or die($db->error);
        //transtype: 0 for buy (USD to JPY), 1 for sell (JPY to USD)
        $query = "INSERT INTO transactions (transtype, userkey, currencyid, amount, rate, receiveamt, time)  VALUES (0, $userkey, $currid, $buyamt, $buyvalue, $newamt, ".time().");";
        $db->query($query) or die($db->error);
        $query = "COMMIT;";
        $db->query($query) or die($db->error);
        calculateWorth($userkey);
        header("Location: ../");
        exit();
    }
    else if(isset($_POST["sellamt"]) && isset($_POST["sellbtn"]) && isset($_POST["currid"]) && !$gameEnded)
    {
        $sellamt = round(floatval($_POST["sellamt"] * 1000000), 2);
        if($sellamt <= 0)
        {
            //naughty naughty
            header("Location: ../");
            exit();
        }
        $currid = intval($_POST["currid"]);
        if($currid <= 1)
        {
            header("Location: ../");
            exit();
        }
        global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
        $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
        //die($buyamt);
        $query = "SELECT sellvalue, shortname FROM currency WHERE currencyid = $currid LIMIT 1";
        $result = $db->query($query) or die($db->error);
        if($result->num_rows <= 0)
        {
            header("Location: ../");
            exit();
        }
        $row = $result->fetch_assoc();
        $sellvalue = $row["sellvalue"];
        $shortname = $row["shortname"];
        $newamt = round($sellamt * $sellvalue, 2);
        $query = "SELECT amount FROM wallet WHERE currencyid=$currid AND userkey=$userkey LIMIT 1";
        $result = $db->query($query) or die($db->error);
        $row = $result->fetch_assoc();
        if($newamt > $row["amount"])
        {
            $newamt = $row["amount"];
            $sellamt = round($newamt / $sellvalue, 2);
        }
        $query = "START TRANSACTION;";
        $db->query($query) or die();
        $query = "UPDATE wallet SET amount=amount-$newamt WHERE currencyid=$currid AND userkey=$userkey;";
        $db->query($query) or die();
        $query = "UPDATE wallet SET amount=amount+$sellamt WHERE currencyid=1 AND userkey=$userkey;";
        $db->query($query) or die();
        //transtype: 0 for buy (USD to JPY), 1 for sell (JPY to USD)
        $query = "INSERT INTO transactions (transtype, userkey, currencyid, amount, rate, receiveamt, time) VALUES (1, $userkey, $currid, $newamt, $sellvalue, $sellamt, ".time().");";
        $db->query($query) or die();
        $query = "COMMIT;";
        $db->query($query) or die();
        header("Location: ../");
        exit();
    }
    else if(isset($_GET["currid"]))
    {
        $currid = intval($_GET["currid"]);
        if($currid <= 1)
        {
            header("Location: ../");
            exit();
        }
        global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
        $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
        $query = "SELECT name, shortname, buyvalue, sellvalue FROM currency WHERE currencyid = $currid LIMIT 1";
        $result = $db->query($query) or die();
        if($result->num_rows <= 0)
        {
            header("Location: ../");
            exit();
        }
        $row = $result->fetch_assoc();
        $currname = $row["name"];
        $currsname = $row["shortname"];
        $buyvalue = $row["buyvalue"];
        $sellvalue = $row["sellvalue"];
        $query = "SELECT amount, currencyid FROM wallet WHERE userkey = $userkey AND (currencyid=1 OR currencyid=$currid) LIMIT 2";
        $result = $db->query($query) or die();
        $basecurramt = $seccurramt = 0;
        if($result->num_rows <= 0)
        {
            header("Location: ../");
            exit();
        }
        while($row = $result->fetch_assoc())
        {
            if($row["currencyid"] == 1)
                $basecurramt = $row["amount"];
            else
                $seccurramt = $row["amount"];
        }
    }
    else
    {
        header("Location: ../");
        exit();
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Forex Trading Simulator - Currency Transaction</title>
        <link rel="stylesheet" type="text/css" href="../../css/main.css" />
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/js/materialize.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/css/materialize.min.css" media="screen,projection" />
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
        <script>
            $(document).ready(function ()
            {
                $.ajaxSetup({cache: false});
                setInterval(function ()
                {
                    $("#news").load('../../ajax/newshandler.php');
                    $("#goLeft").load('../../ajax/headerhandler.php');
                }, 30000);
            });
            var buyvalue = <?php echo $buyvalue ?>;
            var sellvalue = <?php echo $sellvalue ?>;
            function buychange()
            {
                var tmp = document.getElementById("buyamt").value;
                tmp = tmp.replace(/[^\d\.\-\ ]/g, '');
                var amt = parseFloat(tmp);
                amt *= buyvalue;
                Number.prototype.formatMoney = function (rate)
                {
                    var n = this,
                            c = isNaN(c = Math.abs(c)) ? 2 : c,
                            d = d == undefined ? "." : d,
                            t = t == undefined ? "," : t,
                            s = n < 0 ? "-" : "",
                            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                            j = (j = i.length) > 3 ? j % 3 : 0;
                    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
                };
                document.getElementById("buyamt2").innerHTML = (amt).formatMoney(2, '.', ',');
            }
            function sellchange()
            {
                var tmp = document.getElementById("sellamt").value;
                tmp = tmp.replace(/[^\d\.\-\ ]/g, '');
                var amt = parseFloat(tmp);
                amt *= sellvalue;
                Number.prototype.formatMoney = function (rate)
                {
                    var n = this,
                            c = isNaN(c = Math.abs(c)) ? 2 : c,
                            d = d == undefined ? "." : d,
                            t = t == undefined ? "," : t,
                            s = n < 0 ? "-" : "",
                            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                            j = (j = i.length) > 3 ? j % 3 : 0;
                    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
                };
                document.getElementById("sellamt2").innerHTML = (amt).formatMoney(2, '.', ',');
            }
            function cleanifyBuy()
            {
                var tmp = document.getElementById("buyamt").value;
                tmp = tmp.replace(/[^\d\.\-\ ]/g, '');
                document.getElementById("buyamt").value = parseFloat(tmp);
                return true;
            }
            function cleanifySell()
            {
                var tmp = document.getElementById("sellamt").value;
                tmp = tmp.replace(/[^\d\.\-\ ]/g, '');
                document.getElementById("sellamt").value = parseFloat(tmp);
                return true;
            }
<?php
    if(isset($remarks))
        echo $remarks;
?>
        </script>
    </head>

    <body>
        <div class="page-module">
            <div style="width:100%;">
                <div id="goRight"><p><a href="../">Home</a> <a href="../history/">History</a> <a href="../leaderboard/">Leaderboards</a> <?php if(isTeacher()) echo "<a href=\"../../admin/\">Admin Console</a>"; ?> <a href="../changepassword/">Change Password</a> <a href="../logout/">Logout</a></p></div>
                <div id="goLeft">
                    <?php
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
            <div style="width: 100%;">
                <div style="float:left; width: 59%; border: 2px solid #c00; border-collapse: collapse; border-width:2px 0px 2px;">
                    <h1>Buy/Sell Foreign Exchange</h1>
                    <h2>Trade <?php echo $basecurrabbv ?>-<?php echo $currsname ?></h2>
                    <div align="center">
                        <canvas id="myChart" width="550" height="200"></canvas>
                    </div>
                    <p style="text-align:center">You own <?php echo $basecurrabbv.number_format($basecurramt / 1000000, 2) ?> million (<?php echo $currsname.number_format(($basecurramt / 1000000) * $buyvalue, 2) ?> million) and <?php echo $currsname.number_format($seccurramt / 1000000, 2) ?> million (<?php echo $basecurrabbv.number_format(($seccurramt / 1000000) / $sellvalue, 2) ?> million).</p>
                    <form name="buy" action="" method="post">
                        <input type="hidden" name="currid" value=<?php echo "\"".$currid."\"" ?> />
                        <table style="width:100%" class="noborder">
                            <tr class="noborder">
                                <td class="noborder">Sell <?php echo $basecurrabbv ?>, Buy <?php echo $currsname ?></td>
                                <td class="noborder">Buy <?php echo $basecurrabbv ?>, Sell <?php echo $currsname ?></td>
                            </tr>
                            <tr class="noborder">
                                <td class="noborder"><?php echo $basecurrabbv ?>1.00 = <?php echo $currsname.number_format($buyvalue, 4) ?></td>
                                <td class="noborder"><?php echo $basecurrabbv ?>1.00 = <?php echo $currsname.number_format($sellvalue, 4) ?></td>
                            </tr>
                            <tr class="noborder">
                                <td class="noborder">Sell <?php echo $basecurrabbv ?> <input type="input" name="buyamt" id="buyamt" onkeyup="buychange()" size="4" <?php if($gameEnded) echo "disabled " ?>/> million <br /> for <br /><?php echo $currsname ?><span id="buyamt2">0.00</span> million</td>
                                <td class="noborder">Buy <?php echo $basecurrabbv ?> <input type="input" name="sellamt" id="sellamt" onkeyup="sellchange()" size="4" <?php if($gameEnded) echo "disabled " ?>/> million <br /> for <br /><?php echo $currsname ?><span id="sellamt2">0.00</span> million</td>
                            </tr>
                            <tr class="noborder">
                                <td class="noborder"><input type="submit" value="Sell USD" name="buybtn" onClick="cleanifyBuy()" <?php if($gameEnded) echo "disabled " ?>/></td>
                                <td class="noborder"><input type="submit" value="Buy USD" name="sellbtn" onClick="cleanifySell()" <?php if($gameEnded) echo "disabled " ?>/></td>
                            </tr>
                        </table>
                    </form>
                    <p style="text-align:center"><a href="../" target="_top">Cancel Transaction</a></p>
                </div>
                <div style="float:right; width:40%;" id="news">
                    <table style="width:100%">
                        <tr class="red">
                            <th colspan="2" class="red">BBT News Headlines</th>
                        </tr>
                        <?php
                            $query = "SELECT newstext, time FROM news WHERE time <= ".time()." ORDER BY time DESC LIMIT 15";
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
            <div style="clear:both"></div>
        </div>
        <script>
            var ctx = $("#myChart").get(0).getContext("2d");
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
                pointHitDetectionRadius: 20,
                datasetStroke: true,
                datasetStrokeWidth: 2,
                datasetFill: true
            };
<?php
    $data = array();
    $labels = array();
//selects the latest 20 currency changes (i.e highest 20) and then sorts them in ascending order (i.e. earliest entries come first)
    $query = "SELECT * FROM(SELECT newbuyvalue, newsellvalue, time FROM valuechanges WHERE currencyid=$currid AND yetcompleted=0 ORDER BY time DESC LIMIT 20) g ORDER BY g.time ASC";
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
?>
            var data = {
                labels: [<?php
    for($i = 0; $i < count($labels) - 1; $i++)
    {
        echo "\"".$labels[$i]."\", ";
    }
    if(count($labels) == 1)
        echo "\"".$labels[0]."\", ";
    echo "\"".$labels[count($labels) - 1]."\"";
?>],
                datasets: [
                    {
                        fillColor: "rgba(204,0,0,0.2)",
                        strokeColor: "rgba(204,0,0,1)",
                        pointColor: "rgba(204,0,0,0.65)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "rgba(210,0,0,1)",
                        pointHighlightStroke: "#fff",
                        data: [<?php
    for($i = 0; $i < count($data) - 1; $i++)
    {
        echo $data[$i].", ";
    }
    if(count($data) == 1)
        echo "\"".$data[0]."\", ";
    echo $data[count($data) - 1];
?>]
                    }
                ]
            };
            new Chart(ctx).Line(data, optionss);
        </script>
    </body>
</html>
*/