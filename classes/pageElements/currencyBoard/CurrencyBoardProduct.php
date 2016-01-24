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
<div id="board" class="card hoverable">
    <table style="width:100%" class="card-content responsive-table">
        <tr class="blue lighten-2" >
            <th class="center">Currency</th>
HTML;
            $this->return .= "<th class=\"center\">".$this->basecurr->getShortName()." Selling Value</th>";
            $this->return .= "<th class=\"center\">USD Buying Value</th>";
            $this->return .= <<<HTML
            <th class="center">Amount Owned</th>
            <th class="center">Buy/Sell</th>
        </tr>
HTML;
            $this->return .= "<tr>";
            $this->return .= "<td class=\"center\">".$this->basecurr->getName()." (".$this->basecurr->getShortName().")</td>";
            $this->return .= "<td class=\"center\">N.A.</td>";
            $this->return .= "<td class=\"center\">N.A.</td>";
            $this->return .= "<td class=\"center\">".number_format($this->basecurr->getAmount(), 2)."</td>";
            $this->return .= "<td class=\"center\">N.A.</td>";
            $this->return .= "</tr>";
            $query = "SELECT currency.name, currency.shortname, currency.sellvalue, currency.buyvalue, currency.currencyid, wallet.amount FROM currency INNER JOIN wallet ON currency.currencyid = wallet.currencyid WHERE currency.currencyid != 1 AND wallet.userkey = $userkey ORDER BY currency.name ASC";
            $result = $db->query($query) or die($db->error);
            while($row = $result->fetch_assoc())
            {
                $this->return .= "<tr>";
                $this->return .= "<td class=\"center\">".$row["name"]." (";
                $this->return .= $row["shortname"].")</td>";
                $this->return .= "<td class=\"center\">".$row["buyvalue"]."</td>";
                $this->return .= "<td class=\"center\">".$row["sellvalue"]."</td>";
                $this->return .= "<td class=\"center\">".number_format($row["amount"], 2)."</td>";
                $this->return .= "<td class=\"center\"><a href='./buysell/?currid=".$row["currencyid"]."' target=\"_top\" data-ftrans=\"slide\" id=\"buysell\">Buy/Sell</a></td>";
                $this->return .= "</tr>";
            }
            $this->return .= "</table></div>";
            $db->close();
            return $this->return;
        }

    }
    