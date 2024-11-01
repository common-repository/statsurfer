<?php
	
	/*	
	 PlugIn: StatSurfer
	 Page: pages/main.php
	 Author: Cattani Simone
	 Author URI: http://cattanisimone.it
	 */
	
	function StatSurferMain()
	{
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		
		?>
		<style>

			.local_css_graph{
				border-width:0px;
				border-style:solid;
				border-spacing:0;
				width:100%;
				clear:both;
				margin:0;
				-moz-border-radius:0px;
				-khtml-border-radius:0px;
				-webkit-border-radius:0px;
				border-radius:0px;
			}

			.local_css_graph td,.widefat th{
				border-bottom-width:0px;
				border-bottom-style:solid;
				font-size:11px;
			}

			.local_css_graph td{
				padding:0px 0px;
				vertical-align:bottom;
			}

		</style>
		<?php
		
		//###############################################################################################
		//###############################################################################################
		// OVERVIEW
		
		{
			print "<div class='wrap'><h2>Dates Selector</h2>";
			print "<table class='widefat'><thead><tr>  
			<th width='5'></th>
			<th width='80'>Select a day</th>
			<th>";
			
			print "<form method=\"GET\" action=\"admin.php\">";
			print "<input type=\"hidden\" name=\"page\" value=\"statsurfer/statsurfer.php\" />";
			DateSelector("find_stat");
			print "<input type=\"submit\" value=\" Look Stats \" />";
			print "</form>";
			
			print "</th> <th scope='col'></th> <th scope='col'></th></tr></thead>";
			
			if(isset($_GET['find_statDay'])){
				print "<tbody id='the-list'><tr><td></td><td colspan='2'>";
				print "ATTENTION! You are looking the stats of <b>" . $_GET['find_statYear'] . "/" . $_GET['find_statMonth'] . "/" . $_GET['find_statDay'] . "</b>   <a href=\"admin.php?page=statsurfer/statsurfer.php\"><b>Return to today</b></a>";
				print "<td><td></td></tr>";
				print "</tbody>";
			}
			
			print "</table></div>";
			
			if(isset($_GET['find_statDay']))
				$timestamp = mktime(11,0,0,$_GET['find_statMonth'],$_GET['find_statDay'],$_GET['find_statYear']);
			else
				$timestamp = current_time('timestamp');
			
			
			// OVERVIEW table
			$unique_color = "#114477";
			$web_color = "#3377B6";
			$rss_color = "#f38f36";
			$spider_color = "#83b4d8";
			$lastmonth = StatSurfer_lastmonth();
			$thismonth = gmdate('Ym', $timestamp);
			$yesterday = gmdate('Ymd', $timestamp - 86400);
			$today = gmdate('Ymd', $timestamp);
			$tlm[0] = my_substr($lastmonth, 0, 4);
			$tlm[1] = my_substr($lastmonth, 4, 2);
			
			print "<div class='wrap'><h2>" . __('Overview', 'StatSurfer') . "</h2>";
			print "<table class='widefat'><thead><tr>
			<th scope='col'></th>
			<th scope='col'>" . __('Total', 'StatSurfer') . "</th>
			<th scope='col'>" . __('Last month', 'StatSurfer') . "<br /><font size=1>" . gmdate('M, Y', gmmktime(0, 0, 0, $tlm[1], 1, $tlm[0])) . "</font></th>
			<th scope='col'>" . __('This month', 'StatSurfer') . "<br /><font size=1>" . gmdate('M, Y', $timestamp) . "</font></th>
			<th scope='col'>" . __('Target', 'StatSurfer') . " " . __('This month', 'StatSurfer') . "<br /><font size=1>" . gmdate('M, Y', $timestamp) . "</font></th>
			<th scope='col'>" . __('Yesterday', 'StatSurfer') . "<br /><font size=1>" . gmdate('d M, Y', $timestamp - 86400) . "</font></th>
			<th scope='col'>" . __('Today', 'StatSurfer') . "<br /><font size=1>" . gmdate('d M, Y', $timestamp) . "</font></th>
			</tr></thead>
			<tbody id='the-list'>";
			
			//###############################################################################################
			// VISITORS ROW
			print "<tr><td><div style='background:$unique_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Visitors', 'StatSurfer') . "</td>";
			
			//TOTAL
			$qry_total = $wpdb->get_row("
										SELECT count(DISTINCT ip) AS visitors
										FROM $table_name
										WHERE feed=''
										AND spider=''
										");
			print "<td>" . $qry_total->visitors . "</td>\n";
			
			//LAST MONTH
			$qry_lmonth = $wpdb->get_row("
										 SELECT count(DISTINCT ip) AS visitors
										 FROM $table_name
										 WHERE feed=''
										 AND spider=''
										 AND date LIKE '" . mysql_real_escape_string($lastmonth) . "%'
										 ");
			print "<td>" . $qry_lmonth->visitors . "</td>\n";
			
			//THIS MONTH
			$qry_tmonth = $wpdb->get_row("
										 SELECT count(DISTINCT ip) AS visitors
										 FROM $table_name
										 WHERE feed=''
										 AND spider=''
										 AND date LIKE '" . mysql_real_escape_string($thismonth) . "%'
										 ");
			if ($qry_lmonth->visitors <> 0)
			{
				$pc = round(100 * ($qry_tmonth->visitors / $qry_lmonth->visitors) - 100, 1);
				if ($pc >= 0)
					$pc = "+" . $pc;
				$qry_tmonth->change = "<code> (" . $pc . "%)</code>";
			}
			print "<td>" . $qry_tmonth->visitors . $qry_tmonth->change . "</td>\n";
			
			//TARGET
			
			$qry_tmonth->target = round($qry_tmonth->visitors / (time() - mktime(0,0,0,date('m'),date('1'),date('Y'))) * (86400 * date('t')));
			if ($qry_lmonth->visitors <> 0)
			{
				$pt = round(100 * ($qry_tmonth->target / $qry_lmonth->visitors) - 100, 1);
				if ($pt >= 0)
					$pt = "+" . $pt;
				$qry_tmonth->added = "<code> (" . $pt . "%)</code>";
			}
			print "<td>" . $qry_tmonth->target . $qry_tmonth->added . "</td>\n";
			
			//YESTERDAY
			$qry_y = $wpdb->get_row("
									SELECT count(DISTINCT ip) AS visitors
									FROM $table_name
									WHERE feed=''
									AND spider=''
									AND date = '" . mysql_real_escape_string($yesterday) . "'
									");
			print "<td>" . $qry_y->visitors . "</td>\n";
			
			//TODAY
			$qry_t = $wpdb->get_row("
									SELECT count(DISTINCT ip) AS visitors
									FROM $table_name
									WHERE feed=''
									AND spider=''
									AND date = '" . mysql_real_escape_string($today) . "'
									");
			print "<td>" . $qry_t->visitors . "</td>\n";
			print "</tr>";
			
			//###############################################################################################
			// PAGEVIEWS ROW
			print "<tr><td><div style='background:$web_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Pageviews', 'StatSurfer') . "</td>";
			
			//TOTAL
			$qry_total = $wpdb->get_row("
										SELECT count(date) as pageview
										FROM $table_name
										WHERE feed=''
										AND spider=''
										");
			print "<td>" . $qry_total->pageview . "</td>\n";
			
			//LAST MONTH
			$prec = 0;
			$qry_lmonth = $wpdb->get_row("
										 SELECT count(date) as pageview
										 FROM $table_name
										 WHERE feed=''
										 AND spider=''
										 AND date LIKE '" . mysql_real_escape_string($lastmonth) . "%'
										 ");
			print "<td>" . $qry_lmonth->pageview . "</td>\n";
			
			//THIS MONTH
			$qry_tmonth = $wpdb->get_row("
										 SELECT count(date) as pageview
										 FROM $table_name
										 WHERE feed=''
										 AND spider=''
										 AND date LIKE '" . mysql_real_escape_string($thismonth) . "%'
										 ");
			if ($qry_lmonth->pageview <> 0)
			{
				$pc = round(100 * ($qry_tmonth->pageview / $qry_lmonth->pageview) - 100, 1);
				if ($pc >= 0)
					$pc = "+" . $pc;
				$qry_tmonth->change = "<code> (" . $pc . "%)</code>";
			}
			print "<td>" . $qry_tmonth->pageview . $qry_tmonth->change . "</td>\n";
			
			//TARGET
			$qry_tmonth->target = round($qry_tmonth->pageview / (time() - mktime(0,0,0,date('m'),date('1'),date('Y'))) * (86400 * date('t')));
			if ($qry_lmonth->pageview <> 0)
			{
				$pt = round(100 * ($qry_tmonth->target / $qry_lmonth->pageview) - 100, 1);
				if ($pt >= 0)
					$pt = "+" . $pt;
				$qry_tmonth->added = "<code> (" . $pt . "%)</code>";
			}
			print "<td>" . $qry_tmonth->target . $qry_tmonth->added . "</td>\n";
			
			//YESTERDAY
			$qry_y = $wpdb->get_row("
									SELECT count(date) as pageview
									FROM $table_name
									WHERE feed=''
									AND spider=''
									AND date = '" . mysql_real_escape_string($yesterday) . "'
									");
			print "<td>" . $qry_y->pageview . "</td>\n";
			
			//TODAY
			$qry_t = $wpdb->get_row("
									SELECT count(date) as pageview
									FROM $table_name
									WHERE feed=''
									AND spider=''
									AND date = '" . mysql_real_escape_string($today) . "'
									");
			print "<td>" . $qry_t->pageview . "</td>\n";
			print "</tr>";
			//###############################################################################################
			// SPIDERS ROW
			print "<tr><td><div style='background:$spider_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Spiders', 'StatSurfer') . "</td>";
			//TOTAL
			$qry_total = $wpdb->get_row("
										SELECT count(date) as spiders
										FROM $table_name
										WHERE feed=''
										AND spider<>''
										");
			print "<td>" . $qry_total->spiders . "</td>\n";
			//LAST MONTH
			$prec = 0;
			$qry_lmonth = $wpdb->get_row("
										 SELECT count(date) as spiders
										 FROM $table_name
										 WHERE feed=''
										 AND spider<>''
										 AND date LIKE '" . mysql_real_escape_string($lastmonth) . "%'
										 ");
			print "<td>" . $qry_lmonth->spiders . "</td>\n";
			
			//THIS MONTH
			$prec = $qry_lmonth->spiders;
			$qry_tmonth = $wpdb->get_row("
										 SELECT count(date) as spiders
										 FROM $table_name
										 WHERE feed=''
										 AND spider<>''
										 AND date LIKE '" . mysql_real_escape_string($thismonth) . "%'
										 ");
			if ($qry_lmonth->spiders <> 0)
			{
				$pc = round(100 * ($qry_tmonth->spiders / $qry_lmonth->spiders) - 100, 1);
				if ($pc >= 0)
					$pc = "+" . $pc;
				$qry_tmonth->change = "<code> (" . $pc . "%)</code>";
			}
			print "<td>" . $qry_tmonth->spiders . $qry_tmonth->change . "</td>\n";
			
			//TARGET
			$qry_tmonth->target = round($qry_tmonth->spiders / (time() - mktime(0,0,0,date('m'),date('1'),date('Y'))) * (86400 * date('t')));
			if ($qry_lmonth->spiders <> 0)
			{
				$pt = round(100 * ($qry_tmonth->target / $qry_lmonth->spiders) - 100, 1);
				if ($pt >= 0)
					$pt = "+" . $pt;
				$qry_tmonth->added = "<code> (" . $pt . "%)</code>";
			}
			print "<td>" . $qry_tmonth->target . $qry_tmonth->added . "</td>\n";
			
			//YESTERDAY
			$qry_y = $wpdb->get_row("
									SELECT count(date) as spiders
									FROM $table_name
									WHERE feed=''
									AND spider<>''
									AND date = '" . mysql_real_escape_string($yesterday) . "'
									");
			print "<td>" . $qry_y->spiders . "</td>\n";
			
			//TODAY
			$qry_t = $wpdb->get_row("
									SELECT count(date) as spiders
									FROM $table_name
									WHERE feed=''
									AND spider<>''
									AND date = '" . mysql_real_escape_string($today) . "'
									");
			print "<td>" . $qry_t->spiders . "</td>\n";
			print "</tr>";
			//###############################################################################################
			// FEEDS ROW
			print "<tr><td><div style='background:$rss_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Feeds', 'StatSurfer') . "</td>";
			//TOTAL
			$qry_total = $wpdb->get_row("
										SELECT count(date) as feeds
										FROM $table_name
										WHERE feed<>''
										AND spider=''
										");
			print "<td>" . $qry_total->feeds . "</td>\n";
			
			//LAST MONTH
			$qry_lmonth = $wpdb->get_row("
										 SELECT count(date) as feeds
										 FROM $table_name
										 WHERE feed<>''
										 AND spider=''
										 AND date LIKE '" . mysql_real_escape_string($lastmonth) . "%'
										 ");
			print "<td>" . $qry_lmonth->feeds . "</td>\n";
			
			//THIS MONTH
			$qry_tmonth = $wpdb->get_row("
										 SELECT count(date) as feeds
										 FROM $table_name
										 WHERE feed<>''
										 AND spider=''
										 AND date LIKE '" . mysql_real_escape_string($thismonth) . "%'
										 ");
			if ($qry_lmonth->feeds <> 0)
			{
				$pc = round(100 * ($qry_tmonth->feeds / $qry_lmonth->feeds) - 100, 1);
				if ($pc >= 0)
					$pc = "+" . $pc;
				$qry_tmonth->change = "<code> (" . $pc . "%)</code>";
			}
			print "<td>" . $qry_tmonth->feeds . $qry_tmonth->change . "</td>\n";
			
			//TARGET
			$qry_tmonth->target = round($qry_tmonth->feeds / (time() - mktime(0,0,0,date('m'),date('1'),date('Y'))) * (86400 * date('t')));
			if ($qry_lmonth->feeds <> 0)
			{
				$pt = round(100 * ($qry_tmonth->target / $qry_lmonth->feeds) - 100, 1);
				if ($pt >= 0)
					$pt = "+" . $pt;
				$qry_tmonth->added = "<code> (" . $pt . "%)</code>";
			}
			print "<td>" . $qry_tmonth->target . $qry_tmonth->added . "</td>\n";
			
			$qry_y = $wpdb->get_row("
									SELECT count(date) as feeds
									FROM $table_name
									WHERE feed<>''
									AND spider=''
									AND date = '" . mysql_real_escape_string($yesterday) . "'
									");
			print "<td>" . $qry_y->feeds . "</td>\n";
			
			$qry_t = $wpdb->get_row("
									SELECT count(date) as feeds
									FROM $table_name
									WHERE feed<>''
									AND spider=''
									AND date = '" . mysql_real_escape_string($today) . "'
									");
			print "<td>" . $qry_t->feeds . "</td>\n";
			
			print "</tr>";
			//echo "</table><br />\n\n";
			
			//print "</div>";
			
		}	
		
		
		//###############################################################################################
		//###############################################################################################
		// GRAPHS
		
		?>
		<style>
			#iframe_loading_img{
				width:100%;
				height:404px;
				background-color:#ffffff;
				padding-top:28px;
			}

			#iframe_display{
				display:none;
				border:0px;
				margin:0px;
			}
		</style>
	
		<script type="text/javascript">
			function iframe_load(){
				//alert("Iframe is loaded");
				var divIframe = document.getElementById("iframe_display");
				var divIMG = document.getElementById("iframe_loading_img");
				divIMG.style.display = 'none';
				divIframe.style.display = 'inline';
			}
		</script>
		<?php
			
		//print "<div class='wrap'>";
		//echo "<table class='widefat'><thead><tr><th><!--graphs--></th></thead><tbody>";
		echo "<tr><td colspan='7' height='437'>";
		
		/*echo "<div class='local_css_graph' >";
		
		{
			//DAYS GRAPH
			
			echo "<h3 style='font:italic 17px Georgia,Times New Roman,Bitstream Charter,Times,serif;padding:0px 0px 0px 0px;margin:7px 0px 7px 0px;'>Day by day</h3>";
			// last "N" days graph  NEW
			$gdays = get_option('StatSurfer_daysinoverviewgraph');
			if ($gdays == 0)
			{
				$gdays = 20;
			}
			//  $start_of_week = get_settings('start_of_week');
			$start_of_week = get_option('start_of_week');
			print '<table width="100%" border="0"><tr>';
			$qry = $wpdb->get_row("
								  SELECT count(date) as pageview, date
								  FROM $table_name
								  GROUP BY date HAVING date >= '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gdays) . "'
								  ORDER BY pageview DESC
								  LIMIT 1
								  ");
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
				$qry_visitors = $wpdb->get_row("
											   SELECT count(DISTINCT ip) AS total
											   FROM $table_name
											   WHERE feed=''
											   AND spider=''
											   AND date = '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gg) . "'
											   ");
				$px_visitors = round($qry_visitors->total * 100 / $maxxday);
				
				//TOTAL PAGEVIEWS (we do not delete the uniques, this is falsing the info.. uniques are not different visitors!)
				$qry_pageviews = $wpdb->get_row("
												SELECT count(date) as total
												FROM $table_name
												WHERE feed=''
												AND spider=''
												AND date = '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gg) . "'
												");
				$px_pageviews = round($qry_pageviews->total * 100 / $maxxday);
				
				//TOTAL SPIDERS
				$qry_spiders = $wpdb->get_row("
											  SELECT count(ip) AS total
											  FROM $table_name
											  WHERE feed=''
											  AND spider<>''
											  AND date = '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gg) . "'
											  ");
				$px_spiders = round($qry_spiders->total * 100 / $maxxday);
				
				//TOTAL FEEDS
				$qry_feeds = $wpdb->get_row("
											SELECT count(ip) AS total
											FROM $table_name
											WHERE feed<>''
											AND spider=''
											AND date = '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gg) . "'
											");
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
			
			
			//MONTHS AND YEARS GRAPHS
			
			$monthName = array(1=> "January", "February", "March", 
							   "April", "May", "June", "July", "August", 
							   "September", "October", "November", "December");
			
			$monthNameNumber = array(1=> "01", "02", "03", "04", "05", "06", 
									 "07", "08", "09", "10", "11", "12");
			
			print "<div class='wrap'>";
			echo "<br /><table width='100%' border='0' cellspacing='0' cellpadding='0'>";
			
			echo "<tr><td><h3 style='font:italic 17px Georgia,Times New Roman,Bitstream Charter,Times,serif;padding:0px 0px 0px 0px;margin:10px 0px 7px 0px;'>Month by month</h3>
			</td><td><h3 style='font:italic 17px Georgia,Times New Roman,Bitstream Charter,Times,serif;padding:0px 0px 0px 0px;margin:10px 0px 7px 0px;'>Year by year</h3>
			</td></tr>";
			
			echo "<tr><td width='70%'>";
			
			$gdays = 12;
			
			$cMonth = gmdate('m', current_time('timestamp'));
			$cMonthID = (int)$cMonth;
			
			$cYearID = (int)gmdate('Y', current_time('timestamp'));
			
			
			if($cMonthID==12){
				$cMonthID = 1; 
			}
			else{
				$cMonthID = $cMonthID + 1;
				$cYearID--;
			}
			
			
			print '<table width="100%" border="0"><tr>';
			
			$dataTab = array(0,0,0,0,
							 0,0,0,0,
							 0,0,0,0,
							 0,0,0,0,
							 0,0,0,0,
							 0,0,0,0,
							 0,0,0,0,
							 0,0,0,0,
							 0,0,0,0,
							 0,0,0,0,
							 0,0,0,0,
							 0,0,0,0);
			$dataTOT = array(0,0,0,0,0,0,0,0,0,0,0,0);
			
			
			for($i=0;$i<12;$i++){
				
				$startDate = $cYearID . $monthNameNumber[$cMonthID] . "00";
				$endDate = $cYearID . $monthNameNumber[$cMonthID] . "32";
				
				//TOTAL VISITORS
				$qry_visitors = $wpdb->get_row("
											   SELECT count(DISTINCT ip) AS total
											   FROM $table_name
											   WHERE feed=''
											   AND spider=''
											   AND date > '" . $startDate . "'
											   AND date < '" . $endDate . "'
											   ");
				$px_visitors = $qry_visitors->total;
				$ID= 0 + (4*$i);
				$dataTab[$ID]=$px_visitors;
				$sub_tot = $px_visitors;
				
				
				//TOTAL PAGEVIEWS (we do not delete the uniques, this is falsing the info.. uniques are not different visitors!)
				$qry_pageviews = $wpdb->get_row("
												SELECT count(date) as total
												FROM $table_name
												WHERE feed=''
												AND spider=''
												AND date > '" . $startDate . "'
												AND date < '" . $endDate . "'
												");
				$px_pageviews = $qry_pageviews->total;
				$ID= 1 + (4*$i);
				$dataTab[$ID]=$px_pageviews;
				$sub_tot = $sub_tot + $px_pageviews;
				
				
				//TOTAL SPIDERS
				$qry_spiders = $wpdb->get_row("
											  SELECT count(ip) AS total
											  FROM $table_name
											  WHERE feed=''
											  AND spider<>''
											  AND date > '" . $startDate . "'
											  AND date < '" . $endDate . "'
											  ");
				$px_spiders = $qry_spiders->total;
				$ID= 2 + (4*$i);
				$dataTab[$ID]=$px_spiders;
				$sub_tot = $sub_tot + $px_spiders;
				
				
				//TOTAL FEEDS
				$qry_feeds = $wpdb->get_row("
											SELECT count(ip) AS total
											FROM $table_name
											WHERE feed<>''
											AND spider=''
											AND date > '" . $startDate . "'
											AND date < '" . $endDate . "'
											");
				$px_feeds = $qry_feeds->total;
				$ID= 3 + (4*$i);
				$dataTab[$ID]=$px_feeds;
				$sub_tot = $sub_tot + $px_feeds;
				
				$dataTOT[$i]=$sub_tot;
				
				$cMonthID++;
				
				if($cMonthID == 13){
					$cMonthID = 1;
					$cYearID++;
				}
				
			}
			
			
			$cMonth = gmdate('m', current_time('timestamp'));
			$cMonthID = (int)$cMonth;
			
			$cYearID = (int)gmdate('Y', current_time('timestamp'));
			
			
			if($cMonthID==12){
				$cMonthID = 1; 
			}
			else{
				$cMonthID = $cMonthID + 1;
				$cYearID--;
			}
			
			$ID = 0;
			
			$maxxday = $dataTOT[0];
			for($i=0;$i<12;$i++){
				if($dataTOT[$i]>$maxxday)
					$maxxday = $dataTOT[$i];
			}
			if ($maxxday == 0)
			{
				$maxxday = 1;
			}
			$gd = (90 / $gdays) . '%';
			
			for($i=0;$i<12;$i++){
				
				$px_visitors = round($dataTab[$ID] * 100 / $maxxday);
				$px_pageviews = round($dataTab[$ID+1] * 100 / $maxxday);
				$px_spiders = round($dataTab[$ID+2] * 100 / $maxxday);
				$px_feeds = round($dataTab[$ID+3] * 100 / $maxxday);
				
				$px_white = 100 - $px_feeds - $px_spiders - $px_pageviews - $px_visitors;
				
				print '<td width="' . $gd . '" valign="bottom"';
				// week-cut
				print "><div style='float:left;height: 100%;width:100%;font-family:Helvetica;font-size:7pt;text-align:center;border-right:1px solid white;color:black;'>
				<div style='background:#ffffff;width:100%;height:" . $px_white . "px;'></div>
				<div style='background:$spider_color;width:100%;height:" . $px_spiders . "px;' title='" . $dataTab[$ID+2] . " " . __('spiders', 'StatSurfer')."'></div>
				<div style='background:$rss_color;width:100%;height:" . $px_feeds . "px;' title='" . $dataTab[$ID+3] . " " . __('feeds', 'StatSurfer')."'></div>
				<div style='background:$web_color;width:100%;height:" . $px_pageviews . "px;' title='" . $dataTab[$ID+1] . " " . __('pageviews', 'StatSurfer')."'></div>
				<div style='background:$unique_color;width:100%;height:" . $px_visitors . "px;' title='" . $dataTab[$ID] . " " . __('visitors', 'StatSurfer')."'></div>
				<div style='background:gray;width:100%;height:1px;'></div>
				<br />" . $monthName[$cMonthID] . '<br />' . $cYearID . "</div></td>\n";
				
				$ID = $ID + 4;
				$cMonthID++;
				
				if($cMonthID == 13){
					$cMonthID = 1;
					$cYearID++;
				}
				
			}
			
			
			print '</tr></table>';
			
			echo "</td><td width='30%' style='border-left:2px dotted gray;'>";
			
			
			
			//YEAR
			
			$gdays = 5;
			
			$cYearID = (int)gmdate('Y', current_time('timestamp'));
			
			
			print '<table width="100%" border="0"><tr>';
			
			$dataTab = array(0,0,0,0,
							 0,0,0,0,
							 0,0,0,0,
							 0,0,0,0,
							 0,0,0,0);
			$dataTOT = array(0,0,0,0,0);
			
			$c = 0;
			for($i=4;$i>(-1);$i--){
				
				$startDate = ($cYearID - $i) . "00" . "00";
				$endDate = ($cYearID - $i) . "13" . "32";
				
				//TOTAL VISITORS
				$qry_visitors = $wpdb->get_row("
											   SELECT count(DISTINCT ip) AS total
											   FROM $table_name
											   WHERE feed=''
											   AND spider=''
											   AND date > '" . $startDate . "'
											   AND date < '" . $endDate . "'
											   ");
				$px_visitors = $qry_visitors->total;
				$ID= 0 + (4*$c);
				$dataTab[$ID]=$px_visitors;
				$sub_tot = $px_visitors;
				
				
				//TOTAL PAGEVIEWS (we do not delete the uniques, this is falsing the info.. uniques are not different visitors!)
				$qry_pageviews = $wpdb->get_row("
												SELECT count(date) as total
												FROM $table_name
												WHERE feed=''
												AND spider=''
												AND date > '" . $startDate . "'
												AND date < '" . $endDate . "'
												");
				$px_pageviews = $qry_pageviews->total;
				$ID= 1 + (4*$c);
				$dataTab[$ID]=$px_pageviews;
				$sub_tot = $sub_tot + $px_pageviews;
				
				
				//TOTAL SPIDERS
				$qry_spiders = $wpdb->get_row("
											  SELECT count(ip) AS total
											  FROM $table_name
											  WHERE feed=''
											  AND spider<>''
											  AND date > '" . $startDate . "'
											  AND date < '" . $endDate . "'
											  ");
				$px_spiders = $qry_spiders->total;
				$ID= 2 + (4*$c);
				$dataTab[$ID]=$px_spiders;
				$sub_tot = $sub_tot + $px_spiders;
				
				
				//TOTAL FEEDS
				$qry_feeds = $wpdb->get_row("
											SELECT count(ip) AS total
											FROM $table_name
											WHERE feed<>''
											AND spider=''
											AND date > '" . $startDate . "'
											AND date < '" . $endDate . "'
											");
				$px_feeds = $qry_feeds->total;
				$ID= 3 + (4*$c);
				$dataTab[$ID]=$px_feeds;
				$sub_tot = $sub_tot + $px_feeds;
				
				$dataTOT[4-$i]=$sub_tot;
				$c++;
				
			}
			
			$ID = 0;
			
			$maxxday = $dataTOT[0];
			for($i=0;$i<5;$i++){
				if($dataTOT[$i]>$maxxday)
					$maxxday = $dataTOT[$i];
			}
			if ($maxxday == 0)
			{
				$maxxday = 1;
			}
			$gd = (90 / $gdays) . '%';
			
			for($i=4;$i>(-1);$i--){
				
				$px_visitors = round($dataTab[$ID] * 100 / $maxxday);
				$px_pageviews = round($dataTab[$ID+1] * 100 / $maxxday); 
				$px_spiders = round($dataTab[$ID+2] * 100 / $maxxday);
				$px_feeds = round($dataTab[$ID+3] * 100 / $maxxday);
				
				$px_white = 100 - $px_feeds - $px_spiders - $px_pageviews - $px_visitors;
				
				print '<td width="' . $gd . '" valign="bottom">';
				print "<div style='float:left;height: 100%;width:100%;font-family:Helvetica;font-size:7pt;text-align:center;border-right:1px solid white;color:black;'>
				<div style='background:#ffffff;width:100%;height:" . $px_white . "px;'></div>
				<div style='background:$spider_color;width:100%;height:" . $px_spiders . "px;' title='" . $dataTab[$ID+2] . " " . __('spiders', 'StatSurfer')."'></div>
				<div style='background:$rss_color;width:100%;height:" . $px_feeds . "px;' title='" . $dataTab[$ID+3] . " " . __('feeds', 'StatSurfer')."'></div>
				<div style='background:$web_color;width:100%;height:" . $px_pageviews . "px;' title='" . $dataTab[$ID+1] . " " . __('pageviews', 'StatSurfer')."'></div>
				<div style='background:$unique_color;width:100%;height:" . $px_visitors . "px;' title='" . $dataTab[$ID] . " " . __('visitors', 'StatSurfer')."'></div>
				<div style='background:gray;width:100%;height:1px;'></div>
				<br />" . ($cYearID - $i) . "<br /> &nbsp;</div></td>\n";
				
				$ID = $ID + 4;
				
			}
			
			
			print '</tr></table>'; 
			
			echo "</td></tr></table><br />";
			echo "</div>";
			
			//----------------------------------------------------------------------------------------------------// 
			//----------------------------------------------------------------------------------------------------//
		}
		
		echo "</div>";*/
			
		//IFRAME TEST
		echo "<div id='iframe_loading_img'>";
		echo "<center><img src='../wp-content/plugins/statsurfer/images/loading.gif' width='500' /></center>";
		echo "</div>";
			
		echo "<iframe onload='iframe_load()' src='admin.php?page=options&other_page=iframe_main_graph' width='100%' height='432' frameborder='0' scrolling='no' id='iframe_display'>";
		echo "<img src='image/loading.gif' width='500' />";
		echo "</iframe>";
		
		echo "</td></tr></tbody></table>";
		print "</div>";
		
		
		//###############################################################################################
		//###############################################################################################
		// DATAS
		
		{
			$querylimit = "LIMIT 20";
			
			// First data row
			{
				echo "<table width='100%' border='0' cellspacing='0' cellpadding='0'>";
				echo "<tr><td width='50%'>";
				
				// Last Search terms
				print "<div class='wrap'><h2>" . __('Last search terms', 'StatSurfer') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'StatSurfer') . "</th><th scope='col'>" . __('Time', 'StatSurfer') . "</th><th scope='col'>" . __('Terms', 'StatSurfer') . "</th><th scope='col'>" . __('Engine', 'StatSurfer') . "</th><th scope='col'>" . __('Result', 'StatSurfer') . "</th></tr></thead>";
				print "<tbody id='the-list'>";
				$qry = $wpdb->get_results("SELECT date,time,referrer,urlrequested,search,searchengine FROM $table_name WHERE search<>'' ORDER BY id DESC $querylimit");
				foreach ($qry as $rk)
				{
					print "<tr><td>" . irihdate($rk->date) . "</td><td>" . $rk->time . "</td><td><a href='" . $rk->referrer . "'>" . StatSurfer_Abbrevia(urldecode($rk->search), 50) . "</a></td><td>" . $rk->searchengine . "</td><td><a href='" . irigetblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "'>" . __('page viewed', 'StatSurfer') . "</a></td></tr>\n";
				}
				print "</table></div>";
				
				echo "</td><td width='50%'>";
				
				// Referrer
				print "<div class='wrap'><h2>" . __('Last referrers', 'StatSurfer') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'StatSurfer') . "</th><th scope='col'>" . __('Time', 'StatSurfer') . "</th><th scope='col'>" . __('URL', 'StatSurfer') . "</th><th scope='col'>" . __('Result', 'StatSurfer') . "</th></tr></thead>";
				print "<tbody id='the-list'>";
				$qry = $wpdb->get_results("SELECT date,time,referrer,urlrequested FROM $table_name WHERE ((referrer NOT LIKE '" . get_option('home') . "%') AND (referrer <>'') AND (searchengine='')) ORDER BY id DESC $querylimit");
				foreach ($qry as $rk)
				{
					print "<tr><td>" . irihdate($rk->date) . "</td><td>" . $rk->time . "</td><td><a href='" . $rk->referrer . "'>" . StatSurfer_Abbrevia($rk->referrer, 35) . "</a></td><td><a href='" . irigetblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "'>" . __('page viewed', 'StatSurfer') . "</a></td></tr>\n";
				}
				print "</table></div>";
				
				echo "</td></tr>";
				echo "</table>";
			}
			
			// Last pages
			{
				print "<div class='wrap'><h2>" . __('Last pages', 'StatSurfer') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'StatSurfer') . "</th><th scope='col'>" . __('Time', 'StatSurfer') . "</th><th scope='col'>" . __('Page', 'StatSurfer') . "</th><th scope='col'>" . __('What', 'StatSurfer') . "</th></tr></thead>";
				print "<tbody id='the-list'>";
				$qry = $wpdb->get_results("SELECT date,time,urlrequested,os,browser,spider FROM $table_name WHERE (spider='' AND feed='') ORDER BY id DESC $querylimit");
				foreach ($qry as $rk)
				{
					print "<tr><td>" . irihdate($rk->date) . "</td><td>" . $rk->time . "</td><td>" . StatSurfer_Abbrevia(StatSurfer_Decode($rk->urlrequested), 60) . "</td><td> " . $rk->os . " " . $rk->browser . " " . $rk->spider . "</td></tr>\n";
				}
				print "</table></div>";
			}
			
			
			// Tabella Last hits
			{
				print "<div class='wrap'><h2>" . __('Last hits', 'StatSurfer') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'StatSurfer') . "</th><th scope='col'>" . __('Time', 'StatSurfer') . "</th><th scope='col'>" . __('IP', 'StatSurfer') . "</th><th scope='col'>" . __('Threat', 'StatSurfer') . "</th><th scope='col'>" . __('Domain', 'StatSurfer') . "</th><th scope='col'>" . __('Page', 'StatSurfer') . "</th><th scope='col'>" . __('OS', 'StatSurfer') . "</th><th scope='col'>" . __('Browser', 'StatSurfer') . "</th><th scope='col'>" . __('Feed', 'StatSurfer') . "</th></tr></thead>";
				print "<tbody id='the-list'>";
				
				$fivesdrafts = $wpdb->get_results("SELECT * FROM $table_name WHERE (os<>'' OR feed<>'') order by id DESC $querylimit");
				foreach ($fivesdrafts as $fivesdraft)
				{
					print "<tr>";
					print "<td>" . irihdate($fivesdraft->date) . "</td>";
					print "<td>" . $fivesdraft->time . "</td>";
					print "<td>" . $fivesdraft->ip . "</td>";
					print "<td>" . $fivesdraft->threat_score;
					if ($fivesdraft->threat_score > 0)
					{
						print "/";
						if ($fivesdraft->threat_type == 0)
							print "Sp"; // Spider
						else
						{
							if (($fivesdraft->threat_type & 1) == 1)
								print "S"; // Suspicious
							if (($fivesdraft->threat_type & 2) == 2)
								print "H"; // Harvester
							if (($fivesdraft->threat_type & 4) == 4)
								print "C"; // Comment spammer
						}
					}
					print "<td>" . $fivesdraft->nation . "</td>";
					print "<td>" . StatSurfer_Abbrevia(StatSurfer_Decode($fivesdraft->urlrequested), 30) . "</td>";
					print "<td>" . $fivesdraft->os . "</td>";
					print "<td>" . $fivesdraft->browser . "</td>";
					print "<td>" . $fivesdraft->feed . "</td>";
					print "</tr>";
				}
				print "</table></div>";
			}
			
			// Last Agents
			{
				print "<div class='wrap'><h2>" . __('Last agents', 'StatSurfer') . "</h2>";
				echo "<table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'StatSurfer') . "</th><th scope='col'>" . __('Time', 'StatSurfer') . "</th><th scope='col'>" . __('Agent', 'StatSurfer') . "</th><th scope='col'>" . __('What', 'StatSurfer') . "</th></tr></thead>";
				print "<tbody id='the-list'>";
				$qry = $wpdb->get_results("SELECT date,time,agent,os,browser,spider FROM $table_name WHERE (agent <>'') ORDER BY id DESC $querylimit");
				foreach ($qry as $rk)
				{
					print "<tr><td>" . irihdate($rk->date) . "</td><td>" . $rk->time . "</td><td>" . $rk->agent . "</td><td> " . $rk->os . " " . $rk->browser . " " . $rk->spider . "</td></tr>\n";
				}
				print "</table></div>";
			}
			
			
			// Last Spiders
			{
				print "<div class='wrap'><h2>" . __('Last spiders', 'StatSurfer') . "</h2>";
				print "<table class='widefat'><thead><tr>";
				print "<th scope='col'>" . __('Date', 'StatSurfer') . "</th>";
				print "<th scope='col'>" . __('Time', 'StatSurfer') . "</th>";
				print "<th scope='col'>" . __('Spider', 'StatSurfer') . "</th>";
				print "<th scope='col'>" . __('Page', 'StatSurfer') . "</th>";
				print "<th scope='col'>" . __('Agent', 'StatSurfer') . "</th>";
				print "</tr></thead><tbody id='the-list'>";
				$qry = $wpdb->get_results("SELECT date,time,agent,spider,urlrequested,agent FROM $table_name WHERE (spider<>'') ORDER BY id DESC $querylimit");
				foreach ($qry as $rk)
				{
					print "<tr><td>" . irihdate($rk->date) . "</td>";
					print "<td>" . $rk->time . "</td>";
					print "<td>" . $rk->spider . "</td>";
					print "<td>" . StatSurfer_Abbrevia(StatSurfer_Decode($rk->urlrequested), 30) . "</td>";
					print "<td> " . $rk->agent . "</td></tr>\n";
				}
				print "</table></div>";
			}
			
			print "<br />";
			print "&nbsp;<i>" . __('StatSurfer table size', 'StatSurfer') . ": <b>" . iritablesize($wpdb->prefix . $_STATSURFER['table_name']) . "</b></i><br />";
			print "&nbsp;<i>" . __('StatSurfer current time', 'StatSurfer') . ": <b>" . current_time('mysql') . "</b></i><br />";
			print "&nbsp;<i>" . __('RSS2 url', 'StatSurfer') . ": <b>" . get_bloginfo('rss2_url') . ' (' . StatSurfer_extractfeedreq(get_bloginfo('rss2_url')) . ")</b></i><br />";
			print "&nbsp;<i>" . __('ATOM url', 'StatSurfer') . ": <b>" . get_bloginfo('atom_url') . ' (' . StatSurfer_extractfeedreq(get_bloginfo('atom_url')) . ")</b></i><br />";
			print "&nbsp;<i>" . __('RSS url', 'StatSurfer') . ": <b>" . get_bloginfo('rss_url') . ' (' . StatSurfer_extractfeedreq(get_bloginfo('rss_url')) . ")</b></i><br />";
			print "&nbsp;<i>" . __('COMMENT RSS2 url', 'StatSurfer') . ": <b>" . get_bloginfo('comments_rss2_url') . ' (' . StatSurfer_extractfeedreq(get_bloginfo('comments_rss2_url')) . ")</b></i><br />";
			print "&nbsp;<i>" . __('COMMENT ATOM url', 'StatSurfer') . ": <b>" . get_bloginfo('comments_atom_url') . ' (' . StatSurfer_extractfeedreq(get_bloginfo('comments_atom_url')) . ")</b></i><br />";
		}
		
	}
	
	?>