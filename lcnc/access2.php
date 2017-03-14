<?php
session_start();



// if loggedIn is not set, set it to false
if ( !isset($_SESSION['passwordOk2']) )
{
    $_SESSION['passwordOk2'] = false;
}

// get POST values from form below
if (isset($_POST['password2']) and isset($_POST['VID2'])) {

    // connect to db
    $dbinfo = explode("\n", file_get_contents('../loginDB.txt'))[0];
    $dbinfo = explode(" ", $dbinfo);
    $user = $dbinfo[1];
    $password = $dbinfo[3];
    $db = $dbinfo[5];
    $host = $dbinfo[7];
    $port = $dbinfo[9];
    $table = $dbinfo[11];

    $mysqli = new mysqli($host, $user, $password, $db);

    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }

    // utf-8 encoding
    mysqli_set_charset($mysqli, 'utf8');

    $stringa = "SELECT LCNC,USER,PASSWORD,NATION FROM LCNC_ACCESS WHERE USER='" . $_POST['VID2'] . "';";
    $result = $mysqli->query($stringa);
    $entries = $result->num_rows;
    if ($entries==0) {
        $_SESSION['passwordOk2'] = false;
        header('Location: ' . 'badPassword2.php');
    } else {

        $row = $result->fetch_array();
        if ($row['PASSWORD'] != $_POST['password2']) {
            $_SESSION['passwordOk2'] = false;
            header('Location: ' . 'badPassword2.php');
        } else{ 
            $_SESSION['passwordOk2'] = true;
            $_SESSION['VID2'] = $_POST['VID2'];
            $_SESSION['LCNC'] = $row['LCNC'];
            $_SESSION['NATION'] = $row['NATION'];
        }
    }
    $result->free();
    $mysqli->close();


} // end isset


// open if
if ( !$_SESSION['passwordOk2'] or !isset($_SESSION['VID2']) ):
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Log in for LC/NC</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    </head>
    <body>

        <br>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4" style="text-align:center"><img style="width:162px;height:243px;" src="../LOGO.jpg" alt="ICPS_logo"></div>
            <div class="col-md-4"></div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h1 style="text-align:center">LC/NC participants list</h1>
            </div>
            <div class="col-md-2"></div>
        </div>

        <br><br>

        <div class="row" style="text-align:center">
            <div class="col-md-2"></div>
            <div class="col-md-8"><b>Log in with your username and password:</b></div>
            <div class="col-md-2"></div>
        </div>

        <br>

        <div class="row" style="text-align:center">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <form method="POST">
                    VID: <input type="text" name="VID2">        Password: <input type="password" name="password2">
                    <input type="submit" name="submit" value="Login" > 
                </form>
            </div>
            <div class="col-md-2"></div>
        </div>


    </body>
</html>


<?php
//close if
exit();
endif;
?>