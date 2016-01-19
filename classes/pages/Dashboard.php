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
     * Description of Dashboard
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    
    require_once("authenticate/SessionAuthenticate.php");
    require_once("pageElements/header/HeaderFactory.php");
    require_once("pageElements/header/HeaderProduct.php");
    require_once("pageElements/navbar/NavbarFactory.php");
    require_once("pageElements/navbar/NavbarProduct.php");
    require_once("pageElements/currencyBoard/CurrencyBoardFactory.php");
    require_once("pageElements/currencyBoard/CurrencyBoardProduct.php");
    require_once("pageElements/newsBoard/NewsBoardFactory.php");
    require_once("pageElements/newsBoard/NewsBoardProduct.php");
    require_once("gameElements/DatabasePurger.php");
    require_once("miscellaneous/GenerateRootPath.php");

    class Dashboard
    {

        public function __construct()
        {
            if(session_status() === PHP_SESSION_NONE)
            {
                session_start();
            }
            $SessAuthWorker = new SessionAuthenticate();
            if(!$SessAuthWorker->authenticate())
            {
                header("Location: ".GenerateRootPath::getRoot(2));
                exit();
            }
            DatabasePurger::purge();
            if(isset($_SESSION["remarks"]))
            {
                $remarks = $_SESSION["remarks"];
                unset($_SESSION["remarks"]);
            }
            $headerFactory = new HeaderFactory();
            if(isset($remarks))
                echo $headerFactory->startFactory(new HeaderProduct("Dashboard - Forex Trading Simulator", 2, $remarks));
            else
                echo $headerFactory->startFactory(new HeaderProduct("Dashboard - Forex Trading Simulator", 2));
            ?>
            <body class="blue lighten-5">
                <?php $navbarFactory = new NavbarFactory();
                echo $navbarFactory->startFactory(new NavbarProduct(2)); ?>
                <div class="row">
                    <div class="col s4"><div class="card">
                        <div class="card-image">
                            <img src="../img/user.jpg" class="activator">
                            <span class="card-title">Username</span>
                        </div>
                        <div class="card-content">
                            <p class="activator">
                                <i class="material-icons right">more_vert</i>
                                I am a very simple card. I am good at containing small bits of information. I am convenient because I require little markup to use effectively.
                            </p>
                        </div>
                        <div class="card-action">
                            <a href="#">This is a link</a>
                        </div>
                        <div class="card-reveal">
                            <span class="card-title grey-text text-darken-4">Card Title<i class="material-icons right">close</i></span>
                            <p>Here is some more information about this product that is only revealed once clicked on.</p>
                        </div>
                    </div></div>
                    
                    <div class="col s8">
                        <?php $currencyBoardFactory = new CurrencyBoardFactory();
                        echo $currencyBoardFactory->startFactory(new CurrencyBoardProduct()); ?>
                    </div>
                    
                    <div class="col s6">
                        <?php $newsFactory = new NewsBoardFactory();
                        echo $newsFactory->startFactory(new NewsBoardProduct(30)); ?>
                    </div>
                </div>
            </body><?php
        }

    }
?>