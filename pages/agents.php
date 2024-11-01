<?php

	/*	
		PlugIn: StatSurfer
		Page: pages/agents.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	function StatSurferAgents()
	{
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		$query = "SELECT date, MAX(time), ip, COUNT(*) as count, agent";
		$query .= " FROM " . $table_name;
		$query .= " WHERE spider = '' AND browser = ''";
		$query .= " GROUP BY date, ip, agent";
		$query .= " ORDER BY date DESC";
		$result = $wpdb->get_results($query);
		
		print "<div class='wrap'><h2>" . __('Unknown User Agents', 'StatSurfer') . "</h2>";
		print "<table class='widefat'><thead><tr>";
		print "<th scope='col'>" . __('Date', 'StatSurfer') . "</th>";
		print "<th scope='col'>" . __('Last Time', 'StatSurfer') . "</th>";
		print "<th scope='col'>" . __('IP', 'StatSurfer') . "</th>";
		print "<th scope='col'>" . __('Count', 'StatSurfer') . "</th>";
		print "<th scope='col'>" . __('User Agent', 'StatSurfer') . "</th>";
		print "</tr></thead><tbody id='the-list'>";
		
		foreach ($result as $line)
		{   
            $col = 0;
            print '<tr>';
            foreach ($line as $col_value){
				$col++;
				if ($col == 1)
					print '<td>' . irihdate($col_value) . '</td>';
				else if ($col == 3)
					print "<td><a href='http://www.projecthoneypot.org/ip_" . $col_value . "' target='_blank'>" . $col_value . "</a></td>";
				else
					print '<td>' . $col_value . '</td>';
			}
            print '</tr>';
		}
		print '</table></div>';
	}
	
	?>