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

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Search results</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- CSS for overriding td class -->
        <link rel="stylesheet" href="tdStyle.css">

        <!-- jquery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

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

        <?php

        // GET POST VARIABLES
        if (isset($_POST['name'])) {
            $queryName0 = $_POST['name'];
            $queryNationality = $_POST['nationality'];
            $queryLcnc = $_POST['lcnc'];
            $sorting = $_POST['sorting'];

        } else {
            $queryName0 = "";
            $queryNationality = "";
            $queryLcnc = "";
            $sorting = "SURNAME_STRIP";
        }
        
        $queryName = explode(" ", $queryName0);
        if (count($queryName)==1) {
            $queryName = array($queryName[0],$queryName[0]);
        }

        // QUERY DB
        $condition1 = "NAME LIKE '%" . $queryName[1] . "%' OR SURNAME LIKE '%" . $queryName[0] . "%' OR NAME_STRIP LIKE '%" . $queryName[1] . "%' OR SURNAME_STRIP LIKE '%" . $queryName[0] . "%'";
        $condition2 = "NAME LIKE '%" . $queryName[0] . "%' OR SURNAME LIKE '%" . $queryName[1] . "%' OR NAME_STRIP LIKE '%" . $queryName[0] . "%' OR SURNAME_STRIP LIKE '%" . $queryName[1] . "%'";
        $condition = "( " . $condition1 . ") OR ( " . $condition2 . " ) "; 
        $condition = "(" . $condition . ") AND NATIONALITY LIKE '%" . $queryNationality . "%' AND LCNC LIKE '%" . $queryLcnc . "%'";
        $stringa = "SELECT * FROM " . $table . " WHERE (" . $condition . ") ORDER BY " . $sorting;

        $result = $mysqli->query($stringa);
        $entries = $result->num_rows;

        ?>

        <nav class="navbar navbar-inverse" data-spy="affix" data-offset-top="10">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Matching entries: <?php echo $entries;?></a></li>
                <li class="active"><a href="logout.php">VID: <?php echo $VID;?> (logout)</a></li>
                <li class="active"><a href="index.php">New search</a></li>
                <li class="active"><a href="search.php">Full list</a></li>
                <li class="active"><a href="excursions_stat.php">Excursions</a></li>
                <li class="active"><a href="roomView.php">Room view</a></li>
                <li class="active"><a href="registrationStats.php">Registration stats</a></li>
            </ul>
        </nav>

        <div class="container-fluid" style="height:1000px">

            <div class="row">
                <!--<div class="col-md-1"></div>-->
                <div class="col-md-12">

                    <table class='table'>
                        <tr>
                            <th style="vertical-align: center">
                            </th>
                            <th>
                                <form action="search.php" method="POST">
                                    <input type="hidden" name="name" value="<?php echo $queryName0;?>"/>
                                    <input type="hidden" name="nationality" value="<?php echo $queryNationality;?>"/>
                                    <input type="hidden" name="lcnc" value="<?php echo $queryLcnc;?>"/>
                                    <input type="hidden" name="sorting" value="SURNAME_STRIP"/>
                                    <input type="submit" value="SURNAME" class="btn btn-link" />
                                </form>
                            </th>
                            <th>
                                <form action="search.php" method="POST">
                                    <input type="hidden" name="name" value="<?php echo $queryName0;?>"/>
                                    <input type="hidden" name="nationality" value="<?php echo $queryNationality;?>"/>
                                    <input type="hidden" name="lcnc" value="<?php echo $queryLcnc;?>"/>
                                    <input type="hidden" name="sorting" value="NAME_STRIP"/>
                                    <input type="submit" value="NAME" class="btn btn-link" />
                                </form>
                            </th>
                            <th>
                                <form action="search.php" method="POST">
                                    <input type="hidden" name="name" value="<?php echo $queryName0;?>"/>
                                    <input type="hidden" name="nationality" value="<?php echo $queryNationality;?>"/>
                                    <input type="hidden" name="lcnc" value="<?php echo $queryLcnc;?>"/>
                                    <input type="hidden" name="sorting" value="NATIONALITY"/>
                                    <input type="submit" value="NATIONALITY" class="btn btn-link" />
                                </form>
                            </th>
                            <th>
                                <form action="search.php" method="POST">
                                    <input type="hidden" name="name" value="<?php echo $queryName0;?>"/>
                                    <input type="hidden" name="nationality" value="<?php echo $queryNationality;?>"/>
                                    <input type="hidden" name="lcnc" value="<?php echo $queryLcnc;?>"/>
                                    <input type="hidden" name="sorting" value="LCNC"/>
                                    <input type="submit" value="LC/NC" class="btn btn-link" />
                                </form>
                            </th>
                            <th>
                                <form action="search.php" method="POST">
                                    <input type="hidden" id="name" name="name" value="<?php echo $queryName0;?>"/>
                                    <input type="hidden" id="nationality"  name="nationality" value="<?php echo $queryNationality;?>"/>
                                    <input type="hidden" id="lcnc" name="lcnc" value="<?php echo $queryLcnc;?>"/>
                                    <input type="hidden" id="sorting" name="sorting" value="DOB"/>
                                    <input type="submit" value="D.O.B." class="btn btn-link" />
                                </form>
                            </th>
                            <th>
                                <form action="search.php" method="POST">
                                    <input type="hidden" id="name" name="name" value="<?php echo $queryName0;?>"/>
                                    <input type="hidden" id="nationality"  name="nationality" value="<?php echo $queryNationality;?>"/>
                                    <input type="hidden" id="lcnc" name="lcnc" value="<?php echo $queryLcnc;?>"/>
                                    <input type="hidden" name="sorting" value="DELEGATE"/>
                                    <input type="submit" value="DELEGATE" class="btn btn-link" />
                                </form>
                            </th>
                            <?php if ( checkp(3,$VID) ):?>
                            <th>
                                <form action="search.php" method="POST">
                                    <input type="hidden" id="name" name="name" value="<?php echo $queryName0;?>"/>
                                    <input type="hidden" id="nationality"  name="nationality" value="<?php echo $queryNationality;?>"/>
                                    <input type="hidden" id="lcnc" name="lcnc" value="<?php echo $queryLcnc;?>"/>
                                    <input type="hidden" id="sorting" name="sorting" value="CONTRIBUTION"/>
                                    <input type="submit" value="CONTRIBUTION" class="btn btn-link" />
                                </form>
                            </th>
                            <th>
                                <form action="search.php" method="POST">
                                    <input type="hidden" id="name" name="name" value="<?php echo $queryName0;?>"/>
                                    <input type="hidden" id="nationality"  name="nationality" value="<?php echo $queryNationality;?>"/>
                                    <input type="hidden" id="lcnc" name="lcnc" value="<?php echo $queryLcnc;?>"/>
                                    <input type="hidden" id="sorting" name="sorting" value="ID"/>
                                    <input type="submit" value="ID" class="btn btn-link" />
                                </form>
                            </th>
                            <th>
                                <form action="search.php" method="POST">
                                    <input type="hidden" id="name" name="name" value="<?php echo $queryName0;?>"/>
                                    <input type="hidden" id="nationality"  name="nationality" value="<?php echo $queryNationality;?>"/>
                                    <input type="hidden" id="lcnc" name="lcnc" value="<?php echo $queryLcnc;?>"/>
                                    <input type="hidden" id="sorting" name="sorting" value="ROOM"/>
                                    <input type="submit" value="ROOM" class="btn btn-link" />
                                </form>
                            </th>
                            <?php endif;?>
                        </tr>

                        <?php

                        // build table printing the rows found

                        while($row = $result->fetch_array())
                        {
                            if ($row['REGISTRATION']=='no') {
                                $str_descr = 'not arrived';
                                $col_descr = 'warning';
                            } elseif ($row['REGISTRATION']=='yes') {
                                $str_descr = 'present';
                                $col_descr = 'success';
                            } else {
                                $str_descr = 'departed';
                                $col_descr = 'info';
                            }
                            
                            // registration
                            $linkto = "registration.php?ID=" . $row['ID'] . "&IDC=" . $row['ID_CHECK'];
                            $col_reg = "<td><a href=\"".$linkto."\" class=\"btn btn-" . $col_descr . "\">". $str_descr ."</a></td>";
                            
                            
                            // link to single entry
                            $linkto = "singleEntry.php?ID=" . $row['ID'] . "&IDC=" . $row['ID_CHECK'];

                            // various columns
                            if ( checkp(3,$VID) ) {
                                $col_surname = "<td><a href=\"".$linkto."\">".$row['SURNAME']."</a></td>";
                            } else {
                                $col_surname = "<td>".$row['SURNAME']."</td>";
                            }
                            
                            

                            $col_name = "<td>".$row['NAME']."</td>";

                            $col_dob = "<td><small>".$row['DOB']."</small></td>";

                            $col_nat = "<td>".$row['NATIONALITY']."</td>";

                            $col_lcnc = "<td>" . ( $row['LCNC']=='IM' ? "" : $row['LCNC'] ) . "</td>";

                            // contribution
                            if ($row['CONTRIBUTION']=='no') {
                                $col_contr = "<td><span id=\"nourl\">NO</span></td>";
                            } else {
                                $col_contr = '';
                                if ($row['CONTRIBUTION']!='post') {
                                    $col_contr .= ($row['URL_TALK']=='' ? "<span id=\"nourl\">talk</span>" : "<span id=\"withurl\">talk</span>");
                                }
                                if ($row['CONTRIBUTION']!='talk') {
                                    $col_contr .= ($col_contr==''?'':' & ') . ($row['URL_POSTER']=='' ? "<span id=\"nourl\">poster</span>" : "<span id=\"withurl\">poster</span>");
                                }
                                $col_contr = '<td>' . $col_contr . '</td>';   
                            }

                            $col_deleg = "<td>" . ( is_null($row['DELEGATE']) ? "no" : "yes" ) . "</td>";

                            
                            // room
                            if ( is_null($row['ROOM']) ) {
                                $col_roompref = '<i>no room</i>';
                            } else {
                                $lonqo = 'roomView.php#' . $row['RESIDENCE'];
                                $col_roompref = '<a href="' . $lonqo . '" style="font-weight:bold">' . substr($row['RESIDENCE'],0,1) . '</a> - ' . $row['ROOM'] ;
                            }
                            /*
                            if ($preference==0) {
                                $col_roompref = "-";
                            } elseif($preference==-1) {
                                $col_roompref = "<span style=\"font-style:italic;\">single</span>";
                            } else {
                                $stronga = "SELECT `SURNAME`, `ID`, `ID_CHECK` FROM `" . $table . "` WHERE `ID`=" . $preference;
                                $rosico = $mysqli->query($stronga);
                                $ronco = $rosico->fetch_array();
                                $linktoronco = "singleEntry.php?ID=" . $ronco['ID'] . '&IDC=' . $ronco['ID_CHECK'];
                                $col_roompref = "<a href=\"" . $linktoronco . "\">" . $ronco['SURNAME'] . " (" . $ronco['ID'] . ")</a>";
                                
                            }
                            */
                            $col_roompref = "<td>" . $col_roompref . "</td>";
                            
                            // id
                            $col_id = "<td>" . $row['ID'] . "</td>";

                            echo "<tr>" . $col_reg . $col_surname . $col_name . $col_nat . $col_lcnc . $col_dob . $col_deleg;
                            if ( checkp(3,$VID) ) {
                                echo $col_contr . $col_id . $col_roompref;
                            }
                            echo "</tr>";


                        }

                        $result->free();
                        $mysqli->close();

                        ?>
                    </table>

                </div>
                <!-- <div class="col-md-1"></div> -->
            </div>

            <br>

        </div>


    </body>
</html>