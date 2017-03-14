<?php
// get VID from session
require('access.php');
$VID = $_SESSION['VID'];
//require('sessioner.php');
//howManyIps();
require('util.php');

// allow access only to admins
if ( !( isset($privileges) and $privileges[$VID]==0 ) ) {
    header('Location: ' . "index.php");
}

error_reporting(E_ALL ^ E_WARNING); 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>History</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>

    <body>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6"><h3>History</h3>  <a href="index.php">BACK</a></div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th>IP</th>
                        <th>Time</th>
                        <th>Country</th>
                        <th>City</th>
                    </tr>
                    <?php

                    function iplocation($ip) {
                        $x = json_decode(file_get_contents("http://ipinfo.io/" . $ip . "/json"));
                        return $x;
                    }

                    $name = 'connected.log';


                    $F = explode("\n", file_get_contents($name));
                    foreach($F as $R) { // loop on rows
                        echo "<tr>";
                        // loop on columns
                        $G = explode(" ",$R);
                        if (count($G)>1) {
                            echo "<td>" . $G[0] . "</td>";
                            $date = strtr($G[1], '/', '-');
                            echo "<td>" . date('Y-m-d H:i:s', $date) . "</td>";
                            $geo = iplocation($G[0]);
                            echo "<td>" . ($geo->country == '' ? 'unknown' : $geo->country) . "</td>";
                            echo "<td>" . ($geo->city == '' ? 'unknown' : $geo->city) . "</td>";
                            // end loop on columns
                            echo "</tr>";
                        }
                    }


                    ?>

                </table>

            </div>
            <div class="col-md-3"></div>
        </div>


    </body>
</html>
