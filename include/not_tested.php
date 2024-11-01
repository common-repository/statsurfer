<?php

	/*	
		PlugIn: StatSurfer
		Page: include/no_functions.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	function StatSurfer_Print($body = ''){
		print StatSurfer_Vars($body);
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurfer_Widget($w = ''){
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurfer_Where($ip)
	{
		$url = "http://api.hostip.info/get_html.php?ip=$ip";
		$res = file_get_contents($url);
		if ($res === false)
		{
			return(array('', ''));
		}
		$res = str_replace("Country: ", "", $res);
		$res = str_replace("\nCity: ", ", ", $res);
		$nation = preg_split('/\(|\)/', $res);
		print "( $ip $res )";
		return(array($res, $nation[1]));
	}
	
	?>