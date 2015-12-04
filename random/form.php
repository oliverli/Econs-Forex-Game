<?php
//init
    require_once("include.php");
    global $mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase, $numTables;
    $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
    $alreadyfilled = false;
    $gotdata = false;
    $wrongpw = false;
    $invalidID = false;
    $idx = false;
    $type = 0; //0 - enter password page, 1 - rating page, 2 - done, 3 - already submitted
    session_start();
    if(isset($_POST["idx"]))
    {
        $idx = $_POST["idx"];
        $data = getData($idx);
        $alreadyfilled = $data["filled"];
        $name = $data["name"];

        $pw = $data["pw"];

        if(isset($_POST["pwd"]))
        {
            $givenpwd = $_POST["pwd"];
            if(strcmp($givenpwd, $pw) != 0)
            {
                $idx = false;
                $wrongpw = true;
                //echo "<script>alert('Something went wrong. Please try again.'); window.location.href = 'form.php';</script>"; //reloads the page without any post data
            }
            else
            {
                $type = 1;
                $_SESSION["user"] = $idx;
                $_SESSION["name"] = $name;
                $_SESSION["filled"] = $alreadyfilled;
            }
        }
    }
    else if(count($_POST) > 24 && isset($_SESSION["user"]) && !$_SESSION["filled"])
    {
        $gotdata = true;
        $type = 2;
        //check that everything is sent
        for($j = 1; $j <= 25; $j++)
        {
            if(!isset($_POST["rating_".$j]))
            {
                if($_SESSION["user"] == $j)
                    $idx = $j;
                else
                    die("<script>alert('Something went terribly wrong. Please try again.'); window.location.href = 'form.php';</script>");
            }
            else
            {
                $_POST["rating_".$j] = $db->escape_string($_POST["rating_".$j]);
            }
        }

        //===================================== handle the data here ======================================
        for($i = 1; $i <= 25; $i++)
        {
            if($i == $idx)
                continue;
            $one = $two = 0;
            if($i > $idx)
            {
                $one = $idx;
                $two = $i;
            }
            else
            {
                $one = $i;
                $two = $idx;
            }
            if(abs($_POST["rating_".$i]) > 5)
                die();
            $query = "UPDATE pairsList SET fIndex=fIndex+".$_POST["rating_".$i]." WHERE studentID1=$one AND studentID2=$two";
            $db->query($query) or die($db->error);
        }
        $type = 2;
        $_SESSION["filled"] = true;
        $query = "UPDATE studentList SET completed=1 WHERE studentID=$idx";
        $db->query($query) or die($db->error);
    }
    else
    {
        $_SESSION = array();
        if(ini_get("session.use_cookies"))
        {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
    if($alreadyfilled)
        $type = 3;

    function getData($id)
    {
        global $db;
        //============================ Add code here to get data from database ============================
        $id = $db->escape_string($id);
        $query = "SELECT name, gender, password, completed FROM studentList WHERE studentID=$id";
        $result = $db->query($query);
        if($result->num_rows == 0)
        {
            //die("what have you done this is not supposed to happen call the logic police");
            $invalidID = true;
        }
        else
        {
            $row = $result->fetch_assoc();
            $ret["name"] = $row["name"];
            $ret["gender"] = $row["gender"]; //0 is F, 1 is M
            $ret["filled"] = ($row["completed"] == 1);
            $ret["pw"] = $row["password"];
            return $ret;
        }
        //var $ret = array();
        //return "Unbekannte Name #".$id;
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>15S6G Friendship Index</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/css/materialize.min.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/js/materialize.min.js"></script>
        <style>
            table
            {
                margin-left: 10px;
            }
            body
            {
                background-color: #ede8e4;
            }

            .main
            {
                display: none;
            }
            .hr
            {
                width: 100%;
                height: 0px;
                border: 1px dashed #ccc;
                margin: 15px 0px;
            }
            .center
            {
                text-align:center;
            }
            .warn
            {
                padding-top: 10px;
                display: inline-block;
                color: #FF4B4B;
            }
            #idxnumber
            {
                margin-top: 10px;
            }
            #idxnumber #icon
            {
                color: black;
            }
            .subbtn
            {
                /*margin-left: 45px;*/
                float: right;
            }
            #idxwrapper
            {
                margin: 20px 10px;
                padding-top: 10px;
                position: absolute;
                left: 0; right: 0; top: 0; bottom: 0;
            }
            @media (min-width: 520px) {
                #idxwrapper
                {
                    margin-left: auto;
                    margin-right: auto;
                    max-width: 500px;
                }
            }
            @media (min-height: 265px) {
                #idxwrapper
                {
                    margin-top: auto;
                    margin-bottom: auto;
                    max-height: 225px;
                }
            }
            #rat
            {
                margin: 20px 10px;
                padding: 20px 10px;
            }

            #rat h4
            {
                margin: 15px;
                color: #333;
            }
            #rat p
            {
                margin: 10px 15px;
                color: #333;
            }
            #rat table
            {
                color: #333;
                border-collapse: collapse;
            }
            #rat td
            {
                padding: 5px;
            }
            #rat .sflex
            {
                width: 100%;
                margin-left: 0px;
            }
            @media (min-width: 520px) {
                #rat .sflex
                {
                    width: 25%;
                }
            }
            #rat span.name
            {
                display: inline-block;
                vertical-align: top;
                color: #333;
            }
            #rat #rowwrap
            {
                margin-bottom: 0px;
            }
            #rat .ratingbox
            {
                margin-bottom: 30px;
            }
            #errorwrapper
            {
                margin: 20px 10px;
                position: absolute;
                padding: 20px;
                left: 0; right: 0; top: 0; bottom: 0;
            }
            @media (min-width: 370px) {
                #errorwrapper
                {
                    margin-left: auto;
                    margin-right: auto;
                    max-width: 350px;
                }
            }
            @media (min-height: 220px) {
                #errorwrapper
                {
                    margin-top: auto;
                    margin-bottom: auto;
                    max-height: 180px;
                }
            }
            #donewrapper {
                margin: 20px 10px;
                position: absolute;
                padding: 20px;
                left: 0;
                right: 0;
                top: 0;
                bottom: 0;
            }
            @media(min-width: 370px) {
                #donewrapper { 
                    margin-left: auto;
                    margin-right: auto;
                    max-width: 350px;
                }
            }
            @media (min-height: 245px) {
                #donewrapper {
                    margin-top: auto;
                    margin-bottom: auto;
                    max-height: 205px;
                }
            }
        </style>
        <script>
            $(window).load(function ()
            {
                $(".main").delay(100).fadeIn(750);
            });

            function update(val, id)
            {
                $("#rat_" + id + " label").html("Your Rating: " + val);
            }
        </script>

        <?php
            if($idx && !$alreadyfilled)
            {
                echo '<script>$(document).ready(function(){$(".ratingInputs").each(function(){update(this.value, this.dataset.idx);});});</script>';
            }
        ?>

    </head>
    <body>
        <?php
