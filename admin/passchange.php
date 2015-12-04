<?php
    session_start();
    require_once("../include.php");
    if(!isIn() || !isTeacher())
    {
        header("Location: ../");
        exit();
    }
    $passchangeFailed = false;
    if(isset($_POST["currpass"]) && isset($_POST["newpass"]))
    {
        global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
        $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
        $query = "SELECT userid FROM users WHERE userkey = ".$_SESSION["userkey"]." LIMIT 1";
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
            header("Location: addschedule.php");
            exit();
        }
        else
        {
            $passchangeFailed = true;
        }
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Forex Trading Simulator - Change Password</title>
        <script>
<?php
    if($passchangeFailed)
        echo "window.onload = function(){document.getElementById(\"currpass\").focus();};";
?>
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
    </head>

    <body>
        <form id="form1" name="form1" method="post" action="passchange.php" onsubmit="return submitValidation()">
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
    </body>
</html>
