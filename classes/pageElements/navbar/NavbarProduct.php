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
     * Description of NavbarProduct
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("mysql/UniversalConnect.php");
    require_once("pageElements/ElementProduct.php");
    require_once("authenticate/PrivilegeAuthenticate.php");
    require_once("gameElements/GameEndedChecker.php");
    require_once("gameElements/trading/BaseCurrency.php");
    require_once("miscellaneous/GenerateRootPath.php");

    class NavbarProduct implements ElementProduct
    {

        private $pathToRoot, $basecurr, $return, $networth, $name;

        public function __construct($directoryLayer)
        {
            $this->pathToRoot = GenerateRootPath::getRoot($directoryLayer);
            $this->basecurr = new BaseCurrency();
            $this->return = "";
        }

        public function giveProduct()
        {
            if(session_status() === PHP_SESSION_NONE)
            {
                session_start();
            }
            $db = UniversalConnect::doConnect();
            $this->return .= <<<HTML
<nav>
    <div id="nav-wrapper" class="blue row">
        <div class="col left">Forex Trading Simulator</div>
        <ul id="nav-mobile" class="col right hide-on-small-and-down">
            <li><a href="$this->pathToRoot/dashboard/">Home</a></li>
            <li><a href="$this->pathToRoot/dashboard/history/">History</a></li>
            <li><a href="$this->pathToRoot/dashboard/leaderboard/">Leaderboards</a></li>
HTML;
            //going back to php
            $PrivAuthWorker = new PrivilegeAuthenticate();
            if($PrivAuthWorker->authenticate())
                $this->return .= "<li><a href=\"$this->pathToRoot/admin/\">Admin Console</a></li>";
            $userkey = intval($_SESSION["userkey"]);
            $query = "SELECT name, networth FROM users WHERE userkey=$userkey LIMIT 1";
            $result = $db->query($query) or die();
            if($row = $result->fetch_assoc())
            {
                $this->name = $row["name"];
                $this->networth = $row["networth"];
            }
            //returning to string mode again
            $this->return .= <<<HTML
            <li><a href="$this->pathToRoot/dashboard/changepassword/">Change Password</a></li>
            <li><a href="$this->pathToRoot/dashboard/logout/">Logout</a></li>
        </ul>
    </div>
</nav>
    <div class="container">
        <div class="row">
            <div class="col s4"><div class="card">
                    <div class="card-image">
                        <img src="$this->pathToRoot/img/user.jpg" class="activator">
                        <span class="card-title">$this->name</span>
                    </div>
                    <div class="card-content">
                        <p class="activator">
                            <i class="material-icons right">more_vert</i>
HTML;
            if(!GameEndedChecker::gameEnded())
            {
                $this->return .= "Hello! You currently own a total of ".$this->basecurr->getShortName().number_format($this->networth, 2).".";
            }
            else
            {
                $this->return .= "Hello! The game has ended. You ended off with a total of ".$this->basecurr->getShortName().number_format($this->networth, 2).".";
            }
            $this->return .= <<<HTML
                        </p>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">Some Title<i class="material-icons right">close</i></span>
HTML;
            if($this->networth === 10000000)
            {
                $this->return .= "<p>You did not make or lose any money.</p>";
            }
            else if($this->networth > 10000000)
            {
                $this->return .= "<p>You made a profit of ".$this->basecurr->getShortName().number_format($this->networth - 10000000, 2)."! Congratulations!</p>";
            }
            else
            {
                $this->return .= "<p>You lost ".$this->basecurr->getShortName().number_format(0 - ($this->networth - 10000000), 2).".</p>";
                if(GameEndedChecker::gameEnded())
                {
                    $this->return .= "<p>Better luck next time!</p>";
                }
                else
                {
                    $this->return .= "<p>There's still time, try harder!</p>";
                }
            }
            $this->return .= <<<HTML
                    </div>
                </div></div></div>
HTML;
            $db->close();
            return $this->return;
        }

    }
    