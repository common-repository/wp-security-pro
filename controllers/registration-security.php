<?php
	
	global $Mo_wpUtility, $mmp_dirName;
	if(current_user_can( 'manage_options' ) && isset($_POST['option']))
	{
		switch(sanitize_text_field($_POST['option']))
		{
			case "mo_wpns_enable_fake_domain_blocking":
				mmp_handle_domain_blocking($_POST);						
				break;
		}
	}
	$openid_url = add_query_arg( array('page' =>'mo_openid_settings'), sanitize_text_field($_SERVER['REQUEST_URI'] ));
	$domain_blocking= get_option('mo_wpns_enable_fake_domain_blocking') 		? "checked" : "";
	$user_verify	= get_option('mo_wpns_enable_advanced_user_verification') 	? "checked" : "";	

	include $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'registration-security.php';
	function mmp_handle_domain_blocking($postvalue)
	{
		$enable_fake_emails = isset($postvalue['mo_wpns_enable_fake_domain_blocking']) ? true : false;
		update_option( 'mo_wpns_enable_fake_domain_blocking', $enable_fake_emails);
		if($enable_fake_emails)
			do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('DOMAIN_BLOCKING_ENABLED'),'SUCCESS');
		else
			do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('DOMAIN_BLOCKING_DISABLED'),'ERROR');
	}
