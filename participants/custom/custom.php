<?php
// get VID from session
require('../access.php');
$VID = $_SESSION['VID'];


// load functions
require('../../util.php');

// connect to db
$dbinfo = explode("\n", file_get_contents('../../loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
#$table_excursions = $dbinfo[15];
$table_late = $dbinfo[19];
$table_total = $dbinfo[21];

//choose table
$table = $table_total;


$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>ICPS 2017 - DB QUERY INTERFACE</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../morestyle.css">

        <!-- jquery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <!-- css and js for the autocomplete -->
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <style>
            /* Note: Try to remove the following lines to see the effect of CSS positioning */
            .affix {
                top: 0;
                width: 100%;
                z-index: 500;
            }

            .affix + .container-fluid {
                padding-top: 70px;
            }

            #withurl {
                color: #00007f !important;
                font-weight: bold;

            }
            #nourl {
                color: #939393 !important;
            }

        </style>

    </head>
    <body>

        <nav class="navbar navbar-inverse" data-spy="affix" data-offset-top="10">
            <ul class="nav navbar-nav">
                <li class="active"><a href="../logout.php">VID: <?php echo $VID;?> (logout)</a></li>
                <li class="active"><a href="../index.php">New search</a></li>
                <li class="active"><a href="../search.php">Full list</a></li>
                <li class="active"><a href="../excursions_stat.php">Excursions</a></li>
                <li class="active"><a href="../roomView.php">Room view</a></li>
                <li class="active"><a href="../registrationStats.php">Registration stats</a></li>
            </ul>
        </nav>

        <div class="container-fluid" style="height:1000px">

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <h1 class="titolo">ICPS2017 Central database - custom search</h1>
                </div>
                <div class="col-md-2"></div>
            </div>

            <br><br>

            <div class="row" style="text-align:left">
                <div class="col-md-2"></div>
                <div class="col-md-6">
                    <p>
                        Mandatory fields are fields participants were required to fill. Optional fields could be left blank. If you select one mandatory field, you will see only people with something in that field. If you select more than one mandatory field, you see people with something in at least one of those fields.
                    </p>
                </div>
                <div class="col-md-4"></div>
            </div>

            <br><br>

            <form action="custom_list.php" method="POST">

                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-8"><h2 class="titolo" style="text-align:left">1. Select the desired mandatory fields</h2></div>
                    <div class="col-md-2"></div>
                </div>

                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <input type="checkbox" name="ID" value="" checked> ID<br>
                        <input type="checkbox" name="SURNAME" value="" checked> Surname<br>
                        <input type="checkbox" name="NAME" value="" checked> Name<br>
                        <input type="checkbox" name="ID_CHECK" value=""> ID_CHECK<br>
                        <input type="checkbox" name="DOB" value=""> Date of birth<br>
                        <input type="checkbox" name="NATIONALITY" value=""> Nationality<br>
                        <input type="checkbox" name="DEGREE" value=""> Currently enrolled in (degree)<br>
                        <input type="checkbox" name="COUNTRY_STUDY" value=""> Country of study<br>
                        <input type="checkbox" name="UNIVERSITY" value=""> University<br>
                        <input type="checkbox" name="EMAIL" value=""> Email address<br>
                        <input type="checkbox" name="PASSPORT" value=""> Passport<br>
                        <input type="checkbox" name="LCNC" value=""> LC / NC / IM<br>
                        <input type="checkbox" name="CONTRIBUTION" value=""> Contribution (no/post/talk/both)<br>
                        <input type="checkbox" name="ASSIGNED" value=""> Excursion <a href="../excursions_stat.php">(A-H)</a><br>
                        <input type="checkbox" name="CITYRALLY" value=""> City rally (1,2)<br>
                        <input type="checkbox" name="MAGO" value=""> Mago's group (0-9)<br>
                        <input type="checkbox" name="RESIDENCE" value=""> Residence (Olimpia/Verdi)<br>
                        <input type="checkbox" name="ROOM" value=""> Room number<br>
                        <input type="checkbox" name="ROOM_PAIRING" value=""> Sleeping with (ID) <br>
                        <input type="checkbox" name="CHECKIN_DATE" value=""> Check-in date<br>
                        <input type="checkbox" name="REGISTRATION" value=""> Registration ('no', 'yes', 'out')<br>
                        <input type="checkbox" name="DEPOSIT" value=""> Deposit ('not collected', 'collected', 'returned', 'withheld')<br>
                        <input type="checkbox" name="OPERATOR" value=""> Last operator (VID) editing registration status<br>
                    </div>
                    <div class="col-md-2"></div>
                </div>

                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-8"><h2 class="titolo" style="text-align:left">2. Select the desired optional fields<br><small>automatically filters on at least one non empty among selected</small></h2></div>
                    <div class="col-md-2"></div>
                </div>

                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <input type="checkbox" name="DIET" value="opt"> Dietary requirements<br>
                        <input type="checkbox" name="ALLERGIES" value="opt"> Allergies<br>
                        <input type="checkbox" name="INFO" value="opt"> Info<br>
                        <input type="checkbox" name="DELEGATE" value="opt" id="DELEGATE" > Delegate (filter on who's a delegate)<br>
                        <input type="checkbox" name="DELEGATE_DETAIL" value="opt" id="DELEGATE_DETAIL"> Delegate detail (type of delegate)<br>
                        <input type="checkbox" name="VISA" value="opt"> Requires visa<br>
                        <input type="checkbox" name="SPORT" value="opt"> Sport (individual) [NULL,'pool','chess']<br>
                        <input type="checkbox" name="NOTES" value="opt"> Notes (made by us)<br>
                    </div>
                    <div class="col-md-2"></div>
                </div>

                <script>
                    // bind DELEGATE and DELEGATE_DETAIL checkboxes
                    $('#DELEGATE').change(function () {
                        if ($(this).prop('checked')) {
                            $('#DELEGATE_DETAIL').prop('checked', true);
                        } else {
                            $('#DELEGATE_DETAIL').prop('checked', false);
                        }
                    });
                    $('#DELEGATE').trigger('change');

                    $('#DELEGATE_DETAIL').change(function () {
                        if ($(this).prop('checked')) {
                            $('#DELEGATE').prop('checked', true);
                        } else {
                            $('#DELEGATE').prop('checked', false);
                        }
                    });
                    $('#DELEGATE_DETAIL').trigger('change');


                </script>

                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-8"><h2 class="titolo" style="text-align:left">3. Sort by</h2></div>
                    <div class="col-md-2"></div>
                </div>

                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <input type="radio" name="SORT" value="ID"> ID<br>
                        <input type="radio" name="SORT" value="SURNAME_STRIP" checked> Surname<br>
                        <input type="radio" name="SORT" value="NAME_STRIP"> Name<br>
                        <input type="radio" name="SORT" value="NATIONALITY"> Nationality<br>
                    </div>
                    <div class="col-md-2"></div>
                </div>

                <br>

                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-2"><input type="submit" value="Download CSV" class="btn btn-success" /></div>
                    <div class="col-md-2"></div>
                    <div class="col-md-2"></div>
                    <div class="col-md-4"></div>
                </div>

            </form>

            <!--
            <br>

            <div class="row" style="text-align:left">
                <div class="col-md-2"></div>
                <div class="col-md-8"><h2 class="titolo" style="text-align:left">Advanced: compose MySQL query</h2></div>
                <div class="col-md-2"></div>
            </div>

            <br>

            <form action="custom_list.php" method="POST">

                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">SELECT</div>
                    <div class="col-md-2"></div>
                </div>
                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-8"><input type="text" name="STRING_1"/></div>
                    <div class="col-md-2"></div>
                </div>
                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">WHERE</div>
                    <div class="col-md-2"></div>
                </div>
                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-8"><input type="text" name="STRING_2"/></div>
                    <div class="col-md-2"></div>
                </div>
                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">ORDER BY</div>
                    <div class="col-md-2"></div>
                </div>
                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-8"><input type="text" name="STRING_3"/></div>
                    <div class="col-md-2"></div>
                </div>
                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-2"><input type="submit" value="Download CSV" class="btn btn-success" /></div>
                    <div class="col-md-2"></div>
                    <div class="col-md-2"></div>
                    <div class="col-md-4"></div>
                </div>
            </form>
-->

            <br>
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-9">
                    <a href="../index.php" class="btn btn-info">Go back home</a>
                </div>
                <div class="col-md-1"></div>
            </div>

        </div>

    </body>


</html>