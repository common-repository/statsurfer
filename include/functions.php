<?php

	/*	
		PlugIn: StatSurfer
		Page: ss_functions.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	if(!function_exists('apache_request_headers')) {	
		function apache_request_headers() {
			$arh = array();
			$rx_http = '/\AHTTP_/';
			foreach($_SERVER as $key => $val) {
				if( preg_match($rx_http, $key) ) {
		            $arh_key = preg_replace($rx_http, '', $key);
		            $rx_matches = array();
		            // do some nasty string manipulations to restore the original letter case
					// this should work in most cases
					$rx_matches = explode('_', $arh_key);
		            if( count($rx_matches) > 0 and strlen($arh_key) > 2) {
				        foreach($rx_matches as $ak_key => $ak_val) 
						$rx_matches[$ak_key] = ucfirst($ak_val);
				        $arh_key = implode('-', $rx_matches);
					}		            
					$arh[$arh_key] = $val;
				}
			}
			return( $arh );
		}
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	/* Redirect */
	
	function redirect($url,$tempo = FALSE ){
		if(!headers_sent() && $tempo == FALSE ){
			header('Location:' . $url);
		}elseif(!headers_sent() && $tempo != FALSE ){
			header('Refresh:' . $tempo . ';' . $url);
		}else{
			if($tempo == FALSE ){
				$tempo = 0;
			}
			echo "<meta http-equiv=\"refresh\" content=\"" . $tempo . "; url=" . $url . "\">";
		}
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	/* Get MicroTime */
	function icwSs_getmicrotime(){
		list( $usec, $sec) = explode( " ", microtime());
		return ( ( float)$usec + ( float)$sec);
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	/* Date Selector */
	function DateSelector($inName, $useDate=0) 
	{ 
		/* create array so we can name months */ 
		$monthName = array( 1 => 'January', 
								 'February', 
								 'March', 
								 'April', 
								 'May', 
								 'June', 
								 'July', 
								 'August', 
								 'September', 
								 'October', 
								 'November', 
								 'December'
						   ); 
		
		/* if date invalid or not supplied, use current time */ 
		if($useDate == 0) 
		{ 
			$useDate = Time(); 
		} 
		
		/* make year selector */ 
		echo "<SELECT NAME=" . $inName . "Year>\n"; 
		$startYear = date( "Y", $useDate); 
		for($currentYear = $startYear - 5; $currentYear <= $startYear+5;$currentYear++) 
		{ 
			echo "<OPTION VALUE=\"$currentYear\""; 
			if(isset($_GET['find_statDay'])){
				if($currentYear== ((int)$_GET['find_statYear'])) 
					echo " SELECTED";
			}
			else{
				if(date( "Y", $useDate)==$currentYear) 
					echo " SELECTED";
			}
			echo ">$currentYear\n"; 
		} 
		echo "</SELECT>"; 
		
		/* make month selector */ 
		echo "<SELECT NAME=" . $inName . "Month>\n"; 
		for($currentMonth = 1; $currentMonth <= 12; $currentMonth++) 
		{ 
			echo "<OPTION VALUE=\""; 
			echo intval($currentMonth); 
			echo "\""; 
			
			if(isset($_GET['find_statDay'])){
				if($currentMonth==(int)$_GET['find_statMonth']) 
					echo " SELECTED";
			}
			else{
				if(intval(date( "m", $useDate))==$currentMonth) 
					echo " SELECTED"; 
			}
			echo ">" . $monthName[$currentMonth] . "\n"; 
		} 
		echo "</SELECT>"; 
		
		/* make day selector */ 
		echo "<SELECT NAME=" . $inName . "Day>\n"; 
		for($currentDay=1; $currentDay <= 31; $currentDay++) 
		{ 
			echo "<OPTION VALUE=\"$currentDay\""; 
			if(isset($_GET['find_statDay'])){
				if($currentDay==$_GET['find_statDay']) 
					echo " SELECTED";
			}
			else{
				if(intval(date( "d", $useDate))==$currentDay)
					echo " SELECTED"; 
			}
			echo ">$currentDay\n"; 
		} 
		echo "</SELECT>"; 
	} 
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurfer_extractfeedreq($url)
	{
		if(!strpos($url, '?') === FALSE){
			list($null, $q) = explode("?", $url);
			list($res, $null) = explode("&", $q);
		}
		else{
			$prsurl = parse_url($url);
			$res = $prsurl['path'] . $$prsurl['query'];
		}
		return $res;
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function irirgbhex($red, $green, $blue)
	{
		$red = 0x10000 * max(0, min(255, $red + 0));
		$green = 0x100 * max(0, min(255, $green + 0));
		$blue = max(0, min(255, $blue + 0));
		// convert the combined value to hex and zero-fill to 6 digits
		return "#" . str_pad(strtoupper(dechex($red + $green + $blue)), 6, "0", STR_PAD_LEFT);
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurfer_Decode($out_url)
	{
      	if(!permalinksEnabled())
      	{
			if ($out_url == '')
			{
				$out_url = __('Page', 'StatSurfer') . ": Home";
			}
			if (my_substr($out_url, 0, 4) == "cat=")
			{
				$out_url = __('Category', 'StatSurfer') . ": " . get_cat_name(my_substr($out_url, 4));
			}
			if (my_substr($out_url, 0, 2) == "m=")
			{
				$out_url = __('Calendar', 'StatSurfer') . ": " . my_substr($out_url, 6, 2) . "/" . my_substr($out_url, 2, 4);
			}
			if (my_substr($out_url, 0, 2) == "s=")
			{
				$out_url = __('Search', 'StatSurfer') . ": " . my_substr($out_url, 2);
			}
			if (my_substr($out_url, 0, 2) == "p=")
			{
				$post_id_7 = get_post(my_substr($out_url, 2), ARRAY_A);
				$out_url = $post_id_7['post_title'];
			}
			if (my_substr($out_url, 0, 8) == "page_id=")
			{
				$post_id_7 = get_page(my_substr($out_url, 8), ARRAY_A);
				$out_url = __('Page', 'StatSurfer') . ": " . $post_id_7['post_title'];
			}
		}
		else
		{
			if ($out_url == '')
			{
				$out_url = __('Page', 'StatSurfer') . ": Home";
			}
			else if (my_substr($out_url, 0, 9) == "category/")
			{
				$out_url = __('Category', 'StatSurfer') . ": " . get_cat_name(my_substr($out_url, 9));
			}
			else if (my_substr($out_url, 0, 8) == "//") // not working yet
			{
				//$out_url = __('Calendar', 'StatSurfer') . ": " . my_substr($out_url, 4, 0) . "/" . my_substr($out_url, 6, 7);
			}
			else if (my_substr($out_url, 0, 2) == "s=")
			{
				$out_url = __('Search', 'StatSurfer') . ": " . my_substr($out_url, 2);
			}
			else if (my_substr($out_url, 0, 2) == "p=") // not working yet 
			{
				$post_id_7 = get_post(my_substr($out_url, 2), ARRAY_A);
				$out_url = $post_id_7['post_title'];
			}
			else if (my_substr($out_url, 0, 8) == "page_id=") // not working yet
			{
				$post_id_7 = get_page(my_substr($out_url, 8), ARRAY_A);
				$out_url = __('Page', 'StatSurfer') . ": " . $post_id_7['post_title'];
			}
		}
		return $out_url;
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurfer_lastmonth()
	{
		if(isset($_GET['find_statDay']))
			$timestamp = mktime(11,0,0,$_GET['find_statMonth'],$_GET['find_statDay'],$_GET['find_statYear']);
		else
			$timestamp = current_time('timestamp');
		$ta = getdate($timestamp);
		$year = $ta['year'];
		$month = $ta['mon'];
		// go back 1 month;
		$month = $month - 1;
		if ($month === 0)
		{
          	// if this month is Jan
            // go back a year
            $year  = $year - 1;
          	$month = 12;
		}
		// return in format 'YYYYMM'
		return sprintf($year . '%02d', $month);
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurfer_Abbrevia($s, $c)
	{
		$res = "";
		if (strlen($s) > $c)
		{
			$res = "...";
		}
		return my_substr($s, 0, $c) . $res;
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function iri_dropdown_caps($default = false)
	{
		global $wp_roles;
		$role = get_role('administrator');
		foreach ($role->capabilities as $cap => $grant)
		{
			print "<option ";
			if ($default == $cap)
			{
				print "selected ";
			}
			print ">$cap</option>";
		}
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function my_substr($str, $x, $y = 0)
	{
		if($y == 0)
		{
			$y = strlen($str) - $x;
		}
		if(function_exists('mb_substr'))
		{
			return mb_substr($str, $x, $y);
		}
		else
		{
			return substr($str, $x, $y);
		}
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function permalinksEnabled()
	{
		global $wpdb; global $_STATSURFER;
		
		$result = $wpdb->get_row('SELECT `option_value` FROM `' . $wpdb->prefix . 'options` WHERE `option_name` = "permalink_structure"');
		if ($result->option_value != '')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	?>