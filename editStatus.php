<?php
// get VID from session
require('access.php');
$VID = $_SESSION['VID'];
// store activity in log
//require('sessioner.php');
//howManyIps();

// load functions
require('util.php');

require "PHPMailer-master/PHPMailerAutoload.php";


if ( !in_array($VID, array('eugenio','michele') ) ) {
    header('Location: ' . "singleEntry.php?ID=" . $ID);
}


// connect to db
$dbinfo = explode("\n", file_get_contents('loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
$table_excursions = $dbinfo[15];
$mail_registration_password = $dbinfo[17];

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');

// ID is sent via GET method
$ID = $_GET['ID'];
$status = $_GET['status'];

// select single row, using ID
$stringa = 'SELECT * FROM ' . $table . ' WHERE ID = ' . $ID;
$result = $mysqli->query($stringa);
$entries = $result->num_rows;

// should never be more than one result! (because ID must be unique)
if ($entries != 1) {
    echo "CONFLICTING IDs !! " . $entries . "<br>";
}
$row = $result->fetch_array();
$result->close();

// UPDATE QUERY
$stringa = "UPDATE " . $table . " SET STATUS='" . $status . "', OPERATOR='" . $VID . "' WHERE ID=" . $ID;
// send update query

// execute query only if privilege
if (checkp(1,$VID)) {
    $result = $mysqli->query($stringa);    
}



// SEND MAIL IF PROVEN
if ($status=='participant'){
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
    $mail->addAddress($row['EMAIL']);     // Add a recipient
    #$mail->addAddress('ellen@example.com');               // Name is optional
    $mail->addReplyTo('icps2017registration@ai-sf.it', '');
    #$mail->addCC('cc@example.com');
    #$mail->addBCC('icps2017registration@ai-sf.it');

    #$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
    #$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    #$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = 'ICPS2017: Participation fee received';
    $mail->Body    = "Dear " . $row['NAME'] .",<br><p>We have received your participation fee for ICPS2017!<br>
        We will send you the invoice of your payment in a following email.</p><p>For any enquiry about your registration, please reply to this email or write to icps2017registration@ai-sf.it.<br>
        Best regards,</p>
        <br><br><p>The ICPS2017 Organizing Committee</p>";
    $mail->send();
}




// EXCURSIONS
$status_bin = status_binary($status); // true if accepted of any sort

$stringa = "SELECT * FROM " . $table_excursions . " WHERE ID = ".$ID;
$result_ex = $mysqli->query($stringa);
$entries_ex = $result_ex->num_rows;
$result_ex->free();

if ($entries_ex==0) {
    // not present 
    if ($status_bin) {
        // -> active
        $stringa_edit = "INSERT INTO " . $table_excursions . "(`ID`,`EXCURSION_ASSIGNED`,`ACTIVE`) VALUES (" . $ID . ",'-',1)";

    } // if not active, do nothing

} elseif ($entries_ex==1) {
    // present
    if ($status_bin) {
        // -> active
        $stringa_edit = "UPDATE " . $table_excursions . " SET ACTIVE = 1 WHERE ID = ".$ID;

    } else {
        // -> not active
        $stringa_edit = "UPDATE " . $table_excursions . " SET ACTIVE = 0 WHERE ID = ".$ID;
    }

} else {
    die('More than one ID.');
}

// update excursions, only if privilege
if (isset($stringa_edit) and checkp(1,$VID)) {
    $result = $mysqli->query($stringa_edit);   
}

$mysqli->close();

header('Location: ' . "singleEntry.php?ID=" . $ID);

?>
