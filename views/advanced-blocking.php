<?php
global $mmp_dirName;
$setup_dirName = $mmp_dirName.'helper'.DIRECTORY_SEPARATOR.'link_tracers.php';
include $setup_dirName;
?>
<?php

echo'<div class="mo_wpns_divided_layout">
	 <div class="mo_wpns_setting_layout">';

echo'		<h2>IP Address Range Blocking <a href='.esc_url($two_factor_premium_docfile['IP Address Range Blocking']).' target="_blank"><span class="dashicons dashicons-external mo_wpns_doc_link" title="More information.."></span></a></h2>
			You can block range of IP addresses here  ( Examples: 192.168.0.100 - 192.168.0.190 )
			<form name="f" method="post" action="" id="iprangeblockingform" >
				<input type="hidden" name="option" value="mo_wpns_block_ip_range" />
				<table id="iprangeblockingtable">';
				
					for($i = 1 ; $i <= $range_count ; $i++)
echo'					<tr><td style="width:300px"><input style="padding:0px 10px"  class="mo_wpns_table_textbox" type="text" name="range_'.esc_attr($i).'"
							 value="'.esc_attr(get_option("mo_wpns_iprange_range_".$i)).'"  placeholder=" e.g 192.168.0.100 - 192.168.0.190" /></td></tr>';

echo'			</table>
				<a style="cursor:pointer" id="add_range">Add More Range</a><br><br>
				<input type="submit" class="button button-primary button-large" value="Block IP range" />
			</form>
		</div>
		
		<div class="mo_wpns_setting_layout">
			<h3>htaccess level blocking <strong class="mo_wpns_premium_feature"><a href="admin.php?page=mo_mmp_upgrade"> [Premium Feature] </a></strong></h3>
			<p>It help you secure your website from unintended user with htaccess website security protection which blocks user request on server(apache) level before reaching your WordPress and saves lots of load on server.</p>

			<form id="mo_wpns_enable_htaccess_blocking" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_enable_htaccess_blocking">
				<b style="padding-right:10px;">Enable htaccess level security</b>
				<label class="mo_wpns_switch_small">
				<input type="checkbox" name="mo_wpns_enable_htaccess_blocking" '.esc_attr($htaccess_block).' disabled >
				<span class="mo_wpns_slider_small mo_wpns_round_small"></span>
				</label>
			</form>
			<br>
		</div>
		
		
		<div class="mo_wpns_setting_layout">
			<h3>Browser Blocking <strong class="mo_wpns_premium_feature"><a href="admin.php?page=mo_mmp_upgrade"> [Premium Feature] </a></strong></h3>
			<!-- <div class="mo_wpns_subheading">This protects your site from robots and other automated scripts.</div> -->
			<form id="mo_wpns_enable_user_agent_blocking" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_enable_user_agent_blocking">
				<b style="padding-right:10px;">Enable Browser Blocking</b>
				<label class="mo_wpns_switch_small">
				<input type="checkbox" name="mo_wpns_enable_user_agent_blocking" '.esc_attr($user_agent).' disabled>
				<span class="mo_wpns_slider_small mo_wpns_round_small"></span>
				</label>
			</form><br>
			<div style="margin-bottom:10px">Select browsers below to block</div>
			<form id="mo_wpns_browser_blocking" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_browser_blocking">
				<table style="width:100%">
				<tr>
					<td width="33%"><input type="checkbox" name="mo_wpns_block_chrome" '.esc_html($block_chrome).' > Google Chrome '.($current_browser=='chrome' ? Mo_wp_Constants::CURRENT_BROWSER : "").'</td>
					<td width="33%"><input type="checkbox" name="mo_wpns_block_firefox" '.esc_html($block_firefox).' > Firefox '.($current_browser=='firefox' ? Mo_wp_Constants::CURRENT_BROWSER : "").'</td>
					<td width="33%"><input type="checkbox" name="mo_wpns_block_ie" '.$block_ie.' > Internet Explorer '.($current_browser=='ie' ? Mo_wp_Constants::CURRENT_BROWSER : "").'</td>
				</tr>
				<tr>
					<td width="33%"><input type="checkbox" name="mo_wpns_block_safari" '.esc_html($block_safari).' > Safari '.($current_browser=='safari' ? Mo_wp_Constants::CURRENT_BROWSER : "").'</td>
					<td width="33%"><input type="checkbox" name="mo_wpns_block_opera" '.esc_html($block_opera).' > Opera '.($current_browser=='opera' ? Mo_wp_Constants::CURRENT_BROWSER : "").'</td>
					<td width="33%"><input type="checkbox" name="mo_wpns_block_edge" '.esc_html($block_edge).' > Microsoft Edge '.($current_browser=='edge' ? Mo_wp_Constants::CURRENT_BROWSER : "").'</td>
				</tr>
				</table>
				<br>
				
			</form>
			<br>
		</div> 

		<div class="mo_wpns_setting_layout">
		<h3>Block HTTP Referer\'s <strong class="mo_wpns_premium_feature"><a href="admin.php?page=mo_mmp_upgrade"> [Premium Feature] </a></strong></h3>
			An "HTTP Referer" is anything online that drives visitors to your website which includes search engines, weblogs link lists, emails etc.<br>
			Examples : google.com
			<form name="f" method="post" action="" id="referrerblockingform" >
				<input type="hidden" name="option" value="mo_wpns_block_referrer" />
				<table id="referrerblockingtable">';

					$count=1;
					foreach($referrers as $referrer)
					{
						echo '<tr><td style="width:300px"><input style="padding:0px 10px" class="mo_wpns_table_textbox" type="text" name="referrer_'.esc_attr($count).'"
						 value="'.esc_url($referrer).'"  placeholder=" e.g  google.com" /></td></tr>';
						$count++;
					}

