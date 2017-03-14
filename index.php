<?php

require('access.php');
$VID = $_SESSION['VID'];
// store activity in log
//require('sessioner.php');
//howManyIps();

require('util.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>ICPS 2017 - DB QUERY INTERFACE</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    </head>
    <body>

        <br>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4" style="text-align:center"><img style="width:135px;height:202px;" src="LOGO.jpg" alt="ICPS_logo"></div>
            <div class="col-md-4"></div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h2 style="text-align:center">Participants registration system</h2>
            </div>
            <div class="col-md-2"></div>
        </div>

        <br>

        <div class="row" style="text-align:center">
            <div class="col-md-2"></div>
            <div class="col-md-8"><h3>Logged in with VID =
                <font color="green"><?php echo $VID;?>    </font>
                <small><a href="logout.php" ><b>LOG OUT</b></a></small>
                </h3></div>
            <div class="col-md-2"></div>
        </div>

        <br><br><br>


        <form action="search.php" method="GET">

            <div class="row" style="text-align:left">
                <div  class="col-md-2"></div>
                <div  class="col-md-2"><b>Search by name, surname</b></div>
                <div class="col-md-2"><b>Sort results by</b></div>
                <div class="col-md-2"><b>Filter on visa or operator</b></div>
                <div class="col-md-2"><b>Filter on status</b></div>
                <div  class="col-md-2"></div>
            </div>

            <div class="row" style="text-align:left">
                <div  class="col-md-2"></div>
                <div  class="col-md-2">
                    <input type="text" name="query" placeholder="leave blank for all" />
                </div>
                <div class="col-md-2">
                    <select name="sorting">
                        <option value="SURNAME_STRIP">Surname</option>
                        <option value="NAME_STRIP">Name</option>
                        <option value="LCNC">LC/NC</option>
                        <option value="CONTRIBUTION">Contribution</option>
                        <option value="DELEGATE">Delegate</option>
                        <option value="DOB">D.O.B.</option>
                        <option value="STATUS">Status</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="cfilter">
                        <option value="all">all</option>
                        <option value="visa">requiring visa</option>
                        <option value="me">last edited by me</option>
                        <option value="others">last edited by others</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="cfilter2">
                        <option value="all">all</option>
                        <option value="accepted">accepted</option>
                        <option value="waiting">waiting list</option>
                        <option value="rejected">rejected</option>
                        <option value="withdrawn">withdrawn</option>
                        <option value="participant">participant</option>
                        <option value="proven">proven</option>
                    </select>
                </div>
                <div  class="col-md-2"><input type="submit" value="Submit query" /></div>
            </div>
        </form>

        <br><br>
        
        <div class="row" style="text-align:left">
            <div  class="col-md-2"></div>
            <div  class="col-md-9"><hr></div>
            <div  class="col-md-1"></div>
        </div>
        
        <br>

        <div class="row" style="text-align:left">
            <div  class="col-md-2"></div>
            <div  class="col-md-2"><b>Jump to ID</b></div>
            <div  class="col-md-8"></div>
        </div>
        <form action="jumpToID.php" method="GET">
            <div class="row" style="text-align:left">
                <div  class="col-md-2"></div>
                <div  class="col-md-2"><input type="text" name="ID" placeholder="ID" /></div>
                <div  class="col-md-2"><input type="submit" value="Go" /></div>
                <div  class="col-md-6"></div>
            </div>
        </form>

        <br>

        <div class="row" style="text-align:left">
            <div class="col-md-2"></div>
            <div class="col-md-2"><a class="btn btn-default" href="map.php" >Map</a></div>
            <div class="col-md-8"></div>
        </div>


        <br><br>

        <p><small>

            <?php
            echo 'Current PHP version: ' . phpversion() . '<br>';
            ?>
            EV.
            </small></p>
        <br><br>
        <?php

        if ( isset($privileges) and $privileges[$VID] == 0 ) {
            echo "<a href=\"monitor.php\"><small>MONITOR</small></a>";
        }
        ?>
        
<!-- ANVEDI -->

    </body>
</html>