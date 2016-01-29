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
     * Description of Currency
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("mysql/UniversalConnect.php");
    require_once("BaseCurrency.php");

    class Currency
    {

        protected $id, $name, $shortname, $buyValue, $sellValue, $amount;

        public function __construct($currencyID)
        {
            if(session_status() === PHP_SESSION_NONE)
            {
                session_start();
            }
            $db = UniversalConnect::doConnect();
            $query = "SELECT * FROM currency WHERE currencyid = $currencyID LIMIT 1";
            $result = $db->query($query) or die($db->error);
            if($result->num_rows <= 0)
            {
                header("Location: ../");
                exit();
            }
            while($row = $result->fetch_assoc())
            {
                $this->id = $row["currencyid"];
                $this->name = $row["name"];
                $this->shortname = $row["shortname"];
                $this->buyValue = $row["buyvalue"];
                $this->sellValue = $row["sellvalue"];
            }
            $query = "SELECT amount FROM wallet WHERE userkey = ".$_SESSION["userkey"]." AND currencyid = $currencyID LIMIT 1";
            $result = $db->query($query) or die();
            if($result->num_rows <= 0)
            {
                header("Location: ../");
                exit();
            }
            while($row = $result->fetch_assoc())
            {
                $this->amount = $row["amount"];
            }
        }

        public function getName()
        {
            return $this->name;
        }

        public function getShortName()
        {
            return $this->shortname;
        }

        public function getAmount()
        {
            return $this->amount;
        }

        public function getSellValue()
        {
            return $this->sellValue;
        }

        public function getBuyValue()
        {
            return $this->buyValue;
        }

        public function buy($baseSellAmount)
        {
            ignore_user_abort(true);
            if(session_status() === PHP_SESSION_NONE)
            {
                session_start();
            }
            $db = UniversalConnect::doConnect();
            date_default_timezone_set('Asia/Singapore');
            $userkey = $_SESSION["userkey"];
            $baseSellAmount = round($baseSellAmount, 2);
            if($baseSellAmount <= 0)
                return false;
            $baseCurr = new BaseCurrency();
            if($baseSellAmount > $baseCurr->getAmount())
                $baseSellAmount = $baseCurr->getAmount();
            $addAmount = round($baseSellAmount * $this->buyValue, 2);
            $db->begin_transaction();
            $query = "UPDATE wallet SET amount=amount+$addAmount WHERE currencyid=$this->id AND userkey=$userkey;";
            $db->query($query);
            $query = "UPDATE wallet SET amount=amount-$baseSellAmount WHERE currencyid=1 AND userkey=$userkey;";
            $db->query($query);
            //transtype: 0 for buy (USD to JPY), 1 for sell (JPY to USD)
            $query = "INSERT INTO transactions (transtype, userkey, currencyid, amount, rate, receiveamt, time)  VALUES (0, $userkey, $this->id, $baseSellAmount, $this->buyValue, $addAmount, ".time().");";
            $db->query($query);
            if(!$db->commit())
            {
                $db->rollback();
                die("An error occurred during transaction. Please try again later. Technical details: ".$db->error);
            }

            //calculates net worth
            $totalvalue = 0.00;
            $query = "SELECT wallet.amount, currency.sellvalue FROM wallet INNER JOIN currency ON currency.currencyid=wallet.currencyid WHERE userkey=$userkey";
            $result2 = $db->query($query) or die($db->error);
            while($row2 = $result2->fetch_assoc())
            {
                $totalvalue += round($row2["amount"] / ($row2["sellvalue"]), 4);
            }
            $totalvalue = round($totalvalue, 2);
            $query = "UPDATE users SET networth=$totalvalue WHERE userkey=$userkey";
            $db->query($query);

            $db->close();
            return true;
        }

        public function sell($baseBuyAmount)
        {
            ignore_user_abort(true);
            if(session_status() === PHP_SESSION_NONE)
            {
                session_start();
            }
            $db = UniversalConnect::doConnect();
            date_default_timezone_set('Asia/Singapore');
            $userkey = $_SESSION["userkey"];
            $baseBuyAmount = round($baseBuyAmount, 2);
            if($baseBuyAmount <= 0)
                return false;
            $baseCurr = new BaseCurrency();
            $reduceAmount = round($baseBuyAmount * $this->sellValue, 2);
            if($reduceAmount > $this->amount)
            {
                $reduceAmount = $this->amount;
                $baseBuyAmount = round($reduceAmount / $this->sellValue, 2);
            }
            $db->begin_transaction();
            $query = "UPDATE wallet SET amount=amount-$reduceAmount WHERE currencyid=$this->id AND userkey=$userkey;";
            $db->query($query);
            $query = "UPDATE wallet SET amount=amount+$baseBuyAmount WHERE currencyid=1 AND userkey=$userkey;";
            $db->query($query);
            //transtype: 0 for buy (USD to JPY), 1 for sell (JPY to USD)
            $query = "INSERT INTO transactions (transtype, userkey, currencyid, amount, rate, receiveamt, time) VALUES (1, $userkey, $this->id, $reduceAmount, $this->sellValue, $baseBuyAmount, ".time().");";
            $db->query($query);
            if(!$db->commit())
            {
                $db->rollback();
                die("An error occurred during transaction. Please try again later. Technical details: ".$db->error);
            }
            $db->close();
            return true;
        }

    }
    