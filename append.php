<?php

	/*	
		PlugIn: StatSurfer
		Page: append.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	function iriDomain($ip)
    {
		if (strstr($ip, ', ')) {
			$ipsA = explode(', ', $ip);
			$ip = $ipsA[0];
		}
		
        $host = gethostbyaddr($ip);
        if (ereg('^([0-9]{1,3}\.){3}[0-9]{1,3}$', $host))
        {
            return "";
        }
        else
        {
            return my_substr(strrchr($host, "."), 1);
        }
    }
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function iriGetQueryPairs($url)
	{
		$parsed_url = parse_url($url);
		$tab = parse_url($url);
		$host = $tab['host'];
		if (key_exists("query", $tab))
		{
			$query = $tab["query"];
			$query = str_replace("&amp;", "&", $query);
			$query = urldecode($query);
			$query = str_replace("?", "&", $query);
			return explode("&", $query);
		}
		else
		{
			return null;
		}
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function iriGetOS($arg)
	{
		$arg = str_replace(" ", "", $arg);
		$lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/def/os.dat');
		foreach ($lines as $line_num => $os)
		{
			list($nome_os, $id_os) = explode("|", $os);
			if (strpos($arg, $id_os) === false)
				continue;
			// riconosciuto
			return $nome_os;
		}
		return '';
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function iriGetBrowser($arg)
	{
		$arg = str_replace(" ", "", $arg);
		$lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/def/browser.dat');
		foreach ($lines as $line_num => $browser)
		{
			list($nome, $id) = explode("|", $browser);
			if (strpos($arg, $id) === false)
				continue;
			// riconosciuto
			return $nome;
		}
		return '';
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function iriCheckBanIP($arg)
	{
		if (file_exists(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '-custom/banips.dat'))
			$lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '-custom/banips.dat');
		else
			$lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/def/banips.dat');
		
        if ($lines !== false)
        {
            foreach ($lines as $banip)
			{
				if (@preg_match('/^' . rtrim($banip, "\r\n") . '$/', $arg)){
					return true;
				}
				// riconosciuto, da scartare
			}
		}
		return false;
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function iriGetSE($referrer = null)
	{
		$key = null;
		$lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/def/searchengines.dat');
		foreach ($lines as $line_num => $se)
		{
			list($nome, $url, $key) = explode("|", $se);
			if (strpos($referrer, $url) === false)
				continue;
			// trovato se
			$variables = iriGetQueryPairs($referrer);
			$i = count($variables);
			while ($i--)
			{
				$tab = explode("=", $variables[$i]);
				if ($tab[0] == $key)
				{
					return($nome . "|" . urlencode($tab[1]));
				}
			}
		}
		return null;
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function iriGetSpider($agent = null)
	{
		$agent = str_replace(" ", "", $agent);
		$key = null;
		$lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/def/spider.dat');
		if (file_exists(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '-custom/spider.dat'))
			$lines = array_merge($lines, file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '-custom/spider.dat'));
		foreach ($lines as $line_num => $spider)
		{
			list($nome, $key) = explode("|", $spider);
			if (strpos($agent, $key) === false)
				continue;
			// trovato
			return $nome;
		}
		return null;
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurfer_is_feed($url) {
		if (stristr($url,get_bloginfo('comments_atom_url')) != FALSE) { return 'COMMENT ATOM'; }
		elseif (stristr($url,get_bloginfo('comments_rss2_url')) != FALSE) { return 'COMMENT RSS'; }
		elseif (stristr($url,get_bloginfo('rdf_url')) != FALSE) { return 'RDF'; }
		elseif (stristr($url,get_bloginfo('atom_url')) != FALSE) { return 'ATOM'; }
		elseif (stristr($url,get_bloginfo('rss_url')) != FALSE) { return 'RSS'; }
		elseif (stristr($url,get_bloginfo('rss2_url')) != FALSE) { return 'RSS2'; }
		elseif (stristr($url,'wp-feed.php') != FALSE) { return 'RSS2'; }
		elseif (stristr($url,'/feed') != FALSE) { return 'RSS2'; }
		return '';
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	// Append Function
	
	function iriStatAppend()
	{
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		global $userdata;
		global $_STATSURFER;
		get_currentuserinfo();
		$feed = '';
		
		// Time
		$timestamp = current_time('timestamp');
		$vdate = gmdate("Ymd", $timestamp);
		$vtime = gmdate("H:i:s", $timestamp);
		
		// IP
		//$ipAddress = $_SERVER['REMOTE_ADDR'];
		$headers_xff = apache_request_headers();
		if (array_key_exists('X-Forwarded-For', $headers_xff)){
			$ipAddress=$headers_xff['X-Forwarded-For'] . ' via ' . $_SERVER["REMOTE_ADDR"];
		} else {
			$ipAddress=$_SERVER["REMOTE_ADDR"];
		}
		
		if (iriCheckBanIP($ipAddress) === true)
		{
			return '';
		}
		
		// Determine Threats if http:bl installed
		$threat_score = 0;
		$threat_type = 0;
		$httpbl_key = get_option("httpbl_key");
		if ($httpbl_key !== false)
		{
			$result = explode( ".", gethostbyname( $httpbl_key . "." .
												  implode ( ".", array_reverse( explode( ".",
																						$ipAddress ) ) ) .
												  ".dnsbl.httpbl.org" ) );
			// If the response is positive
			if ($result[0] == 127)
			{
				$threat_score = $result[2];
				$threat_type = $result[3];
			}
		}
		
		// URL (requested)
		$urlRequested = StatSurfer_URL();
		if (eregi(".ico$", $urlRequested))
		{
			return '';
		}
		if (eregi("favicon.ico", $urlRequested))
		{
			return '';
		}
		if (eregi(".css$", $urlRequested))
		{
			return '';
		}
		if (eregi(".js$", $urlRequested))
		{
			return '';
		}
		if (stristr($urlRequested, "/wp-content/plugins") != false)
		{
			return '';
		}
		if (stristr($urlRequested, "/wp-content/themes") != false)
		{
			return '';
		}
		
		$referrer = (isset($_SERVER['HTTP_REFERER']) ? htmlentities($_SERVER['HTTP_REFERER']) : '');
		$userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? htmlentities($_SERVER['HTTP_USER_AGENT']) : '');
		$spider = iriGetSpider($userAgent);
		
		if (($spider != '') and (get_option('StatSurfer_donotcollectspider') == 'checked'))
		{
			return '';
		}
		
		if ($spider != '')
		{
			$os = '';
			$browser = '';
		}
		else
		{
			// Trap feeds
			$prsurl = parse_url(get_bloginfo('url'));
			$feed = StatSurfer_is_feed($prsurl['scheme'] . '://' . $prsurl['host'] . $_SERVER['REQUEST_URI']);
			// Get OS and browser
			$os = iriGetOS($userAgent);
			$browser = iriGetBrowser($userAgent);
			list($searchengine, $search_phrase) = explode("|", iriGetSE($referrer));
		}
		// Auto-delete visits if...
		if (get_option('StatSurfer_autodelete_spider') != '') 
		{
			$t = gmdate("Ymd", strtotime('-' . get_option('StatSurfer_autodelete_spider')));
			$results = $wpdb->query("DELETE FROM " . $table_name . " WHERE date < '" . $t . "' AND spider <> ''");
		}
		if (get_option('StatSurfer_autodelete') != '')
		{
			$t = gmdate("Ymd", strtotime('-' . get_option('StatSurfer_autodelete')));
			$results = $wpdb->query("DELETE FROM " . $table_name . " WHERE date < '" . $t . "'");
		}
		if ((!is_user_logged_in()) or (get_option('StatSurfer_collectloggeduser') == 'checked'))
		{
			if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
			{
				StatSurfer_CreateTable();
			}
			
			$insert = "INSERT INTO " . $table_name . " (date, time, ip, urlrequested, agent, referrer, search,nation,os,browser,searchengine,spider,feed,user,threat_score,threat_type,timestamp) " . "VALUES ('$vdate','$vtime','$ipAddress','" . mysql_real_escape_string($urlRequested) . "','" . mysql_real_escape_string(strip_tags($userAgent)) . "','" . mysql_real_escape_string($referrer) . "','" . mysql_real_escape_string(strip_tags($search_phrase)) . "','" . iriDomain($ipAddress) . "','" . mysql_real_escape_string($os) . "','" . mysql_real_escape_string($browser) . "','$searchengine','$spider','$feed','$userdata->user_login',$threat_score,$threat_type,'$timestamp')";
			$results = $wpdb->query($insert);
		}
		
		
		///WHITE CONNECTION
		/*$table_name_country = $wpdb->prefix . "StatSurfer_countries";
		 $url = "http://api.hostip.info/country.php?ip=" . $ipAddress;
		 $ciso = file_get_contents($url);
		 
		 $cry = $wpdb->get_results("SELECT w_conn_c FROM ".$table_name_country." WHERE iso3166_c='".$ciso."'");
		 foreach ($cry as $ck){
		 
		 $w_conn_c = $ck->w_conn_c;
		 $w_conn_c++;
		 $upy = "UPDATE ".$table_name_country." SET w_conn_c = '".$w_conn_c."'  WHERE iso3166_c='".$ciso."'";
		 $wpdb->query($upy); 
		 }*/
		
		$control_empty = 0;
		$empty = $wpdb->get_results("SELECT ip FROM ".$table_name." WHERE ip='".$ipAddress."'");
		foreach ($empty as $empty){
			$control_empty++;
		}
		
		if($control_empty< 2){ 
			
			$table_name_country = $wpdb->prefix . "StatSurfer_countries";
			$url = "http://api.hostip.info/country.php?ip=" . urlencode($ipAddress);
			$ciso = file_get_contents($url);
			
			$cry = $wpdb->get_results("SELECT conn_c FROM ".$table_name_country." WHERE iso3166_c='".$ciso."'");
			foreach ($cry as $ck){
				$conn_c = $ck->conn_c;
				$conn_c++;
				$upy = "UPDATE ".$table_name_country." SET conn_c = '".$conn_c."'  WHERE iso3166_c='".$ciso."'";
				$wpdb->query($upy); 
			}
		}
		
	}
	
	?>