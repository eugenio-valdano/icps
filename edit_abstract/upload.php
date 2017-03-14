<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>ICPS 2017 - DB QUERY INTERFACE</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <style>
            .small_skip{
                margin-top: 20px;
                margin-bottom: 20px}
            .med_skip{
                margin-top: 40px;
                margin-bottom: 40px}
        </style>
    </head>
    <body>
        <div class="small_skip"></div>
        <div class="row">
            <div class="col-md-3" style="text-align:center"></div>
            <div class="col-md-6" align="center">
                <h2>Upload your poster/talk abstract</h2>
            </div>
            <div class="col-md-3"></div>
        </div>

        <div class="med_skip"></div>


        <?php

        require "../PHPMailer-master/PHPMailerAutoload.php";

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
            die($mysqli->connect_error);
            exit();
        }

        // utf-8 encoding
        mysqli_set_charset($mysqli, 'utf8');

        $uid = $_POST['unique_id'];
        $email = $_POST['email'];
        $contribution = $_POST['type'];
        $id = substr($uid, -3, 3);
        //echo $id;

        $stringa = "SELECT * FROM " . $table . " WHERE ID=".$id;
        $result = $mysqli->query($stringa);
        $entries = $result->num_rows;

        if ($entries==0){
            echo "<div class=\"row\">
                    <div class=\"col-md-3\" style=\"text-align:center\"></div>
                        <div class=\"col-md-6\" align=\"center\">
                            <h3>ERROR 401</h3>
                            Your data have not been found in the ICPS2017 database!<br>
                            Please check your <b>uid</b>.<br>
                            If the problem persists please write to icps2017registration@ai-sf.it
                        </div>
                        <div class=\"col-md-3\"></div>
                    </div>";
        } elseif ($entries>1){
            echo "<div class=\"row\">
                    <div class=\"col-md-3\" style=\"text-align:center\"></div>
                        <div class=\"col-md-6\" align=\"center\">
                            <h3>ERROR 402</h3>
                            Your data could not be retrieved in the ICPS2017 database!<br>
                            Please write to icps2017registration@ai-sf.it
                        </div>
                        <div class=\"col-md-3\"></div>
                    </div>";
        } else {

            $row = $result->fetch_assoc();



            //UPLOAD FILE
            if(isset($_FILES['abstract'])){
                $errors= array();
                $file_name = preg_replace('/\s+/', '_',$_FILES['abstract']['name']);
                $file_size =$_FILES['abstract']['size'];
                $file_tmp =preg_replace('/\s+/', '_',$_FILES['abstract']['tmp_name']);
                $file_type=$_FILES['abstract']['type'];
                $file_ext=strtolower(explode('.',$file_name)[1]);

                $expensions= array("pdf","doc","docx","txt");

                if(in_array($file_ext,$expensions)=== false){
                    $errors[]="extension not allowed, please use a PDF, DOC, DOCX or TXT file.";
                }

                if($file_size > 1048576){
                    $errors[]='File size must not be larger than 1 MB';
                }

                $new_file_name = $uid ."_".$contribution."." . $file_ext;
                if(empty($errors)==true){
                    move_uploaded_file($file_tmp,"uploads/".$new_file_name);
                    //echo "Success";
                    echo "<div class=\"row\">
                        <div class=\"col-md-3\" style=\"text-align:center\"></div>
                            <div class=\"col-md-6\" align=\"center\">
                            <h3>SUCCESS!</h3>
                            Your abstract was successfully uploaded. We have sent you a confirmation email.
                        </div>
                        <div class=\"col-md-3\"></div>
                    </div>";
                }else{
                    echo "<script type=\"text/javascript\">"."alert('ERROR: ".$errors[0]." ".$error[1]."');"."</script>";
                }
            }

            $url = "http://www.ai-sf.it/dbicps/edit_abstract/uploads/".$new_file_name;

            $mail = new PHPMailer;

            #$mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'icps2017registration@ai-sf.it';                 // SMTP username
            $mail->Password = 'ICPSitalia4ever';                           // SMTP password
            $mail->SMTPSecure = 'ssl';    // Enable encryption, 'ssl' also accepted
            $mail->Port = 587;

            $mail->From = 'icps2017registration@ai-sf.it';
            $mail->FromName = 'ICPS2017 Organizing Committee';
            $mail->addAddress($email);     // Add a recipient
            #$mail->addAddress('ellen@example.com');               // Name is optional
            #$mail->addReplyTo('info@example.com', 'Information');
            #$mail->addCC('cc@example.com');
            #$mail->addBCC('bcc@example.com');

            #$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
            #$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            #$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            $mail->isHTML(true);                                  // Set email format to HTML

            $mail->Subject = 'ICPS2017 abstract';
            $mail->Body    = "Dear " . $row['NAME'] .",<br><p>Your abstract has been successfully uploaded!<br><br>
        You can find it at the link ".$url."<br>You will be able to upload a new version of your abstract, using the same form, until the 20th of April.</p><p></p><p>The ICPS2017 Organizing Committee</p>";
            $mail->send();


            $result->free();
            if ($_POST['type']=='poster'){
                $stringa1 = "UPDATE " . $table . " SET URL_POSTER='".$url."' WHERE ID=".$id;
                if ($row['CONTRIBUTION']=='talk'){
                    $stringa2 = "UPDATE " . $table . " SET CONTRIBUTION='both' WHERE ID=".$id;
                } else{
                    $stringa2 = "UPDATE " . $table . " SET CONTRIBUTION='poster' WHERE ID=".$id;
                }
            } elseif ($_POST['type']=='talk'){
                $stringa1 = "UPDATE " . $table . " SET URL_TALK='".$url."' WHERE ID=".$id;
                if ($row['CONTRIBUTION']=='poster'){
                    $stringa2 = "UPDATE " . $table . " SET CONTRIBUTION='both' WHERE ID=".$id;
                } else{
                    $stringa2 = "UPDATE " . $table . " SET CONTRIBUTION='talk' WHERE ID=".$id;
                }
            }
            $result = $mysqli->query($stringa1);
            $result = $mysqli->query($stringa2);
            $mysqli->close();

        }
        ?>