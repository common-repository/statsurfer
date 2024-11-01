<?php

	/*	
		PlugIn: StatSurfer
		Page: pages/search.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	function StatSurferSearch($what = '')
	{
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		
		$f['urlrequested'] = __('URL Requested', 'StatSurfer');
		$f['agent'] = __('Agent', 'StatSurfer');
		$f['referrer'] = __('Referrer', 'StatSurfer');
		$f['search'] = __('Search terms', 'StatSurfer');
		$f['searchengine'] = __('Search engine', 'StatSurfer');
		$f['os'] = __('Operative system', 'StatSurfer');
		$f['browser'] = __('Browser', 'StatSurfer');
		$f['spider'] = __('Spider', 'StatSurfer');
		$f['ip'] = __('IP', 'StatSurfer');
	
		echo "<div class='wrap'><h2>" . _e('Search', 'StatSurfer') . "</h2>";
		echo "<form method=get><table>";

		for ($i = 1; $i <= 3; $i++)
		{
			print "<tr>";
			print "<td>" . __('Field', 'StatSurfer') . " <select name=where$i><option value=''></option>";
			foreach (array_keys($f) as $k)
			{
				print "<option value='$k'";
				if ($_GET["where$i"] == $k)
				{
					print " SELECTED ";
				}
				print ">" . $f[$k] . "</option>";
			}
			print "</select></td>";
			print "<td><input type=checkbox name=groupby$i value='checked' " . $_GET["groupby$i"] . "> " . __('Group by', 'StatSurfer') . "</td>";
			print "<td><input type=checkbox name=sortby$i value='checked' " . $_GET["sortby$i"] . "> " . __('Sort by', 'StatSurfer') . "</td>";
			print "<td>, " . __('if contains', 'StatSurfer') . " <input type=text name=what$i value='" . $_GET["what$i"] . "'></td>";
			print "</tr>";
		}
	
		echo "</table><br>";
		echo "<table><tr><td>";

		//SubTable
		echo "<table>";
		echo "<tr><td><input type=checkbox name=oderbycount value=checked" . $_GET['oderbycount'] . ">" . _e('sort by count if grouped', 'StatSurfer') . "</td></tr>";
		echo "<tr><td><input type=checkbox name=spider value=checked " . $_GET['spider'] . ">" . _e('include spiders/crawlers/bot', 'StatSurfer') . "</td></tr>";
		echo "<tr><td><input type=checkbox name=feed value=checked " . $_GET['feed'] . ">" . _e('include feed', 'StatSurfer') . "</td></tr>";
		echo "<tr><td><input type=checkbox name=distinct value=checked " . $_GET['distinct'] . ">" ._e('SELECT DISTINCT', 'StatSurfer') . "</td></tr>";
		echo "</table>";

		echo "</td><td width=15></td><td>";
		
		echo "<table>";
		echo "<tr><td>" . _e('Limit results to', 'StatSurfer') . "<select name=limitquery>";
		if ($_GET['limitquery'] > 0)
		{
			print "<option>" . $_GET['limitquery'] . "</option>";
		}
		echo "<option>1</option><option>5</option><option>10</option><option>20</option><option>50</option><option>100</option><option>250</option><option>500</option>";
		echo "</select></td></tr>";
		
		echo "<tr><td>&nbsp;</td></tr>";
		
		echo "<tr><td align=right><input type=submit value=" . _e('Search', 'StatSurfer') . " name=searchsubmit></td></tr>";
		echo "</table>";

		echo "</td></tr></table>"; 
		echo "<input type=hidden name=page value='statsurfer/statsurfer.php'><input type=hidden name=StatSurfer_action value=search>";
		echo "</form><br>";
		//fine form
		

		if (isset($_GET['searchsubmit']))
		{
			// query builder
			$qry = "";
			// FIELDS
			$fields = "";
			for ($i = 1; $i <= 3; $i++)
			{
				if ($_GET["where$i"] != '')
				{
					$fields .= $_GET["where$i"] . ",";
				}
			}
			$fields = rtrim($fields, ",");
			// WHERE
			$where = "WHERE 1=1";
			if ($_GET['spider'] != 'checked')
			{
				$where .= " AND spider=''";
			}
			if ($_GET['feed'] != 'checked')
			{
				$where .= " AND feed=''";
			}
			for ($i = 1; $i <= 3; $i++)
			{
				if (($_GET["what$i"] != '') && ($_GET["where$i"] != ''))
				{
					$where .= " AND " . $_GET["where$i"] . " LIKE '%" . mysql_real_escape_string($_GET["what$i"]) . "%'";
				}
			}
			// ORDER BY
			$orderby = "";
			for ($i = 1; $i <= 3; $i++)
			{
				if (($_GET["sortby$i"] == 'checked') && ($_GET["where$i"] != ''))
				{
					$orderby .= $_GET["where$i"] . ',';
				}
			}
		
			// GROUP BY
			$groupby = "";
			for ($i = 1; $i <= 3; $i++)
			{
				if (($_GET["groupby$i"] == 'checked') && ($_GET["where$i"] != ''))
				{
					$groupby .= $_GET["where$i"] . ',';
				}
			}
			if ($groupby != '')
			{
				$groupby = "GROUP BY " . rtrim($groupby, ',');
				$fields .= ",count(*) as totale";
				if ($_GET['oderbycount'] == 'checked')
				{
					$orderby = "totale DESC," . $orderby;
				}
			}
		
			if ($orderby != '')
			{
				$orderby = "ORDER BY " . rtrim($orderby, ',');
			}
		
		
			$limit = "LIMIT " . $_GET['limitquery'];
		
			if ($_GET['distinct'] == 'checked')
			{
				$fields = " DISTINCT " . $fields;
			}
		
			// Results
			print "<h2>" . __('Results', 'StatSurfer') . "</h2>";
			$sql = "SELECT $fields FROM $table_name $where $groupby $orderby $limit;";
			//  print "$sql<br>";
			print "<table class='widefat'><thead><tr>";
			for ($i = 1; $i <= 3; $i++)
			{
				if ($_GET["where$i"] != '')
				{
					print "<th scope='col'>" . ucfirst($_GET["where$i"]) . "</th>";
				}
			}
			if ($groupby != '')
			{
				print "<th scope='col'>" . __('Count', 'StatSurfer') . "</th>";
			}
			print "</tr></thead><tbody id='the-list'>";
			$qry = $wpdb->get_results($sql, ARRAY_N);
			foreach ($qry as $rk)
			{
				print "<tr>";
				for ($i = 1; $i <= 3; $i++)
				{
					print "<td>";
					if ($_GET["where$i"] == 'urlrequested')
					{
						print StatSurfer_Decode($rk[$i - 1]);
					}
					else
					{
						print $rk[$i - 1];
					}
					print "</td>";
				}
				print "</tr>";
			}
			print "</table>";
			print "<br /><br /><font size=1 color=gray>sql: $sql</font></div>";
		}
	}
	
	?>