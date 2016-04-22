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
        <title>Forex Trading Simulator - Add Currencies</title>
    </head>

    <body>
        <p>Add a base currency - the currency that the students will receive the initial 10 million in.</p>
        <form name="form1" action="./addcurrencies2.php" method="post">
            <p>
                <label for="name">Full name of currency: </label>
                <input type="text" name="name" id="name" />
            </p>
            <p>
                <label for="shortname">Abbreviation: </label>
                <input type="text" name="shortname" id="shortname" />
            </p>
            <p>
                <label for="numcurrency">Number of other currencies to be added: </label>
                <input type="text" name="numcurrency" id="numcurrency" />
            </p>
            <p>
                <input type="submit" name="submit" id="submit" value="Next" />
            </p>
        </form>
    </body>
</html>