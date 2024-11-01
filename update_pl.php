<?php

	/*	
		PlugIn: StatSurfer
		Page: update_pl.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	function StatSurferUpdate()
	{
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		
		$wpdb->show_errors();
		// update table
		print "" . __('Updating table struct', 'StatSurfer') . " $table_name... ";
		StatSurfer_CreateTable();
		print "" . __('done', 'StatSurfer') . "<br>";
		
		// Update Feed
		print "" . __('Updating Feeds', 'StatSurfer') . "... ";
		$wpdb->query("UPDATE $table_name SET feed='';");
		
		// standard blog info urls
		$s = StatSurfer_extractfeedreq(get_bloginfo('comments_atom_url'));
		if ($s != '')
		{
			$wpdb->query("UPDATE $table_name SET feed='COMMENT ATOM' WHERE INSTR(urlrequested,'$s')>0 AND feed='';");
		}
		$s = StatSurfer_extractfeedreq(get_bloginfo('comments_rss2_url'));
		if ($s != '')
		{
			$wpdb->query("UPDATE $table_name SET feed='COMMENT RSS' WHERE INSTR(urlrequested,'$s')>0 AND feed='';");
		}
		$s = StatSurfer_extractfeedreq(get_bloginfo('atom_url'));
		if ($s != '')
		{
			$wpdb->query("UPDATE $table_name SET feed='ATOM' WHERE INSTR(urlrequested,'$s')>0 AND feed='';");
		}
		$s = StatSurfer_extractfeedreq(get_bloginfo('rdf_url'));
		if ($s != '')
		{
			$wpdb->query("UPDATE $table_name SET feed='RDF'  WHERE INSTR(urlrequested,'$s')>0 AND feed='';");
		}
		$s = StatSurfer_extractfeedreq(get_bloginfo('rss_url'));
		if ($s != '')
		{
			$wpdb->query("UPDATE $table_name SET feed='RSS'  WHERE INSTR(urlrequested,'$s')>0 AND feed='';");
		}
		$s = StatSurfer_extractfeedreq(get_bloginfo('rss2_url'));
		if ($s != '')
		{
			$wpdb->query("UPDATE $table_name SET feed='RSS2' WHERE INSTR(urlrequested,'$s')>0 AND feed='';");
		}
		
		// not standard
		$wpdb->query("UPDATE $table_name SET feed='RSS2' WHERE urlrequested LIKE '%/feed%' AND feed='';");
		$wpdb->query("UPDATE $table_name SET feed='RSS2' WHERE urlrequested LIKE '%wp-feed.php%' AND feed='';");
		
		
		print "" . __('done', 'StatSurfer') . "<br>";
		
		// Update OS
		print "" . __('Updating OS', 'StatSurfer') . "... ";
		$wpdb->query("UPDATE $table_name SET os = '';");
		$lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/def/os.dat');
		foreach ($lines as $line_num => $os)
		{
			list($nome_os, $id_os) = explode("|", $os);
			$qry = "UPDATE $table_name SET os = '$nome_os' WHERE os='' AND replace(agent,' ','') LIKE '%" . $id_os . "%';";
			$wpdb->query($qry);
		}
		print "" . __('done', 'StatSurfer') . "<br>";
		
		// Update Browser
		print "". __('Updating Browsers', 'StatSurfer') ."... ";
		$wpdb->query("UPDATE $table_name SET browser = '';");
		$lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/def/browser.dat');
		foreach ($lines as $line_num => $browser)
		{
			list($nome, $id) = explode("|", $browser);
			$qry = "UPDATE $table_name SET browser = '$nome' WHERE browser='' AND replace(agent,' ','') LIKE '%" . $id . "%';";
			$wpdb->query($qry);
		}
		print "" . __('done', 'StatSurfer') . "<br>";
		
		print "" . __('Updating Spiders', 'StatSurfer') . "... ";
		$wpdb->query("UPDATE $table_name SET spider = '';");
		$lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/def/spider.dat');
		if (file_exists(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '-custom/spider.dat'))
			$lines = array_merge($lines, file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '-custom/spider.dat'));
		foreach ($lines as $line_num => $spider)
		{
			list($nome, $id) = explode("|", $spider);
			$qry = "UPDATE $table_name SET spider = '$nome',os='',browser='' WHERE spider='' AND replace(agent,' ','') LIKE '%" . $id . "%';";
			$wpdb->query($qry);
		}
		print "" . __('done', 'StatSurfer') . "<br>";
		
		// Update feed to ''
		print "" . __('Updating Feeds', 'StatSurfer') . "... ";
		$wpdb->query("UPDATE $table_name SET feed = '' WHERE isnull(feed);");
		print "" . __('done', 'StatSurfer') . "<br>";
		
		// Update Search engine
		print "" . __('Updating Search engines', 'StatSurfer') . "... ";
		print "<br>";
		$wpdb->query("UPDATE $table_name SET searchengine = '', search='';");
		print "..." . __('null-ed', 'StatSurfer') . "!<br>";
		$qry = $wpdb->get_results("SELECT id, referrer FROM $table_name WHERE referrer !=''");
		print "..." . __('select-ed', 'StatSurfer') . "!<br>";
		foreach ($qry as $rk)
		{
			list($searchengine, $search_phrase) = explode("|", iriGetSE($rk->referrer));
			if ($searchengine <> '')
			{
				$q = "UPDATE $table_name SET searchengine = '$searchengine', search='" . addslashes($search_phrase) . "' WHERE id=" . $rk->id;
				$wpdb->query($q);
			}
		}
		print "" . __('done', 'StatSurfer') . "<br>";
		
		$wpdb->hide_errors();
		
		print "<br>&nbsp;<h1>" . __('Updated', 'StatSurfer') . "!</h1>";
	}
	
	?>