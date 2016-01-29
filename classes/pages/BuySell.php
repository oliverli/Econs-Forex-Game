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

    /**
     * Description of BuySell
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("mysql/UniversalConnect.php");
    require_once("authenticate/SessionAuthenticate.php");
    require_once("gameElements/DatabasePurger.php");
    require_once("pageElements/header/HeaderFactory.php");
    require_once("pageElements/navbar/NavbarFactory.php");
    require_once("pageElements/newsBoard/NewsBoardFactory.php");
    require_once("pageElements/currencyChart/CurrencyChartFactory.php");
    require_once("pageElements/newsBoard/NewsBoardFactory.php");
    require_once("gameElements/trading/BaseCurrency.php");
    require_once("gameElements/trading/Currency.php");
    require_once("gameElements/GameEndedChecker.php");
    require_once("miscellaneous/FormatTimePassed.php");
    require_once("miscellaneous/GenerateRootPath.php");

    class BuySell
    {

        private $baseCurr, $secCurr, $pathToRoot;

        public function __construct()
        {

            $this->pathToRoot = GenerateRootPath::getRoot(3);
            $SessAuthWorker = new SessionAuthenticate();
            if(!$SessAuthWorker->authenticate())
            {
                header("Location: ".$this->pathToRoot);
                exit();
            }
            $this->baseCurr = new BaseCurrency();
            date_default_timezone_set('Asia/Singapore');
            DatabasePurger::purge();
            $db = UniversalConnect::doConnect();
            if(session_status() === PHP_SESSION_NONE)
            {
                session_start();
            }
            if(isset($_SESSION["remarks"]))
            {
                $remarks = $_SESSION["remarks"];
                unset($_SESSION["remarks"]);
            }
            if(isset($_POST["buyamt"]) && isset($_POST["buybtn"]) && isset($_POST["currid"]) && !GameEndedChecker::GameEnded())
            {
                $buyamt = round(floatval($_POST["buyamt"] * 1000000), 2);
                $currid = intval($_POST["currid"]);
                $this->secCurr = new Currency($currid);
                $this->secCurr->buy($buyamt);
                header("Location: $this->pathToRoot/dashboard/");
                exit();
            }
            else if(isset($_POST["sellamt"]) && isset($_POST["sellbtn"]) && isset($_POST["currid"]) && !GameEndedChecker::GameEnded())
            {
                $sellamt = round(floatval($_POST["sellamt"] * 1000000), 2);
                $currid = intval($_POST["currid"]);
                $this->secCurr = new Currency($currid);
                $this->secCurr->sell($sellamt);
                header("Location: $this->pathToRoot/dashboard/");
                exit();
            }
            else if(isset($_GET["currid"]))
            {
                $currid = intval($_GET["currid"]);
                if($currid <= 1)
                {
                    header("Location: $this->pathToRoot/dashboard/");
                    exit();
                }
                $this->secCurr = new Currency($currid);
            }
            else
            {
                header("Location: $this->pathToRoot/dashboard/");
                exit();
            }
            $headerFactory = new HeaderFactory();
            echo $headerFactory->startFactory(new HeaderProduct("Transact Currencies - Forex Trading Simulator", 3));
            echo "<body class=\"blue lighten-5\">";
            $navbarFactory = new NavbarFactory();
            echo $navbarFactory->startFactory(new NavbarProduct(3, 0));
            ?>
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
                var buyValue = <?php echo $this->secCurr->getBuyValue() ?>;
                var sellValue = <?php echo $this->secCurr->getSellValue() ?>;
                function buychange()
                {
                    var tmp = document.getElementById("buyamt").value;
                    tmp = tmp.replace(/[^\d\.\-\ ]/g, '');
                    var amt = parseFloat(tmp);
                    amt *= buyValue;
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
                    amt *= sellValue;
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
            <div id="chart" class="card">
                <span class="card-title">Trade <?php echo $this->baseCurr->getShortName() ?>-<?php echo $this->secCurr->getShortName() ?></span>
                <div class="card-content center">
                    <?php
                    $chartWorker = new CurrencyChartFactory();
                    echo $chartWorker->startFactory(new CurrencyChartProduct($currid))
                    ?>
                </div>
            </div>
            <div class="row">
                <div id="tradeTable" class="card col s8">
                    <div class="card-content">

                        <p style="text-align:center">You own <?php echo $this->baseCurr->getShortName().number_format($this->baseCurr->getAmount() / 1000000, 2) ?> million (<?php echo $this->secCurr->getShortName().number_format(($this->baseCurr->getAmount() / 1000000) * $this->secCurr->getBuyValue(), 2) ?> million) and <?php echo $this->secCurr->getShortName().number_format($this->secCurr->getAmount() / 1000000, 2) ?> million (<?php echo $this->baseCurr->getShortName().number_format(($this->secCurr->getAmount() / 1000000) / $this->secCurr->getSellValue(), 2) ?> million).</p>
                        <form name="buy" action="" method="post">
                            <input type="hidden" name="currid" value=<?php echo "\"".$currid."\"" ?> />
                            <table style="width:100%" class="noborder">
                                <tr class="noborder">
                                    <td class="noborder">Sell <?php echo $this->baseCurr->getShortName() ?>, Buy <?php echo $this->secCurr->getShortName() ?></td>
                                    <td class="noborder">Buy <?php echo $this->baseCurr->getShortName() ?>, Sell <?php echo $this->secCurr->getShortName() ?></td>
                                </tr>
                                <tr class="noborder">
                                    <td class="noborder"><?php echo $this->baseCurr->getShortName() ?>1.00 = <?php echo $this->secCurr->getShortName().number_format($this->secCurr->getBuyValue(), 4) ?></td>
                                    <td class="noborder"><?php echo $this->baseCurr->getShortName() ?>1.00 = <?php echo $this->secCurr->getShortName().number_format($this->secCurr->getSellValue(), 4) ?></td>
                                </tr>
                                <tr class="noborder">
                                    <td class="noborder">Sell <?php echo $this->baseCurr->getShortName() ?> <input type="number" name="buyamt" id="buyamt" onchange="buychange()" onkeyup="buychange()" <?php if(GameEndedChecker::GameEnded()) echo "disabled " ?>/> million <br /> for <br /><?php echo $this->secCurr->getShortName() ?><span id="buyamt2">0.00</span> million</td>
                                    <td class="noborder">Buy <?php echo $this->baseCurr->getShortName() ?> <input type="number" name="sellamt" id="sellamt" onchange="sellchange()" onkeyup="sellchange()" <?php if(GameEndedChecker::GameEnded()) echo "disabled " ?>/> million <br /> for <br /><?php echo $this->secCurr->getShortName() ?><span id="sellamt2">0.00</span> million</td>
                                </tr>
                                <tr class="noborder">
                                    <td class="noborder"><input type="submit" value="Sell USD" name="buybtn" onClick="cleanifyBuy()" <?php if(GameEndedChecker::GameEnded()) echo "disabled " ?>/></td>
                                    <td class="noborder"><input type="submit" value="Buy USD" name="sellbtn" onClick="cleanifySell()" <?php if(GameEndedChecker::GameEnded()) echo "disabled " ?>/></td>
                                </tr>
                            </table>
                        </form>
                        <p style="text-align:center"><a href="../" target="_top">Cancel Transaction</a></p>
                    </div>
                </div>
                <div class="col s4" id="news">
                    <?php
                    $newsBoardWorker = new NewsBoardFactory();
                    echo $newsBoardWorker->startFactory(new NewsBoardProduct(20));
                    ?>
                </div>
            </div>
            </body>
            </html><?php
        }

    }
    