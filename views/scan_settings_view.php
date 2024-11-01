<?php
	add_action('admin_footer','mo_malware_config_page_submit_wpns');
	$mo_mmp_scan_files_extensions = get_option('mo_wpns_scan_files_extensions');
	if($mo_mmp_scan_files_extensions == false)
		$mo_mmp_scan_files_extensions = "php";
	$mo_mmp_skip_folders = get_option('mo_wpns_skip_folders');
	$mo_mmp_skip_folders_array = array();
	if(!empty($mo_mmp_skip_folders)){
		$mo_mmp_skip_folders_array = explode(";",$mo_mmp_skip_folders);
	}
	$mo_mmp_white_url = get_option('mo_wpns_white_url');
	$mo_mmp_white_urls_array = array();
	if(!empty($mo_mmp_white_url)){
		$mo_mmp_white_urls_array = explode(";",$mo_mmp_white_url);
	}
	$mo_mmp_custom_sign = get_option('mo_wpns_custom_sign');
	$mo_mmp_custom_sign_array = array();
	if(!empty($mo_mmp_custom_sign)){
		$mo_mmp_custom_sign_array = explode(";",$mo_mmp_custom_sign);
	}
?>
<div class="mo_wpns_setting_layout">		
	<div class="mo_wpns_subheading"></div>
	<br>
	<form id="" method="post" action="">
		<input type="hidden" name="option" value="mo_mmp_scan_configuration">
		<table class="mo_wpns_settings_table">
		<tr>
			<td style="width:30%"><b>Select Folders to Scan : </b></td>
			<td>
			<input type="checkbox" name="mo_mmp_scan_plugins" id="mo_mmp_scan_plugins" value="1" <?php checked(get_option('mo_wpns_scan_plugins') == 1);?>> WordPress Plugins folder<br>
			<input type="checkbox" name="mo_mmp_scan_themes" id="mo_mmp_scan_themes" value="1" <?php checked(get_option('mo_wpns_scan_themes') == 1);?>> WordPress Themes folder<br>
			<input type="checkbox" name="mo_mmp_scan_wp_files" id="mo_mmp_scan_wp_files" value="1" <?php checked(get_option('mo_wpns_scan_wp_files') == 1);?>> WordPress files
			</td>
		</tr>
		<tr><td>&nbsp;</td><td></td></tr>
		<tr>
			<td style="width:30%"><b>Select Type of files to scan : </b></td>
			<td><input class="mo_wpns_table_textbox" type="text" id="mo_mmp_scan_files_extensions" name="mo_mmp_scan_files_extensions" required placeholder="comma separated file extensions e.g. php,inc" value="<?php echo esc_html($mo_mmp_scan_files_extensions)?>" /></td>
		</tr>
		<tr><td>&nbsp;</td><td></td></tr>
		<tr>
			<td style="width:30%"><b>Select Scan Level : </b></td>
			<td>
			<input type="checkbox" name="mo_mmp_check_vulnerable_code" id="mo_mmp_check_vulnerable_code" value="1" <?php checked(get_option('mo_wpns_check_vulnerable_code') == 1);?>> <b>Check PHP files vulnerable code <span class="mo_green">( Highly Recommeded )</span></b><br>
			Checks if your website has a code which is kept hidden or obfuscated to harm your website.<br><br>
			<input type="checkbox" name="mo_mmp_check_sql_injection" id="mo_mmp_check_sql_injection" value="1" <?php checked(get_option('mo_wpns_check_sql_injection') == 1);?>> <b>SQL Injection and injected shell script check <span class="mo_green">( Highly Recommeded )</span></b><br>
			Checks for injected SQL queries which can harm your database and injected shell scripts which can harm your server by executing any commands.<br><br>
			<input type="checkbox" name="mo_mmp_check_external_link" id="mo_mmp_check_external_link" value="1" <?php checked(get_option('mo_wpns_check_external_link') == 1);?>> <b>External Links Detection</b><br>
			Checks if anyone creating backlinks from your website. Backlinks to blacklisted sites can add your website to spam websites list.<br><br>
			<input type="checkbox" name="mo_mmp_scan_files_with_repo" id="mo_mmp_scan_files_with_repo" value="1" <?php checked(get_option('mo_wpns_scan_files_with_repo') == 1);?>> <b>Check Files with repository</b><br>
			Check the Wordpress, plugin and theme files with its repository. It is helpful to determine if extra files added to or missing any of repository files.<br><br>

			<input type="checkbox" name="mo_mmp_adv_sign" id="mo_mmp_adv_sign" value="1" <?php checked(get_option('mo_wpns_adv_sign') == 1);?>> <b>Use Advanced Signatures For Malware Detection.</b><b class="mo_red"> (Deep Scan) </b><br>
			Advanced Signatures help to scan your website better. miniOrange has it own premium signatures used to detect more advanced malwares in the files.<br><br>
			<input type="checkbox" name="mo_mmp_check_remote_file_inclusion" id="mo_mmp_check_remote_file_inclusion" value="1" <?php checked(get_option('mo_wpns_check_remote_file_inclusion') == 1);?>> <b>Remote File Inclusion</b><b class="mo_red"> (Deep Scan) </b><br>
			Inclusion of remote files can be harmful as code return in remote files will be executed on your server.<br><br>
			<input type="checkbox" name="mo_mmp_check_domain" id="mo_mmp_check_domain" value="1" <?php checked(get_option('mo_wpns_check_domain') == 1);?>> <b>Check For Blacklisted Domains.</b><b class="mo_red"> (Deep Scan) </b><br>
			Checks for links to Blacklisted Domains so that your site does not get a bad reputation.<br><br>
			<input type="checkbox" name="mo_mmp_trojan_check" id="mo_mmp_trojan_check" value="1" <?php checked(get_option('mo_wpns_check_trojan') == 1);?>> <b>Check For Trojans.</b><b class="mo_red"> (Deep Scan) </b><br>
			Checks for presence of Trojans in your system. It looks like a normal file but can help the attacker gain remote access to your system.<br><br>
			<input type="checkbox" name="mo_mmp_backdoor_check" id="mo_mmp_backdoor_check" value="1" <?php checked(get_option('mo_wpns_check_backdoor') == 1);?>> <b>Check For Backdoors.</b><b class="mo_red"> (Deep Scan) </b><br>
			Checks for presence of Backdoors in your code. A backdoor is a malware type that dodges the authentication process to gain remote access.
			</td>
		</tr>
		<tr><td>&nbsp;</td><td></td></tr>
		<tr>
			<td style="width:30%"><b>Skip folders with paths : </b></td>
			<td>
			<table style="width:100%" id="skip_folders">
				<?php for($i=0;$i<count($mo_mmp_skip_folders_array);$i++){ ?>
					<tr><td><input type="text" name="mo_mmp_skip_folders_<?php echo esc_html($i+1);?>" id="mo_mmp_skip_scan_folder" class="mo_wpns_table_textbox" placeholder="comma separated folders full path" style="width:100%;" value="<?php echo esc_html($mo_mmp_skip_folders_array[$i]);?>" /></td></tr>
				<?php }
					if($i==0){ ?>
						<tr><td><input type="text" name="mo_mmp_skip_folders_<?php echo esc_html($i+1);?>" id="mo_mmp_skip_scan_folder" class="mo_wpns_table_textbox" placeholder="comma separated folders full path" style="width:100%;" value="" /></td></tr>
					<?php }
				?>
			</table>
			<a style="cursor:pointer" onclick="mo_add_skip_folders();">Add More Folders</a>
			</td>
		</tr>
		<tr><td>&nbsp;</td><td></td></tr>
		<tr>
			<td style="width:30%"><b>Whitelist URLs : </b></td>
			<td>
			<table style="width:100%" id="white_url">
				<?php for($i=0;$i<count($mo_mmp_white_urls_array);$i++){ ?>
					<tr><td><input type="text" name="mo_mmp_white_url_<?php echo esc_html($i+1);?>" id="mo_mmp_url_white" class="mo_wpns_table_textbox" placeholder="enter URLs to be whitelisted" style="width:100%;" value="<?php echo esc_html($mo_mmp_white_urls_array[$i]);?>" disabled /></td></tr>
				<?php }
					if($i==0){ ?>
						<tr><td><input type="text" name="mo_mmp_white_url_<?php echo esc_html($i+1);?>" id="mo_mmp_url_white" class="mo_wpns_table_textbox" placeholder="enter URLs to be whitelisted" style="width:100%;" value="" disabled /></td></tr>
					<?php }
				?>
			</table>
			<a style="cursor:pointer" onclick="mo_add_white_url();">Add More URLs</a>
			</td>
		</tr>
		<tr><td>&nbsp;</td><td></td></tr>
		<tr>
			<td style="width:30%"><b>Custom Signatures : </b></td>
			<td>
			<table style="width:100%" id="sign_custom">
				<?php for($i=0;$i<count($mo_mmp_custom_sign_array);$i++){ ?>
					<tr><td><input type="text" name="mo_mmp_custom_sign_<?php echo esc_html($i+1);?>" id="mo_mmp_sign_custom" class="mo_wpns_table_textbox" placeholder="enter string or code to be added as custom signature" style="width:100%;" value="<?php echo esc_html($mo_mmp_custom_sign_array[$i]);?>" disabled /></td></tr>
				<?php }
					if($i==0){ ?>
						<tr><td><input type="text" name="mo_mmp_custom_sign_<?php echo esc_html(i+1);?>" id="mo_mmp_sign_custom" class="mo_wpns_table_textbox" placeholder="enter string or code to be added as custom signature" style="width:100%;" value="" disabled /></td></tr>
					<?php }
				?>
			</table>
			<a style="cursor:pointer" onclick="mo_add_custom_sign();">Add More Signatures</a>
			</td>
		</tr>
		<tr>
		<td></td><td><br><input type="button" name="Save_malware_config" id="Save_malware_config" style="width:100px;" value="Save" class="mo_wpns_scan_button"> </td>
		</tr>
		</table>

	</form>
