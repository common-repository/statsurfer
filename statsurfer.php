<?php
	/*
	Plugin Name: StatSurfer
	Plugin URI: http://cattanisimone.it/statsurfer
	Description: Surf in your stats
	Version: 1.2.1
	License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
	Author: Cattani Simone
	Author URI: http://cattanisimone.it
	*/
	
	/*
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	 
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	 
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/
  
	$_STATSURFER['version'] = '1.2.1';
	$_STATSURFER['feedtype'] = '';
	
	
	include("append.php");
	include("widget.php");
	include("db_widget.php");
	include("install_pl.php");
	include("update_pl.php");
	
	include("include/functions.php");
	include("include/system.php");
	include("include/not_tested.php");
	
	include("pages/main.php");
	include("pages/details.php");
	include("pages/spy.php");
	include("pages/options.php");
	include("pages/maps.php");
	include("pages/goals.php");
	include("pages/agents.php");
	//include("pages/search.php");
	
	include("iframe/main_graphs.php");
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	
	if(!get_option('statsurfer_statpress')){
		update_option('statsurfer_statpress','statsurfer');
		update_option('statsurfer_statpress_check','unchecked');
	}
	
	if(get_option('statsurfer_statpress')=='statpress')
		$_STATSURFER['table_name'] = 'statpress';
	else
		$_STATSURFER['table_name'] = 'StatSurfer';
  
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	// Function selection
	
	if ($_GET['StatSurfer_action'] == 'exportnow')
	{
		StatSurferExportNow();
	}
	
	function StatSurfer()
	{
		if ($_GET['StatSurfer_action'] == 'export')
		{
			StatSurferExport();
		}
		elseif ($_GET['StatSurfer_action'] == 'map')
		{
			StatSurferMap();
		}
		elseif ($_GET['StatSurfer_action'] == 'up')
		{
			StatSurferUpdate();
		}
		elseif ($_GET['StatSurfer_action'] == 'spy')
		{
			StatSurferSpy();
		}
		elseif ($_GET['StatSurfer_action'] == 'details')
		{
			StatSurferDetails();
		}
		elseif ($_GET['StatSurfer_action'] == 'options')
		{
			StatSurferOptions();
		}
		elseif ($_GET['StatSurfer_action'] == 'overview')
		{
			StatSurferMain();
		}
		elseif ($_GET['StatSurfer_action'] == 'agents')
		{
			StatSurferAgents();
		}
		elseif ($_GET['StatSurfer_action'] == 'goals')
		{
			StatSurferGoals();
		}
		elseif ($_GET['StatSurfer_action'] == 'add_goal')
		{
			StatSurferAddGoals();
		}
		elseif ($_GET['StatSurfer_action'] == 'del_goal')
		{
			StatSurferDelGoals();
		}
		elseif ($_GET['StatSurfer_action'] == 'show_goal')
		{
			StatSurferShowGoals();
		}
		//Main
		else
		{
			StatSurferMain();
		}
	}
	
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	// Menu Installation
	
	function iri_add_pages()
	{
		// Create table if it doesn't exist
		global $wpdb; global $_STATSURFER;
		$table_name = $wpdb->prefix . $_STATSURFER['table_name'];
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
		{
			StatSurfer_CreateTable();
			update_option('StatSurfer_showcharts','checked');
		}
		
		// add submenu
		$mincap = get_option('StatSurfer_mincap');
		if ($mincap == '')
		{
			$mincap = 'level_8';
		}
		
		add_menu_page('StatSurfer', 'StatSurfer', $mincap, __FILE__, 'StatSurfer');
		add_submenu_page(__FILE__, __('Details', 'StatSurfer'), __('Details', 'StatSurfer'), $mincap, 'details', 'StatSurferDetails');
		add_submenu_page(__FILE__, __('Spy', 'StatSurfer'), __('Spy', 'StatSurfer'), $mincap, 'spy', 'StatSurferSpy');
		add_submenu_page(__FILE__, __('Goals', 'StatSurfer'), __('Goals', 'StatSurfer'), $mincap, 'goals', 'StatSurferGoals');
		add_submenu_page(__FILE__, __('Map', 'StatSurfer'), __('Map', 'StatSurfer'), $mincap, 'map', 'StatSurferMap');
		//add_submenu_page(__FILE__, __('Export', 'StatSurfer'), __('Export', 'StatSurfer'), $mincap, 'export', 'StatSurferExport');
		add_submenu_page(__FILE__, __('Options', 'StatSurfer'), __('Options', 'StatSurfer'), $mincap, 'options', 'StatSurferOptions');
		add_submenu_page(__FILE__, __('User Agents', 'StatSurfer'), __('User Agents', 'StatSurfer'), $mincap, 'agents', 'StatSurferAgents');
		//add_submenu_page(__FILE__, __('StatSurferUpdate', 'StatSurfer'), __('StatSurferUpdate', 'StatSurfer'), $mincap, 'up', 'StatSurferUpdate');
	}
	
	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
    // Init
	
	function widget_StatSurfer_init($args)
	{
		if (!function_exists('register_sidebar_widget') || !function_exists('register_widget_control'))
			return;
		
		// Multifunctional StatSurfer pluging
		function widget_StatSurfer_control()
		{
			$options = get_option('widget_StatSurfer');
			if (!is_array($options))
				$options = array('title' => 'StatSurfer', 'body' => 'Visits today: %visits%');
			if ($_POST['StatSurfer-submit'])
			{
				$options['title'] = strip_tags(stripslashes($_POST['StatSurfer-title']));
				$options['body'] = stripslashes($_POST['StatSurfer-body']);
				update_option('widget_StatSurfer', $options);
			}
			$title = htmlspecialchars($options['title'], ENT_QUOTES);
			$body = htmlspecialchars($options['body'], ENT_QUOTES);
			// the form
			echo '<p style="text-align:right;"><label for="StatSurfer-title">' . __('Title:') . ' <input style="width: 250px;" id="StatSurfer-title" name="StatSurfer-title" type="text" value="' . $title . '" /></label></p>';
			echo '<p style="text-align:right;"><label for="StatSurfer-body"><div>' . __('Body:', 'widgets') . '</div><textarea style="width: 288px;height:100px;" id="StatSurfer-body" name="StatSurfer-body" type="textarea">' . $body . '</textarea></label></p>';
			echo '<input type="hidden" id="StatSurfer-submit" name="StatSurfer-submit" value="1" /><div style="font-size:7pt;">%totalvisits% %visits% %thistotalvisits% %os% %browser% %ip% %since% %visitorsonline% %usersonline% %toppost% %topbrowser% %topos%</div>';
		}
		
		function widget_StatSurfer($args)
		{
			extract($args);
			$options = get_option('widget_StatSurfer');
			$title = $options['title'];
			$body = $options['body'];
			echo $before_widget;
			print($before_title . $title . $after_title);
			print StatSurfer_Vars($body);
			echo $after_widget;
		}
		register_sidebar_widget('StatSurfer', 'widget_StatSurfer');
		register_widget_control(array('StatSurfer', 'widgets'), 'widget_StatSurfer_control', 300, 210);
		
		// Top posts
		function widget_StatSurfertopposts_control()
		{
			$options = get_option('widget_StatSurfertopposts');
			if (!is_array($options))
			{
				$options = array('title' => 'StatSurfer TopPosts', 'howmany' => '5', 'showcounts' => 'checked');
			}
			if ($_POST['StatSurfertopposts-submit'])
			{
				$options['title'] = strip_tags(stripslashes($_POST['StatSurfertopposts-title']));
				$options['howmany'] = stripslashes($_POST['StatSurfertopposts-howmany']);
				$options['showcounts'] = stripslashes($_POST['StatSurfertopposts-showcounts']);
				if ($options['showcounts'] == "1")
				{
					$options['showcounts'] = 'checked';
				}
				update_option('widget_StatSurfertopposts', $options);
			}
			$title = htmlspecialchars($options['title'], ENT_QUOTES);
			$howmany = htmlspecialchars($options['howmany'], ENT_QUOTES);
			$showcounts = htmlspecialchars($options['showcounts'], ENT_QUOTES);
			// the form
			echo '<p style="text-align:right;"><label for="StatSurfertopposts-title">' . __('Title', 'StatSurfer') . ' <input style="width: 250px;" id="StatSurfer-title" name="StatSurfertopposts-title" type="text" value="' . $title . '" /></label></p>';
			echo '<p style="text-align:right;"><label for="StatSurfertopposts-howmany">' . __('Limit results to', 'StatSurfer') . ' <input style="width: 100px;" id="StatSurfertopposts-howmany" name="StatSurfertopposts-howmany" type="text" value="' . $howmany . '" /></label></p>';
			echo '<p style="text-align:right;"><label for="StatSurfertopposts-showcounts">' . __('Visits', 'StatSurfer') . ' <input id="StatSurfertopposts-showcounts" name="StatSurfertopposts-showcounts" type=checkbox value="checked" ' . $showcounts . ' /></label></p>';
			echo '<input type="hidden" id="StatSurfer-submitTopPosts" name="StatSurfertopposts-submit" value="1" />';
		}
		
		function widget_StatSurfertopposts($args)
		{
			extract($args);
			$options = get_option('widget_StatSurfertopposts');
			$title = htmlspecialchars($options['title'], ENT_QUOTES);
			$howmany = htmlspecialchars($options['howmany'], ENT_QUOTES);
			$showcounts = htmlspecialchars($options['showcounts'], ENT_QUOTES);
			echo $before_widget;
			print($before_title . $title . $after_title);
			print StatSurfer_TopPosts($howmany, $showcounts);
			echo $after_widget;
		}
		register_sidebar_widget('StatSurfer TopPosts', 'widget_StatSurfertopposts');
		register_widget_control(array('StatSurfer TopPosts', 'widgets'), 'widget_StatSurfertopposts_control', 300, 110);
	}

	
	/*-------------------------------------------------------------------------------------------------------------------------------*/
	// Main
      
		// a custom function for loading localization
		function StatSurfer_load_textdomain() {
		//check whether necessary core function exists
		if ( function_exists('load_plugin_textdomain') ) {
		//load the plugin textdomain
		load_plugin_textdomain('StatSurfer', 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/locale');
		}
		}
		// call the custom function on the init hook
		add_action('init', 'StatSurfer_load_textdomain');
      
      add_action('admin_menu', 'iri_add_pages');
      add_action('plugins_loaded', 'widget_StatSurfer_init');
      //add_action('wp_head', 'iriStatAppend');
      add_action('send_headers', 'iriStatAppend');
      
      register_activation_hook(__FILE__, 'StatSurfer_CreateTable');
	
?>