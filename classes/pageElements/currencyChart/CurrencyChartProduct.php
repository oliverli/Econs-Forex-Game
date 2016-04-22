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
     * Description of CurrencyChartProduct
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("mysql/UniversalConnect.php");
    require_once("pageElements/ElementProduct.php");

    class CurrencyChartProduct implements ElementProduct
    {

        private $currencyID, $dataCount, $return;

        public function __construct($currencyID, $dataCount = 20)
        {
            $this->currencyID = $currencyID;
            $this->dataCount = $dataCount;
            $this->return = "";
        }

        public function giveProduct()
        {
            if(session_status() === PHP_SESSION_NONE)
            {
                session_start();
            }
            $db = UniversalConnect::doConnect();
            $query = "SELECT starttime FROM startendtime LIMIT 1";
            $result = $db->query($query);
            $row = $result->fetch_assoc();
            $startTime = $row["starttime"];
            $this->return .= <<<HTML
<div><canvas id="myChart$this->currencyID" class="chart"></canvas></div>
<script>
                var ctx = $("#myChart$this->currencyID").get(0).getContext("2d");
                var options = {
                    scaleShowGridLines: true,
                    scaleGridLineColor: "rgba(0,0,0,.05)",
                    scaleGridLineWidth: 1,
                    scaleShowHorizontalLines: true,
                    scaleShowVerticalLines: true,
                    bezierCurve: true,
                    pointDot: true,
                    pointDotRadius: 4,
                    pointDotStrokeWidth: 1,
                    pointHitDetectionRadius: 20,
                    datasetStroke: true,
                    datasetStrokeWidth: 2,
                    datasetFill: true,
                    responsive: true,
                    maintainAspectRatio: false
                };
HTML;
            $bidRate = array();
            $labels = array();
//selects the latest 20 currency changes (i.e highest 20) and then sorts them in ascending order (i.e. earliest entries come first)
            if($this->dataCount !== 0)
                $query = "SELECT * FROM(SELECT newbuyvalue, time FROM valuechanges WHERE currencyid=$this->currencyID AND yetcompleted=0 ORDER BY time DESC LIMIT $this->dataCount) g ORDER BY g.time ASC";
            else
                $query = "SELECT newbuyvalue, time FROM valuechanges WHERE currencyid=$this->currencyID AND yetcompleted=0 ORDER BY time ASC";
            $result = $db->query($query) or die($query.$db->error);
            if($result->num_rows > 0)
            {
                while($row = $result->fetch_assoc())
                {
                    //$a = ($row["newbuyvalue"] + $row["newsellvalue"]) / 2;
                    array_push($bidRate, $row["newbuyvalue"]);
                    array_push($labels, date("H:i", $row["time"]+$startTime));
                }
            }
            if(count($labels) === 1)
            {
                array_push($bidRate, $bidRate[0]);
                array_push($labels, $labels[0]);
            }
            $this->return .= "var data = { labels: [";
            for($i = 0; $i < count($labels) - 1; $i++)
            {
                $this->return .= "\"".$labels[$i]."\", ";
            }
            if(count($labels) == 1)
                $this->return .= "\"".$labels[0]."\", ";
            $this->return .= "\"".$labels[count($labels) - 1]."\"";
            $this->return .= <<<JAVASCRIPT
],
                    datasets: [
                        {
                            label: "Bid Rate",
                            fillColor: "rgba(33, 150, 243,0.2)",
                            strokeColor: "rgba(33, 150, 243,1)",
                            pointColor: "rgba(33, 150, 243,0.65)",
                            pointStrokeColor: "#fff",
                            pointHighlightFill: "rgb(30,136,229)",
                            pointHighlightStroke: "#fff",
                            data: [
JAVASCRIPT;
            for($i = 0; $i < count($bidRate) - 1; $i++)
            {
                $this->return .= $bidRate[$i].", ";
            }
            $this->return .= $bidRate[count($bidRate) - 1];
            $this->return .= <<<JAVASCRIPT
]}]}; 
    var currencyChart = new Chart(ctx).Line(data, options);
</script>
JAVASCRIPT;
            $db->close();
            return $this->return;
        }

    }
    