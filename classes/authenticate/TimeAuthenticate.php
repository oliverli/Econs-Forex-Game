<?php

/*
     * The MIT License
     *
     * Copyright 2015 Li Yicheng, Sun Yudong, and Walter Kong.
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
     * Description of TimeAuthenticate
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("mysql/UniversalConnect.php");
    require_once("IAuthenticator.php");
    
    class TimeAuthenticate implements IAuthenticator
    {
        public function authenticate()
        {
            $db = UniversalConnect::doConnect();
            date_default_timezone_set('Asia/Singapore');
            $query = "SELECT starttime FROM startendtime WHERE timeid = 1 LIMIT 1";
            $result = $db->query($query);
            $row = $result->fetch_assoc();
            if(time() < $row["starttime"])
                return false;
            else
                return true;
        }
    }
    