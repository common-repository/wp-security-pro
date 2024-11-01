<?php

class Mo_wp_Logger
{
	function __construct()
	{
		add_action( 'log_403' , array( $this, 'log_403' ) );
		add_action( 'template_redirect', array( $this, 'log_404' ) );
	}	


	function log_403()
	{
		global $Mo_wpUtility;
			$mo_wpns_config = new Mo_wpHandler();
			$userIp 		= $Mo_wpUtility->get_client_ip();
			$url			= $Mo_wpUtility->get_current_url();
			$user  			= wp_get_current_user();
			$username		= is_user_logged_in() ? $user->user_login : 'GUEST';
			$mo_wpns_config->add_transactions($userIp,$username,Mo_wp_Constants::ERR_403, Mo_wp_Constants::ACCESS_DENIED,$url);
	}

	function log_404()
	{
		global $Mo_wpUtility;

		if(!is_404())
			return;
			$mo_wpns_config = new Mo_wpHandler();
			$userIp 		= $Mo_wpUtility->get_client_ip();
			$url			= $Mo_wpUtility->get_current_url();
			$user  			= wp_get_current_user();
			$username		= is_user_logged_in() ? $user->user_login : 'GUEST';
			$mo_wpns_config->add_transactions($userIp,$username,Mo_wp_Constants::ERR_404, Mo_wp_Constants::ACCESS_DENIED,$url);
	}
}
new Mo_wp_Logger;