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
    if(!empty($_POST))
    {
        date_default_timezone_set('Asia/Singapore');
        $currgroupnum = 0;
        $query = "SELECT changegroup FROM valuechanges ORDER BY changegroup DESC LIMIT 1";
        $result = $db->query($query) or die($db->error);
        if($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            $changegroupnum = $row["changegroup"] + 1;
        }
        else
        {
            $changegroupnum = 0;
        }
        $query = "SELECT currencyid FROM currency WHERE currencyid != 1";
        $result = $db->query($query) or die($db->error);
        while($row = $result->fetch_assoc())
        {
            if(isset($_POST["buyvalue".$row["currencyid"]]) && isset($_POST["sellvalue".$row["currencyid"]]))
            {
                $newbuyvalue = $_POST["buyvalue".$row["currencyid"]];
                $newsellvalue = $_POST["sellvalue".$row["currencyid"]];
                $query = "INSERT INTO valuechanges (currencyid, newbuyvalue, newsellvalue, time, changegroup) VALUES ('".$row["currencyid"]."', $newbuyvalue, $newsellvalue, ".strtotime($_POST["dt"]).", $changegroupnum)";
                $db->query($query) or die($db->error.$query);
            }
        }
        header("Location: addschedule.php");
        exit();
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Forex Trading Simulator - Schedule Currency Change</title>
        <style>
            .border{
                border: 1px solid black;
                border-collapse: collapse;
            }
            th, td {
                padding: 5px;
                text-align:center;
            }
        </style>
        <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" media="screen"
              href="http://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css" />
    </head>

    <body>
        <form id="form1" name="form1" method="post" action="addschedule2.php">
            <div id="datetimepicker" class="input-append date">
                <label for="dt">Time: </label><input type="text" name="dt" id="dt"<?php
                    $query = "SELECT time FROM valuechanges ORDER BY changegroup DESC LIMIT 1";
                    $result = $db->query($query);
                    $row = $result->fetch_assoc();
                    $timefill = $row["time"] + 60;
                    echo " value=\"".date("Y/m/d H:i:s", $timefill)."\"";
                ?>></input>
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
            <table style="width:100%" class="border">
                <tr class="border">
                    <th class="border">Name</th>
                    <th class="border">Abbreviation</th>
                    <th class="border">New Buying Value</th>
                    <th class="border">New Selling Value</th>
                </tr>
                <?php
                    /* $basecurrabbv = "";
                      $query = "SELECT shortname FROM currency WHERE currencyid = 1";
                      $result = $db->query($query) or die($db->error);
                      while($row = $result->fetch_assoc())
                      {
                      $basecurrabbv = $row["shortname"];
                      } */
                    $query = "SELECT name,shortname,currencyid FROM currency WHERE currencyid != 1 ORDER BY name ASC";
                    $result = $db->query($query) or die($db->error);
                    while($row = $result->fetch_assoc())
                    {
                        echo "<tr class=\"border\">";
                        echo "<td class=\"border\">".$row["name"]."</td>";
                        echo "<td class=\"border\">".$row["shortname"]."</td>";
                        echo "<td class=\"border\"><input type=\"text\" name=\"buyvalue".$row["currencyid"]."\"></input></td>";
                        echo "<td class=\"border\"><input type=\"text\" name=\"sellvalue".$row["currencyid"]."\"></input></td>";
                    }
                    $db->close();
                ?>
            </table>
            <input type="submit" name="submit" id="submit" value="Schedule Currency Change" />
        </form>
    </body>
</html>
