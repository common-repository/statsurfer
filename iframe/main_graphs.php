<?php

	/*	
		PlugIn: StatSurfer
		Page: iframe/main_graphs.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	function StatSurfer_mainGraphs()
	{
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		
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
		
		?>
		
		<style>
			.iframe_main_graph{
				width:100%;
				background-color:#ffffff;
				position:absolute;
				left:0px;
				top:0px;
				z-index:5;
			}

			.iframe_background{
				width:100%;
				height:100%;
				background-color:#ffffff;
				position:absolute;
				left:0px;
				top:0px;
				z-index:4;
			}
		</style>

		<?php
			
		print "<div class='iframe_background'></div>";
			
		print "<div class='iframe_main_graph'>";
		
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
		
		print "</div>";
			
	}
	
	?>