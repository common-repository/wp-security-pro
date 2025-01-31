<?php 

	global $Mo_wpUtility,$mmp_dirName;
	if(current_user_can( 'manage_options' ) && isset($_REQUEST['option']))
	{
		switch(sanitize_text_field($_REQUEST['option']))
		{
			case "mo_wpns_enable_brute_force":
				mmp_handle_bf_enable_form($_POST);				break;
			case "mo_wpns_brute_force_configuration":
				mmp_handle_bf_configuration_form($_POST);		break;
			case "mo_wpns_slow_down_attacks":
				wpns_handle_dos_enable_form($_POST);			break;
			case "mo_wpns_slow_down_attacks_config":
				wpns_handle_dos_configuration($_POST);			break;
			case "mo_wpns_activate_recaptcha":
				mmp_handle_enable_recaptcha($_POST);			break;
			case "mo_wpns_recaptcha_settings":
				mmp_handle_recaptcha_configuration($_POST);	break;
			case "mo_wpns_enable_rename_login_url":
				mmp_handle_enable_rename_login_url($_POST);	break;
			case "mo_wpns_rename_login_url_configuration":
				mmp_handle_rename_login_url_configuration($_POST);	break;
		}
	}

	$allwed_login_attempts 	= get_option('mo_wpns_allwed_login_attempts')	  ? get_option('mo_wpns_allwed_login_attempts')  : 10;
	$time_of_blocking_type 	= get_option('mo_wpns_time_of_blocking_type')	  ? get_option('mo_wpns_time_of_blocking_type')  : "permanent";
	$time_of_blocking_val 	= get_option('mo_wpns_time_of_blocking_val')	  ? get_option('mo_wpns_time_of_blocking_val')   : 3;
	$brute_force_enabled 	= get_option('mo_wpns_enable_brute_force') 		  ? "checked"	: "";
	$remaining_attempts 	= get_option('mo_wpns_show_remaining_attempts')   ? "checked" 	: "";
	$slow_down_attacks		= get_option('mo_wpns_slow_down_attacks') 		  ? "checked" 	: "";					
	$google_recaptcha		= get_option('mo_wpns_activate_recaptcha')		  ? "checked"	: "";
	$test_recaptcha_url = "";
	$test_recaptcha_url		= add_query_arg( array('option'=>'testrecaptchaconfig'), sanitize_text_field($_SERVER['REQUEST_URI'] ));
		$captcha_url		= 'https://www.google.com/recaptcha/admin#list';
		$captcha_site_key	= get_option('mo_wpns_recaptcha_site_key');
		$captcha_secret_key = get_option('mo_wpns_recaptcha_secret_key');
		$captcha_login		= get_option('mo_wpns_activate_recaptcha_for_login') 		? "checked" : "";
		$captcha_reg		= get_option('mo_wpns_activate_recaptcha_for_registration') ? "checked" : "";
	
	include $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'login-security.php';

	//Function to handle enabling and disabling of brute force protection
	function mmp_handle_bf_enable_form($postData)
	{
		$enable  =  isset($postData['enable_brute_force_protection']) ? $postData['enable_brute_force_protection'] : false;
		update_option( 'mo_wpns_enable_brute_force', $enable );

		if($enable)
			do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('BRUTE_FORCE_ENABLED'),'SUCCESS');
		else
			do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('BRUTE_FORCE_DISABLED'),'ERROR');
	}


	//Function to handle brute force configuration
	function mmp_handle_bf_configuration_form($postData)
	{
		$login_attempts 	= $postData['allwed_login_attempts'];
		$blocking_type  	= $postData['time_of_blocking_type'];
		$blocking_value 	= isset($postData['time_of_blocking_val'])	 ? $postData['time_of_blocking_val']: false;
		$remaining_attempts = isset($postData['show_remaining_attempts'])? $postData['show_remaining_attempts'] : false;

		update_option( 'mo_wpns_allwed_login_attempts'	, $login_attempts 		  );
		update_option( 'mo_wpns_time_of_blocking_type'	, $blocking_type 		  );
		update_option( 'mo_wpns_time_of_blocking_val' 	, $blocking_value   	  );
		update_option( 'mo_wpns_show_remaining_attempts', $remaining_attempts 	  );

		do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('CONFIG_SAVED'),'SUCCESS');
	}
	//Function to handle enabling and disabling google recaptcha
	function mmp_handle_enable_recaptcha($postData)
	{
		$enable = isset($postData['mo_wpns_activate_recaptcha']) ? $postData['mo_wpns_activate_recaptcha'] : false;
		update_option( 'mo_wpns_activate_recaptcha', $enable );

		if($enable)
			do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('RECAPTCHA_ENABLED'),'SUCCESS');
		else
		{
			update_option( 'mo_wpns_activate_recaptcha_for_login'		, false );
			update_option( 'mo_wpns_activate_recaptcha_for_registration', false );
            update_option( 'mo_wpns_activate_recaptcha_for_woocommerce_login'		, false );
			update_option( 'mo_wpns_activate_recaptcha_for_woocommerce_registration', false );
			do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('RECAPTCHA_DISABLED'),'ERROR');
		}
	}


	//Function to handle recaptcha configuration
	function mmp_handle_recaptcha_configuration($postData)
	{
		$enable_login= isset($postData['mo_wpns_activate_recaptcha_for_login']) 		? true : false;
		$enable_reg  = isset($postData['mo_wpns_activate_recaptcha_for_registration'])  ? true : false;
		$site_key 	 = sanitize_text_field($_POST['mo_wpns_recaptcha_site_key']);
		$secret_key  = sanitize_text_field($_POST['mo_wpns_recaptcha_secret_key']); 

		update_option( 'mo_wpns_activate_recaptcha_for_login'		, $enable_login );
		update_option( 'mo_wpns_recaptcha_site_key'			 		, $site_key     );
		update_option( 'mo_wpns_recaptcha_secret_key'				, $secret_key   );
		update_option( 'mo_wpns_activate_recaptcha_for_registration', $enable_reg   );
        update_option( 'mo_wpns_activate_recaptcha_for_woocommerce_login'		, $enable_login );
		update_option( 'mo_wpns_activate_recaptcha_for_woocommerce_registration', $enable_reg   );
		do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('RECAPTCHA_ENABLED'),'SUCCESS');
	}
	

	function mmp_handle_enable_rename_login_url($postData){
		$enable_rename_login_url_checkbox = false;
		if(isset($postData['enable_rename_login_url_checkbox'])  && $postData['enable_rename_login_url_checkbox']){
			$enable_rename_login_url_checkbox = sanitize_text_field($postData['enable_rename_login_url_checkbox']);
			do_action('mo_mmp_show_message','Rename Admin Login Page URL is enabled.','SUCCESS');
		}else {
			do_action('mo_mmp_show_message','Rename Admin Login Page URL is disabled.','SUCCESS');
		}
		$loginurl = get_option('login_page_url');
		if ($loginurl == "") {
			update_option('login_page_url', "mylogin");
		}
		update_option( 'mo_wpns_enable_rename_login_url', $enable_rename_login_url_checkbox);
	}
	
	function mmp_handle_rename_login_url_configuration($postData){
		if ($postData['login_page_url']) {
			update_option('login_page_url', sanitize_text_field($postData['login_page_url']));
		} else {
			update_option('login_page_url', sanitize_text_field('mylogin'));
		}
		do_action('mo_mmp_show_message','Your configuration has been saved.','SUCCESS');
	}
