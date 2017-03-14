<?php
// get VID from session
require('access.php');
$VID = $_SESSION['VID'];
// store activity in log
//require('sessioner.php');
//howManyIps();

// connect to db
$dbinfo = explode("\n", file_get_contents('loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');



// data container
$data = array(); // will be data['all'] (no filter) and data['accepted'] (filter on accepted)


// all submissions
$stringa = "SELECT b.NATIONALITY_ISO,a.`COUNT(NATIONALITY)` as 'COUNT' FROM (SELECT NATIONALITY,COUNT(NATIONALITY) FROM " . $table . " GROUP BY NATIONALITY) a LEFT JOIN (SELECT NATIONALITY,NATIONALITY_ISO FROM nat2iso) b ON a.NATIONALITY=b.NATIONALITY";
$result = $mysqli->query($stringa);
$result_array = array();
while($row = $result->fetch_array()) {
    $result_array[] = array($row['NATIONALITY_ISO'],$row['COUNT']);
}
$data['all'] = json_encode($result_array);
$result->free();

// filter on accepted
$stringa = "SELECT b.NATIONALITY_ISO,a.`COUNT(NATIONALITY)` as 'COUNT' FROM (SELECT NATIONALITY,COUNT(NATIONALITY) FROM " . $table . $saccepted . " WHERE STATUS='accepted' GROUP BY NATIONALITY) a LEFT JOIN (SELECT NATIONALITY,NATIONALITY_ISO FROM nat2iso) b ON a.NATIONALITY=b.NATIONALITY";
$result = $mysqli->query($stringa);
$result_array = array();
while($row = $result->fetch_array()) {
    $result_array[] = array($row['NATIONALITY_ISO'],$row['COUNT']);
}
$data['accepted'] = json_encode($result_array);
$result->free();


$mysqli->close();

$which_data = ( isset($_GET['filter']) ? $_GET['filter'] : 'all' );
$next_data = ( $which_data == 'all' ? 'accepted' : 'all' );

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>ICPS 2017 - DB QUERY INTERFACE</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <script src="http://d3js.org/d3.v3.min.js"></script>
        <script src="http://d3js.org/topojson.v1.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <!-- I recommend you host this file on your own, since this will change without warning -->
        <!--<script src="https://datamaps.github.io/scripts/datamaps.world.js"></script>-->
        <script src="datamaps.js"></script>

        <style>

            .container {
                margin-bottom: 5px;
                overflow: hidden;
                border: 2px solid silver;
                border-radius: 6px;
                background: white;
            }


            .zoom-button {
                width: 30px;
                height: 30px;
                border-radius: 4px;
                border: none;
                background: silver;
                font-size: 16px;
                color: white;
                cursor: pointer;
            }

            #zoom-info {
                display: inline-block;
                padding: 10px;
            }
        </style>

    </head>
    <body>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h3><?php echo ($which_data=='all' ? 'Submissions' : 'Currently accepted')?>, by nationality</h3>
            </div>
            <div class="col-md-2"></div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <?php echo "<a class=\"btn btn-default\" href=\"map.php?filter=" . $next_data. "\" >Switch to " . $next_data . "</a>"; ?>
            </div>
            <div class="col-md-2"></div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">Zoom:
                <button class="zoom-button" data-zoom="reset">0</button>
                <button class="zoom-button" data-zoom="out">-</button>
                <button class="zoom-button" data-zoom="in">+</button>
                <div id="zoom-info"></div>
            </div>
            <div class="col-md-2"></div>
        </div>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div id="container" class="container" style="float: left; width: 100%; height: 600px;"></div>
            </div>
            <div class="col-md-2"></div>
        </div>

        <script>

            // green "#99FF99","#004c00"
            // blue "#EFEFFF","#02386F"
            // gold "#ffd700","#665600"

            // ALL (data)
            var series = <?php echo $data[$which_data]; ?>; // series = [["BLR",75],["BLZ",43],];

            // choose colors

            var which_data_js = <?php echo json_encode($which_data); ?>;

            if (which_data_js=='all') {
                color_range = ["#ffd700","#665600"];
            } else {
                color_range = ["#99FF99","#004c00"];
            }


            // Datamaps expect data in format:
            // { "USA": { "fillColor": "#42a844", numberOfWhatever: 75},
            //   "FRA": { "fillColor": "#8dc386", numberOfWhatever: 43 } }
            var dataset = {};

            // We need to colorize every country based on "numberOfWhatever"
            // colors should be uniq for every value.
            // For this purpose we create palette(using min/max series-value)
            var onlyValues = series.map(function(obj){ return obj[1]; });
            var minValue = Math.min.apply(null, onlyValues),
                maxValue = Math.max.apply(null, onlyValues);

            // create color palette function
            // color can be whatever you wish
            var paletteScale = d3.scale.linear()
            .domain([minValue,maxValue])
            .range(color_range); // blue: "#EFEFFF","#02386F".

            // fill dataset in appropriate format
            series.forEach(function(item){ //
                // item example value ["USA", 70]
                var iso = item[0],
                    value = item[1];
                dataset[iso] = { numberOfThings: value, fillColor: paletteScale(value) };
            });



            // CUT & PASTE
            function Zoom(args) {
                $.extend(this, {
                    $buttons: $(".zoom-button"),
                    $info: $("#zoom-info"),
                    scale: {
                        max: 50,
                        currentShift: 0
                    },
                    $container: args.$container,
                    datamap: args.datamap
                });

                this.init();
            }

            Zoom.prototype.init = function() {
                var paths = this.datamap.svg.selectAll("path"),
                    subunits = this.datamap.svg.selectAll(".datamaps-subunit");

                // preserve stroke thickness
                paths.style("vector-effect", "non-scaling-stroke");

                // disable click on drag end
                subunits.call(
                    d3.behavior.drag().on("dragend", function() {
                        d3.event.sourceEvent.stopPropagation();
                    })
                );

                this.scale.set = this._getScalesArray();
                this.d3Zoom = d3.behavior.zoom().scaleExtent([1, this.scale.max]);

                this._displayPercentage(1);
                this.listen();
            };

            Zoom.prototype.listen = function() {
                this.$buttons.off("click").on("click", this._handleClick.bind(this));

                this.datamap.svg
                    .call(this.d3Zoom.on("zoom", this._handleScroll.bind(this)))
                    .on("dblclick.zoom", null); // disable zoom on double-click
            };

            Zoom.prototype.reset = function() {
                this._shift("reset");
            };

            Zoom.prototype._handleScroll = function() {
                var translate = d3.event.translate,
                    scale = d3.event.scale,
                    limited = this._bound(translate, scale);

                this.scrolled = true;

                this._update(limited.translate, limited.scale);
            };

            Zoom.prototype._handleClick = function(event) {
                var direction = $(event.target).data("zoom");

                this._shift(direction);
            };

            Zoom.prototype._shift = function(direction) {
                var center = [this.$container.width() / 2, this.$container.height() / 2],
                    translate = this.d3Zoom.translate(),
                    translate0 = [],
                    l = [],
                    view = {
                        x: translate[0],
                        y: translate[1],
                        k: this.d3Zoom.scale()
                    },
                    bounded;

                translate0 = [
                    (center[0] - view.x) / view.k,
                    (center[1] - view.y) / view.k
                ];

                if (direction == "reset") {
                    view.k = 1;
                    this.scrolled = true;
                } else {
                    view.k = this._getNextScale(direction);
                }

                l = [translate0[0] * view.k + view.x, translate0[1] * view.k + view.y];

                view.x += center[0] - l[0];
                view.y += center[1] - l[1];

                bounded = this._bound([view.x, view.y], view.k);

                this._animate(bounded.translate, bounded.scale);
            };

            Zoom.prototype._bound = function(translate, scale) {
                var width = this.$container.width(),
                    height = this.$container.height();

                translate[0] = Math.min(
                    (width / height) * (scale - 1),
                    Math.max(width * (1 - scale), translate[0])
                );

                translate[1] = Math.min(0, Math.max(height * (1 - scale), translate[1]));

                return {
                    translate: translate,
                    scale: scale
                };
            };

            Zoom.prototype._update = function(translate, scale) {
                this.d3Zoom
                    .translate(translate)
                    .scale(scale);

                this.datamap.svg.selectAll("g")
                    .attr("transform", "translate(" + translate + ")scale(" + scale + ")");

                this._displayPercentage(scale);
            };

            Zoom.prototype._animate = function(translate, scale) {
                var _this = this,
                    d3Zoom = this.d3Zoom;

                d3.transition().duration(350).tween("zoom", function() {
                    var iTranslate = d3.interpolate(d3Zoom.translate(), translate),
                        iScale = d3.interpolate(d3Zoom.scale(), scale);

                    return function(t) {
                        _this._update(iTranslate(t), iScale(t));
                    };
                });
            };

            Zoom.prototype._displayPercentage = function(scale) {
                var value;

                value = Math.round(Math.log(scale) / Math.log(this.scale.max) * 100);
                this.$info.text(value + "%");
            };

            Zoom.prototype._getScalesArray = function() {
                var array = [],
                    scaleMaxLog = Math.log(this.scale.max);

                for (var i = 0; i <= 10; i++) {
                    array.push(Math.pow(Math.E, 0.1 * i * scaleMaxLog));
                }

                return array;
            };

            Zoom.prototype._getNextScale = function(direction) {
                var scaleSet = this.scale.set,
                    currentScale = this.d3Zoom.scale(),
                    lastShift = scaleSet.length - 1,
                    shift, temp = [];

                if (this.scrolled) {

                    for (shift = 0; shift <= lastShift; shift++) {
                        temp.push(Math.abs(scaleSet[shift] - currentScale));
                    }

                    shift = temp.indexOf(Math.min.apply(null, temp));

                    if (currentScale >= scaleSet[shift] && shift < lastShift) {
                        shift++;
                    }

                    if (direction == "out" && shift > 0) {
                        shift--;
                    }

                    this.scrolled = false;

                } else {

                    shift = this.scale.currentShift;

                    if (direction == "out") {
                        shift > 0 && shift--;
                    } else {
                        shift < lastShift && shift++;
                    }
                }

                this.scale.currentShift = shift;

                return scaleSet[shift];
            };

            function Datamap(dvx,scontainer) {
                this.$container = $(scontainer);
                this.instance = new Datamaps({
                    //scope: 'world',
                    element: this.$container.get(0),
                    projection: 'mercator',
                    fills: { defaultFill: '#F5F5F5' }, // countries don't listed in dataset will be painted with this color
                    data: dvx,
                    geographyConfig: {
                        borderColor: '#DEDEDE',
                        highlightBorderWidth: 2,
                        // don't change color on mouse hover
                        highlightFillColor: function(geo) {
                            return geo['fillColor'] || '#F5F5F5';
                        },
                        // only change border
                        highlightBorderColor: '#B7B7B7',
                        // show desired information in tooltip
                        popupTemplate: function(geo, data) {
                            // don't show tooltip if country not present in dataset
                            if (!data) { return ; }
                            // tooltip content
                            return ['<div class="hoverinfo">',
                                    '<strong>', geo.properties.name, '</strong>',
                                    '<br><strong>', data.numberOfThings, '</strong>',
                                    '</div>'].join('');
                        }
                    },
                    done: this._handleMapReady.bind(this)
                });// end datamaps
            }

            Datamap.prototype._handleMapReady = function(datamap) {
                this.zoom = new Zoom({
                    $container: this.$container,
                    datamap: datamap
                });
            }

            new Datamap(dataset,"#container");
            //new Datamap(dataset,"#container2");

        </script>

        <br>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <a class="btn btn-default" href="index.php" >New search</a>
                <a class="btn btn-default" href="search.php?query=&sorting=SURNAME_STRIP&cfilter=all&cfilter2=all" >Full list</a>
            </div>
            <div class="col-md-2"></div>
        </div>


    </body>
</html>