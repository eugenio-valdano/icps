<?php

require('access.php');
$VID = $_SESSION['VID'];
// store activity in log
//require('sessioner.php');
//howManyIps();

//require('util.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>ICPS 2017 - DB QUERY INTERFACE</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="morestyle.css">

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
                <li class="active"><a href="logout.php">VID: <?php echo $VID;?> (logout)</a></li>
                <li class="active"><a href="index.php">Home</a></li>
                <li class="active"><a href="search.php">Full list</a></li>
                <li class="active"><a href="excursions_stat.php">Excursions</a></li>
                <li class="active"><a href="roomView.php">Room view</a></li>
                <li class="active"><a href="registrationStats.php">Registration stats</a></li>
                <li class="active"><a href="custom/custom.php">Custom CSV</a></li>
            </ul>
        </nav>

        <div class="container-fluid" style="height:1000px">

            <br>

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <h1 class="titolo">ICPS2017 Central database</h1>
                </div>
                <div class="col-md-2"></div>
            </div>

            <br><br>


            <form action="search.php" method="POST">

                <input type="hidden" name="sorting" value="SURNAME_STRIP" />

                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-8"><h2 class="titolo" style="text-align:left">Option 1: Query matching conditions</h2></div>
                    <div class="col-md-2"></div>
                </div>

                <br>

                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-2"><b>name or surname</b></div>
                    <div class="col-md-2"><b>nationality</b></div>
                    <div class="col-md-2"><b>LC/NC/IM</b></div>
                    <div class="col-md-4"></div>
                </div>

                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-2"><input id="queryName" type="text" name="name" placeholder="leave blank for all" class="auto" value="" /></div>
                    <div class="col-md-2"><input id="queryNationality" type="text" name="nationality" placeholder="leave blank for all" class="auto" value="" /></div>
                    <div class="col-md-2"><input id="queryLcnc" type="text" name="lcnc" placeholder="leave blank for all"  class="auto" value="" /></div>
                    <div class="col-md-4"></div>
                </div>

                <br>

                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-2"><input type="submit" value="Submit query" class="btn btn-primary" /></div>
                    <div class="col-md-2"></div>
                    <div class="col-md-2"></div>
                    <div class="col-md-4"></div>
                </div>

            </form>


            <br>

            <form action="jumpToID.php" method="POST">

                <div class="row" style="text-align:left">
                    <div class="col-md-2"></div>
                    <div class="col-md-8"><h2 class="titolo" style="text-align:left">Option 2: Go to ID</h2></div>
                    <div class="col-md-2"></div>
                </div>


                <div class="row" style="text-align:left">
                    <div  class="col-md-2"></div>
                    <div  class="col-md-1"><input type="text" id="ID" name="ID" placeholder="ID" class="auto" /></div>
                    <div  class="col-md-9"></div>
                </div>
                <br>
                <div class="row" style="text-align:left">
                    <div  class="col-md-2"></div>
                    <div  class="col-md-1"><input type="submit" value="Go" class="btn btn-primary" /></div>
                    <div  class="col-md-9"></div>
                </div>
            </form>

            <br>

            <div class="row" style="text-align:left">
                <div class="col-md-2"></div>
                <div class="col-md-8"><h2 class="titolo" style="text-align:left">Option 3: Download customized CSV</h2></div>
                <div class="col-md-2"></div>
            </div>

            <div class="row" style="text-align:left">
                <div  class="col-md-2"></div>
                <div  class="col-md-1"><a href="custom/custom.php" class="btn btn-primary">Go to download page</a></div>
                <div  class="col-md-9"></div>
            </div>

            <br>

            <div class="row" style="text-align:left">
                <div class="col-md-2"></div>
                <div class="col-md-8"><h2 class="titolo" style="text-align:left">Option 4: Room view</h2></div>
                <div class="col-md-2"></div>
            </div>

            <div class="row" style="text-align:left">
                <div  class="col-md-2"></div>
                <div  class="col-md-1"><a href="roomView.php" class="btn btn-primary">Go to room view</a></div>
                <div  class="col-md-1"></div>
                <div  class="col-md-8"></div>
            </div>

            <br>

            <!--<div class="row" style="text-align:left">
<div class="col-md-2"></div>
<div class="col-md-2"><a class="btn btn-default" href="map.php" >Map</a></div>
<div class="col-md-8"></div>
</div>-->


            <!-- scripts. automatic selection, and autocompletion -->
            <script type="text/javascript">

                // NAME
                $(function() {
                    //autocomplete
                    $("#queryName").autocomplete({
                        source: "autocompletion_name.php",
                        minLength: 2
                    });                
                });

                // NATIONALITY
                $(function() {
                    //autocomplete
                    $("#queryNationality").autocomplete({
                        source: "autocompletion_nationality.php",
                        minLength: 2
                    });                
                });

                // LCNC
                $(function() {
                    //autocomplete
                    $("#queryLcnc").autocomplete({
                        source: "autocompletion_lcnc.php",
                        minLength: 2
                    });                
                });


            </script>


        </div>
    </body>


</html>