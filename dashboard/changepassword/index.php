<?php
    session_start();
    require_once("../../include.php");
    if(!isIn())
    {
        header("Location: ../../");
        exit();
    }
    global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
    $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
    purgeDatabase();
    $query = "SELECT name FROM users WHERE userkey=".$_SESSION["userkey"]." LIMIT 1";
    $result = $db->query($query) or die();
    $passchangeFailed = false;
    if(isset($_POST["currpass"]) && isset($_POST["newpass"]))
    {
        $query = "SELECT userid FROM users WHERE userkey = ".$_SESSION["userkey"]."LIMIT 1";
        $result = $db->query($query) or die($db->error);
        $row = $result->fetch_assoc();
        if(passCheck($_POST["currpass"], $row["userid"]))
        {
            $salt = generateSalt();
            $password = passHash($_POST["newpass"], $row["userid"], $salt);
            $salt = $db->escape_string($salt);
            $query = "UPDATE users SET password='$password', salt='$salt' WHERE userkey=".$_SESSION["userkey"];
            $db->query($query) or die($db->error);
            $_SESSION["remarks"] = "<script>alert('Password changed successfully.');</script>";
            header("Location: ../");
            exit();
        }
        else
        {
            $passchangeFailed = true;
            echo "<script>window.onload = function(){document.getElementById(\"currpass\").focus();};</script>";
        }
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Forex Trading Simulator - Change Password</title>
        <link rel="stylesheet" type="text/css" href="../../css/main.css">
            <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
            <script>
                $(document).ready(function ()
                {
                    $.ajaxSetup({cache: false});
                    setInterval(function ()
                    {
                        $("#goLeft").load('../../ajax/headerhandler.php');
                    }, 30000);
                });
                var passwordsMatch = false;
                function checkpass()
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
                        document.getElementById("checkpassresult").innerHTML = "<p style=\"color:red\">Passwords do not match!</p>";
                        passwordsMatch = false;
                        console.log("out");
                    }
                }
                function submitValidation()
                {
                    checkpass();
                    return passwordsMatch;
                }
            </script>
            <?php
                if(isset($remarks))
                    echo $remarks;
            ?>
    </head>
    <body>
        <div class="page-module">
            <div style="width:100%;">
                <div id="goRight"><p><a href="../">Home</a> <a href="../history/">History</a> <a href="../leaderboard/">Leaderboards</a> <?php if(isTeacher()) echo "<a href=\"../../admin/\">Admin Console</a>"; ?> <a href="./">Change Password</a> <a href="../logout/">Logout</a></p></div>
                <div id="goLeft">
                    <?php
                        $userkey = intval($_SESSION["userkey"]);
                        if($userkey <= 0)
                            return;
                        $query = "SELECT name, networth FROM users WHERE userkey=$userkey LIMIT 1";
                        $result = $db->query($query) or die();
                        $totalvalue = 0;
                        if($row = $result->fetch_assoc())
                        {
                            echo "<p>Hello, ".$row["name"].".";
                            $totalvalue = $row["networth"];
                        }
                        $gameEnded = gameEnded();
                        $query = "SELECT shortname FROM currency WHERE currencyid = 1 LIMIT 1";
                        $result = $db->query($query) or die($db->error);
                        while($row = $result->fetch_assoc())
                            $basecurrabbv = $row["shortname"];
                        if(!$gameEnded)
                            echo " Your net worth is ".$basecurrabbv.number_format($totalvalue, 2).".</p>";
                        else
                        {
                            echo " The game has ended.</p><p>";
                            if($totalvalue == 10000000)
                            {
                                echo " You did not make or lose any money.</p>";
                            }
                            else if($totalvalue > 10000000)
                            {
                                echo " You made a profit of ".$basecurrabbv.number_format($totalvalue - 10000000, 2)."!</p>";
                            }
                            else
                            {
                                echo " You lost ".$basecurrabbv.number_format(0 - ($totalvalue - 10000000), 2).".</p>";
                            }
                        }
                    ?>
                </div>
            </div>
            <div style="clear:both"></div>
            <hr />
            <form id="form1" name="form1" method="post" action="" onsubmit="return submitValidation();">
                <p>
                    <label for="currpass">Current Password: </label>
                    <input type="password" name="currpass" id="currpass" />
                </p>
                <p>
                    <label for="newpass" onchange="checkpass()">New Password: </label>
                    <input type="password" name="newpass" id="newpass" onkeyup="checkpass()" />
                </p>
                <p>
                    <label for="conpass">Confirm Password: </label>
                    <input type="password" name="conpass" id="conpass" onkeyup="checkpass()"/>
                </p>
                <span id="checkpassresult"><p style="color:red">Please fill in all password fields.</p></span>
                <p>
                    <input type="submit" name="submit" id="submit" value="Change Password" />
                </p>
            </form>
            <?php
                if($passchangeFailed)
                    echo "<script>alert('Your current password was incorrect.');</script>";
            ?>
        </div>
    </body>
</html>