echo'			</table>
				<a style="cursor:pointer" id="add_referer">Add More Referer\'s</a><br><br>
				
			</form>
			<br>
		</div> 
		
		<div class="mo_wpns_setting_layout">
			<h2>Country Blocking <strong class="mo_wpns_premium_feature"><a href="admin.php?page=mo_mmp_upgrade"> [Premium Feature] </a></strong></h2>
			<b>Select countries from below which you want to block.</b><br><br>
			<form name="f" method="post" action="" id="countryblockingform" >
				<input type="hidden" name="option" value="mo_wpns_block_countries" />
				<table id="countryblockingtable" style="width:100%">';
						
						foreach($country as $key => $value)
							echo '<tr class="one-third"><td><input type="checkbox" name="'.esc_attr($key).'"/ >'.esc_attr($value).'</td></tr>';

echo'			</table><br>
				
			</form>
		</div>
	</div>
	<script>		
		jQuery( document ).ready(function() {
			var countrycodes = "'.esc_attr($codes).'";
			var countrycodesarray = countrycodes.split(";");
			for (i = 0; i < countrycodesarray.length; i++) { 
				if(countrycodesarray[i]!="")
					$("#countryblockingform :input[name=\'"+countrycodesarray[i]+"\']").prop("checked", true);
			}

			$("#add_range").click(function() {
				var last_index_name = $("#iprangeblockingtable tr:last .mo_wpns_table_textbox").attr("name");
				var splittedArray = last_index_name.split("_");
				var last_index = parseInt(splittedArray[splittedArray.length-1])+1;
				var new_row = \'<tr><td><input style="padding:0px 10px" class="mo_wpns_table_textbox" type="text" name="range_\'+last_index+\'" value=""   placeholder=" e.g 192.168.0.100 - 192.168.0.190" /></td></tr>\';
				$("#iprangeblockingtable tr:last").after(new_row);
			});

			$("#add_referer").click(function() {
				var last_index_name = $("#referrerblockingtable tr:last .mo_wpns_table_textbox").attr("name");
				var splittedArray = last_index_name.split("_");
				var last_index = parseInt(splittedArray[splittedArray.length-1])+1;
				var new_row = \'<tr><td><input style="padding:10px 0px" class="mo_wpns_table_textbox" type="text" name="referrer_\'+last_index+\'" value=""   placeholder=" e.g  google.com" /></td></tr>\';
				$("#referrerblockingtable tr:last").after(new_row);
			});
	
		});
	</script>';