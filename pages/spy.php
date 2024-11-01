<?php

	/*	
		PlugIn: StatSurfer
		Page: pages/spy.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	function StatSurferSpy()
	{
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		
		$LIMIT = 20;
		
		if(isset($_GET['pn']))
		{
          	// Get Current page from URL
          	$page = $_GET['pn'];
          	if($page <= 0)
          	{
          		// Page is less than 0 then set it to 1
          		$page = 1;
          	}
		}
		else
		{
          	// URL does not show the page set it to 1
          	$page = 1;
		}
		
		// Create MySQL Query String
		$strqry = "SELECT id FROM $table_name WHERE (spider='' AND feed='') GROUP BY ip";
		$query = $wpdb->get_results($strqry);
		$TOTALROWS = $wpdb->num_rows;
		$NumOfPages = $TOTALROWS / $LIMIT;
		$LimitValue = ($page * $LIMIT) - $LIMIT;
		
		
		// Spy
		$today = gmdate('Ymd', current_time('timestamp'));
		$yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
		print "<div class='wrap'><h2>" . __('Spy', 'StatSurfer') . "</h2>";
		$sql = "SELECT ip,nation,os,browser,agent FROM $table_name WHERE (spider='' AND feed='') GROUP BY ip ORDER BY id DESC LIMIT $LimitValue, $LIMIT";
		$qry = $wpdb->get_results($sql);
	
		//JAVASCRIPT
		?><script>
			function ttogle(thediv){
				if (document.getElementById(thediv).style.display=="inline") {
					document.getElementById(thediv).style.display="none"
				} else {document.getElementById(thediv).style.display="inline"}
			}
		</script><?php
		//JAVASCRIPT END
		
		echo "<div align='center'>";
		echo "<table class='widefat'><thead><tr><th scope='col' colspan='2'>";
		echo "<div id='paginating' align='center'>Pages:";

	
		// Check to make sure we’re not on page 1 or Total number of pages is not 1
		if($page == ceil($NumOfPages) && $page != 1) {
			for($i = 1; $i <= ceil($NumOfPages)-1; $i++) {
				// Loop through the number of total pages
				if($i > 0) {
					// if $i greater than 0 display it as a hyperlink
					echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?page=statsurfer/statsurfer.php&StatSurfer_action=spy&pn=' . $i . '">' . $i . '</a> ';
				}
			}
		}
		if($page == ceil($NumOfPages) ) {
			$startPage = $page;
		} else {
			$startPage = 1;
		}
		for ($i = $startPage; $i <= $page+19; $i++) {
			// Display first 7 pages
			if ($i <= ceil($NumOfPages)) {
				// $page is not the last page
				if($i == $page) {
					// $page is current page
					echo " [{$i}] ";
				} else {
					// Not the current page Hyperlink them
					echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?page=statsurfer/statsurfer.php&StatSurfer_action=spy&pn=' . $i . '">' . $i . '</a> ';
				}
			}
		}
	
		echo "</div></th></tr></thead><tbody id='the-list'>";

		foreach ($qry as $rk)
		{
			echo "<tr>";
			echo "<td valign='top' width='300' style='padding:10px;padding-left:15px;'>";
		
			print "<IMG SRC='http://api.hostip.info/flag.php?ip=" . $rk->ip . "' border=0 width=18 height=12>";
			print " <strong><span><font size='2' color='#7b7b7b'>" . $rk->ip . "</font></span></strong> ";
		
			print "<br />";
			print "<div id='" . $rk->ip . "' name='" . $rk->ip . "'>" . $rk->os . ", " . $rk->browser;
			print "<br><iframe style='overflow:hide;border:0px;width:100%;height:50px;font-family:helvetica;paddng:0;' scrolling='no' marginwidth=0 marginheight=0 src=http://api.hostip.info/get_html.php?ip=" . $rk->ip . "></iframe>";
			if ($rk->nation){
				print "<br><small>" . gethostbyaddr($rk->ip) . "</small>";
			}
			print "<br><small>" . $rk->agent . "</small>";
			print "</div>";
		
			echo "</td>";
			echo "<td valign='top' style='padding:10px;'>";
		
		
			echo "<table width='100%' cellspacing='0' cellpadding='0' border='0'>";
			$qry2 = $wpdb->get_results("SELECT * FROM $table_name WHERE ip='" . $rk->ip . "' AND (date BETWEEN '$yesterday' AND '$today') order by id LIMIT 10");
			foreach ($qry2 as $details)
			{
				print "<tr>";
				print "<td style='border:0px;' valign='top' width='151'><div><font size='1' color='#3B3B3B'><strong>" . irihdate($details->date) . " " . $details->time . "</strong></font></div></td>";
				print "<td style='border:0px;><div><a href='" . irigetblogurl() . ((strpos($details->urlrequested, 'index.php') === FALSE) ? $details->urlrequested : '') . "' target='_blank'>" . StatSurfer_Decode($details->urlrequested) . "</a>";
				if ($details->searchengine != '')
				{
					print "<br><small>" . __('arrived from', 'StatSurfer') . " <b>" . $details->searchengine . "</b> " . __('searching', 'StatSurfer') . " <a href='" . $details->referrer . "' target=_blank>" . urldecode($details->search) . "</a></small>";
				}
				elseif ($details->referrer != '' && strpos($details->referrer, get_option('home')) === false)
				{
				print "<br><small>" . __('arrived from', 'StatSurfer') . " <a href='" . $details->referrer . "' target=_blank>" . $details->referrer . "</a></small>";
				}
				print "</div></td>";
				print "</tr>";
				echo "<tr height='3'><td colspan='2' style='border:0px;'></td></tr>";
			}
			echo "</table>";
		
		
			echo "</td>";
			echo "</tr>";
		}

		echo "</tbody></table></div>";

	}
	
	?>