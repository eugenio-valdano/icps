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
        <link rel="stylesheet" href="morestyle.css">

        <!-- jquery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <!-- css and js for the autocomplete -->
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    </head>
    <body>

        <br>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h1 style="text-align:center">ICPS2017 Central database</h1>
            </div>
            <div class="col-md-2"></div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-9"><h4>Logged in as <font color="green"><?php echo $VID;?></font></h4>
                <a href="logout.php" class="btn btn-info">log out</a>
                </div>
            <div class="col-md-2"></div>
        </div>

        <br><br><br>


        <form action="search.php" method="POST">
            
            <input type="hidden" name="sorting" value="SURNAME_STRIP" />

            <div class="row" style="text-align:left">
                <div class="col-md-3"></div>
                <div class="col-md-2"></div>
                <div class="col-md-2"><h4>Filters</h4></div>
                <div class="col-md-2"></div>
                <div class="col-md-3"></div>
            </div>
            
            <br>
            
            <div class="row" style="text-align:left">
                <div class="col-md-3"></div>
                <div class="col-md-2"><b>name or surname</b></div>
                <div class="col-md-2"><b>nationality</b></div>
                <div class="col-md-2"><b>LC/NC/IM</b></div>
                <div class="col-md-3"></div>
            </div>

            <div class="row" style="text-align:left">
                <div class="col-md-3"></div>
                <div class="col-md-2"><input id="queryName" type="text" name="name" placeholder="leave blank for all" class="auto" value="" /></div>
                <div class="col-md-2"><input id="queryNationality" type="text" name="nationality" placeholder="leave blank for all" class="auto" value="" /></div>
                <div class="col-md-2"><input id="queryLcnc" type="text" name="lcnc" placeholder="leave blank for all"  class="auto" value="" /></div>
                <div class="col-md-3"></div>
            </div>

            <br>

            <div class="row" style="text-align:left">
                <div class="col-md-3"></div>
                <div class="col-md-2"><input type="submit" value="Submit query" class="btn btn-primary" /></div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>
                <div class="col-md-3"></div>
            </div>

        </form>


        <br><br><br><br><br>
        
        <form action="jumpToID.php" method="GET">
            <div class="row" style="text-align:left">
                <div  class="col-md-3"></div>
                <div  class="col-md-2" style="text-align:right"><b>Jump to ID</b></div>
                <div  class="col-md-2"><input type="text" name="ID" placeholder="ID" /></div>
                <div  class="col-md-5"><input type="submit" value="Go" class="btn btn-primary" /></div>
            </div>
        </form>

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


    </body>


</html>