<?php
// get VID from session
require('access2.php');
$VID = $_SESSION['VID2'];
$LCNC = $_SESSION['LCNC'];
$NATION = $_SESSION['NATION'];

// store activity in log
//require('../sessioner.php');
//howManyIps();

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

// table : UNION between early and late
$table = "( (SELECT * FROM `" . $table . "`) UNION ALL (SELECT * FROM `" . $table_late . "`) ) as `everybody`";

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');


// QUERIES
$stringa = "SELECT * FROM " . $table . " WHERE LCNC='" . $LCNC . "' AND ( STATUS='accepted' OR STATUS='proven' OR STATUS='participant' ) ORDER BY SURNAME;";
$result = $mysqli->query($stringa);
$entries = $result->num_rows;

$stringa = "SELECT * FROM " . $table . " WHERE LCNC='IM' AND '". $NATION . "' LIKE CONCAT('%',`COUNTRY_STUDY`,'%') AND ( STATUS='accepted' OR STATUS='proven' OR STATUS='participant' ) ORDER BY SURNAME;";
$result_IM = $mysqli->query($stringa);
$entries_IM = $result_IM->num_rows;

$mysqli->close();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Search results</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <style>
            .mytextarea {
                font-size: 8ptx;
            }
        </style>

    </head>
    <body>





        <br>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6"><h5>User: <?php echo $VID; ?></h5></div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6"><h3><?php echo $LCNC; ?></h3><h4>tot: <?php echo $entries;?></h4></div>
            <div class="col-md-3"><small><a href="logout2.php" ><b>LOG OUT</b></a></small></div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th>earlybird/late</th>
                        <th>Surname</th>
                        <th>Name</th>
                        <th>D.O.B.</th>
                        <th>Country of study</th>
                        <?php if ($LCNC == 'IM'):?>
                        <th>Membership status</th>
                        <?php endif;?>
                    </tr>

                    <?php
                    while($row = $result->fetch_array()) {
                        echo "<tr>";
                        $earlylate = coloring_earlylate($row['ID'], true);
                        $col_earlylate = '<td style="' . $earlylate['style'] . '">' .  $earlylate['string'] . '</td>';
                        echo $col_earlylate;
                        echo "<td>" . $row['SURNAME'] . "</td>";
                        echo "<td>" . $row['NAME'] . "</td>";
                        echo "<td>" . $row['DOB'] . "</td>";
                        //echo "<td>" . $row['LCNC'] . "</td>";
                        echo "<td>" . $row['COUNTRY_STUDY'] . "</td>";
                        if ($LCNC == 'IM') {
                            $stquo  = ($row['LCNC_BOOL']=='P' ? 'not ' : 'already ');
                            $bcolor = ($row['LCNC_BOOL']=='P' ? 'info ' : 'warning');
                            echo "<td><a class=\"btn btn-" . $bcolor . "\" href=\"changePayment.php?ID=" . $row['ID'] . "\">Mark as <b>" . $stquo . "paid</b></a></td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
            <div class="col-md-3"></div>
        </div>


        <!-- FROM HERE ONLY FOR NATIONAL COMMITTEES -->

        <?php if ($entries_IM > 0):?>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6"><h3>Individual members potentially pertaining to <?php echo $LCNC; ?></h3><h4>tot: <?php echo $entries_IM;?></h4></div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th>earlybird/late</th>
                        <th>Surname</th>
                        <th>Name</th>
                        <th>D.O.B.</th>
                        <th>Country of study</th>
                        <th>Add to <?php echo $LCNC;?></th>
                    </tr>

                    <?php
                    while($row = $result_IM->fetch_array()) {
                        echo "<tr>";
                        $earlylate = coloring_earlylate($row['ID'], true);
                        $col_earlylate = '<td style="' . $earlylate['style'] . '">' .  $earlylate['string'] . '</td>';
                        echo $col_earlylate;
                        echo "<td>" . $row['SURNAME'] . "</td>";
                        echo "<td>" . $row['NAME'] . "</td>";
                        echo "<td>" . $row['DOB'] . "</td>";
                        //echo "<td>" . $row['LCNC'] . "</td>";
                        echo "<td>" . $row['COUNTRY_STUDY'] . "</td>";
                        echo "<td><a class=\"btn btn-default\" href=\"changeAffiliation.php?ID=" . $row['ID'] . "\">Add</a></td>";
                        echo "</tr>";

                    }
                    ?>
                </table>
            </div>
            <div class="col-md-3"></div>
        </div>
        <?php endif;?>



        <!--
 <form method="post" action="sendNote.php">
            <textarea name="comment" class="mytextarea" rows="2" cols="20" placeholder="comment"></textarea>
            <input type="hidden" name="ID" value="10" />
            <input type="submit" value="send" />
        </form>
<button id="showarea" name="showarea" type="button" value="Show Textarea" />
<textarea id="textarea" name="text"></textarea>
<button id="textarea-ok" name="ok" type="button" value="Ok" />
<script type="text/javascript">
$("#textarea, #textarea-ok").hide(); // or you can have hidden w/ CSS
$("#showarea").click(function(){
$("#textarea").show();
});
$("#textarea-ok").click(function(){
$("#textarea").hide();
});
</script>
-->


    </body>
</html>