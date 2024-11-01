<?php 
	class Mo_wp_LoginHandler
	{
		function __construct()
		{
			add_action( 'init' , array( $this, 'mo_wpns_init' ) );		
			if(get_option('mo_wpns_enable_brute_force'))
			{
				add_action('wp_login'				 , array( $this, 'mo_wpns_login_success' 	       )		);
				add_action('wp_login_failed'		 , array( $this, 'mo_wpns_login_failed'	 	       ) 	    );
			}
                if(get_option('mo_wpns_activate_recaptcha_for_woocommerce_registration') ){
				add_action( 'woocommerce_register_post', array( $this,'wooc_validate_user_captcha_register'), 1, 3);
			} 
		}	
		function mo_wpns_init()
		{
			global $Mo_wpUtility,$mmp_dirName;
			$WAFEnabled = get_option('WAFEnabled');
			$WAFLevel = get_option('WAF');
			$Mo_wp_scanner_parts = new Mo_wp_scanner_parts();
			$Mo_wp_scanner_parts->file_cron_scan();
			if($WAFEnabled == 1)
			{	
				if($WAFLevel == 'PluginLevel')
				{
					if(file_exists($mmp_dirName .'handler'.DIRECTORY_SEPARATOR.'WAF'.DIRECTORY_SEPARATOR.'mo-waf-plugin.php'))
						include_once($mmp_dirName .'handler'.DIRECTORY_SEPARATOR.'WAF'.DIRECTORY_SEPARATOR.'mo-waf-plugin.php');
				}
			}
			
				$userIp 			= $Mo_wpUtility->get_client_ip();
				$mo_wpns_config 	= new Mo_wpHandler();
				$isWhitelisted   	= $mo_wpns_config->is_whitelisted($userIp);
				$isIpBlocked 		= false;
				if(!$isWhitelisted){
				$isIpBlocked = $mo_wpns_config->is_ip_blocked_in_anyway($userIp);
				}
				 if($isIpBlocked)
				 	include $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR.'403.php';

				$requested_uri = sanitize_text_field($_SERVER["REQUEST_URI"]);
				$option = false;
				if (is_user_logged_in()) {
					if (strpos($requested_uri, chr(get_option('login_page_url'))) != false) {
						wp_safe_redirect(site_url());
						die;
					}
				} else {
					$option = get_option('mo_wpns_enable_rename_login_url');
				}
				if ($option) {
                    if (strpos($requested_uri, '/wp-login.php?checkemail=confirm') !== false) {
                        $requested_uri = str_replace("wp-login.php","",$requested_uri);
                        wp_safe_redirect($requested_uri);
                        die;
                    } else if (strpos($requested_uri, '/wp-login.php?checkemail=registered') !== false) {
                        $requested_uri = str_replace("wp-login.php","",$requested_uri);
                        wp_safe_redirect($requested_uri);
                        die;
                    }

                    if (strpos($requested_uri, '/wp-login.php') !== false) {
						wp_safe_redirect(site_url());
					}
					else if (strpos($requested_uri, get_option('login_page_url')) !== false ) {
						@require_once ABSPATH . 'wp-login.php';
						die;
					}
				}

		}

		function wooc_validate_user_captcha_register($username, $email, $validation_errors) {

			if (empty($_POST['g-recaptcha-response'])) {
				$validation_errors->add( 'woocommerce_recaptcha_error', __('Please verify the captcha', 'woocommerce' ) );
			}
		}

		//Function to handle successful user login
		function mo_wpns_login_success($username)
		{
			global $Mo_wpUtility;

				$mo_wpns_config = new Mo_wpHandler();
				$userIp 		= $Mo_wpUtility->get_client_ip();

				$mo_wpns_config->move_failed_transactions_to_past_failed($userIp);

				if(get_option('mo_wpns_enable_unusual_activity_email_to_user'))
					$Mo_wpUtility->sendNotificationToUserForUnusualActivities($username, $userIp, Mo_wp_Constants::LOGGED_IN_FROM_NEW_IP);


				$mo_wpns_config->add_transactions($userIp, $username, Mo_wp_Constants::LOGIN_TRANSACTION, Mo_wp_Constants::SUCCESS);
		}
		//Function to handle failed user login attempt
		function mo_wpns_login_failed($username)
		{
			global $Mo_wpUtility;
				$userIp 		= $Mo_wpUtility->get_client_ip();
				if(empty($userIp) || empty($username) || !get_option('mo_wpns_enable_brute_force'))
					return;

				$mo_wpns_config = new Mo_wpHandler();
				$isWhitelisted  = $mo_wpns_config->is_whitelisted($userIp);

				$mo_wpns_config->add_transactions($userIp, $username, Mo_wp_Constants::LOGIN_TRANSACTION, Mo_wp_Constants::FAILED);
					if(get_option('mo_wpns_enable_unusual_activity_email_to_user'))
							$Mo_wpUtility->sendNotificationToUserForUnusualActivities($username, $userIp, Mo_wp_Constants::FAILED_LOGIN_ATTEMPTS_FROM_NEW_IP);
					$failedAttempts 	 = $mo_wpns_config->get_failed_attempts_count($userIp);
					$allowedLoginAttepts = get_option('mo_wpns_allwed_login_attempts') ? get_option('mo_wpns_allwed_login_attempts') : 5;

					if($allowedLoginAttepts - $failedAttempts<=0)
						$this->handle_login_attempt_exceeded($userIp);
					else if(get_option('mo_wpns_show_remaining_attempts'))
						$this->show_limit_login_left($allowedLoginAttepts,$failedAttempts);
		}
		//Function to show number of attempts remaining
		function show_limit_login_left($allowedLoginAttepts,$failedAttempts)
		{
			global $error;
			$diff = $allowedLoginAttepts - $failedAttempts;
			$error = "<br>You have <b>".$diff."</b> login attempts remaining.";
		}
		//Function to handle login limit exceeded
		function handle_login_attempt_exceeded($userIp)
		{
			global $Mo_wpUtility, $mmp_dirName;
			$mo_wpns_config = new Mo_wpHandler();
			$mo_wpns_config->block_ip($userIp, Mo_wp_Constants::LOGIN_ATTEMPTS_EXCEEDED, false);
			include $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR.'403.php';
		}

	}
	new Mo_wp_LoginHandler;
