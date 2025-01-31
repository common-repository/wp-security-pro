<?php
class Mo_wpHandler
{
	function is_ip_blocked($ipAddress)
	{
		global $wpnsDbQueries;
		if(empty($ipAddress))
			return false;
		$user_count = $wpnsDbQueries->get_ip_blocked_count($ipAddress);
		if($user_count)
			$user_count = intval($user_count);
		if($user_count>0)
			return true;
		
		return false;
	}
	function get_blocked_attacks_count($attackName)
	{
		global $wpnsDbQueries;
		$attackCount = $wpnsDbQueries->get_blocked_attack_count($attackName);
		if($attackCount)
			$attackCount =  intval($attackCount);
		return $attackCount;
	}
	function get_blocked_countries()
	{
		$countrycodes 	= get_option('mo_wpns_countrycodes');
		$countries 		= explode(';', $countrycodes);
		return sizeof($countries)-1;
	}
	function get_blocked_ip_waf()
	{
		global $wpnsDbQueries;
		$ip_count = $wpnsDbQueries->get_total_blocked_ips_waf();
		if($ip_count)
			$ip_count = intval($ip_count);
		return $ip_count;
	}
	function get_manual_blocked_ip_count()
	{
		global $wpnsDbQueries;
		$ip_count = $wpnsDbQueries->get_total_manual_blocked_ips();
		if($ip_count)
			$ip_count = intval($ip_count);

		return $ip_count;
	}
	function get_blocked_ips()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_blocked_ip_list();
	}
	function get_blocked_sqli()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_blocked_sqli_list();
	}
	function get_blocked_rfi()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_blocked_rfi_list();	
	}
	function get_blocked_lfi()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_blocked_lfi_list();
	}
	function get_blocked_rce()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_blocked_rce_list();
	}
	function get_blocked_xss()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_blocked_xss_list();	
	}
	
	function block_ip($ipAddress, $reason, $permenently)
	{
		global $wpnsDbQueries;
		if(empty($ipAddress))
			return;
		if($this->is_ip_blocked($ipAddress))
			return;
		$blocked_for_time = null;
		if(!$permenently && get_option('mo_wpns_time_of_blocking_type'))
		{
			$blocking_type = get_option('mo_wpns_time_of_blocking_type');
			$time_of_blocking_val = 3;
			if(get_option('mo_wpns_time_of_blocking_val'))
				$time_of_blocking_val = get_option('mo_wpns_time_of_blocking_val');
			if($blocking_type=="months")
				$blocked_for_time = current_time( 'timestamp' )+$time_of_blocking_val * 30 * 24 * 60 * 60;
			else if($blocking_type=="days")
				$blocked_for_time = current_time( 'timestamp' )+$time_of_blocking_val * 24 * 60 * 60;
			else if($blocking_type=="hours")
				$blocked_for_time = current_time( 'timestamp' )+$time_of_blocking_val * 60 * 60;
		}
		
		$wpnsDbQueries->insert_blocked_ip($ipAddress, $reason,$blocked_for_time);
		
		//send notification
		global $Mo_wpUtility;
		if(get_option('mo_wpns_enable_ip_blocked_email_to_admin'))
			$Mo_wpUtility->sendIpBlockedNotification($ipAddress,Mo_wp_Constants::LOGIN_ATTEMPTS_EXCEEDED);
			
	}
	
	function unblock_ip_entry($entryid)
	{
		global $wpnsDbQueries;
		
		$wpnsDbQueries->delete_blocked_ip($entryid);
	}
	
	function is_whitelisted($ipAddress)
	{
		global $wpnsDbQueries;
		$count = $wpnsDbQueries->get_whitelisted_ip_count($ipAddress);

		if(empty($ipAddress))
			return false;
		if($count)
			$count = intval($count);

		if($count>0)
			return true;
		return false;
	}
	
	function whitelist_ip($ipAddress)
	{
		global $wpnsDbQueries;	
		if(empty($ipAddress))
			return;
		if($this->is_whitelisted($ipAddress))
			return;

		$wpnsDbQueries->insert_whitelisted_ip($ipAddress);
	}

	function remove_whitelist_entry($entryid)
	{
		global $wpnsDbQueries;
		$wpnsDbQueries->delete_whitelisted_ip($entryid);
	}
	
	function get_whitelisted_ips()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_whitelisted_ips_list();
	}
	
	function is_email_sent_to_user($username, $ipAddress)
	{
		global $wpnsDbQueries;
		if(empty($ipAddress))
			return false;
		$sent_count = $wpnsDbQueries->get_email_audit_count($ipAddress,$username);
		if($sent_count)
			$sent_count = intval($sent_count);
		if($sent_count>0)
			return true;
		return false;
	}
	
	function audit_email_notification_sent_to_user($username, $ipAddress, $reason)
	{
		if(empty($ipAddress) || empty($username))
			return;
		global $wpnsDbQueries;
		$wpnsDbQueries->insert_email_audit($ipAddress,$username,$reason);
	}
	
	function add_transactions($ipAddress, $username, $type, $status, $url=null)
	{
		global $wpnsDbQueries;
		$wpnsDbQueries->insert_transaction_audit($ipAddress, $username, $type, $status, $url);
	}

	function get_login_transaction_report()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_login_transaction_report();
	}
	
	function get_error_transaction_report()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_error_transaction_report();
	}


	function get_all_transactions()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_transasction_list();
	}
	
	function move_failed_transactions_to_past_failed($ipAddress)
	{
		global $wpnsDbQueries;
		$wpnsDbQueries->update_transaction_table(array('status'=>Mo_wp_Constants::FAILED),
			array('ip_address'=>$ipAddress,'status'=>Mo_wp_Constants::PAST_FAILED));
	}
	
	function remove_failed_transactions($ipAddress)
	{
		global $wpnsDbQueries;
		$wpnsDbQueries->delete_transaction($ipAddress);	
	}
	
	function get_failed_attempts_count($ipAddress)
	{
		global $wpnsDbQueries;
		$count = $wpnsDbQueries->get_failed_transaction_count($ipAddress);
		if($count)
		{
			$count = intval($count);
			return $count;
		}
		return 0;
	}
	
	function is_ip_blocked_in_anyway($userIp)
	{
		$isBlocked = false;
		if($this->is_ip_blocked($userIp))
			$isBlocked = true;
		else if($this->is_ip_range_blocked($userIp))
			$isBlocked = true;
		else if($this->is_browser_blocked())
			$isBlocked = true;
		else if($this->is_country_blocked($userIp))
			$isBlocked = true;
		else if($this->is_referer_blocked())
			$isBlocked = true;

		return $isBlocked;
	}

	function is_ip_range_blocked($userIp)
	{
		if(empty($userIp))
			return false;
		$range_count = 0;
		if(is_numeric(get_option('mo_wpns_iprange_count')))
			$range_count = intval(get_option('mo_wpns_iprange_count'));
		for($i = 1 ; $i <= $range_count ; $i++){ 
			$blockedrange  = get_option('mo_wpns_iprange_range_'.$i);
			$rangearray = explode("-",$blockedrange);
			if(sizeof($rangearray)==2){
				$lowip = ip2long(trim($rangearray[0]));
				$highip = ip2long(trim($rangearray[1]));
				if(ip2long($userIp)>=$lowip && ip2long($userIp)<=$highip){
					$mo_wpns_config = new Mo_wpHandler();
					$mo_wpns_config->block_ip($userIp, Mo_wp_Constants::IP_RANGE_BLOCKING, true);
					return true;
				}
			}
		}
		return false;
	}
	
	
	function is_browser_blocked()
	{
		global $Mo_wpUtility;
		if(get_option( 'mo_wpns_enable_user_agent_blocking'))
		{			
			$current_browser = $Mo_wpUtility->getCurrentBrowser();
			if(get_option('mo_wpns_block_chrome') && $current_browser=='chrome')
				return true;
			else if(get_option('mo_wpns_block_firefox') && $current_browser=='firefox')
				return true;
			else if(get_option('mo_wpns_block_ie') && $current_browser=='ie')
				return true;
			else if(get_option('mo_wpns_block_opera') && $current_browser=='opera')
				return true;
			else if(get_option('mo_wpns_block_safari')&& $current_browser=='safari')
				return true;
			else if(get_option('mo_wpns_block_edge') && $current_browser=='edge')
				return true;
		}
		return false;
	}
	
	
	function is_country_blocked($userIp)
	{			
		$countrycodes = get_option('mo_wpns_countrycodes');
		if($countrycodes && !empty($countrycodes)){
			$ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$userIp));    
			if($ip_data && $ip_data->geoplugin_countryName != null){
				$country_code = $ip_data->geoplugin_countryCode;
				if(!empty($country_code)){
					$countrycodes = get_option('mo_wpns_countrycodes');
					$codes = explode(";", $countrycodes);
					foreach($codes as $code){
						if(!empty($code) && strcasecmp($code,$country_code)==0)
							return true;
					}
				}
			}
		}
		return false;
	}


	function is_referer_blocked()
	{
		if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) && get_option('mo_wpns_referrers')){
			$userreferer = sanitize_text_field($_SERVER['HTTP_REFERER']);
			$referrers = explode(";",get_option('mo_wpns_referrers'));
			foreach($referrers as $referrer){
				if(!empty($referrer) && strpos(strtolower($userreferer), strtolower($referrer)) !== false){
					return true;
				}
			}
		}
		return false;
	}

	public static function random_str( $length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ) {
		$randomString     = '';
		$charactersLength = strlen( $keyspace );
		$keyspace         = $keyspace . microtime( true );
		$keyspace         = str_shuffle( $keyspace );
		for ( $i = 0; $i < $length; $i ++ ) {
			$randomString .= $keyspace[ rand( 0, $charactersLength - 1 ) ];
		}

		return $randomString;

	}
	
} ?>