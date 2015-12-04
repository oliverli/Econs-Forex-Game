<?php
    session_start();
    require_once("include.php");
    if(isIn())
    {
        header("Location: ./dashboard/");
        exit();
    }
    $loginFailed = false;
    $gameNotStarted = false;
    $startTime = 0;
    if(isset($_POST["username"]) && isset($_POST["password"]))
    {
        if(passCheck($_POST["password"], $_POST["username"]))
        {
            date_default_timezone_set('Asia/Singapore');
            $startTime = gameStartTime();
            if(isTeacher())
            {
                header("Location: ./admin/");
            }
            else if($startTime <= time())
            {
                header("Location: ./dashboard/");
            }
            else
            {
                $gameNotStarted = true;
            }
        }
        else
        {
            $loginFailed = true;
            echo "<script>window.onload = function(){document.getElementById(\"password\").focus();};</script>";
        }
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Forex Trading Simulator - Login</title>
        <?php if($gameNotStarted) echo "<script>alert('The game has not started yet. It starts in ".nicetime($startTime).".');window.onload = function(){document.getElementById(\"password\").focus();};</script>"; ?>
        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/js/materialize.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/css/materialize.min.css" media="screen,projection" />
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
        <link href="css/main.css" rel="stylesheet" />
        <style>
   /* label focus color */
    .input-field input[type=text]:focus + label {
     color:  #3f51b5;
   }
   /* label underline focus color */
   .input-field input[type=text]:focus {
     border-bottom: 1px solid  #3f51b5;
   }
   /* icon prefix focus color */
   .input-field .prefix.active {
     color:  #3f51b5;
   }</style>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    </head>

    <body class="indigo lighten-5">
        <nav>
            <div class="nav-wrapper indigo row">
                <div class="col s12">Forex Trading Simulator</div>
            </div>
        </nav>
        <div class="container">
            <div id="login-card" class="card">
                <div class="center" id="Logo"><img src="./img/hci.png" height="50px" style="opacity:0.87;"/></div>
                <form id="loginform" name="loginform" method="post">
                    <div class="row">
                        <div class="input-field col s12 m10 l10 push-m1 push-l1">
                            <i class="material-icons prefix">account_circle</i>
                            <input type="text" required="" name="username" id="username"<?php if($loginFailed || $gameNotStarted) echo " value=\"".$_POST["username"]."\""; ?>/>
                            <label for="username">Username: </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 m10 l10 push-m1 push-l1">
                            <i class="material-icons prefix">vpn_key</i>
                            <label for="password">Password: </label>
                            <input type="password" name="password" id="password" />
                        </div>
                    </div>
                    <div class="row input-field center" id="Submit">
                        <button class="btn waves-effect waves-light indigo accent-4" type="submit" name="action">Login
                        </button>
                    </div>
                </form>
                <?php
                    if($loginFailed)
                        echo "<script>alert('Login failed - username or password incorrect.');</script>";
                ?>
            </div>
        </div>
    </body>
</html>