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
     * Description of CurrencyBoardProduct
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("mysql/UniversalConnect.php");
    require_once("gameElements/trading/BaseCurrency.php");
    require_once("gameElements/trading/Currency.php");
    require_once("pageElements/ElementProduct.php");

    class CurrencyBoardProduct implements ElementProduct
    {

        private $basecurr, $return;

        public function __construct()
        {
            $this->basecurr = new BaseCurrency();
            $this->return = "";
        }

        public function giveProduct()
        {
            if(session_status() === PHP_SESSION_NONE)
            {
                session_start();
            }
            $userkey = intval($_SESSION["userkey"]);
            $db = UniversalConnect::doConnect();
            $this->return .= <<<HTML
<script>
$(document).ready(function(){
    $('.collapsible').collapsible({
      accordion : false // A setting that changes the collapsible behavior to expandable instead of the default accordion style
    });
  });
</script>
<ul class="collapsible card" data-collapsible="accordion">
    <li class="blue">
        <div class="row" style="padding: 5px 10px;">
            <div class="col s3 center card-title">
                Currency
            </div>
HTML;
            $this->return .= "<div class=\"col s3 center card-title\">Bid Rate</div>";
            $this->return .= "<div class=\"col s3 center card-title\">Offer Rate</div>";
            $this->return .= <<<HTML
        <div class="col s3 center card-title">Amount</div>
        </div>
    </li>
HTML;
            $this->return .= "<li><div class=\"row\">";
            $this->return .= "<div class=\"col s3 center card-content\"><p>".$this->basecurr->getName()." (".$this->basecurr->getShortName().")</p></div>";
            $this->return .= "<div class=\"col s3 center card-content\"><p>N.A.</p></div>";
            $this->return .= "<div class=\"col s3 center card-content\"><p>N.A.</p></div>";
            $this->return .= "<div class=\"col s3 center card-content\"><p>".number_format($this->basecurr->getAmount(), 2)."</p></div>";
            $this->return .= "</div></li>";
            $query = "SELECT currencyid FROM currency WHERE currencyid != 1 ORDER BY currencyid ASC";
            $result = $db->query($query) or die($db->error);
            while($row = $result->fetch_assoc())
            {
                $base_shortname = $this->basecurr->getShortName();
                $secCurr = new Currency($row["currencyid"]);
                $name = $secCurr->getName();
                $shortname = $secCurr->getShortName();
                $bidRate = $secCurr->getBuyValue();
                $offerRate = $secCurr->getSellValue();
                $currencyID = $row["currencyid"];
                $this->return .= <<<JAVASCRIPT
<script>
    function sellchange$currencyID()
    {
        var tmp = document.getElementById("sellamt$currencyID").value;
        tmp = tmp.replace(/[^\d\.\-\ ]/g, '');
        var amt = parseFloat(tmp);
        amt *= $bidRate;
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
        document.getElementById("disp_sellamt$currencyID").innerHTML = (amt).formatMoney(2, '.', ',');
    }
    function buychange$currencyID()
    {
        var tmp = document.getElementById("buyamt$currencyID").value;
        tmp = tmp.replace(/[^\d\.\-\ ]/g, '');
        var amt = parseFloat(tmp);
        amt *= $offerRate;
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
        document.getElementById("disp_buyamt$currencyID").innerHTML = (amt).formatMoney(2, '.', ',');
    }
    function cleanify(elementID)
    {
        var tmp = document.getElementById(elementID).value;
        tmp = tmp.replace(/[^\d\.\-\ ]/g, '');
        document.getElementById(elementID).value = parseFloat(tmp);
        return true;
    }
</script>
JAVASCRIPT;
                $this->return .= <<<HTML
<li>
    <div class="collapsible-header">
        <div class="row">
            <div class="col s3 center card-content">
                <p>$name ($shortname)</p>
            </div>
            <div class="col s3 center card-content">
                <p>$bidRate</p>
            </div>
            <div class="col s3 center card-content">
                <p>$offerRate</p>
            </div>
            <div class="col s3 center card-content">
                <p>
HTML;
                $this->return .= number_format($secCurr->getAmount(), 2);
                $this->return .= <<<HTML
                </p>
            </div>
        </div>
    </div>
    <div class="collapsible-body">
        <!--<div class="row">
            <div class="col s6 center">
                <p>Sell $base_shortname, Buy $shortname</p>
            </div>
            <div class="col s6 center">
                <p>Buy $base_shortname, Sell $shortname</p>
            </div>
        </div>-->
        <form name="transact$currencyID" action="./" method="post">
            <div class="row">  
                <input type="hidden" name="currid" value="$currencyID" />
                <div class="col s6 center">
                    <p style="text-align:center;">
                        Sell $base_shortname <input type="number" name="sellamt$currencyID" id="sellamt$currencyID" onchange="sellchange$currencyID()" onkeyup="sellchange$currencyID()" style="width: 40px;"/> million for $shortname <span id="disp_sellamt$currencyID">0.00</span> million
                    </p>
                </div>
                <div class="col s6 center">
                    <p style="text-align:center;">
                        Buy $base_shortname <input type="number" name="buyamt$currencyID" id="buyamt$currencyID" onchange="buychange$currencyID()" onkeyup="buychange$currencyID()" style="width: 40px;"/> million for $shortname <span id="disp_buyamt$currencyID">0.00</span> million
                    </p>
                </div>
            
            </div>
            <div class="row">
                <div class="col s6 center">
                    <button class="btn waves-effect waves-light center" type="submit" name="sellBase$currencyID">Sell $base_shortname
                      <i class="material-icons right">attach_money</i>
                    </button>
                </div>
                <div class="col s6 center">
                    <button class="btn waves-effect waves-light center" type="submit" name="buyBase$currencyID">Buy $base_shortname
                      <i class="material-icons right">attach_money</i>
                    </button>
                </div>
                <p></p><!-- hacky solution for bottom padding -->
            </div>
        </form>
    </div>
</li>
HTML;
            }
            $this->return .= "</ul>";
            $db->close();
            return $this->return;
        }

    }
    