<?php

	/*	
		PlugIn: StatSurfer
		Page: pages/maps.php
		Author: Cattani Simone
		Author URI: http://cattanisimone.it
	*/
	
	function StatSurferMap_update(){
		
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		$table_name_country = $wpdb->prefix . "StatSurfer_countries";
		
		$tsStart = get_option('StatSurfer_lastmaptimestamp');
		$tsEnd = current_time('timestamp');
		
		//update_option('StatSurfer_lastmaptimestamp', $tsEnd);
		
		$qry = $wpdb->get_results("SELECT ip FROM ".$table_name." WHERE timestamp>".$tsStart." AND timestamp<=".$tsEnd." ORDER BY timestamp ASC");
		foreach ($qry as $rk){
			$url = "http://api.hostip.info/country.php?ip=" . $rk->ip;
			$ciso = file_get_contents($url);
			
			$cry = $wpdb->get_results("SELECT conn_c FROM ".$table_name_country." WHERE iso3166_c='".$ciso."'");
			foreach ($cry as $ck){
				$conn_c = $ck->conn_c;
				$conn_c++;
				$upy = "UPDATE ".$table_name_country." SET conn_c = '".$conn_c."'  WHERE iso3166_c='".$ciso."'";
				$wpdb->query($upy); 
			}
		}
	}
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	function StatSurferMap(){
		
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . "StatSurfer_countries";
		
		print "<div class='wrap'><h2>Connections' Map</h2><table class='widefat'><thead><tr><th>World Map</th></tr></thead>";
		print "<tbody id='the-list'><tr><td>";
		
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
		
		echo "<table width='100%' cellspacing='0' cellpadding='0' border='0'>";
		echo "<tr><td valign='top' style='border:0px;padding-top:10px;' width='440'>";
		
		//graduated world
		echo "<div style='float:left;'><table class='widefat' style='margin:10px;width:600px;' width='600'><tbody id='the-list'><tr><td>";
		print "<img src='http://chart.apis.google.com/chart?cht=map:fixed=-60,-170,80,-170&chs=600x350&chld=".$sel_countries."&chd=t:".$val_countries."&chco=d5d5d5,d5d5d5,114477' />\n";
		echo "</td></tr></tbody></table></div>";
		
		//world
		if(get_option('StatSurfer_showmap_world')=='checked'){
			echo "<div style='float:left;'><table class='widefat' style='margin:10px;width:600px;' width='600'><tbody id='the-list'><tr><td>";
			print "<img src='http://chart.apis.google.com/chart?cht=map:fixed=-60,-170,80,-170&chs=600x350&chld=".$sel_countries."&chd=t:".$val_countries."&chco=d5d5d5,114477,114477' />\n";
			echo "</td></tr></tbody></table></div>";
		}
		
		//europe
		if(get_option('StatSurfer_showmap_europe')=='checked'){
			echo "<div style='float:left;'><table class='widefat' style='margin:10px;width:600px;' width='600'><tbody id='the-list'><tr><td>";
			print "<img src='http://chart.apis.google.com/chart?cht=map:fixed=30,-31,71,89&chs=600x350&chld=".$sel_countries."&chd=t:".$val_countries."&chco=d5d5d5,114477,114477' />\n";
			echo "</td></tr></tbody></table></div>";
		}
		
		//north america
		if(get_option('StatSurfer_showmap_northamerica')=='checked'){
			echo "<div style='float:left;'><table class='widefat' style='margin:10px;width:600px;' width='600'><tbody id='the-list'><tr><td>";
			print "<img src='http://chart.apis.google.com/chart?cht=map:fixed=10,-171,71,-21&chs=600x350&chld=".$sel_countries."&chd=t:".$val_countries."&chco=d5d5d5,114477,114477' />\n";
			echo "</td></tr></tbody></table></div>";
		}
		
		//south america
		if(get_option('StatSurfer_showmap_southamerica')=='checked'){
			echo "<div style='float:left;'><table class='widefat' style='margin:10px;width:600px;' width='600'><tbody id='the-list'><tr><td>";
			print "<img src='http://chart.apis.google.com/chart?cht=map:fixed=-61,-171,10,-21&chs=600x350&chld=".$sel_countries."&chd=t:".$val_countries."&chco=d5d5d5,114477,114477' />\n";
			echo "</td></tr></tbody></table></div>";
		}
		
		//asia
		if(get_option('StatSurfer_showmap_asia')=='checked'){
			echo "<div style='float:left;'><table class='widefat' style='margin:10px;width:600px;' width='600'><tbody id='the-list'><tr><td>";
			print "<img src='http://chart.apis.google.com/chart?cht=map:fixed=0,30,71,-170&chs=600x350&chld=".$sel_countries."&chd=t:".$val_countries."&chco=d5d5d5,114477,114477' />\n";
			echo "</td></tr></tbody></table></div>";
		}
		
		//africa
		if(get_option('StatSurfer_showmap_africa')=='checked'){
			echo "<div style='float:left;'><table class='widefat' style='margin:10px;width:600px;' width='600'><tbody id='the-list'><tr><td>";
			print "<img src='http://chart.apis.google.com/chart?cht=map:fixed=-35,-45,37,90&chs=600x350&chld=".$sel_countries."&chd=t:".$val_countries."&chco=d5d5d5,114477,114477' />\n";
			echo "</td></tr></tbody></table></div>";
		}
		
		//oceania
		if(get_option('StatSurfer_showmap_oceania')=='checked'){
			echo "<div style='float:left;'><table class='widefat' style='margin:10px;width:600px;' width='600'><tbody id='the-list'><tr><td>";
			print "<img src='http://chart.apis.google.com/chart?cht=map:fixed=-50,85,5,-175&chs=600x350&chld=".$sel_countries."&chd=t:".$val_countries."&chco=d5d5d5,114477,114477' />\n";
			echo "</td></tr></tbody></table></div>";
		}
		
		echo "</td><td valign='top' style='border:0px;padding-right:20px;' width='450'>";
		
		$list = 1;
		echo "<div>";
		
		$table_name_c = $wpdb->prefix . "StatSurfer_countries";
		$qry_count_countries = $wpdb->get_row("SELECT count(id_c) as total FROM $table_name_c");
		
		
		echo "<table width='450'>";
		echo "<tr><td colspan='2'><h3 style='font:italic 17px Georgia,Times New Roman,Bitstream Charter,Times,serif;padding:0px 0px 0px 0px;margin:7px 0px 7px 0px;'>Countries' datas </h3></td></tr>";
		echo "<tr><td width='330'><b>Known Countries</b> (included Europen Union)</td><td align='right' >".$qry_count_countries->total."</td></tr>";
		echo "<tr><td><b>Countries connected</b></td><td align='right'>".$count_countries."</td></tr>";
		echo "<tr><td><b>Total Connections</b> (only from known countries)</td><td align='right'>".$total_conn."</td></tr>";
		echo "<tr><td><b>Connections' mean</b></td><td align='right'>";
		if($count_countries == 0){
			echo "-";
		}
		else{
			echo round(($total_conn/$count_countries),0);
		}
		echo "</td></tr>";
		echo "</table>";
		echo "<br />";
		
		echo "<table width='450'>";
		$qry = $wpdb->get_results("SELECT* FROM ".$table_name." WHERE conn_c<>0 ORDER BY conn_c DESC");
		foreach ($qry as $rk){
			echo "<tr><td align='right' width='30'>";
			echo $list;
			echo "</td><td width='280'>";
			echo $rk->name_c;
			echo "</td><td width='20' align='center' valign='bottom'>";
			print "<IMG SRC='../wp-content/plugins/statsurfer/images/flags/".strtolower($rk->iso3166_c).".png' style='margin:0px;padding:0px;border:0px;' width='16' border=0 />";
			echo "</td><td width='20' align='center'>";
			echo $rk->iso3166_c;
			echo "</td><td align='right' width='100'>";
			echo $rk->conn_c;
			//echo "</td><td align='right' width='100'>";
			//echo $rk->w_conn_c;
			echo "</td></tr>";
			
			$list++;
		}
		echo "</table></div>";
		
		echo "</td><td style='background: url(../wp-content/plugins/statsurfer/images/flag_globe.png) no-repeat top right;'></td></tr></table>";
		print "</td></tr></tbody></table></div>";
		
	}
	
	?>