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
    
    class NavbarProduct implements ElementProduct
    {
        private $pathToRoot, $basecurr, $return;
        
        public function __construct($directoryLayer)
        {
            if($directoryLayer === 1)
                $this->pathToRoot = ".";
            else
                $this->pathToRoot = "..";
            for($i=2;$i<$directoryLayer;$i++)
            {
                $this->pathToRoot .= "/..";
            }
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
    <div id="nav-wrapper" class="indigo row">
        <div class="col left">Forex Trading Simulator</div>
        <ul id="nav-mobile" class="col right hide-on-small-and-down">
            <li><a href="$this->pathToRoot/dashboard/">Home</a></li> 
            <li><a href="$this->pathToRoot/dashboard/history/">History</a></li>
            <li><a href="$this->pathToRoot/dashboard/leaderboard/">Leaderboards</a></li>
HTML;
            $PrivAuthWorker = new PrivilegeAuthenticate();
            if($PrivAuthWorker->authenticate())
            $this->return .= "<li><a href=\"$this->pathToRoot/admin/\">Admin Console</a></li>";
            $this->return .= <<<HTML
            <li><a href="$this->pathToRoot/dashboard/changepassword/">Change Password</a></li>
            <li><a href="$this->pathToRoot/dashboard/logout/">Logout</a></li>
        </ul>
    </div>
</nav>
    <div class="container">
        <div class="card">
            <div class="card-content center">
HTML;
            $userkey = intval($_SESSION["userkey"]);
            $query = "SELECT name, networth FROM users WHERE userkey=$userkey LIMIT 1";
            $result = $db->query($query) or die();
            $totalvalue = 0;
            if($row = $result->fetch_assoc())
            {
                $this->return .= "<p>Hello, ".$row["name"].".";
                $totalvalue = $row["networth"];
            }
            if(!GameEndedChecker::gameEnded())
                $this->return .= " Your net worth is ".$this->basecurr->getShortName().number_format($totalvalue, 2).".</p>";
            else
            {
                $this->return .= " The game has ended.</p><p>";
                if($totalvalue == 10000000)
                {
                    $this->return .= " You did not make or lose any money.</p>";
                }
                else if($totalvalue > 10000000)
                {
                    $this->return .= " You made a profit of ".$this->basecurr->getShortName().number_format($totalvalue - 10000000, 2)."!</p>";
                }
                else
                {
                    $this->return .= " You lost ".$this->basecurr->getShortName().number_format(0 - ($totalvalue - 10000000), 2).".</p>";
                }
            }
            $this->return .= "</div></div>";
            $db->close();
            return $this->return;
        }
    }
    