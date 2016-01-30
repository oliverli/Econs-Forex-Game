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
     * Description of ProfileCardProduct
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("mysql/UniversalConnect.php");
    require_once("pageElements/ElementProduct.php");
    require_once("gameElements/GameEndedChecker.php");
    require_once("gameElements/trading/BaseCurrency.php");
    require_once("miscellaneous/GenerateRootPath.php");

    class ProfileCardProduct implements ElementProduct
    {

        private $return, $basecurr, $networth, $name, $pathToRoot;

        public function __construct($directoryLayer)
        {
            if(session_status() === PHP_SESSION_NONE)
            {
                session_start();
            }
            $this->pathToRoot = GenerateRootPath::getRoot($directoryLayer);
            $userkey = intval($_SESSION["userkey"]);
            $db = UniversalConnect::doConnect();
            $query = "SELECT name, networth FROM users WHERE userkey=$userkey LIMIT 1";
            $result = $db->query($query) or die();
            if($row = $result->fetch_assoc())
            {
                $this->name = $row["name"];
                $this->networth = $row["networth"];
            }
            $this->basecurr = new BaseCurrency();
            $this->return = "";
            $db->close();
        }

        public function giveProduct()
        {
            $this->return .= <<<HTML
            <div id="profile" class="card small hoverable">
                    <div class="card-image">
	                    <img src="$this->pathToRoot/img/joker.jpg" class="activator">
	                    <span class="card-title activator">$this->name</span>
	                </div>
	                <div class="card-content">
	                    <p>
	                        <i class="material-icons right activator">library_books</i>
                            <b>Account Balance</b>
                            <span>
HTML;
            if(!GameEndedChecker::gameEnded())
            {
                // USD
                $this->return .= "<br /><i class=\"material-icons tiny left green-text\">trending_up</i>5.00 ";
                $this->return .= "".$this->basecurr->getShortName().number_format($this->networth, 2);
                
                // JPY
                $this->return .= "<br /><i class=\"material-icons tiny left red-text\">trending_down</i>-3.14 ";
                $this->return .= "".$this->basecurr->getShortName().number_format($this->networth, 2);
            }
            else
            {
                $this->return .= "Game Over. You ended off with a total of ".$this->basecurr->getShortName().number_format($this->networth, 2).".";
            }
            $this->return .= <<<HTML
                        </span>
                       </p> 
	                </div>
	                <div class="card-reveal">
	                    <span class="card-title grey-text text-darken-4">Trading Statistics<i class="material-icons right">close</i></span>
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
            $this->return .= "</div></div>";
            return $this->return;
        }

    }
    