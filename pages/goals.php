<?php

	/*	
		PlugIn: StatSurfer
		Page: pages/goals.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	function StatSurferGoals()
	{
		
		StatSurferCheckGoals();
		
		print "<div class='wrap'><h2>Goals</h2>";
		print "<table class='widefat'><thead><tr>";
		print "<th scope='col'>Set a new goal</th>";
		print "</tr></thead><tbody id='the-list'>";
		print "<tr><td>";
		
		//Form
		echo "
			<form method='GET'>
			<input type=hidden name=page value='statsurfer/statsurfer.php'>
			<input type=hidden name=StatSurfer_action value=add_goal>
			<table width='100%' cellspacing='0' cellpadding='0' border='0'>
			<tr><td width='70' style='text-align:right;font-weight:bold;'></td>
			<td colspan='3' width='360'>
			Goal name &nbsp;<input type='text' name='name' size='53' />
			</td><td width='150'>
			Goal value <input type='text' name='goal' size='10' />
			</td><td rowspan='6' style='border:0px;padding-top:10px;'></td><td rowspan='6' style='border:0px;padding-top:10px;' width='35'>
			<img src='../wp-content/plugins/StatSurfer/images/info.png' width='35' border='0' />
			</td><td rowspan='6' style='border:0px;padding-top:10px;' width='400'>
			<div style='color:#666666;'>
			INSTRUCTION:<br />
			- Set the name of the new goal<br />
			- Set the value of the goal<br />
			- Choose the connections types you want to count<br />
			- Choose the type of goal: with the first type you can see if in a certain period of time, you have defined, you have reached a certain number of connections, with the second one, you can set daily goals, the system will tell you the day in which you have reached this goal<br />
			</div>
			</td></tr><tr bgcolor='red'>
			<td style='text-align:right;font-weight:bold;'>Coon. type</td>
			<td width='120'>
			<input type='checkbox' name='check_visitor' value='I' /> Visitors
			</td><td width='120'>
			<input type='checkbox' name='check_page' value='I' /> Pageviews
			</td><td width='120'>
			<input type='checkbox' name='check_spider' value='I' /> Spieders
			</td><td>
			<input type='checkbox' name='check_feed' value='I' /> Feeds
			</td></tr><tr>
			<td style='text-align:right;font-weight:bold;' rowspan='3'>Goal type</td>
			<td colspan='2' style='border:0px;'>
			<input type='radio' name='goal_type' value='type_1' checked /> Type 1
			</td><td colspan='2' style='border:0px;'>
			<input type='radio' name='goal_type' value='type_2' /> Type 2
			</td></tr><tr><td colspan='2' style='border:0px;'>
			<input type='text' name='st_date_Y' size='3' value='yyyy' />&nbsp;<input type='text' name='st_date_m' size='1' value='mm' />&nbsp;<input type='text' name='st_date_d' size='1' value='dd' /> Start Date
			</td><td colspan='2' style='border:0px;'>
			</td></tr><tr><td colspan='2'>
			<input type='text' name='ed_date_Y' size='3' value='yyyy' />&nbsp;<input type='text' name='ed_date_m' size='1' value='mm' />&nbsp;<input type='text' name='ed_date_d' size='1' value='dd' /> End Date
			</td><td colspan='2'>
			</td></tr><tr>
			<td style='border:0px;text-align:right;font-weight:bold;'></td>
			<td style='border:0px;'>
			<input type='submit' value='Insert goal' />
			</td><td colspan='3' style='border:0px;'></td></tr>
			</table>
			</form>
			";

	
	print "</td></tr></tbody></table><br />";
	
	global $wpdb; global $_STATSURFER;
	$table_name = $wpdb->prefix . "StatSurfer_goals";
	
	echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td width='50%' valign='top' style='padding-right:5px;'>";
	
	echo "<h3 style='font:italic 17px Georgia,Times New Roman,Bitstream Charter,Times,serif;padding:0px 0px 0px 0px;margin:10px 0px 7px 0px;'>Open Goals</h3>";
	
	$qry = $wpdb->get_results("SELECT* FROM ".$table_name." WHERE result_g='Y' ORDER BY id_g DESC");
	foreach ($qry as $rk){
		
		print "<table class='widefat' style='margin-bottom:7px;'><thead><tr>";
		print "<th width='12'><img src='../wp-content/plugins/StatSurfer/images/ic_open.png' width='12' border='0' /></th>";
		print "<th>".$rk->name_g."</th>";
		print "<th width='14'><a href='admin.php?page=statsurfer/statsurfer.php&StatSurfer_action=del_goal&gid=".$rk->id_g."'><img src='../wp-content/plugins/StatSurfer/images/del.png' width='14' border='0' /></a></th>";
		print "</tr></thead><tbody id='the-list'>";
		print "<tr><td colspan='3'>";
		
		if($rk->start_date_g=="0000-00-00"){
			$type = "Goals type: 2";
			$time = " in one day";
		}
		else{
			$type = "Goals type: 1";
			$time = " from <b>" . $rk->start_date_g . "</b> to <b>" . $rk->end_date_g . "</b>";
		}
		
		print $type . "<br />";
		
		print "<b>" . $rk->goal_g . "</b> ";
		
		$conn_type = $rk->conn_type_g;
		if($conn_type=="IIII")
			echo " total visits";
		else{
			if($conn_type[0]=="I")
				echo " visitors,";
			if($conn_type[1]=="I")
				echo " pageviews,";
			if($conn_type[2]=="I")
				echo " spiders,";
			if($conn_type[3]=="I")
				echo " feeds,";
		}
		
		echo $time . "<br />";
		
		StatSurferPartialGoal($rk->id_g);
		
		print "</td></tr></tbody></table>";
	}
	
	echo "</td><td width='50%' valign='top' style='padding-left:5px;'>";
	
	echo "<h3 style='font:italic 17px Georgia,Times New Roman,Bitstream Charter,Times,serif;padding:0px 0px 0px 0px;margin:10px 0px 7px 0px;'>Last Closed Goals</h3>";
	
	$qry = $wpdb->get_results("SELECT* FROM ".$table_name." WHERE result_g<>'Y' ORDER BY end_date_g DESC LIMIT 0,5");
	foreach ($qry as $rk){
		
		print "<table class='widefat' style='margin-bottom:7px;'><thead><tr>";
		print "<th width='12'><img src='../wp-content/plugins/statsurfer/images/";
		if($rk->result_g=="G")
			echo "ic_reached.png";
		if($rk->result_g=="R")
			echo "ic_faild.png";
		print "' width='12' border='0' /></th>";
		print "<th>".$rk->name_g."</th>";
		print "<th width='14'><a href='admin.php?page=statsurfer/statsurfer.php&StatSurfer_action=del_goal&gid=".$rk->id_g."'><img src='../wp-content/plugins/StatSurfer/images/del.png' width='14' border='0' /></a></th>";
		print "</tr></thead><tbody id='the-list'>";
		print "<tr><td colspan='3'>";
		
		if($rk->start_date_g=="0000-00-00"){
			$type = "Goals type: 2";
			$time = " in one day";
		}
		else{
			$type = "Goals type: 1";
			$time = " from <b>" . $rk->start_date_g . "</b> to <b>" . $rk->end_date_g . "</b>";
		}
		
		print $type . "<br />";
		
		print "<b>" . $rk->goal_g . "</b> ";
		
		$conn_type = $rk->conn_type_g;
		if($conn_type=="IIII")
			echo " total visits";
		else{
			if($conn_type[0]=="I")
				echo " visitors,";
			if($conn_type[1]=="I")
				echo " pageviews,";
			if($conn_type[2]=="I")
				echo " spiders,";
			if($conn_type[3]=="I")
				echo " feeds,";
		}
		
		echo $time . "<br />";
		
		StatSurferPartialGoal($rk->id_g);
		
		print "</td></tr></tbody></table>";
	}
	
	echo "<a href='admin.php?page=statsurfer/statsurfer.php&StatSurfer_action=show_goal' style='text-decoration:none;'>See all goals</a>";
	
	echo "</td></tr></table>";
	
	
	echo "</div>";
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurferPartialGoal($id_g){
		
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . "StatSurfer_goals";
		$table_StatSurfer = $wpdb->prefix . $_STATSURFER['table_name'];
		
		$qry = $wpdb->get_results("SELECT* FROM ".$table_name." WHERE id_g='".$id_g."'");
		foreach ($qry as $rk){
			
			if($rk->result_g=='Y'){
				
				$pc = ($rk->partial_g*100)/$rk->goal_g; 
				echo "Your partial result is " .  $rk->partial_g . " (" . round($pc,2) . "%)";
				
				if($rk->start_date_g!='0000-00-00'){
					if($rk->partial_g>$rk->goal_g)
						echo " <b>You have already reached this goal</b>";
				}
			}
			
			else{
				
				$pc = ($rk->partial_g*100)/$rk->goal_g; 
				echo "Result: " . $rk->partial_g . " (" . round($pc,2) . "%)";
				
				if($rk->result_g=='G')
					echo " <b>You have reached this goal</b>";
				if($rk->result_g=='R')
					echo " <b>You have faild this goal</b>";
				
				if($rk->start_date_g=='0000-00-00')
					echo " <b>at " . $rk->end_date_g . "</b>";
				
			}
			
		}
		
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurferShowGoals(){
		
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . "StatSurfer_goals";
		$table_StatSurfer = $wpdb->prefix . $_STATSURFER['table_name'];
		
		print "<div class='wrap'><h2>All goals</h2>";
		
		echo "<a href='admin.php?page=statsurfer/statsurfer.php&StatSurfer_action=goals' style='text-decoration:none;'>Return to the goals page</a>";
		
		echo "<table width='100%' cellpadding='0' cellspacing='0' border='0' style='margin-top:8px;'>";
		
		$i=0;
		$qry = $wpdb->get_results("SELECT* FROM ".$table_name." ORDER BY id_g DESC");
		foreach ($qry as $rk){
			
			if($i==0)
				echo "<tr>";
			
			echo "<td width='33%' style='padding-right:10px;'>";
			
			print "<table class='widefat' style='margin-bottom:10px;'><thead><tr>";
			print "<th width='12'><img src='../wp-content/plugins/statsurfer/images/";
			if($rk->result_g=="G")
				echo "ic_reached.png";
			if($rk->result_g=="R")
				echo "ic_faild.png";
			if($rk->result_g=="Y")
				echo "ic_open.png";
			print "' width='12' border='0' /></th>";
			print "<th>".$rk->name_g."</th>";
			print "<th width='14'><a href='admin.php?page=statsurfer/statsurfer.php&StatSurfer_action=del_goal&gid=".$rk->id_g."'><img src='../wp-content/plugins/statsurfer/images/del.png' width='14' border='0' /></a></th>";
			print "</tr></thead><tbody id='the-list'>";
			print "<tr><td colspan='3'>";
			
			if($rk->start_date_g=="0000-00-00"){
				$type = "Goals type: 2";
				$time = " in one day";
			}
			else{
				$type = "Goals type: 1";
				$time = " from <b>" . $rk->start_date_g . "</b> to <b>" . $rk->end_date_g . "</b>";
			}
			
			print $type . "<br />";
			
			print "<b>" . $rk->goal_g . "</b> ";
			
			$conn_type = $rk->conn_type_g;
			if($conn_type=="IIII")
				echo " total visits";
			else{
				if($conn_type[0]=="I")
					echo " visitors,";
				if($conn_type[1]=="I")
					echo " pageviews,";
				if($conn_type[2]=="I")
					echo " spiders,";
				if($conn_type[3]=="I")
					echo " feeds,";
			}
			
			echo $time . "<br />";
			
			StatSurferPartialGoal($rk->id_g);
			
			print "</td></tr></tbody></table>";
			
			echo "</td>";
			
			$i++;
			if($i==3){
				$i=0;
				echo "</tr>";
			}
			
		}
		
		if($i!=0)
			echo "</tr>";
		
		echo "</table>";
		
		print "</div>";
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurferCheckGoals(){
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . "StatSurfer_goals";
		$table_StatSurfer = $wpdb->prefix . $_STATSURFER['table_name'];
		
		//type 1 control
		$qry = $wpdb->get_results("SELECT* FROM ".$table_name." WHERE result_g='Y' AND start_date_g!='0000-00-00' ");
		foreach ($qry as $rk){
			if($rk->end_date_g<gmdate("Y-m-d", current_time('timestamp'))){
				$control=1;
			}
			$total_result = 0;
			$conn_type = $rk->conn_type_g;
			
			list($year, $month, $day) = split("-", $rk->start_date_g);
			$startDate = date('Ymd', mktime(0, 0, 0, $month, $day, $year));
			list($year, $month, $day) = split("-", $rk->end_date_g);
			$endDate = date('Ymd', mktime(0, 0, 0, $month, $day, $year));
			
			if($conn_type[0]=="I"){
				$qry_visitors = $wpdb->get_row("
											   SELECT count(DISTINCT ip) AS total
											   FROM ".$table_StatSurfer."
											   WHERE feed=''
											   AND spider=''
											   AND date > '" . $startDate . "'
											   AND date < '" . $endDate . "'
											   ");
				$total_result = $total_result + $qry_visitors->total;
			}
			if($conn_type[1]=="I"){
				$qry_pageviews = $wpdb->get_row("
												SELECT count(date) as total
												FROM ".$table_StatSurfer."
												WHERE feed=''
												AND spider=''
												AND date > '" . $startDate . "'
												AND date < '" . $endDate . "'
												");
				$total_result = $total_result + $qry_pageviews->total;
			}
			if($conn_type[2]=="I"){
				$qry_spiders = $wpdb->get_row("
											  SELECT count(ip) AS total
											  FROM ".$table_StatSurfer."
											  WHERE feed=''
											  AND spider<>''
											  AND date > '" . $startDate . "'
											  AND date < '" . $endDate . "'
											  ");
				$total_result = $total_result + $qry_spiders->total;
			}
			if($conn_type[3]=="I"){
				$qry_feeds = $wpdb->get_row("
											SELECT count(ip) AS total
											FROM ".$table_StatSurfer."
											WHERE feed<>''
											AND spider=''
											AND date > '" . $startDate . "'
											AND date < '" . $endDate . "'
											");
				$total_result = $total_result + $qry_feeds->total; 
			}
			
			if($control==1){	
				if($rk->goal_g<=$total_result)
					$result = "G";
				else
					$result = "R";
			}
			else
				$result = "Y";
			
			$qry = "UPDATE ".$table_name." SET result_g = '".$result."', partial_g='".$total_result."' WHERE id_g='".$rk->id_g."'";
			$wpdb->query($qry);
			
		}
		
		
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . "StatSurfer_goals";
		$table_StatSurfer = $wpdb->prefix . $_STATSURFER['table_name'];
		//type 2 control
		$query = $wpdb->get_results("SELECT* FROM ".$table_name." WHERE result_g='Y' AND start_date_g='0000-00-00' ");
		foreach ($query as $rk){
			
			//assegnazione prima data
			/*if($rk->end_date_g=="0000-00-00"){
			 $conn = $wpdb->get_results("SELECT* FROM ".$table_StatSurfer." ORDER BY id ASC ");
			 $st_Timestamp = $conn->timestamp;
			 $st_Date = gmdate('Y-m-d', $st_Timestamp);
			 $st_up = "UPDATE ".$table_name." SET end_date_g = '".$st_Date."' WHERE id_g='".$rk->id_g."'";
			 $wpdb->query($st_up);
			 }*/
			
			$exDate = $rk->end_date_g;
			list($year, $month, $day) = split("-", $exDate);
			$startDate = mktime(0,0,0,$month,$day,$year);
			
			for($i=$startDate;$i<=(current_time('timestamp'));$i=$i+86400){
				$startDate = gmdate('Ymd', $i);
				
				$total_result = 0;
				$conn_type = $rk->conn_type_g;
				if($conn_type[0]=="I"){
					$qry_visitors = $wpdb->get_row("
												   SELECT count(DISTINCT ip) AS total
												   FROM ".$table_StatSurfer."
												   WHERE feed=''
												   AND spider=''
												   AND date = '" . $startDate . "'
												   ");
					$total_result = $total_result + $qry_visitors->total;
				}
				if($conn_type[1]=="I"){
					$qry_pageviews = $wpdb->get_row("
													SELECT count(date) as total
													FROM ".$table_StatSurfer."
													WHERE feed=''
													AND spider=''
													AND date = '" . $startDate . "'
													");
					$total_result = $total_result + $qry_pageviews->total;
				}
				if($conn_type[2]=="I"){
					$qry_spiders = $wpdb->get_row("
												  SELECT count(ip) AS total
												  FROM ".$table_StatSurfer."
												  WHERE feed=''
												  AND spider<>''
												  AND date = '" . $startDate . "'
												  ");
					$total_result = $total_result + $qry_spiders->total;
				}
				if($conn_type[3]=="I"){
					$qry_feeds = $wpdb->get_row("
												SELECT count(ip) AS total
												FROM ".$table_StatSurfer."
												WHERE feed<>''
												AND spider=''
												AND date = '" . $startDate . "'
												");
					$total_result = $total_result + $qry_feeds->total; 
				}
				if($rk->goal_g<=$total_result){
					$qry = "UPDATE ".$table_name." SET result_g = 'G' WHERE id_g='".$rk->id_g."'";
					$wpdb->query($qry);
				}
				$qry = "UPDATE ".$table_name." SET end_date_g = '".$startDate."', partial_g='".$total_result."' WHERE id_g='".$rk->id_g."'";
				$wpdb->query($qry);
				
			}
			
		}
	}	
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurferAddGoals(){
		
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . "StatSurfer_goals";
		$control = 1;
		
		$conn_type = "OOOO";
		if($_GET['check_visitor']=="I")
			$conn_type[0] = "I";
		if($_GET['check_page']=="I")
			$conn_type[1] = "I";
		if($_GET['check_spider']=="I")
			$conn_type[2] = "I";
		if($_GET['check_feed']=="I")
			$conn_type[3] = "I";
		
		$start_date = "00000000";
		$end_date = "00000000";
		if($_GET['goal_type']=="type_1"){
			$start_date = $_GET['st_date_Y'] . $_GET['st_date_m'] . $_GET['st_date_d'];
			$end_date = $_GET['ed_date_Y'] . $_GET['ed_date_m'] . $_GET['ed_date_d'];
		}
		else
			$end_date = gmdate('Ymd', current_time('timestamp'));
		
		
		if($conn_type=='OOOO'){
			$control = 0;
			$control_msg = "You have to set at least one connection type";
		}
		
		if($start_date>$end_date){
			$control = 0;
			$control_msg = "Attention, the initial date have to be previous by the final one";
		}
		
		if($_GET['goal_type']=="type_1"&&$_GET['st_date_Y']=='yyyy'&&$_GET['ed_date_Y']=='yyyy'&&$_GET['st_date_m']=='mm'&&$_GET['ed_date_m']=='mm'&&$_GET['st_date_d']=='dd'&&$_GET['ed_date_d']=='dd'){
			$control = 0;
			$control_msg = "If you have chosen the goals type 1, you have to set the start and the end dates";
		}
		
		if($_GET['name']==''){
			$control = 0;
			$control_msg = "You have to set a name of the goal";
		}
		
		if($_GET['goal']==''){
			$control = 0;
			$control_msg = "You have to set a value in the GOAL field";
		}
		
		if($control==1){
			$insert = "INSERT INTO " . $table_name . " (name_g, conn_type_g, goal_g, start_date_g, end_date_g, result_g) ";
			$insert .= "VALUES ('".$_GET['name']."','".$conn_type."','".$_GET['goal']."','" . $start_date . "','" . $end_date . "','Y')";
			$results = $wpdb->query($insert);
		}
		
		
		print "<div class='wrap'><h2>Goals</h2>";
		print "<table class='widefat'><thead><tr>";
		print "<th scope='col'><center>Goal insertion</center></th>";
		print "</tr></thead><tbody id='the-list'>";
		print "<tr><td style='padding-top:10px;'><center>";
		if($control==1)
			echo "Goal correctly inserted";
		else
			echo "ERROR! " . $control_msg . "!";
		
		echo "<br /><a href='admin.php?page=statsurfer/statsurfer.php&StatSurfer_action=goals'><button style='margin-bottom:10px;margin-top:6px;'>Return to the Goals page</button></a>";
		
		print "</center></td></tr></tbody></table></div>";
		
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurferDelGoals(){
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . "StatSurfer_goals";
		
		$results = $wpdb->query("DELETE FROM " . $table_name . " WHERE id_g='".$_GET['gid']."'");
		
		StatSurferGoals();
	}
	
	?>