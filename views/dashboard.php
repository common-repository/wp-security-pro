<?php
global $Mo_wpUtility,$mmp_dirName;
include_once $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'navbar.php';
echo '<div id="mo_switch_message" style=" padding:8px"></div>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<div class="mo_wpns_divided_layout">	
		<div class="mo_wpns_dashboard_layout">
				<div class ="mo_wpns_inside_dashboard_layout">Infected Files<p class =" mo_wpns_dashboard_text" >'.esc_attr($total_malicious).'</p></div>
				<div class ="mo_wpns_inside_dashboard_layout ">Failed Login<p class =" mo_wpns_dashboard_text" >'.esc_attr($wpns_attacks_blocked).'</p></div>
				<div class ="mo_wpns_inside_dashboard_layout">Attacks Blocked <p class =" mo_wpns_dashboard_text">'.esc_attr($totalAttacks).'</p></div>
				<div class ="mo_wpns_inside_dashboard_layout">Blocked IPs<p class =" mo_wpns_dashboard_text">'.esc_attr($wpns_count_ips_blocked).'</p></div>
				<div class ="mo_wpns_inside_dashboard_layout">White-listed IPs<p class =" mo_wpns_dashboard_text">'.esc_attr($wpns_count_ips_whitelisted).'</p></div>		
		</div></div>	';

?>