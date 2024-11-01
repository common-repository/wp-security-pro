<?php 
	global $Mo_wpUtility,$mmp_dirName;
	if ( current_user_can( 'manage_options' ) and isset( $_POST['option'] ) )
	{
		$option = trim(sanitize_text_field($_POST['option']));
		switch($option)
		{
			case "mo_wpns_register_customer":
				mo_mmp_register_customer($_POST);																   
				break;
			case "mo_wpns_verify_customer":
				mo_mmp_verify_customer($_POST);																	   
				break;
			case "mo_wpns_cancel":
				mo_mmp_revert_back_registration();																   
				break;
			case "mo_wpns_reset_password":
				mo_mmp_reset_password();
			case "mo_wpns_log_out":
				mo_wpns_log_out($_POST); 																		   
				break;
		}
	} 

	if (!$Mo_wpUtility->icr()) 
	{
		delete_option ( 'password_mismatch' );
		update_option ( 'mo_wpns_new_registration', 'true' );
		$current_user 	= wp_get_current_user();
		$admin_email = get_option('mo_wpns_admin_email') ? get_option('mo_wpns_admin_email') : "";		
		include $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'account'.DIRECTORY_SEPARATOR.'register.php';
	} 
	else
	{
		$email = get_option('mo_wpns_admin_email');
		$key   = get_option('mo_wpns_admin_customer_key');
		$api   = get_option('mo_wpns_admin_api_key');
		$token = get_option('mo_wpns_customer_token');
		include $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'account'.DIRECTORY_SEPARATOR.'profile.php';
	}

	//Function to register new customer
	function mo_mmp_register_customer($post)
	{
		//validate and sanitize
		global $Mo_wpUtility;
		$email 			 = sanitize_email($post['email']);
		$password 		 = sanitize_text_field($post['password']);
		$confirmPassword = sanitize_text_field($post['confirmPassword']);
		if( strlen( $password ) < 6 || strlen( $confirmPassword ) < 6)
		{
			do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('PASS_LENGTH'),'ERROR');
			return;
		}	
		if( $password != $confirmPassword )
		{
			do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('PASS_MISMATCH'),'ERROR');
			return;
		}
		if( $Mo_wpUtility->check_empty_or_null( $email ) || $Mo_wpUtility->check_empty_or_null( $password ) 
			|| $Mo_wpUtility->check_empty_or_null( $confirmPassword ) ) 
		{
			do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('REQUIRED_FIELDS'),'ERROR');
			return;
		} 
		update_option( 'mo_wpns_admin_email', $email );
		update_option( 'mo_wpns_password'   , $password );
		$customer = new Mo_wp_MocURL();
		$content  = $customer->check_customer($email);
		if(!is_array($content))
		$content=json_decode($content,true);	
		switch ($content['status'])
		{
			case 'CUSTOMER_NOT_FOUND':
				mo_mmp_create_customer($email,"",$password);
				break;
			default:
				mo_mmp_get_current_customer($email,$password);
				break;
		}
	}
	function mo_mmp_create_customer($email, $company, $password)
	{
			$customer = new Mo_wp_MocURL();
			$customerKey = json_decode($customer->create_customer($email, $company, $password, $phone = '', $first_name = '', $last_name = ''), true);

			if(strcasecmp($customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0) 
			{	
				mo_mmp_get_current_customer($email,$password);
			} 
			else if(strcasecmp($customerKey['status'], 'SUCCESS') == 0) 
			{
				mo_mmp_save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']);
				do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('REG_SUCCESS'),'SUCCESS');
			}else{
				do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('UNKNOWN_ERROR'),'ERROR');
			}
	}

	//Function to go back to the registration page
	function mo_mmp_revert_back_registration()
	{
		delete_option('mo_wpns_admin_email');
		delete_option('mo_wpns_registration_status');
		delete_option('mo_wpns_verify_customer');
	}
	//Function to reset customer's password
	function mo_mmp_reset_password()
	{
		$customer = new Mo_wp_MocURL();
		$forgot_password_response = json_decode($customer->mo_wpns_forgot_password());
		if($forgot_password_response->status == 'SUCCESS')
			do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('RESET_PASS'),'SUCCESS');
	}
	//Function to verify customer
	function mo_mmp_verify_customer($post)
	{
		global $Mo_wpUtility;
		$email 	  = sanitize_email( $post['email'] );
		$password = sanitize_text_field( $post['password'] );

		if( $Mo_wpUtility->check_empty_or_null( $email ) || $Mo_wpUtility->check_empty_or_null( $password ) ) 
		{
			do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('REQUIRED_FIELDS'),'ERROR');
			return;
		} 
		mo_mmp_get_current_customer($email,$password);
	}
	//Function to get customer details
	function mo_mmp_get_current_customer($email,$password)
	{
		$customer 	 = new Mo_wp_MocURL();
		$content     = $customer->get_customer_key($email,$password);
		$customerKey = json_decode($content, true);
		if(json_last_error() == JSON_ERROR_NONE) 
		{
			update_option( 'mo_wpns_admin_phone', $customerKey['phone'] );
			update_option( 'mo_wpns_admin_email', $email );
			mo_mmp_save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']);
			do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('REG_SUCCESS'),'SUCCESS');
		} 
		else 
		{
			update_option('mo_wpns_verify_customer', 'true');
			delete_option('mo_wpns_new_registration');
			do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('ACCOUNT_EXISTS'),'ERROR');
		}
	}
	
		
	//Save all required fields on customer registration/retrieval complete.
	function mo_mmp_save_success_customer_config($id, $apiKey, $token, $appSecret)
	{
		update_option( 'mo_wpns_admin_customer_key'  , $id 		  );
		update_option( 'mo_wpns_admin_api_key'       , $apiKey    );
		update_option( 'mo_wpns_customer_token'		 , $token 	  );
		update_option( 'mo_wpns_app_secret'			 , $appSecret );
		update_option( 'mo_wpns_enable_log_requests' , true 	  );
		update_option( 'mo_wpns_password'			 , ''		  );
		delete_option( 'mo_wpns_verify_customer'				  );
		delete_option( 'mo_wpns_registration_status'			  );
		delete_option( 'mo_wpns_password'						  );
	}

	function mo_wpns_log_out($POSTED){

		$nonce = $POSTED['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'mo-wpns-log-out' ) ){
			wp_send_json('ERROR');
			do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('ERR_ACCOUNT_LOGOUT'),'ERROR');
			return;
		}

		delete_option( 'mo_wpns_admin_customer_key'  );
		delete_option( 'mo_wpns_admin_api_key'       );
		delete_option( 'mo_wpns_customer_token'		 );
		delete_option( 'mo_wpns_app_secret'			 );
		delete_option( 'mo_wpns_enable_log_requests' );
		delete_option( 'mo_wpns_password'			 );
		delete_option('mo_wpns_admin_email'			);
		delete_option('mo_wpns_admin_phone'			);
		do_action('mo_mmp_show_message',Mo_wp_Messages::showMessage('SUCCESS_ACCOUNT_LOGOUT'),'SUCCESS');
	}