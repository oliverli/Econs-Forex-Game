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
    if(isset($_POST["ds"]) && isset($_POST["de"]))
    {
        date_default_timezone_set('Asia/Singapore');
        $starttime = strtotime($_POST["ds"]);
        $endtime = strtotime($_POST["de"]);
        if($endtime < $starttime)
        {
            header("Location: addschedule.php");
            exit();
        }
        $query = "TRUNCATE startendtime";
        $db->query($query) or die($db->error.$query);
        $query = "INSERT INTO startendtime (starttime, endtime) VALUES ($starttime, $endtime)";
        $db->query($query) or die($db->error.$query);
        $query = "UPDATE valuechanges SET time = $starttime WHERE time<$starttime";
        $db->query($query) or die($db->error.$query);
        $query = "UPDATE valuechanges SET time = $endtime WHERE time>$endtime";
        $db->query($query) or die($db->error.$query);
        $query = "UPDATE news SET time = $starttime WHERE time<$starttime";
        $db->query($query) or die($db->error.$query);
        $query = "UPDATE news SET time = $starttime WHERE time>$endtime";
        $db->query($query) or die($db->error.$query);
        header("Location: addschedule.php");
        exit();
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Forex Trading Simulator - Schedule Game Start/End</title>
        <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" media="screen"
              href="http://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css" />
    </head>

    <body>
        <form id="form1" name="form1" method="post" action="">
            <div id="datetimepicker" class="input-append date">
                <p>
                    <label for="ds">Game Start Time: </label><input type="text" name="ds" id="ds"></input>
                    <span class="add-on">
                        <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                    </span>
                </p>
            </div>
            <div id="datetimepicker2" class="input-append date">
                <p>
                    <label for="de">Game End Time: </label><input type="text" name="de" id="de"></input>
                    <span class="add-on">
                        <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                    </span>
                </p>
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
                $('#datetimepicker2').datetimepicker({
                    format: 'yyyy/MM/dd hh:mm:ss',
                    language: 'en'
                });
            </script>
            <p>
                <input type="submit" name="submit" id="submit" value="Schedule Start and End Time" />
            </p>
        </form>
    </body>
</html>
