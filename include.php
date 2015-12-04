<?php

    $mysqllocation = "localhost";
    $mysqlusername = "redacted";
    $mysqlpassword = "redacted";
    $mysqldatabase = "forex";

    function generateSalt()
    {
        $length = rand(30, 60);
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_=+[]{}\|:<>,./?`~';
        $charactersLength = strlen($characters);
        $randomString = '';
        for($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function passHash($password, $username, $salt)
    {
        return hash("sha512", $username.$password.$salt);
    }

    function passCheck($password, $username)
    {
        global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
        $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
        $username = $db->real_escape_string($username);
        $query = "SELECT password, salt, userkey, usertype FROM users WHERE userid='$username' LIMIT 1";
        $result = $db->query($query) or die($db->error);
        while($row = $result->fetch_assoc())
        {
            if($row["password"] === passHash($password, $username, $row["salt"]))
            {
                $_SESSION["userkey"] = $row["userkey"];
                $_SESSION["usertype"] = $row["usertype"];
                $db->close();
                return true;
            }
            $db->close();
            return false;
        }
    }

    function isIn()
    {
        if(isset($_SESSION["userkey"]) && isset($_SESSION["usertype"]))
        {
            if(isTeacher())
            {
                return true;
            }
            else
            {
                date_default_timezone_set('Asia/Singapore');
                return gameStartTime() <= time() ? true : false;
            }
        }
        return false;
    }

    function gameStartTime()
    {
        global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
        $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
        date_default_timezone_set('Asia/Singapore');
        $query = "SELECT starttime FROM startendtime WHERE timeid = 1 LIMIT 1";
        $result = $db->query($query);
        $row = $result->fetch_assoc();
        return $row["starttime"];
    }

    function isTeacher()
    {
        if(isset($_SESSION["usertype"]) && $_SESSION["usertype"] == 2)
            return true;
        return false;
    }

    function gameEnded()
    {
        date_default_timezone_set('Asia/Singapore');
        global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
        $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
        $query = "SELECT endtime FROM startendtime WHERE timeid=1 LIMIT 1";
        $result = $db->query($query) or die($db->error);
        $row = $result->fetch_assoc();
        if($row["endtime"] < time())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function nicetime($unix_date)
    {
        if(empty($unix_date))
        {
            return "No date provided";
        }
        $periods = array("sec", "min", "hr", "day", "week", "month", "year", "decade");
        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");
        date_default_timezone_set('Asia/Singapore');
        $now = time();
        // is it future date or past date
        if($now > $unix_date)
        {
            $difference = $now - $unix_date;
            $tense = " ago";
        }
        else
        {
            $difference = $unix_date - $now;
            $tense = "";
        }
        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++)
        {
            $difference /= $lengths[$j];
        }
        $difference = round($difference);
        if($difference != 1)
        {
            $periods[$j].= "s";
        }
        return "$difference $periods[$j]{$tense}";
    }

    function purgeDatabase()
    {
        ignore_user_abort(true);
        global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
        $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
        date_default_timezone_set('Asia/Singapore');
        $query = "SELECT valuechangeid, currencyid, newsellvalue, newbuyvalue FROM valuechanges WHERE yetcompleted=1 AND time<=".time()." ORDER BY time DESC LIMIT 1";
        $result = $db->query($query);
        if($result->num_rows >= 1)
        {
            while($row = $result->fetch_assoc())
            {
                $query = "UPDATE currency SET buyvalue=".$row["newbuyvalue"].", sellvalue=".$row["newsellvalue"]." WHERE currencyid=".$row["currencyid"];
                $db->query($query) or die($db->error.$query);
                $query = "UPDATE valuechanges SET yetcompleted=0 WHERE time <= ".time();
                $db->query($query) or die($db->error);
            }

            //recalculate everyone's net worth
            $query = "SELECT userkey FROM users";
            $result = $db->query($query);
            while($row = $result->fetch_assoc())
            {
                calculateWorth($row["userkey"]);
            }
        }
        $db->close();
    }
    function calculateWorth($userkey)
    {
        ignore_user_abort(true);
        global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
        $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
        date_default_timezone_set('Asia/Singapore');
        $totalvalue = 0.00;
        $query = "SELECT wallet.amount, currency.sellvalue FROM wallet INNER JOIN currency ON currency.currencyid=wallet.currencyid WHERE userkey=$userkey";
        $result2 = $db->query($query) or die($db->error);
        while($row2 = $result2->fetch_assoc())
        {
            $totalvalue += round($row2["amount"] / ($row2["sellvalue"]), 4);
        }
        $totalvalue = round($totalvalue, 2);
        $query = "UPDATE users SET networth=$totalvalue WHERE userkey=$userkey";
        $db->query($query) or die($db->error);
        $db->close();
    }

?>
