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
     * Description of LoginHome
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("authenticate/SessionAuthenticate.php");
    require_once("authenticate/PasswordAuthenticate.php");
    require_once("miscellaneous/FormatTimePassed.php");
    require_once("pageElements/header/HeaderFactory.php");
    require_once("pageElements/header/HeaderProduct.php");
    require_once("mysql/UniversalConnect.php");
    require_once("miscellaneous/GenerateRootPath.php");

    class LoginHome
    {

        private $authenticationStatus = -1;

        public function __construct()
        {
            //Checks if user is logged in or has posted passwords. Redirects as appropriate.
            $SessAuthWorker = new SessionAuthenticate();
            if($SessAuthWorker->authenticate())
            {
                header("Location: ".GenerateRootPath::getRoot(1)."/dashboard/");
                exit();
            }
            if(isset($_POST["username"]) && isset($_POST["password"]))
            {
                $PassAuthWorker = new PasswordAuthenticate();
                $this->authenticationStatus = $PassAuthWorker->authenticate($_POST["username"], $_POST["password"]);
                if($this->authenticationStatus === 1)
                {
                    if(session_status() === PHP_SESSION_NONE)
                    {
                        session_start();
                    }
                    $db = UniversalConnect::doConnect();
                    $query = "SELECT userkey, usertype FROM users WHERE userid=\"".$db->real_escape_string(trim($_POST["username"]))."\" LIMIT 1";
                    $result = $db->query($query);
                    if($result->num_rows < 1) die("An unexpected error has occurred. The problem should go away by itself after some time.");
                    $row = $result->fetch_assoc();
                    $_SESSION["userkey"] = $row["userkey"];
                    $_SESSION["usertype"] = $row["usertype"];
                    header("Location: ".GenerateRootPath::getRoot(1)."/dashboard/");
                    exit();
                }
            }

            //generates header from <!DOCTYPE html> all the way to </head>
            //Title of the page is set in constructor i.e. new HeaderProduct("Title of page here");
            $headerFactory = new HeaderFactory();
            echo $headerFactory->startFactory(new HeaderProduct("Login - Forex Trading Simulator ", 1));
            echo <<<HTML
    <body class="blue lighten-5">
        <div class="container">
            <div id="login-card" class="pageCenter card
HTML;
            if($this->authenticationStatus === 0)
                echo " failed";
            echo <<<HTML
">
                <div class="center">
                    <h3 class="title">Forex Trading Simulator</h3>
                    <h5 class="title top-margin">Master Forex, For Free</h5>
                </div>
                <form id="loginform" name="loginform" method="post">
                    <div class="row">
                        <div class="input-field col s12 m10 l10 push-m1 push-l1">
                            <i class="material-icons prefix">account_circle</i>
HTML;
            echo "<input type=\"text\" required=\"\" name=\"username\" id=\"username\"";
            if($this->authenticationStatus === 2 || $this->authenticationStatus === 0)
                echo " value=\"".htmlentities($_POST["username"], ENT_QUOTES, "UTF-8")."\"";
            echo "/>";
            echo <<<HTML
                            <label for="username">Username: </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 m10 l10 push-m1 push-l1">
                            <i class="material-icons prefix">vpn_key</i>
                            <label for="password">Password: </label>
                            <input type="password" name="password" id="password" />
                        </div>
                    </div>
                    <div class="row input-field center" id="Submit">
                        <button class="btn waves-effect waves-light pink accent-4" type="submit" name="action">Login
                        </button>
                    </div>
                </form>
HTML;
            if($this->authenticationStatus === 2)
            {
                $db = new UniversalConnect();
                $result = $db->query("SELECT starttime FROM startendtime LIMIT 1");
                $row = $result->fetch_assoc();
                $startTime = $row["starttime"];
                echo "<script>alert('The game has not started yet. It starts in ".FormatTimePassed::format($startTime).".');window.onload = function(){document.getElementById(\"password\").focus();};</script>";
                $db->close();
            }
            echo <<<HTML
            </div>
        </div>
    </body>
</html>
HTML;
        }

    }
