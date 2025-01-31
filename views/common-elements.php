<?php
	function Mo_wp_is_customer_valid()
	{
		global $Mo_wpUtility;
		$url 	=	add_query_arg( array('page' => 'mo_mmp_account'), sanitize_text_field($_SERVER['REQUEST_URI']) );
		if (!$Mo_wpUtility->icr())
			echo '<div class="warning_div">Please <a href="'.esc_url($url).'">Register or Login with miniOrange</a> to configure the WP Security Pro Plugin.</div>';
	}
	//Function to show Login Transactions
	function Mo_wp_showLoginTransactions($usertranscations)
	{
		 foreach($usertranscations as $usertranscation)
        {
        		echo "<tr><td>".esc_attr($usertranscation->ip_address)."</td><td>".esc_attr($usertranscation->username)."</td><td>";
				if($usertranscation->status==Mo_wp_Constants::FAILED || $usertranscation->status==Mo_wp_Constants::PAST_FAILED)
					echo "<span style=color:red>".esc_html(Mo_wp_Constants::FAILED)."</span>";
				else if($usertranscation->status==Mo_wp_Constants::SUCCESS)
					echo "<span style=color:green>".esc_html(Mo_wp_Constants::SUCCESS)."</span>";
				else
					echo "N/A";
				echo "</td><td>".esc_attr(gmdate("M j, Y, g:i:s a",$usertranscation->created_timestamp))."</td></tr>";
		}
	}


	//Function to show 404 and 403 Reports
	function Mo_wp_showErrorTransactions($usertransactions)
	{
		foreach($usertransactions as $usertranscation)
        {
    		echo "<tr><td>".esc_attr($usertranscation->ip_address)."</td><td>".esc_attr($usertranscation->username)."</td>";
			echo "<td>".esc_url($usertranscation->url)."</td><td>".esc_attr($usertranscation->type)."</td>";
			echo "</td><td>".esc_attr(date("M j, Y, g:i:s a",$usertranscation->created_timestamp))."</td></tr>";
		}
	}


	//Function to show google recaptcha form
	function Mo_wp_show_google_recaptcha_form()
	{
		echo'
			<link rel="stylesheet" type="text/css" media="all" href="'.esc_url(site_url()).'/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load%5B%5D=dashicons,admin-bar,common,forms,admin-menu,dashboard,list-tables,edit,revisions,media,themes,about,nav-menus,widgets,site-icon,&amp;load%5B%5D=l10n,buttons,wp-auth-check&amp;ver=4.5.2"/>
			<style> .button.button-large { height: 30px; line-height: 28px; padding: 0 12px 2px; } .button-primary { background: #0085ba; border-color: #0073aa #006799 #006799; -webkit-box-shadow: 0 1px 0 #006799; box-shadow: 0 1px 0 #006799; color: #fff; text-decoration: none; text-shadow: 0 -1px 1px #006799,1px 0 1px #006799,0 1px 1px #006799,-1px 0 1px #006799; border-radius: 3px; cursor: pointer; border-width: 1px; border-style: solid; font-size: 15px; width: 300px; } </style>
			<script src="'.esc_url(Mo_wp_Constants::RECAPTCHA_URL).'"></script>
			<div style="font-family:\'Open Sans\',sans-serif;margin:0px auto;width:303px;text-align:center;">
				<br><br><h2>Test google reCAPTCHA keys</h2>
				<form method="post">
					<div class="g-recaptcha" data-sitekey="'.esc_attr (get_option('mo_wpns_recaptcha_site_key')).'"></div>
					<br><input class="button button-primary button-large" type="submit" value="Test Keys" class="button button-primary button-large">
				</form>
			</div>';
		exit();
	}