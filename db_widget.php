<?php

	/*	
	 PlugIn: StatSurfer
	 Page: db_widget.php
	 Author: Cattani Simone
	 Author URI: http://cattanisimone.it
	 */
	
	if( !class_exists( 'StatSurfer_DashboardWidget') ) {
		class StatSurfer_DashboardWidget {
			function StatSurferDashWidget(){
				
				StatSurfer_DashboardWidget_Tabel();
				
			}
			
			function StatSurferDashWidget_Loader(){
				wp_add_dashboard_widget('statsurfer', 'StatSurfer', array( 'StatSurfer_DashboardWidget', 'StatSurferDashWidget' ));
			}
		}
		add_action( 'wp_dashboard_setup', array( 'StatSurfer_DashboardWidget', 'StatSurferDashWidget_Loader' ) );
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurfer_DashboardWidget_Tabel(){
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		
		// OVERVIEW table
		$unique_color = "#114477";
		$web_color = "#3377B6";
		$rss_color = "#f38f36";
		$spider_color = "#83b4d8";
		$thismonth = gmdate('Ym', current_time('timestamp'));
		$yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
		$today = gmdate('Ymd', current_time('timestamp'));
		$tlm[0] = my_substr($lastmonth, 0, 4);
		$tlm[1] = my_substr($lastmonth, 4, 2);
		
		print "<div class='wrap' style='margin-top:20px;'>";
		print "<table class='widefat' style='border:0px;'><tbody id='the-list'><tr>
		<td width='20%' style='border:0px;'></th>
		<td width='20%' style='border:0px;'><b>" . __('Total', 'StatSurfer') . "</b></td>
		<td width='20%' style='border:0px;'><b>" . __('This month', 'StatSurfer') . "</b></td>
		<td width='20%' style='border:0px;'><b>" . __('Yesterday', 'StatSurfer') . "</b><br /></td>
		<td width='20%' style='border:0px;'><b>" . __('Today', 'StatSurfer') . "</b><br /></td>
		</tr>";
		echo "<tr>
		<td width='20%'></th>
		<td width='20%'></td>
		<td width='20%'><font size=1 style='padding-top:100px;'>" . gmdate('M, Y', current_time('timestamp')) . "</font></td>
		<td width='20%'><font size=1>" . gmdate('d M, Y', current_time('timestamp') - 86400) . "</font></td>
		<td width='20%'><font size=1>" . gmdate('d M, Y', current_time('timestamp')) . "</font></td>
		</tr>";
		
		//###############################################################################################
		// VISITORS ROW
		print "<tr><td><div style='background:$unique_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Visitors', 'StatSurfer') . "</td>";
		
		//TOTAL
		$qry_total = $wpdb->get_row("SELECT count(DISTINCT ip) AS visitors FROM $table_name WHERE feed='' AND spider='' ");
		print "<td>" . $qry_total->visitors . "</td>\n";
		
		//THIS MONTH
		$qry_tmonth = $wpdb->get_row("SELECT count(DISTINCT ip) AS visitors FROM $table_name WHERE feed='' AND spider='' AND date LIKE '" . mysql_real_escape_string($thismonth) . "%' ");
		if ($qry_lmonth->visitors <> 0)
		{
			$pc = round(100 * ($qry_tmonth->visitors / $qry_lmonth->visitors) - 100, 1);
			if ($pc >= 0)
				$pc = "+" . $pc;
			$qry_tmonth->change = "<code> (" . $pc . "%)</code>";
		}
		print "<td>" . $qry_tmonth->visitors . $qry_tmonth->change . "</td>\n";
		
		//YESTERDAY
		$qry_y = $wpdb->get_row("SELECT count(DISTINCT ip) AS visitors FROM $table_name WHERE feed='' AND spider='' AND date = '" . mysql_real_escape_string($yesterday) . "' ");
		print "<td>" . $qry_y->visitors . "</td>\n";
		
		//TODAY
		$qry_t = $wpdb->get_row("SELECT count(DISTINCT ip) AS visitors FROM $table_name WHERE feed='' AND spider='' AND date = '" . mysql_real_escape_string($today) . "' ");
		print "<td>" . $qry_t->visitors . "</td>\n";
		print "</tr>";
		
		//###############################################################################################
		// PAGEVIEWS ROW
		print "<tr><td><div style='background:$web_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Pageviews', 'StatSurfer') . "</td>";
		
		//TOTAL
		$qry_total = $wpdb->get_row(" SELECT count(date) as pageview FROM $table_name WHERE feed='' AND spider='' ");
		print "<td>" . $qry_total->pageview . "</td>\n";
		
		//THIS MONTH
		$qry_tmonth = $wpdb->get_row("SELECT count(date) as pageview FROM $table_name  WHERE feed=''  AND spider=''  AND date LIKE '" . mysql_real_escape_string($thismonth) . "%' ");
		if ($qry_lmonth->pageview <> 0)
		{
			$pc = round(100 * ($qry_tmonth->pageview / $qry_lmonth->pageview) - 100, 1);
			if ($pc >= 0)
				$pc = "+" . $pc;
			$qry_tmonth->change = "<code> (" . $pc . "%)</code>";
		}
		print "<td>" . $qry_tmonth->pageview . $qry_tmonth->change . "</td>\n";
		
		//YESTERDAY
		$qry_y = $wpdb->get_row("SELECT count(date) as pageview FROM $table_name WHERE feed='' AND spider='' AND date = '" . mysql_real_escape_string($yesterday) . "'");
		print "<td>" . $qry_y->pageview . "</td>\n";
		
		//TODAY
		$qry_t = $wpdb->get_row("SELECT count(date) as pageview FROM $table_name WHERE feed='' AND spider='' 	AND date = '" . mysql_real_escape_string($today) . "' ");
		print "<td>" . $qry_t->pageview . "</td>\n";
		print "</tr>";
		//###############################################################################################
		// SPIDERS ROW
		print "<tr><td><div style='background:$spider_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Spiders', 'StatSurfer') . "</td>";
		//TOTAL
		$qry_total = $wpdb->get_row("SELECT count(date) as spiders FROM $table_name WHERE feed='' AND spider<>''");
		print "<td>" . $qry_total->spiders . "</td>\n";
		
		//THIS MONTH
		$prec = $qry_lmonth->spiders;
		$qry_tmonth = $wpdb->get_row("SELECT count(date) as spiders  FROM $table_name  WHERE feed=''  AND spider<>'' AND date LIKE '" . mysql_real_escape_string($thismonth) . "%' ");
		if ($qry_lmonth->spiders <> 0)
		{
			$pc = round(100 * ($qry_tmonth->spiders / $qry_lmonth->spiders) - 100, 1);
			if ($pc >= 0)
				$pc = "+" . $pc;
			$qry_tmonth->change = "<code> (" . $pc . "%)</code>";
		}
		print "<td>" . $qry_tmonth->spiders . $qry_tmonth->change . "</td>\n";
		
		//YESTERDAY
		$qry_y = $wpdb->get_row("SELECT count(date) as spiders FROM $table_name WHERE feed='' AND spider<>'' AND date = '" . mysql_real_escape_string($yesterday) . "' ");
		print "<td>" . $qry_y->spiders . "</td>\n";
		
		//TODAY
		$qry_t = $wpdb->get_row("SELECT count(date) as spiders FROM $table_name WHERE feed='' AND spider<>'' AND date = '" . mysql_real_escape_string($today) . "'");
		print "<td>" . $qry_t->spiders . "</td>\n";
		print "</tr>";
		//###############################################################################################
		// FEEDS ROW
		print "<tr><td><div style='background:$rss_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Feeds', 'StatSurfer') . "</td>";
		//TOTAL
		$qry_total = $wpdb->get_row("SELECT count(date) as feeds FROM $table_name WHERE feed<>'' AND spider=''");
		print "<td>" . $qry_total->feeds . "</td>\n";
		
		//THIS MONTH
		$qry_tmonth = $wpdb->get_row("SELECT count(date) as feeds  FROM $table_name  WHERE feed<>'' AND spider='' AND date LIKE '" . mysql_real_escape_string($thismonth) . "%'  ");
		if ($qry_lmonth->feeds <> 0)
		{
			$pc = round(100 * ($qry_tmonth->feeds / $qry_lmonth->feeds) - 100, 1);
			if ($pc >= 0)
				$pc = "+" . $pc;
			$qry_tmonth->change = "<code> (" . $pc . "%)</code>";
		}
		print "<td>" . $qry_tmonth->feeds . $qry_tmonth->change . "</td>\n";
		
		$qry_y = $wpdb->get_row("SELECT count(date) as feeds FROM $table_name WHERE feed<>'' AND spider='' AND date = '" . mysql_real_escape_string($yesterday) . "'");
		print "<td>" . $qry_y->feeds . "</td>\n";
		
		$qry_t = $wpdb->get_row("SELECT count(date) as feeds FROM $table_name WHERE feed<>'' AND spider='' AND date = '" . mysql_real_escape_string($today) . "'");
		print "<td>" . $qry_t->feeds . "</td>\n";
		
		print "</tr></tbody></table><br />\n\n";
		echo "</div>";
		
		
		print "<div class='wrap' style='margin-top:20px;margin-bottom:30px;'>";
		
		
		/*-------------------------------------------------------------------------------------------------------------------------------*/
		/***** Graph *********************************************************************************************************************/
		
		// last "N" days graph  NEW
		$gdays = 7;
		//  $start_of_week = get_settings('start_of_week');
		$start_of_week = get_option('start_of_week');
		print '<table width="100%" border="0"><tr>';
		$qry = $wpdb->get_row("SELECT count(date) as pageview, date FROM $table_name GROUP BY date HAVING date >= '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gdays) . "' ORDER BY pageview DESC LIMIT 1 ");
		$maxxday = $qry->pageview;
		if ($maxxday == 0)
		{
			$maxxday = 1;
		}
		// Y
		$gd = (90 / $gdays) . '%';
		for ($gg = $gdays - 1; $gg >= 0; $gg--)
		{
			//TOTAL VISITORS
			$qry_visitors = $wpdb->get_row("SELECT count(DISTINCT ip) AS total FROM $table_name WHERE feed='' AND spider='' AND date = '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gg) . "'");
			$px_visitors = round($qry_visitors->total * 100 / $maxxday);
			
			//TOTAL PAGEVIEWS (we do not delete the uniques, this is falsing the info.. uniques are not different visitors!)
			$qry_pageviews = $wpdb->get_row("SELECT count(date) as total FROM $table_name WHERE feed='' AND spider='' AND date = '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gg) . "'");
			$px_pageviews = round($qry_pageviews->total * 100 / $maxxday);
			
			//TOTAL SPIDERS
			$qry_spiders = $wpdb->get_row("SELECT count(ip) AS total FROM $table_name WHERE feed='' AND spider<>'' AND date = '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gg) . "'");
			$px_spiders = round($qry_spiders->total * 100 / $maxxday);
			
			//TOTAL FEEDS
			$qry_feeds = $wpdb->get_row("SELECT count(ip) AS total FROM $table_name WHERE feed<>'' AND spider='' AND date = '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gg) . "'");
			$px_feeds = round($qry_feeds->total * 100 / $maxxday);
			
			$px_white = 100 - $px_feeds - $px_spiders - $px_pageviews - $px_visitors;
			
			print '<td width="' . $gd . '" valign="bottom"';
			if ($start_of_week == gmdate('w', current_time('timestamp') - 86400 * $gg))
			{
				print ' style="border-left:2px dotted gray;"';
			}
			// week-cut
			print "><div style='float:left;height: 100%;width:100%;font-family:Helvetica;font-size:7pt;text-align:center;border-right:1px solid white;color:black;'>
			<div style='background:#ffffff;width:100%;height:" . $px_white . "px;'></div>
			<div style='background:$spider_color;width:100%;height:" . $px_spiders . "px;' title='" . $qry_spiders->total . " " . __('spiders', 'StatSurfer')."'></div>
			<div style='background:$rss_color;width:100%;height:" . $px_feeds . "px;' title='" . $qry_feeds->total . " " . __('feeds', 'StatSurfer')."'></div>
			<div style='background:$web_color;width:100%;height:" . $px_pageviews . "px;' title='" . $qry_pageviews->total . " " . __('pageviews', 'StatSurfer')."'></div>
			<div style='background:$unique_color;width:100%;height:" . $px_visitors . "px;' title='" . $qry_visitors->total . " " . __('visitors', 'StatSurfer')."'></div>
			<div style='background:gray;width:100%;height:1px;'></div>
			<br />" . gmdate('d', current_time('timestamp') - 86400 * $gg) . '<br />' . gmdate('M', current_time('timestamp') - 86400 * $gg) . "</div></td>\n";
		}
		print '</tr></table>';
		
		print '</div>';
		
		
		/*-------------------------------------------------------------------------------------------------------------------------------*/
		/***** Maps **********************************************************************************************************************/
		
		if(get_option('StatSurfer_show_dashoboardmap')=='checked'){
			echo "<div class='wrap' style='margin-top:20px;margin-bottom:30px;'>";
			
			$table_name = $wpdb->prefix . "StatSurfer_countries";
			$sel_countries = "";
			$val_countries = "";
			$first_check=1;
			$total_conn = 0;
			$count_countries = 0;
			
			$qry = $wpdb->get_results("SELECT* FROM ".$table_name." WHERE conn_c<>0 ORDER BY conn_c DESC");
			foreach ($qry as $rk){
				
				if($rk->iso3166_c!='EU'){
					
					if($first_check==1){
						$max_val = $rk->conn_c;
					}
					$first_check=0;
					
					$val = ($rk->conn_c)*100/$max_val;
					$val = round($val,0);
					
					$sel_countries .= $rk->iso3166_c . "|";
					$val_countries .= $val . ",";
					
				}
				$total_conn = $total_conn + $rk->conn_c;
				$count_countries = $count_countries + 1;
			}
			
			$val_countries=my_substr($val_countries,0,strlen($val_countries)-1);
			$sel_countries=my_substr($sel_countries,0,strlen($sel_countries)-1);
			
			if(get_option('StatSurfer_dashboard_mapGrad')=='checked')
				$dbMapColor = "d5d5d5,d5d5d5,114477";
			else
				$dbMapColor = "d5d5d5,114477,114477";
			
			print "<center><img src='http://chart.apis.google.com/chart?cht=map:fixed=".get_option('StatSurfer_dashboard_typemap')."&chs=440x260&chld=".$sel_countries."&chd=t:".$val_countries."&chco=".$dbMapColor."' /></center>";
			
			echo "</div>";
		}
		
		
	}	
	
	?>