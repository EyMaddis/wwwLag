<?php

	/*
		*******************************************************************************
		wwwLag simple class file
		(c)2013 by sourcemaker (www.widecraft.de)
		*******************************************************************************
		
		
		*******************************************************************************
		Usage:
		*******************************************************************************
		
		$wwwLag = new wwwLag(DB-Hostname, DB-Name, DB-User, DB-Password);
		
		$wwwLag->getmaxmin("max","players",1,"week") returns the servers´ players peak.
			Possible paramaters:
				1. "max", "min"
				2. "players", "tps", "memory"
				3. All valid numbers from 1 to whatever
				4. "minute", "minutes", "hour", "hours, "day", "days", "week", "weeks" 
		
		$wwwLag->get_averages("memory",1,"day") returns the servers max available memory for the past day
			Possible paramaters:
				1. "players", "tps", "memory"
				2. All valid numbers from 1 to whatever
				3. "minute", "minutes", "hour", "hours, "day", "days", "week", "weeks" 
	*/

	class wwwLag {
				
		function __construct($db_host, $dbname, $dbuser, $dbpass) {	
			@mysql_connect($db_host,$dbuser,$dbpass) or die('wwwLag: No route or access to DB host');
			@mysql_select_db($dbname) or die('wwwLag: Database unknown or no access');
		}
		
		function get_maxmin($what,$amount,$value) {
			$sqlbuilder 	= 	"";
			$time_start 	= 	time();
			$time_end		=	time();
			
			switch ($value) {
				case 'minute': 
				case 'minutes':
					$time_start = $time_end - (60 * $amount);
					break;
					
				case 'hour': 
				case 'hours':
					$time_start = $time_end - (60*60*$amount);
					break;
					
				case 'day':	
				case 'days':
					$time_start = $time_end - (60*60*24*$amount);
					break;	
					
				case 'week': 
				case 'weeks':
					$time_start = $time_end - (60*60*24*7*$amount);
					break;
				
			}
			
			$sqlbuilder = "SELECT ";
			switch ($type) {
				case 'players':
					$sqlbuilder .= "players";
					break;
				case 'tps':
					$sqlbuilder = "tps";
					break;	
				case 'memory':
					$sqlbuilder = "memory";
					break;
			}
			$sqlbuilder .= " FROM `performance` WHERE `zeitstempel`>='".$time_start."' AND `zeitstempel`<='".$time_end."' ORDER";
			if ($what=="min") {
				$sqlbuilder .= "DESC LIMIT 1";
			} else {
				$sqlbuilder .= "ASC LIMIT 1";
			}
			$res = @mysql_query($sqlbuilder);
			$dat = @mysql_fetch_array($res);
			return $dat[0];
		}
		
		
		function get_averages($type, $amount, $value) {
			$sqlbuilder 	= 	"";
			$temp_counter 	= 	array();
			$time_start 	= 	time();
			$time_end		=	time();
			
			switch ($value) {
				case 'minute': 
				case 'minutes':
					$time_start = $time_end - (60 * $amount);
					break;
					
				case 'hour': 
				case 'hours':
					$time_start = $time_end - (60*60*$amount);
					break;
					
				case 'day':	
				case 'days':
					$time_start = $time_end - (60*60*24*$amount);
					break;	
					
				case 'week': 
				case 'weeks':
					$time_start = $time_end - (60*60*24*7*$amount);
					break;
				
			}
			
			$sqlbuilder = "SELECT ";
			switch ($type) {
				case 'players':
					$sqlbuilder .= "players";
					break;
				case 'tps':
					$sqlbuilder = "tps";
					break;	
				case 'memory':
					$sqlbuilder = "memory";
					break;
			}
			$sqlbuilder .= " FROM `performance` WHERE `zeitstempel`>='".$time_start."' AND `zeitstempel`<='".$time_end."'";
			
			$res = @mysql_query($sqlbuilder);
			while ($dat = @mysql_fetch_array($res)) {
				$temp_counter[0] = $temp_counter[0] + $dat[0];
				$temp_counter[1]++;
			}
			
			return ($temp_counter[0]/$temp_counter[1]);
		}
		
		
		function __get_day_ts($before) {
			$start = strtotime('-'.$before.' day', time());
			$end   = $start + (60*60*24)-1;
			return array($start,$end);				
		}
		
		
	}


?>