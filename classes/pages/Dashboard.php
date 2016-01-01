<?php

    /*
     * The MIT License
     *
     * Copyright 2016 Li Yicheng, Sun Yudong, and Walter Kong.
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

    /**
     * Description of Dashboard
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    //Highly WIP, code pasted here so that existing page will still work even after
    //require_once in dashboard/
    session_start();
    require_once("include.php");
    require_once("authenticate/SessionAuthenticate.php");
    require_once("authenticate/PasswordAuthenticate.php");
    require_once("miscellenous/FormatTimePassed.php");
    require_once("pageElements/header/HeaderFactory.php");
    require_once("pageElements/header/HeaderProduct.php");
    require_once("mysql/UniversalConnect.php");
    $SessAuthWorker = new SessionAuthenticate();
    if(!$SessAuthWorker->authenticateSession())
    {
        header("Location: ../");
        exit();
    }
    date_default_timezone_set('Asia/Singapore');
    purgeDatabase(); //TODO: Object this
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
    $headerFactory = new HeaderFactory();
    if(isset($remarks))
        echo $headerFactory->startFactory(new HeaderProduct("Dashboard - Forex Trading Simulator", 2, $remarks));
    else
        echo $headerFactory->startFactory(new HeaderProduct("Dashboard - Forex Trading Simulator", 2));
?>
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
            <div class="card"><div class="card-content center"><?php
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
                ?></div></div>
            <div id="board" class="card">
                <table style="width:100%" class="card-content">
                    <tr class="indigo lighten-2" >
                        <th class="center">Currency</th>
                        <th class="center">USD Selling Value</th>
                        <th class="center">USD Buying Value</th>
                        <th class="center">Amount Owned</th>
                        <th class="center">Buy/Sell</th>
                    </tr>
                    <?php
                        $query = "SELECT amount FROM wallet WHERE currencyid=1 AND userkey = $userkey LIMIT 1";
                        $result = $db->query($query) or die($db->error);
                        while($row = $result->fetch_assoc())
                        {
                            echo "<tr>";
                            echo "<td class=\"center\">$basecurrname ($basecurrabbv)</td>";
                            echo "<td class=\"center\">N.A.</td>";
                            echo "<td class=\"center\">N.A.</td>";
                            echo "<td class=\"center\">".number_format($row["amount"], 2)."</td>";
                            echo "<td class=\"center\">N.A.</td>";
                            echo "</tr>";
                            break;
                        }
                        $query = "SELECT currency.name, currency.shortname, currency.sellvalue, currency.buyvalue, currency.currencyid, wallet.amount FROM currency INNER JOIN wallet ON currency.currencyid = wallet.currencyid WHERE currency.currencyid != 1 AND wallet.userkey = $userkey ORDER BY currency.name ASC";
                        $result = $db->query($query) or die($db->error);
                        while($row = $result->fetch_assoc())
                        {
                            echo "<tr>";
                            echo "<td class=\"center\">".$row["name"]." (";
                            echo $row["shortname"].")</td>";
                            echo "<td class=\"center\">".$row["buyvalue"]."</td>";
                            echo "<td class=\"center\">".$row["sellvalue"]."</td>";
                            echo "<td class=\"center\">".number_format($row["amount"], 2)."</td>";
                            echo "<td class=\"center\"><a href='./buysell/?currid=".$row["currencyid"]."' target=\"_top\" data-ftrans=\"slide\" id=\"buysell\">Buy/Sell</a></td>";
                            echo "</tr>";
                        }
                    ?>
                </table>
            </div>
            <div id="news" class="card">
                <table style="width:100%" class="card-content center">
                    <tr class="indigo lighten-2">
                        <th colspan="2" class="center">BBT News Headlines</th>
                    </tr>
                    <?php
                        $query = "SELECT newstext, time FROM news WHERE time <= ".time()." ORDER BY time DESC LIMIT 30";
                        $result = $db->query($query) or die($db->error);
                        if($result->num_rows <= 0)
                            echo "<tr><td colspan=\"2\" class=\"center\">There are no news reports at the moment.</td></tr>";
                        while($row = $result->fetch_assoc())
                        {
                            echo "<tr>";
                            echo "<td class=\"center\">".$row["newstext"]."</td>";
                            echo "<td class=\"center\" style=\"width:25%\">".nicetime($row["time"])."</td>";
                            echo "</tr>";
                        }
                    ?>
                </table>
            </div>
        </div>
    </body>
</html>
    