//Index Number Section
            if($type == 0)
            {
                ?>
                <div class="card main" id="idxwrapper">
                    <div class="row">
                        <form class="col s12" id="idxnumber" method="post" action="form.php" autocomplete="off">
                            <div class="row" style="margin-bottom: 0px;">
                                <div class="input-field col s12">
                                    <i class="material-icons prefix" id="icon">local_library</i>
                                    <input id="idxi" type="number" class="validate" placeholder="Enter your index number" name="idx" min="1" max="25" required>
                                    <label for="idxi">Index Number</label>
                                </div>
                            </div>
                            <div class="row" style="">
                                <div class="input-field col s12">
                                    <input id="pwd" type="password" class="validate" name="pwd" required placeholder="The one we PM-ed you">
                                    <label for="pwd">Password</label>
                                    <br>
                                    <?php if($invalidID) echo '<span class="warn">The index number you entered was invalid. Was that a typo?</span>' ?>
                                    <?php if($wrongpw) echo '<span class="warn">Incorrect password.</span>' ?>
                                    <button class="btn waves-effect waves-light subbtn" type="submit" name="action"><i class="material-icons">send</i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
            }
        ?>

        <?php
//Rating Section
            if($type == 1)
            {
                ?>
                <div class="card main" id="rat">
                    <div class="row" id="rowwrap">
                        <form class="col s12" id="rating" method="post" action="form.php">
                            <h4>Friendship Index</h4>
                            <p>Hello<?php if($idx) echo " ".$name ?>!</p>
                            <p>Sorry we have to do this (&gt;.&lt;) but we need you to rate your relationship with your classmates. This is so that we can better distribute the people in class, statistically speaking, for the seating plan. So we really thank you for helping out! Please be real honest okay? :) Don't worry, all your responses are anonymized - not even us can know what values you put for who! Of course, everything is protected by a very secure password, so nothing will get leaked :D</p>
                            <p>Anyway the rating guidelines are as follows:</p>
                            <p>
                            <table>
                                <tr>
                                    <td>5</td>
                                    <td>We are absolutely BFFs!</td>
                                </tr>
                                <tr>
                                    <td>2.5</td>
                                    <td>Quite nice terms with each other</td>
                                </tr>
                                <tr>
                                    <td>0</td>
                                    <td>Neutral</td>
                                </tr>
                                <tr>
                                    <td>-2.5</td>
                                    <td>Mildly dislike</td>
                                </tr>
                                <tr>
                                    <td>-5</td>
                                    <td>You hate this person</td>
                                </tr>
                            </table>
                            </p>
                            <p>Thanks!</p>
                            <p style="text-align: right;">~ 15S6G #nolifeeverydaycoding Team</p>
                            <div class="hr"></div>

                            <?php
                            if($idx && $type == 1)
                            {
                                $output = '';
                                $count = 0;
                                for($i = 1; $i <= 25; $i ++)
                                {
                                    if($i != $idx)
                                    {
                                        $count ++;
                                        if($count == 1)
                                        {
                                            $output .= '<div class="row">';
                                        }
                                        $dat = getData($i);

                                        $output .= '<div class="range-field col sflex ratingbox" id="rat_'.$i.'">';

                                        if($dat["gender"] == 1)
                                            $col = "#09c";
                                        else
                                            $col = "#c03";

                                        $output .= '<i class="material-icons prefix" id="icon" style="color: '.$col.'">person</i>';
                                        $output .= '<span class="name">&nbsp;('.$i.') '.$dat["name"].'</span>';
                                        $output .= '<input id="rating_'.$i.'" data-idx="'.$i.'" type="range" class="validate ratingInputs" name="rating_'.$i.'" min="-5" max="5" step="0.5" onmousemove="update(this.value, '.$i.');" value="0">';
                                        $output .= '<label for="rating_'.$i.'">Your Rating: &endash;</label>';
                                        $output .= '</div>';

                                        if($count == 4)
                                        {
                                            $output .= "</div>";
                                            $count = 0;
                                        }
                                    }
                                }
                                echo $output;
                            }
                            ?>
                            <button class="btn waves-effect waves-light" type="submit" name="action" style="float: right;"><i class="material-icons">send</i></button>
                            <p>Thanks! :)</p>
                        </form>
                    </div>
                </div>
                <?php
            }
        ?>

        <?php
            //Error Already Filled
            if($alreadyfilled)
            {
                ;
                ?>
                <div class="card main" id="errorwrapper">
                    <div class="row">
                        <p class="center"><i class="material-icons prefix" id="icon">error</i></p>
                        <p class="center">You have already filled up the form.</p>
                        <p class="center">Just message any one of us if you need help.</p>
                        <?php
                        $_SESSION = array();
                        if(ini_get("session.use_cookies"))
                        {
                            $params = session_get_cookie_params();
                            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
                            );
                        }
                        session_destroy();
                        ?>
                    </div>
                </div>
                <?php
            }
        ?>

        <?php
