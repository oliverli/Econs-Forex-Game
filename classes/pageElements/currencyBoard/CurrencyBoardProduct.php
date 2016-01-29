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
            $this->return .= "<div class=\"col s3 center card-content\">".$this->basecurr->getName()." (".$this->basecurr->getShortName().")</div>";
            $this->return .= "<div class=\"col s3 center card-content\">N.A.</div>";
            $this->return .= "<div class=\"col s3 center card-content\">N.A.</div>";
            $this->return .= "<div class=\"col s3 center card-content\">".number_format($this->basecurr->getAmount(), 2)."</div>";
            $this->return .= "</div></li>";
            $query = "SELECT currency.name, currency.shortname, currency.sellvalue, currency.buyvalue, currency.currencyid, wallet.amount FROM currency INNER JOIN wallet ON currency.currencyid = wallet.currencyid WHERE currency.currencyid != 1 AND wallet.userkey = $userkey ORDER BY currency.name ASC";
            $result = $db->query($query) or die($db->error);
            while($row = $result->fetch_assoc())
            {
                $this->return .= "<li><div class=\"row collapsible-header\">";
                $this->return .= "<div class=\"col s3 center card-content\">".$row["name"]." (";
                $this->return .= $row["shortname"].")</div>";
                $this->return .= "<div class=\"col s3 center card-content\">".$row["buyvalue"]."</div>";
                $this->return .= "<div class=\"col s3 center card-content\">".$row["sellvalue"]."</div>";
                $this->return .= "<div class=\"col s3 center card-content\">".number_format($row["amount"], 2)."</div>";
                //$this->return .= "<td class=\"center\"><a href='./buysell/?currid=".$row["currencyid"]."' target=\"_top\" data-ftrans=\"slide\" id=\"buysell\">Buy/Sell</a></td>";
                //$this->return
                $this->return .= "</div></li>";
            }
            $this->return .= "</ul>";
            $db->close();
            return $this->return;
        }

    }
    