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
        <style>
            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
            }
            th, td {
                padding: 5px;
                text-align:center;
            }
        </style>
        <script language="javascript" type="text/javascript">
            <!--
        function popitup(url)
            {
                newwindow = window.open(url, 'Scheduled Currency Values', 'width=600,height=400');
                if(window.focus)
                {
                    newwindow.focus()
                }
                return false;
            }

            // -->
        </script>
        <title>Forex Trading Simulator - Add Schedule</title>
        <?php
            global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
            date_default_timezone_set('Asia/Singapore');
            $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
        ?>
    </head>

    <body>
        <h1>Scheduled Events</h1>
        <table style="width:100%">
            <tr>
                <th>Time</th>
                <th>Type</th>
                <th>Values/Headline</th>
                <th>Delete</th>
            </tr>
            <?php
                $query = "SELECT starttime, endtime FROM startendtime WHERE timeid=1 LIMIT 1";
                $result = $db->query($query);
                $row = $result->fetch_assoc();
                $starttime = $row["starttime"];
                $endtime = $row["endtime"];
                $dt = new DateTime();
                if($result->num_rows <= 0)
                    $starttime = $endtime = -1;
                //TODO: implement multiple-table fetching
                //see https://stackoverflow.com/questions/6790479/select-separate-rows-from-two-tables-order-by-date
                if($starttime === -1 && $endtime === -1)
                {
                    echo "<tr><td colspan='4'>No start time in system</td></tr>";
                }
                else
                {
                    $dt->setTimestamp($starttime);
                    echo "<tr><td>".$dt->format("d/m/Y H:i:s")."</td>";
                    echo "<td>Game Starts</td>";
                    echo "<td>Game begins and students are able to log in and start trading.</td>";
                    echo "<td><a href=\"delete.php?deletekey=1&type=1\">Delete</a></td></tr>";
                }
                $currgroup = -1;
                $finaltable = array();
                $valuechanges = array();
                $news = array();

                $query = "SELECT changegroup, time FROM valuechanges ORDER BY time ASC";
                $result = $db->query($query);
                while($row = $result->fetch_assoc())
                {
                    if($row["changegroup"] == $currgroup)
                        continue;
                    else
                    {
                        $rowarr = array();
                        $rowarr[0] = $row["time"];
                        $rowarr[1] = 2;
                        $rowarr[2] = "<a href=\"schedulecurrval.php?curr=".$row["changegroup"]."\" target=\"popup\" onclick=\"return popitup('schedulecurrval.php?curr=".$row["changegroup"]."')\">View currency values</a>";
                        $rowarr[3] = $row["changegroup"];
                        array_push($valuechanges, $rowarr);
                        $currgroup = $row["changegroup"];
                    }
                }

                $query = "SELECT newstext, time, newsid FROM news ORDER BY time ASC";
                $result = $db->query($query);
                while($row = $result->fetch_assoc())
                {
                    $rowarr = array();
                    $rowarr[0] = $row["time"];
                    $rowarr[1] = 3;
                    $rowarr[2] = $row["newstext"];
                    $rowarr[3] = $row["newsid"];
                    array_push($news, $rowarr);
                }

                $newsPos = $valuePos = 0;
                while($newsPos < count($news) && $valuePos < count($valuechanges)) //serves as sanity check
                {
                    if($news[$newsPos][0] < $valuechanges[$valuePos][0])
                    {
                        array_push($finaltable, $news[$newsPos]);
                        $newsPos++;
                    }
                    else
                    {
                        array_push($finaltable, $valuechanges[$valuePos]);
                        $valuePos++;
                    }
                }
                for(; $newsPos < count($news); $newsPos++)
                {
                    array_push($finaltable, $news[$newsPos]);
                }
                for(; $valuePos < count($valuechanges); $valuePos++)
                {
                    array_push($finaltable, $valuechanges[$valuePos]);
                }

                foreach($finaltable as $row)
                {
                    $dt->setTimestamp($row[0]+$starttime);
                    echo "<tr><td>".$dt->format("d/m/Y H:i:s")."</td>";
                    echo "<td>".($row[1] == 2 ? "Currency Value Change" : "News")."</td>";
                    echo "<td>".$row[2]."</td>";
                    echo "<td><a href=\"delete.php?deletekey=".$row[3]."&type=".$row[1]."\">Delete</a></td></tr>";
                }

                if($newsPos === 0 && $valuePos === 0)
                    echo "<tr><td colspan='4'>No scheduled currency changes and news.</td></tr>";
                if($starttime === -1 && $endtime === -1)
                {
                    echo "<tr><td colspan='4'>No end time in system</td></tr>";
                }
                else
                {
                    $dt = new DateTime();
                    $dt->setTimestamp($endtime);
                    echo "<tr><td>".$dt->format("d/m/Y H:i:s")."</td>";
                    echo "<td>Game Ends</td>";
                    echo "<td>Game ends. Students are no longer able to trade and their final profit/loss are shown.</td>";
                    echo "<td><a href=\"delete.php?deletekey=1&type=4\">Delete</a></td></tr>";
                }
            ?>
            <tr>
                <td><a href="addschedule2.php">Schedule Currency Change</a></td><td><?php
                        //if($startendcount != 0)
                        echo "<a href=\"addschedule4.php\">Schedule Game Start/End</a>";
                    ?></td><td><a href="addschedule3.php">Schedule News Report</a></td>
                <td><a href="delete.php?deletekey=-1&type=2" onclick="return confirm('This will delete ALL your entered events! You will have to enter all values again. Are you sure?')">Delete All</a></td>
            </tr>
        </table>
    </body>
</html>