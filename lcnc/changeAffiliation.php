<?php
// get VID from session
require('access2.php');
$VID = $_SESSION['VID2'];
$LCNC = $_SESSION['LCNC'];
$NATION = $_SESSION['NATION'];

// store activity in log
require('../sessioner.php');
//howManyIps();

// connect to db
$dbinfo = explode("\n", file_get_contents('../loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
$gmail_pwd = $dbinfo[13];

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');

// ID is sent via GET method
$ID = $_GET['ID'];

// select single row, using ID
$stringa = 'SELECT * FROM ' . $table . ' WHERE ID = '.$ID;
$result = $mysqli->query($stringa);
$entries = $result->num_rows;

// should never be more than one result! (because ID must be unique)
if ($entries != 1) {
    die("CONFLICTING IDs !! " . $entries);
}

// UPDATE QUERY
$stringa = "UPDATE " . $table . " SET LCNC='" . $LCNC . "', LCNC_BOOL='LN' WHERE ID=" . $ID;

// UNCOMMENT HERE TO MAKE IT ACTIVE
// send update query
$result = $mysqli->query($stringa);

$mysqli->close();


// body of the email message
$ip = getIP();
$date = strtr(time(), '/', '-');
$date = date('Y-m-d H:i:s', $date);
$mail_body = 'user: ' . $VID . '<br>time: ' . $date . '<br>IP: ' . $ip . '<br>query: ' . $stringa . '<br>';
$mail_body_alt = 'user: ' . $VID . ' time: ' . $date . ' IP: ' . $ip . ' query: ' . $stringa;


require "../../dbaisf/PHPMailer-master/PHPMailerAutoload.php";

// SEND A MAIL
$mail = new PHPMailer;

#$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'ass.it.stud.fisica@gmail.com';                 // SMTP username
$mail->Password = $gmail_pwd;                           // SMTP password
$mail->SMTPSecure = 'ssl';    // Enable encryption, 'ssl' also accepted
$mail->Port = 587;

$mail->From = 'ass.it.stud.fisica@gmail.com';
$mail->FromName = 'DBICPS';
$mail->addAddress('eugenio.valdano@ai-sf.it');     // Add a recipient
#$mail->addAddress('ellen@example.com');               // Name is optional
#$mail->addReplyTo('info@example.com', 'Information');
#$mail->addCC('cc@example.com');
#$mail->addBCC('bcc@example.com');

#$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
#$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
#$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'LCNC MONITOR: activity';
$mail->Body    = $mail_body;
$mail->AltBody = $mail_body_alt;
$mail->send();
/*
        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
        */

header('Location: ' . 'lcnc.php');


?>