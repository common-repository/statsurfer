<?php

	/*	
		PlugIn: StatSurfer
		Page: install_pl.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	function StatSurfer_CreateTable()
	{
		global $wpdb; global $_STATSURFER;
		global $wp_db_version;
		
		$table_name_statpress = $wpdb->prefix . "statpress";
		$qry = $wpdb->get_results("SELECT ip FROM ".$table_name_statpress."");
		
		$ctr=0;
		foreach ($qry as $rk)
		{
			$ctr++;
		}
		
		if(!get_option('statsurfer_statpress')){
			if($ctr>=1){
				update_option('statsurfer_statpress','statpress');
				update_option('statsurfer_statpress_check','checked');
			}
			else{
				update_option('statsurfer_statpress','statsurfer');
			}
		}
		
		$table_name = $wpdb->prefix . "StatSurfer";
		$sql_createtable = "CREATE TABLE " . $table_name . " (
			id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
			date TINYTEXT,
			time TINYTEXT,
			ip TINYTEXT,
			urlrequested TEXT,
			agent TEXT,
			referrer TEXT,
			search TEXT,
			nation TINYTEXT,
			os TINYTEXT,
			browser TINYTEXT,
			searchengine TINYTEXT,
			spider TINYTEXT,
			feed TINYTEXT,
			user TINYTEXT,
			timestamp TINYTEXT,
			threat_score SMALLINT,
			threat_type SMALLINT,
			UNIQUE KEY id (id)
			);";
		if ($wp_db_version >= 5540)
			$page = 'wp-admin/includes/upgrade.php';
		else
			$page = 'wp-admin/upgrade-functions.php';
		require_once(ABSPATH . $page);
		dbDelta($sql_createtable);
		
		StatSurfer_CreateTable_Goals();
		StatSurfer_CreateTable_Countries();
	}
	
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	// Country Table
	
	function StatSurfer_CreateTable_Countries(){
		global $wpdb; global $_STATSURFER;
		global $wp_db_version;
		$table_name_c = $wpdb->prefix . "StatSurfer_countries";
		$sql_createtable = "CREATE TABLE " . $table_name_c . " (
			id_c MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
			iso3166_c VARCHAR(2),
			name_c TINYTEXT,
			conn_c INT(20),
			UNIQUE KEY id_c (id_c)
			);";
		if ($wp_db_version >= 5540)
			$page = 'wp-admin/includes/upgrade.php';
		else
			$page = 'wp-admin/upgrade-functions.php';
		require_once(ABSPATH . $page);
		dbDelta($sql_createtable);
		
		
		$qry = $wpdb->get_results("SELECT* FROM ".$table_name_c ."");
		$control = 1;
		foreach ($qry as $rk){
			$control = 0;
		}
		if($control==1){
			StatSurfer_CreateTable_Countries_iRow();
		}
	}
	
	
	function StatSurfer_CreateTable_Countries_iRow(){
		
		global $wpdb; global $_STATSURFER;
		global $wp_db_version;
		$table_name = $wpdb->prefix . "StatSurfer_countries";
		
		$lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/data/countrylist.dat');
		
		foreach($lines as $country_line){
			list($iso3166, $name) = explode("|", $country_line);
			
			$insert = "INSERT INTO " . $table_name . " (name_c, iso3166_c, conn_c) ";
			$insert .= "VALUES ('".$name."','".$iso3166."','0')";
			$results = $wpdb->query($insert);
		}
		
		update_option('StatSurfer_lastmaptimestamp', 0);
		
		//StatSurfer_CreateTable_Countries_fCompiler();
	}
	
	
	function StatSurfer_CreateTable_Countries_fCompiler(){
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		$table_name_country = $wpdb->prefix . "StatSurfer_countries";
		
		if($_GET['StatSurfer_action']!='StatSurfer_CreateTable_Countries_fCompiler'){
			$timestamp = current_time('timestamp');
			$lStart = 0;
			$lEnd = 50;
			$lPage = 1;
		}
		else{
			$timestamp = $_GET['my_timestamp'];
			$lPage = $_GET['my_page'];
			$lEnd = $lPage * 50;
			$lStart = $lEnd - 50;
		}
		
		$end = 1;
		$qry_string = "SELECT DISTINCT ip FROM ".$table_name." WHERE timestamp<=".$timestamp." ORDER BY timestamp ASC LIMIT ".$lStart.",50 ";
		$qry = $wpdb->get_results($qry_string);
		foreach ($qry as $rk){
			$end = 0;
			
			$url = "http://api.hostip.info/country.php?ip=" . $rk->ip;
			$ciso = file_get_contents($url);
			
			$cry = $wpdb->get_results("SELECT conn_c FROM ".$table_name_country." WHERE iso3166_c='".$ciso."'");
			foreach ($cry as $ck){
				$conn_c = $ck->conn_c;
				$conn_c++;
				$upy = "UPDATE ".$table_name_country." SET conn_c = '".$conn_c."'  WHERE iso3166_c='".$ciso."'";
				$wpdb->query($upy); 
			}
		}
		
		if($end == 1){
			update_option('StatSurfer_lastmaptimestamp', $timestamp);
			echo "muore";
		}
		else{
			$lPage++;
			$dir = 'admin.php?page=statsurfer/statsurfer.php&StatSurfer_action='.'StatSurfer_CreateTable_Countries_fCompiler';
			$dir .= '&my_timestamp=' . $timestamp;
			$dir .= '&my_page=' . $lPage;
			//echo "<script>alert('".$qry_string."');</script>";
			redirect($dir);
		}
		
	}
	
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	// Goal Table
	
	function StatSurfer_CreateTable_Goals(){
		
		global $wpdb; global $_STATSURFER;
		global $wp_db_version;
		$table_name_goals = $wpdb->prefix . "StatSurfer_goals";
		$sql_createtable = "CREATE TABLE " . $table_name_goals . " (
			id_g MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
			name_g TINYTEXT,
			conn_type_g TINYTEXT,
			goal_g INT(20),
			partial_g INT(20),
			start_date_g DATE,
			end_date_g DATE,
			result_g TINYTEXT,
			UNIQUE KEY id_g (id_g)
			);";
		if ($wp_db_version >= 5540)
			$page = 'wp-admin/includes/upgrade.php';
		else
			$page = 'wp-admin/upgrade-functions.php';
		require_once(ABSPATH . $page);
		dbDelta($sql_createtable);
	}
	
	?>