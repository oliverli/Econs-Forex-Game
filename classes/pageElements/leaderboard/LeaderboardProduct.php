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
     * Description of LeaderboardProduct
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("pageElements/ElementCreator.php");
    require_once("pageElements/ElementProduct.php");
    require_once("gameElements/trading/BaseCurrency.php");

    class LeaderboardProduct implements ElementProduct
    {

        private $return, $basecurr;

        public function __construct()
        {
            $this->return = "";
            $this->basecurr = new BaseCurrency();
        }

        public function giveProduct()
        {
            $this->return .= <<<HTML
                <div id="leaderboard" class="card">
                <table style="width:100%">
                    <tr class="blue">
                        <th class="center">Rank</th>
                        <th class="center">Name</th>
                        <th class="center">
HTML;
            $this->return .= "Net Profit ";
            $this->return .= "(".$this->basecurr->getShortName().")</th></tr>";
            $db = UniversalConnect::doConnect();
            $query = "SELECT name, networth FROM users WHERE usertype=1 ORDER BY networth DESC";
            $result = $db->query($query) or die($db->error);
            $number = 1;
            $tietally = 0;
            $previousNetWorth = -PHP_INT_MAX;
            while($row = $result->fetch_assoc())
            {
                $this->return .= "<tr>";
                if($row["networth"] == $previousNetWorth)
                {
                    $number--;
                    $this->return .= "<td class=\"center\">".$number."</td>";
                    $tietally++;
                    $this->return .= "<td class=\"center\">".$row["name"]."</td>";
                    //if($gameEnded)
                        $this->return .= "<td class=\"center\">".number_format($row["networth"] - 10000000, 2)."</td>";
                    //else
                    //    $this->return .= "<td class=\"center\">".number_format($row["networth"], 2)."</td>";
                }
                else if($tietally != 0)
                {
                    $number += $tietally;
                    $this->return .= "<td class=\"center\">".$number."</td>";
                    $tietally = 0;
                    $this->return .= "<td class=\"center\">".$row["name"]."</td>";
                    //if($gameEnded)
                        $this->return .= "<td class=\"center\">".number_format($row["networth"] - 10000000, 2)."</td>";
                    //else
                    //    $this->return .= "<td class=\"center\">".number_format($row["networth"], 2)."</td>";
                }
                else
                {
                    $this->return .= "<td class=\"center\">".$number."</td>";
                    $this->return .= "<td class=\"center\">".$row["name"]."</td>";
                    //if($gameEnded)
                        $this->return .= "<td class=\"center\">".number_format($row["networth"] - 10000000, 2)."</td>";
                    //else
                    //    $this->return .= "<td class=\"center\">".number_format($row["networth"], 2)."</td>";
                }
                $number++;
                $previousNetWorth = $row["networth"];
                $this->return .= "</tr>";
            }
            $this->return .= "</table></div>";
            return $this->return;
        }

    }
    