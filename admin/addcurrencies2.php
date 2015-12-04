<?php
    session_start();
    require_once("../include.php");
    global $mysqlusername, $mysqlpassword, $mysqldatabase, $mysqllocation;
    if(!isIn() || !isTeacher())
    {
        header("Location: ../");
        exit();
    }
    if(!isset($_POST["name"]) || !isset($_POST["shortname"]) || !isset($_POST["numcurrency"]))
    {
        header("Location: addcurrencies.php");
        exit();
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Forex Trading Simulator - Add Currencies</title>
        <?php
            $numcurr = intval($_POST["numcurrency"]);
            $db = new mysqli($mysqllocation, $mysqlusername, $mysqlpassword, $mysqldatabase);
            $name = $db->escape_string($_POST["name"]);
            $shortname = $db->escape_string($_POST["shortname"]);
            $query = "INSERT INTO currency (name, shortname, buyvalue, sellvalue) VALUES ('$name', '$shortname', 1, 1)";
            if($db->query($query) === TRUE)
            {
                echo "<script>alert(\"$name has been added successfully.\");</script>";
            }
            else
            {
                echo "<script>alert(\"Currency addition failed. If problem persists, take a screenshot and contact Yicheng.\");</script>";
                die($db->error);
            }
            if($numcurr <= 0)
                die("You need to have at least 1 other currency.");
        ?>
    </head>

    <body>
        <p>Enter the remaining <?php echo $numcurr ?> currencies.</p>
        <form id="form1" name="form1" method="post" action="addcurrencies3.php">
            <input type="hidden" name="numcurr" id="numcurr" value="<?php echo $numcurr ?>"/>
            <?php
                for($i = 0; $i < $numcurr; $i++)
                    echo "<hr/><p><label for=\"name".$i."\">Full name: </label><input type=\"text\" name=\"name".$i."\" id=\"name".$i."\" /></p><p><label for=\"shortname".$i."\">Abbreviation: </label><input type=\"text\" name=\"shortname".$i."\" id=\"shortname".$i."\" /></p><p><label for=\"buyvalue".$i."\">Starting buying value: </label><input type=\"text\" name=\"buyvalue".$i."\" id=\"buyvalue".$i."\" /></p><p><label for=\"sellvalue".$i."\">Starting selling value: </label><input type=\"text\" name=\"sellvalue".$i."\" id=\"sellvalue".$i."\" /></p>";
                echo "<hr/>";
            ?>
            <p>
                <input type="submit" name="submit" id="submit" value="Next" />
            </p>
        </form>
    </body>
</html>
