<?php

	/*	
		PlugIn: StatSurfer
		Page: include/sy_functions.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function iritablesize($table)
	{
		global $wpdb; 
		global $_STATSURFER;
		$res = $wpdb->get_results("SHOW TABLE STATUS LIKE '$table'");
		foreach ($res as $fstatus)
		{
			$data_lenght = $fstatus->Data_length;
			$data_rows = $fstatus->Rows;
		}
		return number_format(($data_lenght / 1024 / 1024), 2, ",", " ") . " MB ($data_rows records)";
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function irihdate($dt = "00000000")
	{
		return mysql2date(get_option('date_format'), my_substr($dt, 0, 4) . "-" . my_substr($dt, 4, 2) . "-" . my_substr($dt, 6, 2));
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function irigetblogurl()
	{
      	$prsurl = parse_url(get_bloginfo('url'));
      	return $prsurl['scheme'] . '://' . $prsurl['host'] . ((!permalinksEnabled()) ? $prsurl['path'] . '/?' : '');
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurfer_URL()
	{
		$urlRequested = (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
		if ($urlRequested == "")
		{
			// SEO problem!
			$urlRequested = (isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '');
		}
		if (my_substr($urlRequested, 0, 2) == '/?')
		{
			$urlRequested = my_substr($urlRequested, 2);
		}
		if ($urlRequested == '/')
		{
			$urlRequested = '';
		}
		return $urlRequested;
	}
	
	?>