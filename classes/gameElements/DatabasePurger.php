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
     * Description of DatabasePurger
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("mysql/UniversalConnect.php");

    class DatabasePurger
    {

        public function purge()
        {
            ignore_user_abort(true);
            $db = UniversalConnect::doConnect();
            date_default_timezone_set('Asia/Singapore');
            $query = "SELECT valuechangeid, currencyid, newsellvalue, newbuyvalue FROM valuechanges WHERE yetcompleted=1 AND time<=".time()." ORDER BY time DESC LIMIT 1";
            $result = $db->query($query);
            if($result->num_rows >= 1)
            {
                while($row = $result->fetch_assoc())
                {
                    $db->begin_transaction();
                    $query = "UPDATE currency SET buyvalue=".$row["newbuyvalue"].", sellvalue=".$row["newsellvalue"]." WHERE currencyid=".$row["currencyid"];
                    $db->query($query);
                    $query = "UPDATE valuechanges SET yetcompleted=0 WHERE time <= ".time();
                    $db->query($query);
                    $db->commit();
                }

                //recalculate everyone's net worth
                $query = "SELECT userkey FROM users";
                $result = $db->query($query);
                while($row = $result->fetch_assoc())
                {
                    //purges database
                    $totalvalue = 0.00;
                    $query = "SELECT wallet.amount, currency.sellvalue FROM wallet INNER JOIN currency ON currency.currencyid=wallet.currencyid WHERE userkey=$userkey";
                    $result2 = $db->query($query);
                    while($row2 = $result2->fetch_assoc())
                    {
                        $totalvalue += round($row2["amount"] / ($row2["sellvalue"]), 4);
                    }
                    $totalvalue = round($totalvalue, 2);
                    $query = "UPDATE users SET networth=$totalvalue WHERE userkey=$userkey";
                    $db->query($query);
                }
            }
            $db->close();
        }

    }
    