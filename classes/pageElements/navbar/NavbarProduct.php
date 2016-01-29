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
     * Description of NavbarProduct
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("mysql/UniversalConnect.php");
    require_once("pageElements/ElementProduct.php");
    require_once("authenticate/PrivilegeAuthenticate.php");
    require_once("miscellaneous/GenerateRootPath.php");

    class NavbarProduct implements ElementProduct
    {

        private $pathToRoot, $return, $pageID;

        public function __construct($directoryLayer, $pageID)
        {
            $this->pathToRoot = GenerateRootPath::getRoot($directoryLayer);
            $this->return = "";
            $this->pageID = $pageID;
            /* $pageID reference table:
             * 0 = home
             * 10 = history
             * 20 = leaderboards
             * 30 = admin console
             * 40 = change password
             */
        }

        public function giveProduct()
        {
            $this->return .= <<<HTML
<nav>
    <div id="nav-wrapper" class="blue row">
        <div class="col left">Forex Trading Simulator</div>
        <ul id="nav-mobile" class="col right hide-on-small-and-down">
HTML;
            $this->return .= "<li".($this->pageID === 0 ? " class=\"active\"" : "")."><a href=\"$this->pathToRoot/dashboard/\">Home</a></li>";
            $this->return .= "<li".($this->pageID === 10 ? " class=\"active\"" : "")."><a href=\"$this->pathToRoot/dashboard/history/\">History</a></li>";
            $this->return .= "<li".($this->pageID === 20 ? " class=\"active\"" : "")."><a href=\"$this->pathToRoot/dashboard/leaderboard/\">Leaderboards</a></li>";
            //going back to php
            $PrivAuthWorker = new PrivilegeAuthenticate();
            if($PrivAuthWorker->authenticate())
                $this->return .= "<li".($this->pageID === 30 ? " class=\"active\"" : "")."><a href=\"$this->pathToRoot/admin/\">Admin Console</a></li>";
            //returning to string mode again
            $this->return .= "<li".($this->pageID === 40 ? " class=\"active\"" : "")."><a href=\"$this->pathToRoot/dashboard/changepassword/\">Change Password</a></li>";
            $this->return .= <<<HTML
            <li><a href="$this->pathToRoot/dashboard/logout/">Logout</a></li>
        </ul>
    </div>
</nav>
HTML;
            return $this->return;
        }

    }
    