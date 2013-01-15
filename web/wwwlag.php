<?php

	// all config here
	// make sure, it's the same token as in your config.yml
	$token		=	"widecraft.de";
	$allowed_ip	=	array('127.0.0.1','46.4.126.67');
	
	// html output settings
	$height = "100%";
	$width  = "100%";
	$how_many_days = 7;
	$show_memory = false;
	$show_chunks = false;
	$show_entities = false;
	
	// database connection
	$db_host	=	"localhost";
	$db_user	=	"dbuser";
	$db_pass	=	"password";
	$db_name	=	"table-name";
	
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
			?>
            	<html>
                <head>
                	<script type='text/javascript' src='http://www.google.com/jsapi'></script>
					<script type='text/javascript'>
                      google.load('visualization', '1', {'packages':['annotatedtimeline']});
                      google.setOnLoadCallback(drawChart);
                      function drawChart() {
                        var data = new google.visualization.DataTable();
                        data.addColumn('date', 'Date');
                        data.addColumn('number', 'TPS');
                        data.addColumn('number', 'Players');
                        <?php
                        
                        if ($show_chunks == true) {
                        	echo "data.addColumn('number', 'Chunks');";
                        }
                        if ($show_entities == true) {
                        	echo "data.addColumn('number', 'Entities');";
                        }
			if ($show_memory == true) {
				echo "data.addColumn('number', 'Memory');";
			}
			
			?>
						
                        data.addRows([
						<?php
							$sql	=	"SELECT * FROM `performance` WHERE `zeitstempel` > ".($how_many_days*24*60*60)." ORDER BY `zeitstempel` ASC";
							$res	=	@mysql_query($sql);
							while ($dat = @mysql_fetch_array($res)) {							
								echo "[new Date(".($dat['zeitstempel']*1000)."), ".$dat['tps'].", ".$dat['players'];
								if ($show_chunks == true) {
									echo ",".$dat['chunks'];
								}
								if ($show_entities == true) {
									echo ", ".$dat['entities'];
								}
								if ($show_memory == true) {
									echo ", ".$dat['memory']."],";
								} else {
									echo "], ";
								}
							}
						
						
						?>
                        ]);
						vizOptions = {
						  dateFormat: 'dd.MM.yy hh:mm',
						  displayAnnotations: true
						};
                
                        var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
                        chart.draw(data, vizOptions);
                      }
                    </script>
                </head>
					<div id="chart_div" style="width:<?php echo $width; ?>; height:<?php echo $height; ?>"></div>
                </html>
            <?php
	}

?>
