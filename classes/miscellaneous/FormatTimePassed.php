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
     * Description of FormatTimePassed
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    class FormatTimePassed
    {

        public function format($originalTime)
        {
            if(empty($originalTime))
            {
                return "No date provided";
            }
            $periods = array("sec", "min", "hr", "day", "week", "month", "year", "decade");
            $lengths = array("60", "60", "24", "7", "4.35", "12", "10");
            date_default_timezone_set('Asia/Singapore');
            $now = time();
            // is it future date or past date
            if($now > $originalTime)
            {
                $difference = $now - $originalTime;
                $tense = " ago";
            }
            else
            {
                $difference = $originalTime - $now;
                $tense = "";
            }
            for($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++)
            {
                $difference /= $lengths[$j];
            }
            $difference = round($difference);
            if($difference != 1)
            {
                $periods[$j].= "s";
            }
            return "$difference $periods[$j]{$tense}";
        }

    }
    