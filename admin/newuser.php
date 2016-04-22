<?php
    session_start();
    require_once("../include.php");
    if(!isIn() || !isTeacher())
    {
        header("Location: ../");
        exit();
    }
    global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Forex Trading Simulator - Add Users</title>
        <?php
            if(isset($_POST["name"]) && isset($_POST["userid"]) && isset($_POST["password"]) && isset($_POST["usertype"]))
            {
                $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
                $name = $db->real_escape_string($_POST["name"]);
                $userid = $db->real_escape_string($_POST["userid"]);
                $password = $db->real_escape_string($_POST["password"]);
                $usertype = $db->real_escape_string($_POST["usertype"]);
                $password = password_hash($password, PASSWORD_DEFAULT);
                $salt = $db->escape_string($salt);
                $query = "INSERT INTO users (name, userid, password, usertype) VALUES ('$name', '$userid', '$password', $usertype);";
                if($db->query($query) === TRUE)
                {
                    $query = "SELECT userkey FROM users WHERE userid='$userid'";
                    $result = $db->query($query);
                    $row = $result->fetch_assoc();
                    $userkey = $row["userkey"];
                    $query = "INSERT INTO wallet (userkey, currencyid, amount) VALUES ($userkey, 1, 10000000)";
                    $db->query($query);
                    //$query = "INSERT INTO wallet (userkey, currencyid, amount) VALUES ($userkey, 2, 1300000000)";
                    //$db->query($query);
                    $query = "SELECT currencyid FROM currency WHERE currencyid != 1";
                    $result = $db->query($query);
                    while($row = $result->fetch_assoc())
                    {
                        //if($row["currencyid"] == 1)
                        //    continue;
                        //else
                        //{
                            $query = "INSERT INTO wallet (userkey, currencyid, amount) VALUES ($userkey, '".$row["currencyid"]."', 1300000000)";
                            if($db->query($query) === TRUE)
                                continue;
                            else
                                die("<script>alert(\"Addition of user wallet failed. Take a screenshot and email Yicheng.\");</script>".$db->error);
                        //}
                    }
                    echo "<script>alert(\"User has been added successfully.\");</script>";
                }
                else
                {
                    echo "<script>alert(\"User addition failed. Take a screenshot and email Yicheng.\");</script>";
                    echo $db->error;
                }
                $db->close();
            }
        ?>
    </head>

    <body>
        <form id="form1" name="form1" method="post" action="newuser.php">
            <p>
                <label for="name">Name: </label>
                <input type="text" name="name" id="name" />
            </p>
            <p>
                <label for="userid">Username: </label>
                <input type="text" name="userid" id="userid" />
            </p>
            <p>
                <label for="password">Password: </label>
                <input type="text" name="password" id="password" />
            </p>
            <p>
                <label for="usertype">User type: </label>
                <select name="usertype" id="usertype">
                    <option value="1" selected="selected">Student</option>
                    <option value="2">Teacher</option>
                </select>
            </p>
            <p>
                <input type="submit" name="submit" id="submit" value="Add user" />
            </p>
        </form>
    </body>
</html>
