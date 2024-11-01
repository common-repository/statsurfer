<?php

	/*	
		PlugIn: StatSurfer
		Page: pages/options.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	function StatSurferOptions()
	{
		if(!isset($_GET['other_page'])){
			
		if ($_POST['saveit'] == 'yes')
		{
			update_option('StatSurfer_show_dashoboardmap', $_POST['StatSurfer_show_dashoboardmap']);
			update_option('StatSurfer_dashboard_mapGrad', $_POST['StatSurfer_dashboard_mapGrad']);
			update_option('StatSurfer_dashboard_typemap', $_POST['StatSurfer_dashboard_typemap']);
			update_option('StatSurfer_showcharts', $_POST['StatSurfer_showcharts']);
			update_option('StatSurfer_collectloggeduser', $_POST['StatSurfer_collectloggeduser']);
			update_option('StatSurfer_autodelete', $_POST['StatSurfer_autodelete']);
			update_option('StatSurfer_daysinoverviewgraph', $_POST['StatSurfer_daysinoverviewgraph']);
			update_option('StatSurfer_mincap', $_POST['StatSurfer_mincap']);
			update_option('StatSurfer_donotcollectspider', $_POST['StatSurfer_donotcollectspider']);
			update_option('StatSurfer_autodelete_spider', $_POST['StatSurfer_autodelete_spider']);
			
			//Map
			update_option('StatSurfer_showmap_world', $_POST['StatSurfer_showmap_world']);
			update_option('StatSurfer_showmap_europe', $_POST['StatSurfer_showmap_europe']);
			update_option('StatSurfer_showmap_northamerica', $_POST['StatSurfer_showmap_northamerica']);
			update_option('StatSurfer_showmap_southamerica', $_POST['StatSurfer_showmap_southamerica']);
			update_option('StatSurfer_showmap_asia', $_POST['StatSurfer_showmap_asia']);
			update_option('StatSurfer_showmap_africa', $_POST['StatSurfer_showmap_africa']);
			update_option('StatSurfer_showmap_oceania', $_POST['StatSurfer_showmap_oceania']);
			
			//Database Table
			update_option('statsurfer_statpress_check', $_POST['statsurfer_statpress_check']);
			if(get_option('statsurfer_statpress_check')=='checked')
				update_option('statsurfer_statpress','statpress');
			else
				update_option('statsurfer_statpress','statsurfer');
			
			// update database too
			//StatSurfer_CreateTable();
			print "<br /><div class='updated'><p>" . __('Saved', 'StatSurfer') . "!</p></div>";
		}
		else
		{
			
			global $_STATSURFER;
			// echo $_STATSURFER['table_name'];
	
			?>
			<div class='wrap'><h2><?php _e('Options', 'StatSurfer'); ?></h2>
			<br /><table class='widefat'><thead><tr colspan='2'><th scope='col'>General settings</th></tr></thead><tbody id='the-list'><tr><td>
			<form method=post><table width=100%>
			<?php
	
			// dashboard
			echo "<tr><td style='border:0px;height:20px;' colspan='2'><b>Dashboard's Widget Settings</b></td></tr>";
			print "<tr><td style='height:20px;border:0px;' colspan='2'><input type=checkbox name='StatSurfer_show_dashoboardmap' value='checked' " . get_option('StatSurfer_show_dashoboardmap') . "> Show connection map in the dashboard</td></tr>";
			?>
			<tr><td style='height:20px;' colspan='2'>Select a map 
			<select name="StatSurfer_dashboard_typemap" <?php if(get_option('StatSurfer_show_dashoboardmap') != 'checked') print "disabled"; ?>>
			<option value="-60,-170,80,-170" <?php if(get_option('StatSurfer_dashboard_typemap') == '-60,-170,80,-170') print "selected"; ?>>World</option>
			<option value="30,-31,71,89" <?php if(get_option('StatSurfer_dashboard_typemap') == '30,-31,71,89') print "selected"; ?>>Europe</option>
			<option value="10,-171,71,-21" <?php if(get_option('StatSurfer_dashboard_typemap') == '10,-171,71,-21') print "selected"; ?>>North America</option>
			<option value="-61,-171,10,-21" <?php if(get_option('StatSurfer_dashboard_typemap') == '-61,-171,10,-21') print "selected"; ?>>South America</option>
			<option value="0,30,71,-170" <?php if(get_option('StatSurfer_dashboard_typemap') == '0,30,71,-170') print "selected"; ?>>Asia</option>
			<option value="-35,-45,37,90" <?php if(get_option('StatSurfer_dashboard_typemap') == '-35,-45,37,90') print "selected"; ?>>Africa</option>
			<option value="-50,85,5,-175" <?php if(get_option('StatSurfer_dashboard_typemap') == '-50,85,5,-175') print "selected"; ?>>Oceania</option>
			</select>
			<?php
			echo"&nbsp;&nbsp;&nbsp;<input type=checkbox name='StatSurfer_dashboard_mapGrad' ";
			if(get_option('StatSurfer_show_dashoboardmap') != 'checked') 
				print "disabled";
			echo " value='checked'" . get_option('StatSurfer_dashboard_mapGrad') . "> Graduate Map<div style='border:0px;margin:0px;padding:0px;width:50%;height:7px;'></div></td></tr>";
				
			//general
			echo "<tr><td style='border:0px;height:20px;' colspan='2'><div style='border:0px;margin:0px;padding:0px;width:50%;height:7px;'></div><b>General Settings</b></td></tr>";
			print "<tr><td style='border:0px;height:20px;' colspan='2'><input type=checkbox name='statsurfer_statpress_check' value='checked' " . get_option('statsurfer_statpress_check') . "> Use StatPress's tables</td></tr>";
			print "<tr><td style='border:0px;height:20px;' colspan='2'><input type=checkbox name='StatSurfer_collectloggeduser' value='checked' " . get_option('StatSurfer_collectloggeduser') . "> " . __('Collect data about logged users, too.', 'StatSurfer') . "</td></tr>";
			print "<tr><td style='border:0px;height:20px;' colspan='2'><input type=checkbox name='StatSurfer_donotcollectspider' value='checked' " . get_option('StatSurfer_donotcollectspider') . "> " . __('Do not collect spiders visits', 'StatSurfer') . "</td></tr>";
	
			?>
			<tr><td style='border:0px;height:20px;' colspan='2'><?php _e('Automatically delete visits older than', 'StatSurfer'); ?>
			<select name="StatSurfer_autodelete">
			<option value="" <?php if (get_option('StatSurfer_autodelete') == '') print "selected"; ?>><?php _e('Never delete!', 'StatSurfer'); ?></option>
			<option value="1 month" <?php if (get_option('StatSurfer_autodelete') == "1 month") print "selected"; ?>>1 <?php _e('month', 'StatSurfer'); ?></option>
			<option value="3 months" <?php if (get_option('StatSurfer_autodelete') == "3 months") print "selected"; ?>>3 <?php _e('months', 'StatSurfer'); ?></option>
			<option value="6 months" <?php if (get_option('StatSurfer_autodelete') == "6 months") print "selected"; ?>>6 <?php _e('months', 'StatSurfer'); ?></option>
			<option value="1 year" <?php if (get_option('StatSurfer_autodelete') == "1 year") print "selected"; ?>>1 <?php _e('year', 'StatSurfer'); ?></option>
			</select></td></tr>

			<tr><td style='border:0px;height:20px;' colspan='2'><?php _e('Automatically delete spider visits older than','StatSurfer'); ?>
			<select name="StatSurfer_autodelete_spider">
			<option value="" <?php if(get_option('StatSurfer_autodelete_spider') =='' ) print "selected"; ?>><?php _e('Never delete!','StatSurfer'); ?></option>
			<option value="1 day" <?php if(get_option('StatSurfer_autodelete_spider') == "1 day") print "selected"; ?>>1 <?php _e('day','StatSurfer'); ?></option>
			<option value="1 week" <?php if(get_option('StatSurfer_autodelete_spider') == "1 week") print "selected"; ?>>1 <?php _e('week','StatSurfer'); ?></option>
			<option value="1 month" <?php if(get_option('StatSurfer_autodelete_spider') == "1 month") print "selected"; ?>>1 <?php _e('month','StatSurfer'); ?></option>
			<option value="1 year" <?php if(get_option('StatSurfer_autodelete_spider') == "1 year") print "selected"; ?>>1 <?php _e('year','StatSurfer'); ?></option>
			</select></td></tr>

			<tr><td style='height:20px;' colspan='2'><?php _e('Days in Overview graph', 'StatSurfer'); ?>
			<select name="StatSurfer_daysinoverviewgraph">
			<option value="7" <?php if (get_option('StatSurfer_daysinoverviewgraph') == 7) print "selected"; ?>>7</option>
			<option value="10" <?php if (get_option('StatSurfer_daysinoverviewgraph') == 10) print "selected"; ?>>10</option>
			<option value="20" <?php if (get_option('StatSurfer_daysinoverviewgraph') == 20) print "selected"; ?>>20</option>
			<option value="30" <?php if (get_option('StatSurfer_daysinoverviewgraph') == 30) print "selected"; ?>>30</option>
			<option value="50" <?php if (get_option('StatSurfer_daysinoverviewgraph') == 50) print "selected"; ?>>50</option>
			</select><div style='border:0px;margin:0px;padding:0px;width:50%;height:7px;'></div></td></tr>
			<?php
				
			//Details
			echo "<tr><td style='border:0px;height:20px;' colspan='2'><div style='border:0px;margin:0px;padding:0px;width:50%;height:7px;'></div><b>Details' Page Settings</b></td></tr>";
			print "<tr><td style='height:20px;' colspan='2'><input type=checkbox name='StatSurfer_showcharts' value='checked' " . get_option('StatSurfer_showcharts') . "> Show charts under details' tables";
			echo "<div style='border:0px;margin:0px;padding:0px;width:50%;height:7px;'></div></td></tr>";
				
			
			//Map
			echo "<tr><td  colspan='2' style='border:0px;height:20px;'><div style='border:0px;margin:0px;padding:0px;width:50%;height:7px;'></div><b>Map's Page Settings</b></td></tr>";
			print "<tr><td style='height:20px;border:0px;' width='200'><input type=checkbox name='StatSurfer_showmap_world' value='checked' " . get_option('StatSurfer_showmap_world') . "> World</td>";
			print "<td style='height:20px;border:0px;'><input type=checkbox name='StatSurfer_showmap_asia' value='checked' " . get_option('StatSurfer_showmap_asia') . "> Asia</td></tr>";
			print "<tr><td style='height:20px;border:0px;'><input type=checkbox name='StatSurfer_showmap_europe' value='checked' " . get_option('StatSurfer_showmap_europe') . "> Europe</td>";
			print "<td style='height:20px;border:0px;'><input type=checkbox name='StatSurfer_showmap_africa' value='checked' " . get_option('StatSurfer_showmap_africa') . "> Africa</td></tr>";
			print "<tr><td style='height:20px;border:0px;'><input type=checkbox name='StatSurfer_showmap_northamerica' value='checked' " . get_option('StatSurfer_showmap_northamerica') . "> North America</td>";
			print "<td style='height:20px;border:0px;'><input type=checkbox name='StatSurfer_showmap_oceania' value='checked' " . get_option('StatSurfer_showmap_oceania') . "> Oceania</td></tr>";
			print "<tr><td colspan='2' style='height:20px;border:0px;'><input type=checkbox name='StatSurfer_showmap_southamerica' value='checked' " . get_option('StatSurfer_showmap_southamerica') . "> South America";
			echo "<div style='border:0px;margin:0px;padding:0px;width:50%;height:7px;'></div></td></tr>";
				
			?>
			<tr><td style='border:0px;height:20px;' colspan='2'><input type=submit value="<?php _e('Save options', 'StatSurfer'); ?>"></td></tr>
	
			</tr></table>
			<input type=hidden name=saveit value=yes>
			<input type=hidden name=page value=StatSurfer><input type=hidden name=StatSurfer_action value=options>
			</form>

			</td></tr></tbody></table>

			<?php  
			//export
			?>
			<br /><table class='widefat'><thead><tr><th scope='col'>Export stats (CSV file)</th></tr></thead><tbody id='the-list'><tr><td>

			<form method=get><table>
			<tr><td style='border:0px;height:20px;'><?php _e('From', 'StatSurfer'); ?></td><td style='border:0px;height:20px;'><input type='text' name='from'> (YYYYMMDD)</td></tr>
			<tr><td style='border:0px;height:20px;'><?php _e('To', 'StatSurfer'); ?></td><td style='border:0px;height:20px;'><input type='text' name='to'> (YYYYMMDD)</td></tr>
			<tr><td style='border:0px;height:20px;'><?php _e('Fields delimiter', 'StatSurfer'); ?></td><td style='border:0px;height:20px;'><select name='del'><option>,</option><option>;</option><option>|</option></select></tr>
			<tr><td style='border:0px;height:20px;'></td><td style='border:0px;height:20px;'><input type='submit' value=<?php _e('Export', 'StatSurfer'); ?>></td></tr>
			<input type='hidden' name='page' value='StatSurfer'><input type='hidden' name='StatSurfer_action' value='exportnow'>
			</table></form>

			</td></tr></tbody></table>
			</div>
			<?php
		}
		
		//other_page
		}else{
			
			if( $_GET['other_page'] == 'iframe_main_graph' )
				StatSurfer_mainGraphs();
			
		}
				
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurferExport()
	{
		echo "<div class='wrap'><h2>" . _e('Export stats to text file', 'StatSurfer') . " (csv)</h2>";
		echo "<form method=get><table>";
		echo "<tr><td>" . _e('From', 'StatSurfer') . "</td><td><input type=text name=from> (YYYYMMDD)</td></tr>";
		echo "<tr><td>" . _e('To', 'StatSurfer') . "</td><td><input type=text name=to> (YYYYMMDD)</td></tr>";
		echo "<tr><td>" . _e('Fields delimiter', 'StatSurfer') . "</td><td><select name=del><option>,</option><option>;</option><option>|</option></select></tr>";
		echo "<tr><td></td><td><input type=submit value=" . _e('Export', 'StatSurfer') . "></td></tr>";
		echo "<input type=hidden name=page value=StatSurfer><input type=hidden name=StatSurfer_action value=exportnow>";
		echo "</table></form>";
		echo "</div>";
	}
	
	
	function StatSurferExportNow()
	{
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		$filename = get_bloginfo('title') . "-StatSurfer_" . $_GET['from'] . "-" . $_GET['to'] . ".csv";
		header('Content-Description: File Transfer');
		header("Content-Disposition: attachment; filename=$filename");
		header('Content-Type: text/plain charset=' . get_option('blog_charset'), true);
		$qry = $wpdb->get_results("SELECT * FROM $table_name WHERE date>='" . (date("Ymd", strtotime(my_substr($_GET['from'], 0, 8)))) . "' AND date<='" . (date("Ymd", strtotime(my_substr($_GET['to'], 0, 8)))) . "';");
		$del = my_substr($_GET['del'], 0, 1);
		print "date" . $del . "time" . $del . "ip" . $del . "urlrequested" . $del . "agent" . $del . "referrer" . $del . "search" . $del . "nation" . $del . "os" . $del . "browser" . $del . "searchengine" . $del . "spider" . $del . "feed\n";
		foreach ($qry as $rk){
			print '"' . $rk->date . '"' . $del . '"' . $rk->time . '"' . $del . '"' . $rk->ip . '"' . $del . '"' . $rk->urlrequested . '"' . $del . '"' . $rk->agent . '"' . $del . '"' . $rk->referrer . '"' . $del . '"' . urldecode($rk->search) . '"' . $del . '"' . $rk->nation . '"' . $del . '"' . $rk->os . '"' . $del . '"' . $rk->browser . '"' . $del . '"' . $rk->searchengine . '"' . $del . '"' . $rk->spider . '"' . $del . '"' . $rk->feed . '"' . "\n";
		}
		die();
	}
	
	?>