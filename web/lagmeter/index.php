<?php
	// all config here
	// make sure, it's the same token as in your config.yml
	$token		=	"your_token";
	$allowed_ip	=	array('127.0.0.1','YOUR_IP');
	// html output settings
	$how_many_days = 7;
	$show_memory = true;
    $password = "your_password"; // leave blank if you don't want to use that feature
	
	// database connection
	$db_host	=	"localhost";
	$db_user	=	"USER";
	$db_pass	=	"PASSWORD";
	$db_name	=	"DB";
    
    // chart options:
    $criticalTPS = 15; // everything below this line is highlighted in red
	
	// table does not exists? 
	/*
		DROP TABLE IF EXISTS `performance`;
		CREATE TABLE `performance` (
		  `zeitstempel` int(11) NOT NULL,
		  `tps` int(11) NOT NULL,
		  `players` int(11) NOT NULL,
		  `memory` float NOT NULL,
		  `chunks` int(11) NOT NULL,
		  `entities` int(11) NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
*/
    
	@mysql_connect($db_host, $db_user, $db_pass);
	@mysql_select_db($db_name);
   
    if (
		($_GET['token'] == $token) && 
		(in_array($_SERVER['REMOTE_ADDR'],$allowed_ip))) 
		{
			$tps		=	floatval($_GET['tps']);
			$memory		=	floatval($_GET['memory']);
			$players	=	intval($_GET['players']);
			$entities	=	intval($_GET['entities']);
			$chunks		=	intval($_GET['chunks']);
			$sql	=	"INSERT INTO `performance` (`tps`, `players`, `zeitstempel`, `memory`, `chunks`, `entities`) VALUES ('".$tps."','".$players."','".time()."','".$memory."', '".$chunks."', '".$entities."')";
			$res	=	@mysql_query($sql) or die($sql);
			echo "New performance data uploaded";
		} else {
            if ($password != "" && $_POST['password'] != $password){
                if (isset ($_POST['password']) && $_POST['password'] != $password) echo "<b style='color:red;'>Wrong password!</b><br />";
                
                echo '
                    This site is password protected! Please enter the password: <br />
                    <form action="./" method="post">
                        <input type="password" name="password" placeholder="password" />
                        <input type="submit" value="enter" />
                    </form>
                ';
                die();
            }
			?>
            	<html>
                <head>
                    <script src="./jquery.js"></script>
                    <script src="./jquery.flot.js"></script>
                    <script src="./jquery.flot.selection.js"></script>
                    <script src="./jquery.flot.threshold.js"></script>
                    <script src="./jquery.flot.resize.js"></script>
                	<script type='text/javascript' src='http://www.google.com/jsapi'></script>
                </head>
                <body>
                    <h1>wwwLag - Lagmeter</h1>
                    <input type="button" id="reset" value="Reset Zoom" />
                    show:
                    <select name="viewSelector">
                        <option value="hour">since 1 hour</option>
                        <option value="day">since 1 day</option>
                        <option value="week">since 1 week</option>
                        <option value="month">since 1 month</option>
                        <option value="everything" selected="selected">everything</option>
                    </select>
                    <div id="choices" style="float:right;"></div>
                    <div id="placeholder" style="width:100%;height:60%;min-height:400px"></div>
                    
                    
                    <script type="text/javascript">
                        $(function () { 
                            var tps = [
                            <?php
                                $sql	=	"SELECT * FROM `performance` WHERE `zeitstempel` > ".($how_many_days*24*60*60)." ORDER BY `zeitstempel` ASC";
                                $res	=	@mysql_query($sql);
                                $i=0;
                                while ($dat = @mysql_fetch_array($res)) {							
                                    $time = $dat['zeitstempel']*1000; // date("H:i:s d.m.Y",zeit);
                                    if ($i==0) $firstTime = $time;
                                    $i++;
                                    echo "
                                    [\"".$time."\",".$dat['tps']."],";
                                }
                                mysql_data_seek($res, 0);
                            ?>];
                            var players = [
                            <?php
                                while ($dat = @mysql_fetch_array($res)) {							
                                    $time = $dat['zeitstempel']*1000; // date("H:i:s d.m.Y",zeit);
                                    echo "
                                    [\"".$time."\",".$dat['players']."],";
                                }
                                mysql_data_seek($res, 0);
                            ?>];
                            var chunks = [
                            <?php
                                while ($dat = @mysql_fetch_array($res)) {							
                                    $time = $dat['zeitstempel']*1000; // date("H:i:s d.m.Y",zeit);
                                    echo "
                                    [\"".$time."\",".$dat['chunks']."],";
                                }
                                mysql_data_seek($res, 0);
                            ?>];
                            <?php if ($show_memory) { ?>
                                var memory = [
                                <?php
                                    while ($dat = @mysql_fetch_array($res)) {							
                                        $time = $dat['zeitstempel']*1000; // date("H:i:s d.m.Y",zeit);
                                        echo "
                                        [\"".$time."\",".$dat['memory']."],";
                                    }
                                    mysql_data_seek($res, 0);
                                ?>];
                            <? } //show_memory?>
                            var entities = [
                            <?php
                                while ($dat = @mysql_fetch_array($res)) {							
                                    $time = $dat['zeitstempel']*1000; // date("H:i:s d.m.Y",zeit);
                                    echo "
                                    [\"".$time."\",".$dat['entities']."],";
                                }
                                mysql_data_seek($res, 0);
                            ?>];
                            var minTime = <?php echo $firstTime; ?>;
                            var maxTime = <?php echo $time; ?>;
                            var minTimeHour = <?php echo $time - 3600000; ?>;
                            var minTimeDay = <?php echo $time - 3600000 * 24; ?>;
                            var minTimeWeek = <?php echo $time - 7 * 3600000 * 24; ?>;
                            var minTimeMonth = <?php echo $time - 30*3600000*24; ?>;
                            
                            //stores current selection/zoom
                            var min = minTime;
                            var max = maxTime;
                            
                            var options = {
                                series: {
                                    lines: {
                                        show: true
                                    },
                                    points: {
                                        show: false
                                    }
                                    
                                    
                                },
                                grid: {
                                    hoverable: true
                                },
                                legend: {
                                    noColumns: 2
                                },
                                xaxis: {
                                    tickDecimals: 0,
                                    mode: "time"
                                },
                                yaxis: {
                                    min: 0
                                },
                                selection: {
                                    mode: "x"
                                }
                            };
                            
                            var data = [
                                {
                                    label: "Ticks per Second",
                                    data: tps,
                                    threshold: {
                                        below: <?php echo $criticalTPS;?>,
                                        color: "red"
                                    }
                                },
                                {
                                    label: "Players",
                                    data: players
                                },
                                <?php if ($show_memory) { ?>
                                {
                                    label: "Memory Usage (MB)",
                                    data: memory
                                },
                                <?php } ?>
                                {
                                    label: "Chunks",
                                    data: chunks
                                },
                                {
                                    label: "Entities",
                                    data: entities
                                }];
                                
                            var placeholder = $("#placeholder");
                            var plot = $.plot(placeholder, data, options); // build graph
                            
                            placeholder.bind("plotselected", function (event, ranges) { //enable selection via mouse
                               min = ranges.xaxis.from;
                               max = ranges.xaxis.to;
                               plotAccordingToChoices();
                                
                            });
                            $("#reset").click(function(){ // reset button for zoom
                                changeView("everything");
                            });
                            
                            $('[name=viewSelector]').change(function() {
                              changeView($(this).find(":selected").val());
                            });
                            
                            //tooltip on hover
                            var previousPoint = null;
                            $("#placeholder").bind("plothover", function (event, pos, item) {
                                if (item) {
                                    if (previousPoint != item.dataIndex) {

                                        previousPoint = item.dataIndex;

                                        $("#tooltip").remove();
                                        var x = item.datapoint[0].toFixed(2),
                                        y = item.datapoint[1].toFixed(2);

                                        showTooltip(item.pageX, item.pageY,
                                            parseInt(y)+" " + item.series.label + " at " + (new Date(parseInt(x))).toLocaleTimeString() + " ("+(new Date(parseInt(x))).toLocaleDateString()+")");
                                    }
                                } else {
                                    $("#tooltip").remove();
                                    previousPoint = null;            
                                }
                            });
                            
                            // hard-code color indices to prevent them from shifting when (de)activating an option 
                            var i = 0;
                            $.each(plot.getData(), function(key, val) {
                                val.color = i;
                                ++i;
                            });

                            // insert checkboxes 
                            var choiceContainer = $("#choices");
                            $.each(plot.getData(), function(key, val) {
                                var checked="";
                                if(val.label != "Chunks" && val.label != "Memory Usage (MB)" && val.label !="Entities") checked="checked=\"checked\"";
                                choiceContainer.append("<input type='checkbox' name='" + key +
                                    "' "+checked+" id='id" + key + "'></input>" +
                                    "<label for='id" + key + "'>"
                                    + val.label + "</label>");
                            });

                            choiceContainer.find("input").click(plotAccordingToChoices);

                            plotAccordingToChoices();
                            
                            function plotAccordingToChoices() {

                                var data = [];

                                choiceContainer.find("input:checked").each(function () {
                                    var key = $(this).attr("name");
                                    if (key && plot.getData()[key]) {
                                        data.push(plot.getData()[key]);
                                    }
                                });

                                if (data.length > 0) {
                                    $.plot("#placeholder", data, $.extend(true, {}, options, {
                                        xaxis: {
                                            min: min,
                                            max: max
                                        }
                                    }));
                                }
                            }
                            
                            function changeView(selection) {
                                switch (selection){
                                    case "hour":
                                        min = minTimeHour;
                                        max = maxTime;
                                        break;
                                    case "day":
                                        min = minTimeDay;
                                        max = maxTime;
                                        break;
                                    case "week":
                                        min = minTimeWeek;
                                        max = maxTime;
                                        break;
                                    case "month":
                                        min = minTimeMonth;
                                        max = maxTime;
                                        break;
                                    default:  //everything
                                        min = minTime;
                                        max = maxTime;
                                        break;
                                }
                                plotAccordingToChoices();
                            }
                            function showTooltip(x, y, contents) {
                            $("<div id='tooltip'>" + contents + "</div>").css({
                                position: "absolute",
                                display: "none",
                                top: y + 5,
                                left: x + 5,
                                border: "1px solid #fdd",
                                padding: "2px",
                                "background-color": "#fee",
                                opacity: 0.80
                            }).appendTo("body").fadeIn(200);
                            

                        }
                            
                        }); // $.
                        
                    </script>
                </body>
            </html>
            <?php
	}

?>