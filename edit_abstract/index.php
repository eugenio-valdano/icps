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

        <div class="row">
            <div class="col-md-3" align="center"><img style="width:135px;height:202px;" src="../LOGO.jpg" alt="ICPS_logo"></div>
            <div class="col-md-6">

                <form action="upload.php" method="POST" enctype="multipart/form-data">
                    <table class='table'>
                        <tr>
                            <td width="30%" align="right"><b>Name</b></td>
                            <td>
                                <input name="name" required="required" type="text" <?php echo (isset($_GET['name']) ? "value=".$_GET['name'] : "placeholder=\"Your name as in the registration form\""); ?> size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Surname</b></td>
                            <td>
                                <input name="surname" required="required" type="text" <?php echo (isset($_GET['surname']) ? "value=".$_GET['surname'] : "placeholder=\"Your surname as in the registration form\""); ?> size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Uid</b></td>
                            <td>
                                <input name="unique_id" required="required" type="text" <?php echo (isset($_GET['uid']) ? "value=".$_GET['uid'] : "placeholder=\"Your unique id\""); ?> size="35"/>
                            </td>
                        </tr>
                  <!--      <tr>
                            <td width="30%" align="right"><b>Date of birth</b></td>
                            <td>
                                <input name="dob" required="required" type="date" size="35" value="<?php echo (isset($_GET['dob']) ? format_data($_GET['dob']) : ""); ?>" />
                            </td>
                        </tr>-->
                        <tr>
                            <td width="30%" align="right"><b>Email</b></td>
                            <td>
                                <input name="email" required="required" type="email" <?php echo (isset($_GET['email']) ? "value=".$_GET['email'] : "placeholder=\"Your email address\""); ?> size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Talk or poster?</b></td>
                            <td>
                                <select id="type" name="type" required="required">
                                    <option disabled selected value> -- select an option -- </option>
                                    <option value="poster" <?php echo ($_GET['type']=='poster' ? "selected" : "") ?>>Poster</option>
                                    <option value="talk"<?php echo ($_GET['type']=='talk' ? "selected" : "") ?>>Talk</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Upload your abstract</b></td>
                            <td>
                                <input type="file" name="abstract" /> <i>Allowed file extensions: pdf,doc,docx,txt</i>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btn-primary" type="submit" value="Submit">Upload</button>
                            </td>
                        </tr>
                    </table>
                </form>

            </div>
        </div>
        <div class="col-md-3"></div>

    </body>
</html>