</div>
<?php

function mo_malware_config_page_submit_wpns(){
	if ( ('admin.php' != basename( $_SERVER['PHP_SELF'] )) || ($_GET['page'] != 'mo_wpns_malwarescan') ) {
        return;
    }
?>
<script>
jQuery(document).ready(function(){
	jQuery('#Save_malware_config').click(function(){
		var data={
			'action':'mo_wpns_malware_redirect',
			'call_type':'submit_malware_settings_form',
			'scan_plugin':jQuery('input[name= "mo_mmp_scan_plugins"]:checked').val(),
			'scan_themes':jQuery('input[name= "mo_mmp_scan_themes"]:checked').val(),
			'scan_core':jQuery('input[name= "mo_mmp_scan_wp_files"]:checked').val(),
			'file_type':jQuery('#mo_mmp_scan_files_extensions').val(),
			'vulnerable_check':jQuery('input[name= "mo_mmp_check_vulnerable_code"]:checked').val(),
			'sql_check':jQuery('input[name= "mo_mmp_check_sql_injection"]:checked').val(),
			'ext_link':jQuery('input[name= "mo_mmp_check_external_link"]:checked').val(),
			'repo_check':jQuery('input[name= "mo_mmp_scan_files_with_repo"]:checked').val(),
			'skip_path':jQuery('#mo_mmp_skip_scan_folder').val(),
		};
		jQuery.post(ajaxurl, data, function(response){
			jQuery("#mo_scan_message").empty();
			jQuery("#mo_scan_message").hide();
			jQuery('#mo_scan_message').show();
			if (response == "folder_error"){
			jQuery('#mo_scan_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >Please select atleast one folder to scan</div>");
			window.scrollTo({ top: 0, behavior: 'smooth' });
			}
			else if(response == "level_error"){
				jQuery('#mo_scan_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;' >Please select atleast one scan level.</div>");
				window.scrollTo({ top: 0, behavior: 'smooth'});
			}
			else{
				jQuery('#mo_scan_message').append("<div class= 'notice notice-success is-dismissible' style='height : 25px;padding-top: 10px;  ' >Scan Configuration Saved Successfully</div>");
				window.scrollTo({ top: 0, behavior: 'smooth' });
			}
		});
	});
	});
</script>
<?php
}
?>
<script>
	function mo_add_skip_folders(){
		var last_index_name = jQuery('#skip_folders tr:last .mo_wpns_table_textbox').attr('name');
		var splittedArray = last_index_name.split("_");
		var countAttributes = parseInt(splittedArray[splittedArray.length-1])+1;
		jQuery("<tr><td><input type='text' name='mo_mmp_skip_folders_"+countAttributes+"' class='mo_wpns_table_textbox' placeholder='comma separated folders full path' style='width:100%;' /></td></tr>").insertAfter(jQuery('#skip_folders tr:last'));
	}
	function mo_add_white_url(){
		var last_index_name = jQuery('#white_url tr:last .mo_wpns_table_textbox').attr('name');
		var splittedArray = last_index_name.split("_");
		var countAttributes = parseInt(splittedArray[splittedArray.length-1])+1;
		jQuery("<tr><td><input type='text' name='mo_mmp_white_url_"+countAttributes+"' class='mo_wpns_table_textbox' placeholder='enter URLs to be whitelisted' style='width:100%;' disabled/></td></tr>").insertAfter(jQuery('#white_url tr:last'));
	}
	function mo_add_custom_sign(){
		var last_index_name = jQuery('#sign_custom tr:last .mo_wpns_table_textbox').attr('name');
		var splittedArray = last_index_name.split("_");
		var countAttributes = parseInt(splittedArray[splittedArray.length-1])+1;
		jQuery("<tr><td><input type='text' name='mo_mmp_custom_sign_"+countAttributes+"' class='mo_wpns_table_textbox' placeholder='enter string or code to be added as custom signature' style='width:100%;' disabled/></td></tr>").insertAfter(jQuery('#sign_custom tr:last'));
	}
</script>
