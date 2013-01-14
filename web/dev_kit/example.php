<?php
	
	/*
		*******************************************************************************
		wwwLag simple class file EXAMPLE
		(c)2013 by sourcemaker (www.widecraft.de)
		*******************************************************************************
	*/
	
	
	// the class file is required of course
	require("wwwlag_class.php");
	
	// init the class
	$wwwLag = new wwwLag("localhost","my_nice_database","root","secretpassword");
	
	// print out the maximum players yesterday (player peak)
	echo "Maximum players yesterday: " . $wwwLag->get_maxmin("players",1,"day") . "<br />";
	
	// print out the average players yesterday
	echo "Average TPS (ticks per second) yesterday: " . $wwwLag->get_averages("tps",1,"day") . "<br />";
	
	// weekly stats for free memory
	echo "The last week we had ".$wwwLag->get_averages("memory",1,"week")." MB available in average. That's ".$wwwLag->get_maxmin("min","memory",1,"week"). " MB at least and ".$wwwLag->get_maxmin("max","memory",1,"week"). " at top";
	
	


?>