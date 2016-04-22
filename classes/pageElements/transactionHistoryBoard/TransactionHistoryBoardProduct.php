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
     * Description of TransactionHistoryBoardProduct
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("mysql/UniversalConnect.php");
    require_once("gameElements/trading/BaseCurrency.php");
    require_once("pageElements/ElementProduct.php");

    class TransactionHistoryBoardProduct implements ElementProduct
    {

        private $return, $baseCurrency;

        public function __construct()
        {
            $this->baseCurrency = new BaseCurrency();
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
<div id="currencyHistoryBoard" class="card">
            <table style="width:100%">
                    <tr class="blue center">
                        <th>No.</th>
                        <th>Time</th>
                        <th>Transaction Type</th>
                        <th>Rate Transacted</th>
                        <th>Amount Sold</th>
                        <th>Amount Received</th>
                    </tr>
HTML;
            $query = "SELECT transactions.transtype, currency.shortname, transactions.amount, transactions.rate, transactions.receiveamt, transactions.time FROM transactions INNER JOIN currency ON currency.currencyid = transactions.currencyid WHERE transactions.userkey = $userkey ORDER BY time DESC";
            $result = $db->query($query) or die($db->error);
            $count = 1;
            if($result->num_rows <= 0)
            {
                $this->return .= "<tr><td class=\"center\" colspan=\"6\">No previous transactions found.</td></tr>";
            }
            else
            {
                while($row = $result->fetch_assoc())
                {
                    $this->return .= "<tr class=\"center\">";
                    if(intval($row["transtype"]) === 0)
                    {
                        $newcurrname = $row["shortname"];
                        $this->return .= "<td>$count</td>";
                        $this->return .= "<td>".FormatTimePassed::format($row["time"])."</td>";
                        $this->return .= "<td>".$this->baseCurrency->getShortName()." to $newcurrname (Sell)</td>";
                        $this->return .= "<td>".$row["rate"]."</td>";
                        $this->return .= "<td>".$this->baseCurrency->getShortName().number_format($row["amount"], 2)."</td>";
                        $this->return .= "<td>$newcurrname".number_format($row["receiveamt"], 2)."</td>";
                    }
                    else
                    {
                        $newcurrname = $row["shortname"];
                        $this->return .= "<td>$count</td>";
                        $this->return .= "<td>".FormatTimePassed::format($row["time"])."</td>";
                        $this->return .= "<td>$newcurrname to ".$this->baseCurrency->getShortName()." (Buy)</td>";
                        $this->return .= "<td>".$row["rate"]."</td>";
                        $this->return .= "<td>$newcurrname".number_format($row["amount"])."</td>";
                        $this->return .= "<td>".$this->baseCurrency->getShortName().number_format($row["receiveamt"])."</td>";
                    }
                    $count++;
                    $this->return .= "</tr>";
                }
            }
            $this->return .= "</table></div>";
            return $this->return;
        }

    }
