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
     * Description of ChangePassword
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("authenticate/SessionAuthenticate.php");
    require_once("pageElements/header/HeaderFactory.php");
    require_once("pageElements/header/HeaderProduct.php");
    require_once("pageElements/navbar/NavbarFactory.php");
    require_once("pageElements/navbar/NavbarProduct.php");
    require_once("gameElements/DatabasePurger.php");
    require_once("miscellaneous/GenerateRootPath.php");
    require_once("authenticate/PasswordAuthenticate.php");
    require_once("mysql/UniversalConnect.php");

    class ChangePassword
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
                header("Location: ".GenerateRootPath::getRoot(3));
                exit();
            }
            if(isset($_POST["currpass"]) && isset($_POST["newpass"]) && isset($_POST["conpass"]))
            {
                if($_POST["newpass"] === $_POST["conpass"])
                {
                    $db = UniversalConnect::doConnect();
                    $query = "SELECT userid FROM users WHERE userkey=".$_SESSION["userkey"]." LIMIT 1";
                    $result = $db->query($query);
                    $row = $result->fetch_assoc();
                    $userid = $row["userid"];
                    if(PasswordAuthenticate::authenticate($userid, $_POST["currpass"]))
                    {
                        $query = "UPDATE users SET password=\"".password_hash($db->real_escape_string(trim($_POST["newpass"])), PASSWORD_DEFAULT)."\" WHERE userkey=".$_SESSION["userkey"];
                        $db->query($query);
                    }
                }
            }
            DatabasePurger::purge();
            $javascript = <<<JAVASCRIPT
<script>
            var passwordsMatch = false;
            function checkPass()
            {
                if(document.getElementById("newpass").value == "" || document.getElementById("conpass").value == "" || document.getElementById("currpass").value == "" || document.getElementById("newpass").value == null || document.getElementById("conpass").value == null || document.getElementById("currpass").value == null)
                {
                    document.getElementById("checkpassresult").innerHTML = "<p style=\"color:red\">Please fill in all password fields.</p>";
                    passwordsMatch = false;
                }
                else if(document.getElementById("newpass").value == document.getElementById("conpass").value)
                {
                    document.getElementById("checkpassresult").innerHTML = "<p style=\"color:green\">Passwords match!</p>";
                    passwordsMatch = true;
                    console.log("in");
                }
                else
                {
                    document.getElementById("checkpassresult").innerHTML = "<p style=\"color:red\">Passwords do not match</p>";
                    passwordsMatch = false;
                    console.log("out");
                }
            }
            function submitValidation()
            {
                checkPass();
                return passwordsMatch;
            }
</script>
JAVASCRIPT;
            $headerFactory = new HeaderFactory();
            echo $headerFactory->startFactory(new HeaderProduct("Change Password - Forex Trading Simulator", 3, $javascript));
            echo "<body class=\"blue lighten-5\">";
            $navbarFactory = new NavbarFactory();
            echo $navbarFactory->startFactory(new NavbarProduct(3, 40));
            ?>
            <div class="container">
                <div class="card">
                    <div class="row">
                        <div class="card-title col s12 center">
                            Change Password
                        </div>
                    </div>
                    <div class="row">
                        <form id="passChangeForm" name="passChangeForm" method="post" action="./" onsubmit="return submitValidation();">
                            <div class="row">
                                <div class="input-field col s8 push-s2">
                                    <input type="password" name="currpass" id="currpass" />
                                    <label for="currpass">Current Password</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s8 push-s2">
                                    <input type="password" name="newpass" id="newpass" onkeyup="checkpass()" onchange="checkpass()" />
                                    <label for="newpass" >New Password</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s8 push-s2">
                                    <input type="password" name="conpass" id="conpass" onkeyup="checkpass()" onchange="checkpass()" />
                                    <label for="conpass">Confirm Password</label>
                                </div>
                            </div>
                            <div id="checkpassresult"></div>
                            <div class="row">
                                <div class="center">
                                    <button class="btn waves-effect waves-light blue accent-4" type="submit" name="action">Change Password
                                        <i class="material-icons right">send</i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }

    }
    