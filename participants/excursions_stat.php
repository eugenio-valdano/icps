<?php
// get VID from session
require('access.php');
$VID = $_SESSION['VID'];
// store activity in log
//require('sessioner.php');
//howManyIps();
//functions
require('../util.php');

// connect to db
$dbinfo = explode("\n", file_get_contents('../loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
$table_late = $dbinfo[19];
$table_total = $dbinfo[21];

// table : choose the table
$table = $table_total;

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');


// counter
$stringa = "SELECT `ASSIGNED`,COUNT(`ASSIGNED`) AS `c` FROM " . $table . " GROUP BY `ASSIGNED` ORDER BY `ASSIGNED`;";
$result = $mysqli->query($stringa);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>ICPS 2017 - DB QUERY INTERFACE</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!--<link rel="stylesheet" type="text/css" href="morestyle.css">-->

        <!-- jquery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <!-- css and js for the autocomplete -->
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    </head>
    <body>


        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h1 class="titolo">Excursions</h1>
            </div>
            <div class="col-md-2"></div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <table class="table">

                    <tr>
                        <th>code</th>
                        <th>count</th>
                        <th>excursion</th>
                    </tr>

                    <?php
                    while ($row = $result->fetch_array()) {

                        $x = $row['ASSIGNED'];
                        $c = $row['c'];

                        echo '<tr>';
                        echo '<td>' . $x . '</td>';
                        echo '<td>' . $c . '</td>';
                        echo '<td>' . $dexcursions[$x] . '</td>';
                        echo '</tr>';

                    }
                    ?>
                </table>
            </div>
            <div class="col-md-3"></div>
        </div>



        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-3">
                <button onclick="goBack()" class="btn btn-info">Go Back</button>
            </div>
            <div class="col-md-3">
                <a href="index.php" class="btn btn-info">New search</a>
            </div>
            <div class="col-md-3"></div>
        </div>
        
        <script>
        function goBack() {
            window.history.back();
        }

        function copyToClipboard(element) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(element).text()).select();
            document.execCommand("copy");
            $temp.remove();
        }
    </script>


    </body>
</html>