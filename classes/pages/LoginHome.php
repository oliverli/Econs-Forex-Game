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
     * Description of LoginHome
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("authenticate/SessionAuthenticate.php");
    require_once("authenticate/PasswordAuthenticate.php");
    require_once("miscellenous/FormatTimePassed.php");
    require_once("pageElements/header/HeaderFactory.php");
    require_once("pageElements/header/HeaderProduct.php");
    require_once("mysql/UniversalConnect.php");
    
    class LoginHome
    {
        private $authenticationStatus = -1;
        
        public function __construct()
        {
            //Checks if user is logged in or has posted passwords. Redirects as appropriate.
            $SessAuthWorker = new SessionAuthenticate();
            if($SessAuthWorker->authenticateSession())
            {
                header("Location: ./dashboard/");
                exit();
            }
            if(isset($_POST["username"]) && isset($_POST["password"]))
            {
                $PassAuthWorker = new PasswordAuthenticate();
                $this->authenticationStatus = $PassAuthWorker->authenticatePassword();
                if($this->authenticationStatus === 1)
                {
                    header("Location: ./dashboard/");
                    exit();
                }
            }
            
            //generates header from <!DOCTYPE html> all the way to </head>
            //Title of the page is set in constructor i.e. new HeaderProduct("Title of page here");
            $headerFactory = new HeaderFactory();
            echo $headerFactory->startFactory(new HeaderProduct("Forex Trading Simulator - Login"));
            echo <<<PAGE
    <body class="indigo lighten-5">
        <nav>
            <div class="nav-wrapper indigo row">
                <div class="col s12">Forex Trading Simulator</div>
            </div>
        </nav>
        <div class="container">
            <div id="login-card" class="card">
                <div class="center" id="Logo"><img src="./img/hci.png" height="50px" style="opacity:0.87;"/></div>
                <form id="loginform" name="loginform" method="post">
                    <div class="row">
                        <div class="input-field col s12 m10 l10 push-m1 push-l1">
                            <i class="material-icons prefix">account_circle</i>
PAGE;
                            echo "<input type=\"text\" required=\"\" name=\"username\" id=\"username\"";
                            if($this->authenticationStatus === 2 || $this->authenticationStatus === 0) echo " value=\"".htmlentities($_POST["username"], ENT_QUOTES, "UTF-8")."\"";
                            echo "/>";
                            echo <<<PAGE
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
                        <button class="btn waves-effect waves-light indigo accent-4" type="submit" name="action">Login
                        </button>
                    </div>
                </form>
PAGE;
                    if($this->authenticationStatus === 0)
                        echo "<script>alert('Login failed - username or password incorrect.');</script>";
                    if($this->authenticationStatus === 2)
                    {
                        $timeFormatWorker = new FormatTimePassed();
                        $db = new UniversalConnect();
                        $result = $db->query("SELECT starttime FROM startendtime LIMIT 1");
                        $row = $result->fetch_assoc();
                        $startTime = $row["starttime"];
                        echo "<script>alert('The game has not started yet. It starts in ".$timeFormatWorker->format($startTime).".');window.onload = function(){document.getElementById(\"password\").focus();};</script>";
                        $db->close();
                    }
echo <<<PAGE
            </div>
        </div>
    </body>
</html>
PAGE;
        }
    }
    