//Data successfully entered into the database
            if($gotdata)
            {
                ?>
                <div class="card main" id="donewrapper">
                    <div class="row">
                        <p class="center"><i class="material-icons prefix" id="icon">done</i></p>
                        <p class="center">Done! Thanks!</p>
                        <p class="center">If have any more questions or need help, just message any one of us. :D</p>
                        <?php
                        $_SESSION = array();
                        if(ini_get("session.use_cookies"))
                        {
                            $params = session_get_cookie_params();
                            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
                            );
                        }
                        session_destroy();
                        ?>
                    </div>
                </div>
                <?php
            }
        ?>
    </body>
</html>
<?php $db->close() ?>

<!-- ARCHIVE
        <div class="row">
                <div class="range-field col sflex ratingbox" id="rat_1">
                        <i class="material-icons prefix" id="icon" style="color: #09c">person</i>
                        <span class="name">js</span>
                        <input id="rating_1" data-idx="1" type="range" class="validate ratingInputs" name="rating_1" min="-5" max="5" step="0.5" onchange="update(this.value, 1);" value="0">
                        <label for="rating_1">Your Rating: &endash;</label>
                </div>
                <div class="range-field col sflex ratingbox" id="rat_2">
                        <i class="material-icons prefix" id="icon" style="color: #09c">person</i>
                        <span class="name">js</span>
                        <input id="rating_2" data-idx="2" type="range" class="validate ratingInputs" name="rating_2" min="-5" max="5" step="0.5" onchange="update(this.value, 2);" value="0">
                        <label for="rating_2">Your Rating: &endash;</label>
                </div>
        </div>
-->
