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
     * Description of NewsBoardProduct
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("mysql/UniversalConnect.php");
    require_once("pageElements/ElementProduct.php");
    require_once("gameElements/GameEndedChecker.php");
    require_once("gameElements/trading/BaseCurrency.php");
    require_once("miscellaneous/FormatTimePassed.php");

    class NewsBoardProduct implements ElementProduct
    {

        private $newsCount, $return;

        public function __construct($NumberNews = -1)
        {
            $this->newsCount = intval($NumberNews);
            $this->return = "";
        }

        public function giveProduct()
        {
            $db = UniversalConnect::doConnect();
            date_default_timezone_set('Asia/Singapore');
            $this->return .= <<<HTML
<div id="news" class="card">
    <table style="width:100%" class="card-content center">
        <tr class="blue lighten-2">
            <th colspan="2" class="center">BBT News Headlines</th>
HTML;
            if($this->newsCount !== -1)
                $query = "SELECT newstext, time FROM news WHERE time <= ".time()." ORDER BY time DESC LIMIT $this->newsCount";
            else
                $query = "SELECT newstext, time FROM news WHERE time <= ".time()." ORDER BY time DESC";
            $result = $db->query($query) or die($db->error);
            if($result->num_rows <= 0)
                $this->return .= "<tr><td colspan=\"2\" class=\"center\">There are no news reports at the moment.</td></tr>";
            while($row = $result->fetch_assoc())
            {
                $this->return .= "<tr>";
                $this->return .= "<td class=\"center\">".$row["newstext"]."</td>";
                $this->return .= "<td class=\"center\" style=\"width:25%\">".FormatTimePassed::format($row["time"])."</td>";
                $this->return .= "</tr>";
            }
            $this->return .= "</table></div>";
            return $this->return;
        }

    }
    