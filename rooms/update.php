<?php
// get VID from session
//require('access.php');
//$VID = $_SESSION['VID'];
// store activity in log
//require('sessioner.php');
//howManyIps();
//functions
require('../util.php');
require "PHPMailer-master/PHPMailerAutoload.php";

// connect to db
$dbinfo = explode("\n", file_get_contents('loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
$mail_registration_password = $dbinfo[17];
$table_late = $dbinfo[19];
$table_total = $dbinfo[21];

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// true if match found.
$FLAG_STATUS = 0; // 0: good, 1: no match from the name surname query, 2: invalid choice

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');

//
// ** get the POST variables
//
$ID = $_POST['ID']; // ID of the person
$ID_CHECK = $_POST['ID_CHECK']; // ID_CHECK of the person


if ( isset($_POST['ID_CHOICE']) ) { // find through ID

    // receiving the ID
    $ID_CHOICE = $_POST['ID_CHOICE']; // ID of whom she has chosen

    $stringa = "SELECT `SURNAME`,`NAME` FROM " . $table_total . " WHERE `ID`=" . $ID_CHOICE;
    $result_namesur = $mysqli->query($stringa);
    $n_namesur = $result_namesur->num_rows();
    if ($n_namesur == 0) {
        die('ID not found');
    }
    $result_namesur = $result_namesur->fetch_array();
    $SURNAME_CHOICE = $result_namesur['SURNAME'];
    $NAME_CHOICE = $result_namesur['NAME'];

} elseif ( isset($_POST['NAME_CHOICE']) ) { // find through NAME

    // receiving the surname and name, and finding ID
    $surname_name = explode(" ", $_POST['NAME_CHOICE']);
    $stringa = "SELECT `SURNAME`,`NAME`,`ID` FROM " . $table_total . " WHERE `SURNAME_STRIP` LIKE '%" . $surname_name[0] . " AND `NAME_STRIP` LIKE '%" . $surname_name[1] . "%'";
    $result_namesur = $mysqli->query($stringa);
    $n_namesur = $result_namesur->num_rows();
    if ($n_namesur != 1) {
        $FLAG_STATUS = 1;
    } else {
        $result_namesur = $result_namesur->fetch_array();
        $ID_CHOICE = $result_namesur['ID'];
        $SURNAME_CHOICE = $result_namesur['SURNAME'];
        $NAME_CHOICE = $result_namesur['NAME'];
    }

} else {
    die('Receiving no choice.'); // receiving no POST variables
}


//
//** check that the choice is allowed
//
// NOT allowed if any of these: 1)ID_CHOICE has chosen single room 2)ID_CHOICE has chosen someone else
if ($FLAG_STATUS==0) {

    $stringa = "SELECT `PREFERENCE` FROM " . $table_total . " WHERE `ID`=" . $ID_CHOICE;
    $result_allowed = $mysqli->query($stringa);
    $result_allowed = $result_allowed->fetch_array();
    if ($result_allowed['PREFERENCE'] != 0 or $result_allowed['PREFERENCE'] != $ID) {
        $FLAG_STATUS = 2; // mark as invalid selection
    }
}


//
//** perform
//
if ($FLAG_STATUS == 0) {

    // UPDATE query
    $stringa = "UPDATE " . $table_total . " SET `PREFERENCE`=" . $ID_CHOICE . " WHERE ID=" . $ID; 
    $result = $mysqli->query($stringa);


    // drop previous links, if necessary (links pointing from a person to ID, if ID_CHOICE != that person)
    if ($ID_CHOICE != "-1") { // not necessary if ID chooses single room

        // select people who chose her
        $stringa = "SELECT * FROM " . $table_total . " WHERE `PREFERENCE`=" . $ID . ' AND `ID`!= '.$ID_CHOICE;
        $result_reset = $mysqli->query($stringa);
        while($row = $result_reset->fetch_array()) {

            // remove the link
            $stringa = "UPDATE " . $table_total . " SET `PREFERENCE`=0 WHERE ID=" . $row['ID'];
            $result_reset_update = $mysqli->query($stringa);

            // send mail to $row['ID']
            $mail = new PHPMailer;
            #$mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'icps2017registration@ai-sf.it';                 // SMTP username
            $mail->Password = $mail_registration_password;                           // SMTP password
            $mail->SMTPSecure = 'ssl';    // Enable encryption, 'ssl' also accepted
            $mail->Port = 587;
            $mail->From = 'icps2017registration@ai-sf.it';
            $mail->FromName = 'ICPS2017 Organizing Committee';
            // MAIL ADDRESS
            //$mail->addAddress($row['EMAIL']);     // Add a recipient
            $mail->addAddress('eugenio.valdano@ai-sf.it');     // Add a recipient TEST
            $mail->addReplyTo('icps2017registration@ai-sf.it', '');
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'ICPS2017: You need to choose your room partner again.';
            $mail->Body    = "Dear " . $row['NAME'] .",<br><p>Unfortunately, the person you chose to be your roommate has picked someone else.</p><p>Please make another choice at https://www.ai-sf.it/rooms/form.php?ID=" . $row['ID'] . "&IDC=" . $row['ID_CHECK'] . "</p><p>You can contact us at icps2017registration@ai-sf.it.<br>
        Best regards,</p>
        <br><br><p>The ICPS2017 Organizing Committee</p>";
            $mail->send();
            // end mail
        } 
    }
    $result_reset->free();

} // end perform

$mysqli->close();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Rooms - failed</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- CSS for overriding td class -->
        <link rel="stylesheet" href="tdStyle.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body>

        <br>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4" style="text-align:center"><img style="width:135px;height:202px;" src="../LOGO.jpg" alt="ICPS_logo"></div>
            <div class="col-md-4"></div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h2 style="text-align:center">Room selection system</h2>
            </div>
            <div class="col-md-2"></div>
        </div>
        <br>

        <!-- SUCCESS -->
        <?php if ($FLAG_STATUS == 0); ?>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8" style="text-align:center">
                <?php
                if ($ID_CHOICE==-1) {
                    echo 'Ok, we will assign you a single room. See you at ICPS!'
                } else {
                    echo "You've chosen to share the room with " . $NAME_CHOICE . " " . $SURNAME_CHOICE . ". If he/she picks you too, you will be together. If he/she picks a single room or someone else, we will give you the opportunity to make another choice. See you at ICPS!"
                }
                ?>
            </div>
            <div class="col-md-2"></div>
        </div>
        <br>


        <?php endif; ?>

        <!-- FAILURE -->
        <?php if ($FLAG_STATUS != 0); ?>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h2 style="text-align:center">Invalid choice</h2>
            </div>
            <div class="col-md-2"></div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8" style="text-align:center">
                <?php
                if ($FLAG_STATUS==1) {
                    echo 'We could not find the person you have chosen among the participants. If you input surname and name yourself, please try again sticking to the autocomplete suggestions.'
                } else {
                    echo 'It seems the person you selected has already chose either a single room, or another roommate. Please select another person.'
                }

                $url_button = "https://www.ai-sf.it/rooms/form.php?ID=" . $ID . "&IDC=" . $ID_CHECK;
                ?>
            </div>
            <div class="col-md-2"></div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h2 style="text-align:center"><a class="btn btn-default" href="<?php echo $url_button; ?>" >Try again</a></h2>
            </div>
            <div class="col-md-2"></div>
        </div>

        <?php endif; ?>

    </body>
</html>