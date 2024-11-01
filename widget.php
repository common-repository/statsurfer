<?php

	/*	
		PlugIn: StatSurfer
		Page: widget.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	// Top Post Widget
	function StatSurfer_TopPosts($limit = 5, $showcounts = 'checked')
	{
		global $wpdb; 
		global $_STATSURFER;
		$res = "\n<ul>\n";
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		$qry = $wpdb->get_results("SELECT urlrequested,count(*) as totale FROM $table_name WHERE spider='' AND feed='' GROUP BY urlrequested ORDER BY totale DESC LIMIT $limit;");
		foreach ($qry as $rk)
		{
			$res .= "<li><a href='" . irigetblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "'>" . StatSurfer_Decode($rk->urlrequested) . "</a></li>\n";
			if (strtolower($showcounts) == 'checked')
			{
				$res .= " (" . $rk->totale . ")";
			}
		}
		return "$res</ul>\n";
	}
	
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	// Statistics Widget
	function StatSurfer_Vars($body)
	{
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		
		// NEW KEYS
		if (strpos(strtolower($body), "%country%") !== false)
		{
			$ipAddress = $_SERVER['REMOTE_ADDR'];
			$url = "http://api.hostip.info/country.php?ip=" . $ipAddress;
			$ciso = file_get_contents($url);
			
			$table_name_c = $wpdb->prefix . "StatSurfer_countries";
			$qry = $wpdb->get_results("SELECT* FROM $table_name_c WHERE iso3166_c='".$ciso."'");
			$c_name = $qry[0]->name_c;
			
			$output = "<IMG SRC='http://api.hostip.info/flag.php?ip=" . $ipAddress . "' border=0 width=15 height=10> " . $c_name . " (" . $ciso . ")" ;
			$body = str_replace("%country%", $output, $body);
		}
		// ----------
		
		if (strpos(strtolower($body), "%visits%") !== false)
		{
			$qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as pageview FROM $table_name WHERE date = '" . gmdate("Ymd", current_time('timestamp')) . "' and spider='' and feed='';");
			$body = str_replace("%visits%", $qry[0]->pageview, $body);
		}
		if (strpos(strtolower($body), "%totalvisits%") !== false)
		{
			$qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as pageview FROM $table_name WHERE spider='' and feed='';");
			$body = str_replace("%totalvisits%", $qry[0]->pageview, $body);
		}
		if (strpos(strtolower($body), "%thistotalvisits%") !== false)
		{
			$qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as pageview FROM $table_name WHERE spider='' and feed='' AND urlrequested='" . mysql_real_escape_string(StatSurfer_URL()) . "';");
			$body = str_replace("%thistotalvisits%", $qry[0]->pageview, $body);
		}
		if (strpos(strtolower($body), "%since%") !== false)
		{
			$qry = $wpdb->get_results("SELECT date FROM $table_name ORDER BY date LIMIT 1;");
			$body = str_replace("%since%", irihdate($qry[0]->date), $body);
		}
		if (strpos(strtolower($body), "%os%") !== false)
		{
			$userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
			$os = iriGetOS($userAgent);
			$body = str_replace("%os%", $os, $body);
		}
		if (strpos(strtolower($body), "%browser%") !== false)
		{
			$userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
			$browser = iriGetBrowser($userAgent);
			$body = str_replace("%browser%", $browser, $body);
		}
		if (strpos(strtolower($body), "%ip%") !== false)
		{
			$ipAddress = $_SERVER['REMOTE_ADDR'];
			$body = str_replace("%ip%", $ipAddress, $body);
		}
		if (strpos(strtolower($body), "%visitorsonline%") !== false)
		{
			$to_time = current_time('timestamp');
			$from_time = strtotime('-4 minutes', $to_time);
			$qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as visitors FROM $table_name WHERE spider='' and feed='' AND timestamp BETWEEN $from_time AND $to_time;");
			$body = str_replace("%visitorsonline%", $qry[0]->visitors, $body);
		}
		if (strpos(strtolower($body), "%usersonline%") !== false)
		{
			$to_time = current_time('timestamp');
			$from_time = strtotime('-4 minutes', $to_time);
			$qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as users FROM $table_name WHERE spider='' and feed='' AND user<>'' AND timestamp BETWEEN $from_time AND $to_time;");
			$body = str_replace("%usersonline%", $qry[0]->users, $body);
		}
		if (strpos(strtolower($body), "%toppost%") !== false)
		{
			$qry = $wpdb->get_results("SELECT urlrequested,count(*) as totale FROM $table_name WHERE spider='' AND feed='' AND urlrequested LIKE '%p=%' GROUP BY urlrequested ORDER BY totale DESC LIMIT 1;");
			$body = str_replace("%toppost%", StatSurfer_Decode($qry[0]->urlrequested), $body);
		}
		if (strpos(strtolower($body), "%topbrowser%") !== false)
		{
			$qry = $wpdb->get_results("SELECT browser,count(*) as totale FROM $table_name WHERE spider='' AND feed='' GROUP BY browser ORDER BY totale DESC LIMIT 1;");
			$body = str_replace("%topbrowser%", StatSurfer_Decode($qry[0]->browser), $body);
		}
		if (strpos(strtolower($body), "%topos%") !== false)
		{
			$qry = $wpdb->get_results("SELECT os,count(*) as totale FROM $table_name WHERE spider='' AND feed='' GROUP BY os ORDER BY totale DESC LIMIT 1;");
			$body = str_replace("%topos%", StatSurfer_Decode($qry[0]->os), $body);
		}
		if(strpos(strtolower($body),"%pagestoday%") !== false)
		{
			$qry = $wpdb->get_results("SELECT count(ip) as pageview FROM $table_name WHERE date = '".gmdate("Ymd",current_time('timestamp'))."' and spider='' and feed='';");
			$body = str_replace("%pagestoday%", $qry[0]->pageview, $body);
		}
		
		if(strpos(strtolower($body),"%thistotalpages%") !== FALSE)
		{
			$qry = $wpdb->get_results("SELECT count(ip) as pageview FROM $table_name WHERE spider='' and feed='';");
			$body = str_replace("%thistotalpages%", $qry[0]->pageview, $body);
		}
		
		if (strpos(strtolower($body), "%latesthits%") !== false)
		{
			$qry = $wpdb->get_results("SELECT search FROM $table_name WHERE search <> '' ORDER BY id DESC LIMIT 10");
			$body = str_replace("%latesthits%", urldecode($qry[0]->search), $body);
			for ($counter = 0; $counter < 10; $counter += 1)
			{
				$body .= "<br>". urldecode($qry[$counter]->search);
			}
		}
		
		if (strpos(strtolower($body), "%pagesyesterday%") !== false)
		{
			$yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
			$qry = $wpdb->get_row("SELECT count(DISTINCT ip) AS visitsyesterday FROM $table_name WHERE feed='' AND spider='' AND date = '" . $yesterday . "'");
			$body = str_replace("%pagesyesterday%", (is_array($qry) ? $qry[0]->visitsyesterday : 0), $body);
		}
		
		return $body;
	}
	
	?>