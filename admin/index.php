<?php
    session_start();
    require_once("../include.php");
    if(!isIn() || !isTeacher())
    {
        header("Location: ../");
        exit();
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Forex Trading Simulator - Admin Console</title>
    </head>

    <frameset rows="40,*">
        <frame src="header.php" frameborder="0" name="alpha" marginheight=0 noresize scrolling="no" border=0/>
        <frame src="addschedule.php" frameborder="0" name="bravo" scrolling="yes" border=0/>
    </frameset>
    <noframes></noframes>
</html>
