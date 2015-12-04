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
    if(isset($_POST["dt"]) && isset($_POST["report"]))
    {
        date_default_timezone_set('Asia/Singapore');
        $text = $db->escape_string($_POST["report"]);
        $query = "INSERT INTO news (newstext, time) VALUES ('$text', ".strtotime($_POST["dt"]).")";
        $db->query($query) or die($db->error.$query);
        header("Location: addschedule.php");
        exit();
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Forex Trading Simulator - Schedule Currency Change</title>
        <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" media="screen"
              href="http://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css" />
    </head>

    <body>
        <form id="form1" name="form1" method="post" action="">
            <div id="datetimepicker" class="input-append date">
                <label for="dt">Time: </label><input type="text" name="dt" id="dt"></input>
                <span class="add-on">
                    <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                </span>
            </div>
            <script type="text/javascript"
                    src="https://code.jquery.com/jquery-2.1.3.min.js">
            </script>
            <script type="text/javascript"
                    src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/js/bootstrap.min.js">
            </script>
            <script type="text/javascript"
                    src="http://tarruda.github.com/bootstrap-datetimepicker/assets/js/bootstrap-datetimepicker.min.js">
            </script>
            <script type="text/javascript">
                $('#datetimepicker').datetimepicker({
                    format: 'yyyy/MM/dd hh:mm:ss',
                    language: 'en'
                });
            </script>
            <p>
                <label for="report">News Headline: </label>
                <input type="text" name="report" id="report" width=100/>
            </p>
            <p>
                <input type="submit" name="submit" id="submit" value="Schedule News Report" />
            </p>
        </form>
    </body>
</html>
