<?php
    session_start();
    require_once("../include.php");
    if(!isIn() || !isTeacher())
    {
        header("Location: ../");
        exit();
    }
    global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
    $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
    $query = "SELECT name FROM users WHERE userkey=".$_SESSION["userkey"];
    $result = $db->query($query) or die();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            #goRight
            {
                float: right;
            }
            #goLeft
            {
                float: left;
            }
        </style>
        <title>header</title>
    </head>

    <body>
        <span id="goRight"><p><a href="../dashboard/" target="_top">Play the Game</a> <a href="../dashboard/leaderboard/?teachermode=1" target="bravo">Leaderboard</a> <a href="addschedule.php" target="bravo">View Schedules</a> <a href="newuser.php" target="bravo">Add Users</a> <a href="passchange.php" target="bravo">Change Password</a> <a href="./logout/" target="_top">Logout</a></p></span>
        <span id="goLeft">
            <?php
                if($row = $result->fetch_assoc())
                    echo "<p>Hello, ".$row["name"].".";
                $db->close();
                if(isset($_SESSION["remarks"]))
                {
                    echo $_SESSION["remarks"];
                    unset($_SESSION["remarks"]);
                }
            ?>
        </span>
    </body>
</html>
