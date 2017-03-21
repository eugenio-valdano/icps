<?php
// get VID from session
require('access.php');
$VID = $_SESSION['VID'];
// store activity in log
//require('sessioner.php');
//howManyIps();

// load functions
require('util.php');

// connect to db
$dbinfo = explode("\n", file_get_contents('loginDB.txt'))[0];
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


function format_date($date){
    $time = strtotime($date);
    return date("d/m/y", $time);
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Single entry result</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <style>
            .tiny_skip{
                margin-top: 5px;
                margin-bottom: 5px}
            .small_skip{
                margin-top: 20px;
                margin-bottom: 20px}
            .med_skip{
                margin-top: 40px;
                margin-bottom: 40px}
        </style>
    </head>
    <body>

        <?php
        // ID is sent via GET method
        $ID = $_GET['ID'];

        // select single row, using ID
        $stringa = "SELECT * FROM " . $table . " WHERE ID = ".$ID;
        $result = $mysqli->query($stringa);
        $entries = $result->num_rows;

        // should never be more than one result! (because ID must be unique)
        if ($entries != 1) {
            die("CONFLICTING IDs !!");
        }

        // fetch data
        $row = $result->fetch_array();

        $result->free();
        $mysqli->close();

        ?>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-7"><h4>VID: <font color="green"><?php echo $VID;?></font></h4></div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <form method="GET" action="editRecord.php">
                    <?php if (checkp(0,$VID)): ?> <!-- RESTRICT -->
                    <table class='table'>
                        <tr>
                            <th>field</th>
                            <th>value</th>
                        </tr>
                        <!--
<tr>
<td><div class="tiny_skip"></div></td>
<td><div class="tiny_skip"></div></td>
</tr>-->
                        <tr>
                            <td>SURNAME</td>
                            <td>
                                <?php echo 
    "<textarea name=\"new_SURNAME\" rows=\"1\" cols=\"40\">" . $row['SURNAME'] . "</textarea>" ;?>
                            </td>
                        </tr>
                        <tr>
                            <td>NAME</td>
                            <td>
                                <?php echo 
    "<textarea name=\"new_NAME\" rows=\"1\" cols=\"40\">" . $row['NAME'] . "</textarea>" ;?>
                            </td>
                        </tr>
                        <tr>
                            <td>SURNAME (STRIP)</td>
                            <td>
                                <?php echo 
    "<textarea name=\"new_SURNAME_STRIP\" rows=\"1\" cols=\"40\">" . $row['SURNAME_STRIP'] . "</textarea>" ;?>
                            </td>
                        </tr>
                        <tr>
                            <td>NAME (STRIP)</td>
                            <td>
                                <?php echo 
    "<textarea name=\"new_NAME_STRIP\" rows=\"1\" cols=\"40\">" . $row['NAME_STRIP'] . "</textarea>" ;?>
                            </td>
                        </tr>
                        <tr>
                            <td>D.O.B.</td>
                            <td>
                                <?php 
                                echo 
                                    "<textarea name=\"new_DOB\" rows=\"1\" cols=\"40\">" .$row['DOB']."</textarea>";
                                ?>
                            </td>
                        <tr>
                            <td>GENDER</td>
                            <td>
                                <?php
                                echo 
                                    "<textarea name=\"new_SEX\" rows=\"1\" cols=\"20\">".$row['SEX']."</textarea>";      
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>NATIONALITY</td>
                            <td>
                                <?php 
                                echo 
                                    "<textarea name=\"new_NATIONALITY\" rows=\"2\" cols=\"40\">". $row['NATIONALITY'] ."</textarea>";
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>COUNTRY OF STUDY</td>
                            <td>
                                <?php 
                                echo 
                                    "<textarea name=\"new_COUNTRY_STUDY\" rows=\"1\" cols=\"40\">". $row['COUNTRY_STUDY'] ."</textarea>";
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>CURRENTLY ENROLLED IN</td>
                            <td>
                                <?php 
                                echo "<textarea name=\"new_DEGREE\" rows=\"1\" cols=\"40\">". $row['DEGREE'] ."</textarea>";
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>LC/NC</td>
                            <td>
                                <?php 
                                echo "<textarea name=\"new_LCNC\" rows=\"1\" cols=\"40\">". $row['LCNC'] ."</textarea>";
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>DELEGATE</td>
                            <td>
                                <?php 
                                echo "<textarea name=\"new_DELEGATE\" rows=\"1\" cols=\"40\">". $row['DELEGATE'] ."</textarea>";
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>INFO</td>
                            <td>
                                <?php 
                                echo "<textarea name=\"new_INFO\" rows=\"1\" cols=\"40\">".$row['INFO'] ."</textarea>";
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>EMAIL</td>
                            <td>
                                <?php 
                                echo "<textarea name=\"new_EMAIL\" rows=\"1\" cols=\"40\">".$row['EMAIL'] ."</textarea>";
                                ?>
                            </td>
                        </tr>
                        <tr>
                        <tr>
                            <td></td>
                            <td>
                                <input type="submit" value="Edit" class="btn btn-default" onclick="return confirm('Do you really want to do it?')" style="float: center;"/>
                            </td>
                        </tr>
                    </table>
                    <?php
                    endif;
                    echo "<input type=\"hidden\" name=\"ID\" value=\"" . $ID . "\"></input>";
                    ?>
                </form >
            </div>
        </div>



        <br>
        <div class="col-md-2"></div>
        <a class="btn btn-default" href="index.php" >New search</a>
        <?php if (checkp(2,$VID)): ?> <!-- RESTRICT -->
        <a class="btn btn-default" href="search.php?query=&sorting=SURNAME_STRIP&cfilter=all&cfilter2=all" >Full list</a>
        <a class="btn btn-default" href="singleEntry.php?ID=<?php echo $ID;?>" >Go back</a>
        <?php endif; ?>

    </body>
</html>