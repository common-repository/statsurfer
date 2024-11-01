<?php

	/*	
		PlugIn: StatSurfer
		Page: pages/details.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	function StatSurferDetails()
	{
		
		?>
		<style>
			table.<table class='detail_content_table'{
				width:100%;
				border:0px;
				padding:0px;
				margin:0px;
			}
		</style>
		<?php
		
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		
		$querylimit = "LIMIT 10";
		
		echo "<div class='wrap'>"; // maindiv
		
		echo "<table class='detail_content_table' cellspacing='0' cellpadding='0' width='100%'>";
		echo "<tr><td width='50%' valign='top' rowspan='2'>";
		// Top days
		iriValueTable(1,0,"date", __('Top days', 'StatSurfer'), 13);
		echo "</td><td valign='top'>";
		// Top Days - Unique visitors
		iriValueTable(1,0,"date", __('Top Days - Unique visitors', 'StatSurfer'), 5, "distinct", "ip", "AND feed='' and spider=''");
		echo "</td></tr><tr><td valign='top'>";
		// Top Days - Pageviews
		iriValueTable(1,0,"date", __('Top Days - Pageviews', 'StatSurfer'), 5, "", "urlrequested", "AND feed='' and spider=''");
		echo "</td></tr></table>";
			
		echo "<br />";
		
		echo "<table class='detail_content_table' cellspacing='0' cellpadding='0'>";
		echo "<tr><td width='50%' valign='top'>";
		// O.S.
		iriValueTable(1,1,"os", __('O.S.', 'StatSurfer'), 30, "", "", "AND feed='' AND spider='' AND os<>''");
		echo "</td><td valign='top'>";
		// Browser
		iriValueTable(1,1,"browser", __('Browser', 'StatSurfer'), 30, "", "", "AND feed='' AND spider='' AND browser<>''");
		echo "</td></tr></table>";
		
		echo "<br />";
		
		// Feeds
		iriValueTable(0,1,"feed", __('Feeds', 'StatSurfer'), 5, "", "", "AND feed<>''");
		
		echo "<br />";
		
		echo "<table class='detail_content_table' cellspacing='0' cellpadding='0'>";
		echo "<tr><td width='50%' valign='top'>";
		// SE
		iriValueTable(1,1,"searchengine", __('Search engines', 'StatSurfer'), 15, "", "", "AND searchengine<>''");
		echo "</td><td valign='top'>";
		// Search terms
		iriValueTable(1,1,"search", __('Top search terms', 'StatSurfer'), 15, "", "", "AND search<>''");
		echo "</td></tr></table>";
		
		
		echo "<br />";
		// Top referrer
		iriValueTable(0,0,"referrer", __('Top referrer', 'StatSurfer'), 10, "", "", "AND referrer<>'' AND referrer NOT LIKE '%" . get_bloginfo('url') . "%'");
		
		echo "<br />";
		// Top Pages
		iriValueTable(0,0,"urlrequested", __('Top pages', 'StatSurfer'), 10, "", "urlrequested", "AND feed='' and spider=''");
		
		echo "<br />";
		
		echo "<table class='detail_content_table' cellspacing='0' cellpadding='0'>";
		echo "<tr><td width='50%' valign='top'>";
		// Spider
		iriValueTable(1,1,"spider", __('Spiders', 'StatSurfer'), 25, "", "", "AND spider<>''");
		echo "</td><td valign='top'>";
		// Countries
		iriValueTable(1,1,"nation", __('Countries (domains)', 'StatSurfer'), 25, "", "", "AND nation<>'' AND spider=''");
		echo "</td></tr></table>";
		
		echo "</div>"; //maindiv
		
		echo "<br /><br />";
		
		/* Maddler 04112007: required patching iriValueTable */
	}
	
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	//PRINT FUNCTION 
	
	function iriValueTable($type = 1, $graph = 1, $fld, $fldtitle, $limit = 0, $param = "", $queryfld = "", $exclude = "")
	{
		/* Maddler 04112007: param addedd */
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		
		if ($queryfld == '')
		{
			$queryfld = $fld;
		}
		print "<div class='wrap'><h2>$fldtitle</h2><table class='widefat'><thead><tr><th></th><th width='60' style='text-align:right;padding-right:40px;'>" . __('Visits', 'StatSurfer') . "</th><th width='30' style='text-align:right;'>%</th><th></th></tr></thead>";
		print "<tbody id='the-list'>";
		$rks = $wpdb->get_var("SELECT count($param $queryfld) as rks FROM $table_name WHERE 1=1 $exclude;");
		if ($rks > 0)
		{
			$sql = "SELECT count($param $queryfld) as pageview, $fld FROM $table_name WHERE 1=1 $exclude  GROUP BY $fld ORDER BY pageview DESC";
			if ($limit > 0)
			{
				$sql = $sql . " LIMIT $limit";
			}
			$qry = $wpdb->get_results($sql);
			$tdwidth = 560;
			if($type==1)
				$tdwidth = 240;
			$red = 131;
			$green = 180;
			$blue = 216;
			$deltacolor = round(250 / count($qry), 0);
			
			if($graph==1){
				$chdl="";
				$chd="t:";
				$pc_s = 0;
				$pc_count = 0;
			}
			
			foreach ($qry as $rk)
			{
				$pc = round(($rk->pageview * 100 / $rks), 1);
				if ($fld == 'date')
				{
					$rk->$fld = irihdate($rk->$fld);
				}
				if ($fld == 'urlrequested')
				{
					$rk->$fld = StatSurfer_Decode($rk->$fld);
				}
				
				if ($fld == 'search')
				{
                  	$rk->$fld = urldecode($rk->$fld);
				}
				
				if($graph==1){
					if($pc_count < 8){
						$chdl.=urlencode(my_substr($rk->$fld,0,50))."|";
						$chd.=($tdwidth*$pc/100).",";
						$pc_s = $pc_s + $pc;
						$pc_count++;
					}
				}
				
				print "<tr><td style='width:400px;overflow: hidden; white-space: nowrap; text-overflow: ellipsis;'>" . my_substr($rk->$fld, 0, 50);
				if (strlen("$rk->fld") >= 50)
				{
					print "...";
				}
				
				print "</td><td style='text-align:right;padding-right:40px;'>" . $rk->pageview . "</td>";
				echo "<td style='text-align:right'>$pc%</td>";
				print "<td><div style='text-align:right;padding:2px;font-family:helvetica;font-size:7pt;font-weight:bold;height:16px;width:" . number_format(($tdwidth * $pc / 100), 1, '.', '') . "px;background:" . irirgbhex($red, $green, $blue) . ";border-top:1px solid " . irirgbhex($red + 20, $green + 20, $blue) . ";border-right:1px solid " . irirgbhex($red + 30, $green + 30, $blue) . ";border-bottom:1px solid " . irirgbhex($red - 20, $green - 20, $blue) . ";'></div>";
				print "</td></tr>\n";
				$red = $red + $deltacolor;
				$blue = $blue - ($deltacolor / 2);
			}
		}
		
		// Show Graphs
		if($graph == 1){
			
			if(get_option(StatSurfer_showcharts)=='checked'){
				echo "<tr><td colspan='4'>";
				
				if($pc_s < 100){
					$chdl .= "Other|";
					$chd .= (100-$pc_s)."|";
				}
				
				$chdl=my_substr($chdl,0,strlen($chl)-1);
				$chd=my_substr($chd,0,strlen($chd)-1);
				
				print "<img src=http://chart.apis.google.com/chart?cht=p&chd=".($chd)."&chs=400x200&chl=".($chdl)."&chco=83b4d8,EDB874&chp=0>\n";
				
				echo "</td></tr>";
			}
			
		}
		
		print "</tbody></table>\n";
		
		print "</div>\n";
	}
	
	?>