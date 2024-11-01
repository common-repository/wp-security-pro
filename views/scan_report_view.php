<?php
add_action('admin_footer','mo_start_scan');

?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<div class="mo_wpns_setting_layout" id="scan_status_table">
		<div>
			<div>
				<div style="float: left;">
					<p id="scanstatus"></p>
					<h2>Malware Scan</h2>
				</div>
				
				<div class="malwaresummarydiv">
					<div class="mo_wpns_malwarescandiv msdivl">
						<div class="hdiv"><b>Scan Now</b></div>
						<p>Kindly choose the Scan Mode according to your needs.</p>
						<p>For Custom Scan, you can configure the settings in Custom Scan Settings Tab.</p>
					</div>

					<div id="summary_scan" class="mo_wpns_malwarescandiv msdivr">
						<div class="hdiv shdiv"><b>Scan Summary</b></div>
					<?php mo_show_summary(); ?>		
					</div>
				</div>
			</div>
		<?php
		if(! isset($_GET['view'])){
		?>
			<div>
				<p class="hdiv">Scan Modes <a href='<?php echo esc_html($two_factor_premium_docfile['Scan Modes']);?>' target="_blank"><span class="dashicons dashicons-external mo_wpns_doc_link" title="More information.."></span></a></p>
			</div>
			<div class="malwaresummarydiv">
				<div class="mo_wpns_sub_scanmode mo_wpns_msdivl">
					<div class="hdiv"><b>Quick Scan</b></div>
					<p class="mo_wpns_scan_desc">Quick Scan checks all Plugins, Themes and Core files for Vulnerable Code and SQL Injections using PHP malware signatures.</p>
					<input type = "hidden" id = "wpns_quick_scan_nonce" value="<?php echo wp_create_nonce('wpns-quick-scan') ?>" >
					<input id="quick_scan_button" type="button" name="quick_scan_button" class="mo_wpns_scan_button" value="Quick Scan">
				</div>
				<div class="mo_wpns_sub_scanmode mo_wpns_msdivr mo_wpns_msdivl">
					<div class="hdiv"><b>Standard Scan</b></div>
					<p class="mo_wpns_scan_desc">Standard Scan checks all Plugins, Themes and Core files for external links and compares with the repository as well.</p>
					<input type = "hidden" id = "wpns_standard_scan_nonce" value="<?php echo wp_create_nonce('wpns-standard-scan') ?>" >
					<input id="standard_scan_button" type="button" name="standard_scan_button" class="mo_wpns_scan_button" value="Standard Scan">
				</div>
				<div class="mo_wpns_sub_scanmode mo_wpns_msdivl mo_wpns_msdivr">
					<div class="hdiv">
						<b>Deep Scan</b>
						<strong><a href="admin.php?page=mo_wpns_upgrade"> <b style="color: red;">[Premium]</b> </a></strong>
					</div>
					<p class="mo_wpns_scan_desc">Deep Scan checks all Plugins, Themes and Core files for RFI, Trojans and Backdoors using advanced signatures and detects blacklisted domains as well.</p>
					<input id="deep_scan_button" type="button" name="deep_scan_button" class="mo_wpns_deep_scan_button" value="Deep Scan">
				</div>
				<div class="mo_wpns_sub_scanmode mo_wpns_msdivr">
					<div class="hdiv"><b>Custom Scan</b></div>
					<p class="mo_wpns_scan_desc">Custom Scan gives you an option to choose which files to scan and what to check for.</p>
					<input type = "hidden" id = "wpns_custom_scan_nonce" value="<?php echo wp_create_nonce('wpns-custom-scan') ?>" >
					<input id="custom_scan_button" type="button" name="custom_scan_button" class="mo_wpns_scan_button" value="Custom Scan">
					<input type="button" name="configure_button" class="mo_wpns_scan_button" value="Configure" style="float: right;" onclick="openTabmalware(event, 'settings_scan')" >
				</div>
			</div>
		<?php
		}
		?>
			
		</div>
	</div>
	<div class="mo_wpns_setting_layout" id="scan_report_table">
	<?php if(! isset($_GET['view'])){ ?>	
		<h2>Malware Scan Report</h2>
	<?php }else{ ?>
		<h2>Detail Report Of Scan
			<a href="<?php echo esc_url($currenturl) ?>"><b style="float: right; padding-right: 4%">Back To Scan</b></a>
		</h2>
	<?php } ?>
		<hr>
		<div id="scandata">
			<?php 
				include_once $mo_dirName. 'controllers'.DIRECTORY_SEPARATOR.'malware_scan_result.php';
				echo mo_wpns_showScanResults();
			?>
		</div>
	</div>
<?php
function mo_start_scan(){
	if ( ('admin.php' != basename( sanitize_text_field($_SERVER['PHP_SELF'] ))) || (sanitize_text_field($_GET['page']) != 'mo_wpns_malwarescan') ) {
        return;
    }
?>
<script>
	var status_var;
	jQuery(document).ready(function(){
		
		var scan_progress= "<?php echo esc_html(get_option('mo_wpns_malware_scan_in_progress')); ?>";
		if(scan_progress=="IN PROGRESS"){
			jQuery('input[name="quick_scan_button"]').attr('disabled', true);
			jQuery('input[name="custom_scan_button"]').attr('disabled', true);
			jQuery('input[name="standard_scan_button"]').attr('disabled', true);
			var mode_scan="<?php echo esc_html(get_option('mo_wpns_scan_mode')); ?>";
			if(mode_scan=="quick_scan"){
				document.getElementById('quick_scan_button').style.backgroundColor = '#20b2aa';
				document.getElementById('custom_scan_button').style.backgroundColor = '#b0d2cf';
				document.getElementById('standard_scan_button').style.backgroundColor = '#b0d2cf';
				document.getElementById('quick_scan_button').value="Scanning..."
			}
			else if(mode_scan=="standard_scan"){
				document.getElementById('quick_scan_button').style.backgroundColor = '#b0d2cf';
				document.getElementById('custom_scan_button').style.backgroundColor = '#b0d2cf';
				document.getElementById('standard_scan_button').style.backgroundColor = '#20b2aa';
				document.getElementById('standard_scan_button').value="Scanning..."
			}
			else if(mode_scan=="custom_scan"){
				document.getElementById('quick_scan_button').style.backgroundColor = '#b0d2cf';
				document.getElementById('custom_scan_button').style.backgroundColor = '#20b2aa';
				document.getElementById('standard_scan_button').style.backgroundColor = '#b0d2cf';
				document.getElementById('custom_scan_button').value="Scanning..."
			}
			status_var = setInterval(fetch_status,5000);
		}
	jQuery('input[name="quick_scan_button"]').click(function(){
		document.getElementById("quick_scan_button").value = "Scanning...";
		jQuery("#scanstatus").removeClass();
		jQuery("#scanstatus").addClass("alert alert-warning");
		jQuery("#scanstatus").html("Malware scan is <strong>in progress.</strong> You can see result in reports after it's done.");

		jQuery('input[name="quick_scan_button"]').attr('disabled', true);
		jQuery('input[name="custom_scan_button"]').attr('disabled', true);
		jQuery('input[name="standard_scan_button"]').attr('disabled', true);
		document.getElementById('quick_scan_button').style.backgroundColor = '#20b2aa';
		document.getElementById('custom_scan_button').style.backgroundColor = '#b0d2cf';
		document.getElementById('standard_scan_button').style.backgroundColor = '#b0d2cf';

		var data={
			'action':'mo_wpns_malware_redirect',
			'call_type':'malware_scan_initiate',
			'scan':'scan_start',
			'scantype':'quick_scan',
			'nonce':jQuery('#wpns_quick_scan_nonce').val()
		};
		jQuery.post(ajaxurl, data, function(response){
			if(response=="ERROR"){
				jQuery('#mo_scan_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;' >ERROR.</div>");
				window.scrollTo({ top: 0, behavior: 'smooth'});
			}else{
				var xmlString = response;
		   		doc = new DOMParser().parseFromString(xmlString, "text/html");
			   	var all_scan_summary=doc.getElementById('summary_all');
			   	var current_scan_summary=doc.getElementById('summary_current');
			   	jQuery('#summary_all').html(all_scan_summary);
			   	jQuery('#summary_current').html(current_scan_summary);
			   	var summary_html= doc.getElementById('summary_all');
			   	summary_html.remove();
			   	var current_summary= doc.getElementById('summary_current');
			   	current_summary.remove();
			   	var status_table= doc.getElementById('scan_status_table');
			   	status_table.remove();
			   	var report_scan= doc.getElementById('scan_report_table');
			   	report_scan.remove();
			   	var s = new XMLSerializer();
			   	var d= doc;
			   	var str=s.serializeToString(d); 
				jQuery('#scandata').html(str);
				jQuery("#scanstatus").removeClass();
				jQuery("#scanstatus").addClass("alert alert-success");
				jQuery("#scanstatus").html("Malware scan is <strong>completed.</strong> You can see result in reports below.");
			}

			jQuery('input[name="quick_scan_button"]').removeAttr('disabled');
			document.getElementById('quick_scan_button').style.backgroundColor = '#20b2aa';
			jQuery('input[name="standard_scan_button"]').removeAttr('disabled');
			document.getElementById('standard_scan_button').style.backgroundColor = '#20b2aa';
			jQuery('input[name="custom_scan_button"]').removeAttr('disabled');
			document.getElementById('custom_scan_button').style.backgroundColor = '#20b2aa'; 
			document.getElementById("quick_scan_button").value = "Quick Scan";
			
		});
	});

	jQuery('input[name="standard_scan_button"]').click(function(){
		document.getElementById("standard_scan_button").value = "Scanning...";
		jQuery("#scanstatus").removeClass();
		jQuery("#scanstatus").addClass("alert alert-warning");
		jQuery("#scanstatus").html("Malware scan is <strong>in progress.</strong> You can see result in reports after it's done.");
		
		jQuery('input[name="quick_scan_button"]').attr('disabled', true);
		jQuery('input[name="custom_scan_button"]').attr('disabled', true);
		jQuery('input[name="standard_scan_button"]').attr('disabled', true);
		document.getElementById('quick_scan_button').style.backgroundColor = '#b0d2cf';
		document.getElementById('custom_scan_button').style.backgroundColor = '#b0d2cf';
		document.getElementById('standard_scan_button').style.backgroundColor = '#20b2aa';

		var data={
			'action':'mo_wpns_malware_redirect',
			'call_type':'malware_scan_initiate',
			'scan':'scan_start',
			'scantype':'standard_scan',
			'nonce':jQuery('#wpns_standard_scan_nonce').val()
		};
		jQuery.post(ajaxurl, data, function(response){
			if(response=="ERROR"){
				jQuery('#mo_scan_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;' >ERROR.</div>");
				window.scrollTo({ top: 0, behavior: 'smooth'});
			}else{
				var xmlString = response;
		   		doc = new DOMParser().parseFromString(xmlString, "text/html");
			   	var all_scan_summary=doc.getElementById('summary_all');
			   	var current_scan_summary=doc.getElementById('summary_current');
			   	jQuery('#summary_all').html(all_scan_summary);
			   	jQuery('#summary_current').html(current_scan_summary);
			   	var summary_html= doc.getElementById('summary_all');
			   	summary_html.remove();
			   	var current_summary= doc.getElementById('summary_current');
			   	current_summary.remove();
			   	var status_table= doc.getElementById('scan_status_table');
			   	status_table.remove();
			   	var report_scan= doc.getElementById('scan_report_table');
			   	report_scan.remove();
			   	var s = new XMLSerializer();
			   	var d= doc;
			   	var str=s.serializeToString(d); 
				jQuery('#scandata').html(str);
				jQuery("#scanstatus").removeClass();
				jQuery("#scanstatus").addClass("alert alert-success");
				jQuery("#scanstatus").html("Malware scan is <strong>completed.</strong> You can see result in reports below.");
			}

			jQuery('input[name="quick_scan_button"]').removeAttr('disabled');
			document.getElementById('quick_scan_button').style.backgroundColor = '#20b2aa';
			jQuery('input[name="standard_scan_button"]').removeAttr('disabled');
			document.getElementById('standard_scan_button').style.backgroundColor = '#20b2aa';
			jQuery('input[name="custom_scan_button"]').removeAttr('disabled');
			document.getElementById('custom_scan_button').style.backgroundColor = '#20b2aa';
			document.getElementById("standard_scan_button").value = "Standard Scan";
			
		});
	});

	jQuery('input[name="custom_scan_button"]').click(function(){
		document.getElementById("custom_scan_button").value = "Scanning...";
		jQuery("#scanstatus").removeClass();
		jQuery("#scanstatus").addClass("alert alert-warning");
		jQuery("#scanstatus").html("Malware scan is <strong>in progress.</strong> You can see result in reports after it's done.");
		
		jQuery('input[name="quick_scan_button"]').attr('disabled', true);
		jQuery('input[name="custom_scan_button"]').attr('disabled', true);
		jQuery('input[name="standard_scan_button"]').attr('disabled', true);
		document.getElementById('quick_scan_button').style.backgroundColor = '#b0d2cf';
		document.getElementById('custom_scan_button').style.backgroundColor = '#20b2aa';
		document.getElementById('standard_scan_button').style.backgroundColor = '#b0d2cf';
		
		var data={
			'action':'mo_wpns_malware_redirect',
			'call_type':'malware_scan_initiate',
			'scan':'scan_start',
			'scantype':'custom_scan',
			'nonce':jQuery('#wpns_custom_scan_nonce').val()
		};
		jQuery.post(ajaxurl, data, function(response){
			if(response=="ERROR"){
				jQuery('#mo_scan_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;' >ERROR.</div>");
				window.scrollTo({ top: 0, behavior: 'smooth'});
			}else{
				var xmlString = response;
		   		doc = new DOMParser().parseFromString(xmlString, "text/html");
			   	var all_scan_summary=doc.getElementById('summary_all');
			   	var current_scan_summary=doc.getElementById('summary_current');
			   	jQuery('#summary_all').html(all_scan_summary);
			   	jQuery('#summary_current').html(current_scan_summary);
			   	var summary_html= doc.getElementById('summary_all');
			   	summary_html.remove();
			   	var current_summary= doc.getElementById('summary_current');
			   	current_summary.remove();
			   	var status_table= doc.getElementById('scan_status_table');
			   	status_table.remove();
			   	var report_scan= doc.getElementById('scan_report_table');
			   	report_scan.remove();
			   	var s = new XMLSerializer();
			   	var d= doc;
			   	var str=s.serializeToString(d); 
				jQuery('#scandata').html(str);
				jQuery("#scanstatus").removeClass();
				jQuery("#scanstatus").addClass("alert alert-success");
				jQuery("#scanstatus").html("Malware scan is <strong>completed.</strong> You can see result in reports below.");
			}

			jQuery('input[name="quick_scan_button"]').removeAttr('disabled');
			document.getElementById('quick_scan_button').style.backgroundColor = '#20b2aa';
			jQuery('input[name="standard_scan_button"]').removeAttr('disabled');
			document.getElementById('standard_scan_button').style.backgroundColor = '#20b2aa';
			jQuery('input[name="custom_scan_button"]').removeAttr('disabled');
			document.getElementById('custom_scan_button').style.backgroundColor = '#20b2aa';
			document.getElementById("custom_scan_button").value = "Custom Scan";
			
		});
	});	
});
	function fetch_status(){

		var data={
			'action':'mo_wpns_malware_redirect',
			'call_type':'malware_scan_status'
		};
		jQuery.post(ajaxurl, data, function(response){
			if(response['scanned']==0){
				jQuery("#scan_files").html("Scanning with repository files<br>");
				jQuery("#malicious_files").html(response['infected']+" files found Malicious");
			}else{
				jQuery("#scan_files").html(response['scanned']+" files scanned<br>");
				jQuery("#malicious_files").html(response['infected']+" files found Malicious");
			}
			if (response['status']=="COMPLETE"){
				
				jQuery('input[name="quick_scan_button"]').removeAttr('disabled');
				document.getElementById('quick_scan_button').style.backgroundColor = '#20b2aa';
				document.getElementById('quick_scan_button').value="Quick Scan";
				jQuery('input[name="standard_scan_button"]').removeAttr('disabled');
				document.getElementById('standard_scan_button').style.backgroundColor = '#20b2aa';
				document.getElementById('standard_scan_button').value="Standard Scan";
				jQuery('input[name="custom_scan_button"]').removeAttr('disabled');
				document.getElementById('custom_scan_button').style.backgroundColor = '#20b2aa';
				document.getElementById('custom_scan_button').value="Custom Scan";
				clearInterval(status_var);
			}
		});
	}
</script>
<?php
}
function mo_show_summary(){
	$mo_wpns_db_handler = new mo_MoWpnsDB();
	$last_id=$mo_wpns_db_handler->get_last_id();
	$send_id=$last_id[0]->max;
	if(is_null($send_id)){
		$total_scan=0;
		$total_malicious=0;
		$last_scan=0;
		$malicious_last_scan=0;
	}else{
		$result = $mo_wpns_db_handler->get_report_with_id($send_id);
		$total_scan=$mo_wpns_db_handler->count_files();
		$total_malicious=$mo_wpns_db_handler->count_malicious_files();
		$last_scan=$mo_wpns_db_handler->count_files_last_scan($send_id);
		$malicious_last_scan=$mo_wpns_db_handler->count_malicious_last_scan($send_id);
	}
?>
	<div id="summary_all" class="malwaresummarydiv"><div class="summarydiv">Total Files scanned: <?php echo esc_html($total_scan); ?></div>
	<div class="summarydiv ">Total Infected Files Found: <?php echo esc_html($total_malicious); ?></div></div>
	<div id="summary_current" class="malwaresummarydiv"><div class="summarydiv">Files Scanned in last scan: <?php echo esc_html($last_scan); ?> </div>
	<div class="summarydiv">Infected Files in last scan: <?php echo esc_html($malicious_last_scan); ?> </div></div>
	
<?php
}

function mo_show_scan_details($detailreport, $result, $ignorefiles){
	$record = $result[0];
	echo "<b>Malicious files found: </b>" .count($detailreport);
?>
	<div style=float:right><b>Scan Time :</b> <?php echo esc_html(date("M j, Y, g:i:s a",$record->start_timestamp)); ?><br><b>Completion Time :</b> <?php echo esc_html(date("M j, Y, g:i:s a",$record->completed_timestamp)); ?></div><br><br><hr><br>
	<table id="reports_table" class="display" cellspacing="0" width="100%">
	<thead><tr><th>Malicious Files</th><th>Issues</th><th>Action</th></tr></thead>
	<tbody> 
<?php
	foreach($detailreport as $report){
		$issues = unserialize($report->report);
		$filename = $report->filename;
		$classdiv = "";
		$issuecolor = "mo_wpns_red";
		$status = "<a href='".esc_html (add_query_arg( array('trust' => base64_encode($report->filename)), sanitize_text_field($_SERVER['REQUEST_URI']) ))."'>I trust this file</a>";
		if(in_array($report->filename,array_keys($ignorefiles))){
			if($ignorefiles[$filename]['signature']==md5_file($report->filename)){
				$classdiv = "mo_wpns_gray";
				$issuecolor = "mo_wpns_brightred";
				$status = "<span class=mo_wpns_lightgreen>trusted</span>";
			}else{
				$classdiv = "mo_wpns_gray";
				$issuecolor = "mo_wpns_brightred";
				$status = "<a href='".esc_html(add_query_arg( array('trustchanged' => $ignorefiles[$filename]['id']), sanitize_text_field($_SERVER['REQUEST_URI']) ))."'>I trust this file</a><br><span class=mo_wpns_brightred><center>( changed )</center></span>";
			}
		} 
		echo "<tr><td class=".esc_html($classdiv).">".esc_attr($report->filename)."</td><td>";
		foreach($issues as $key=>$value){
			if($key=='scan'){
				echo "<div><span class='".esc_html($issuecolor)." issue'><b>Malware</b></span></div>";
				echo "<div class='issuecontent' data-line='".esc_attr($key)."' data-issue='".esc_attr($issues[$key])."'>Issue Found: ".esc_html($issues[$key])."</div>";
			} 
				
			if($key=='repo'){
				echo "<div><span class='".esc_html($issuecolor)." issue'><b>Check File with Repo: </b></span><div><div class='issuecontent'>File Status: ".esc_html($value["exist"])."</div>";
			} 
			if($key=='extl'){
				echo "<div><span class='".esc_html($issuecolor)." issue'><b>External Link:</b></span></div>";
				foreach ($value as $issue) {
					echo "<div class='issuecontent' data-line='".esc_attr($issue["l"])."' data-issue='".esc_html($issue["d"])."'>Link: ".esc_html($issue["d"])." Line: ".esc_attr($issue["l"])."</div>";
				}
			} 	
		}
		echo "</td><td>".esc_html($status)."</td></tr>";
	}
?>
	</tbody>
	</table>
<?php
}

function mo_show_scan_report($currenturl, $result){
	$mo_wpns_db_handler = new mo_MoWpnsDB();
?>
<table id="reports_table" class="display" cellspacing="0" width="100%">
<thead><tr><th>Scan Type</th><th>Scanned Folders</th><th>Status</th><th>Scan Time</th><th>Action</th></tr></thead>
<tbody>
	<?php 
	if(! is_null($result)){
		foreach($result as $report){
			$vresult = $mo_wpns_db_handler->get_vulnerable_files_count_for_reportid($report->id);
			if(count($vresult)>0)
				$vulnerablefies = $vresult[0]->count;
			else
				$vulnerablefies = 0;

			echo "<tr><td style=text-align:center>".$report->scan_mode."</td>";
			echo "<td style=text-align:center>";
			if(!empty($report->scanned_folders)){
				foreach(explode(";",$report->scanned_folders) as $folder){
					if(!empty($folder)){
						echo $folder."<br>";
					}
				}
			}
			echo "</td><td style=text-align:center>";
			echo "<span style=color:green id=scan_files>".esc_html($report->scanned_files)." files scanned<br></span>";
			echo "<span style=color:red id=malicious_files>".esc_html($vulnerablefies)." files found Malicious</span>";
			echo "</td><td style=text-align:center id=start_time>".esc_html(date("M j, Y, g:i:s a",$report->start_timestamp))."</td>";
			echo "<td><a href='".esc_html(add_query_arg( array('tab' => 'default', 'view' => $report->id), $currenturl ))."'>View Details</a> <a href='".esc_html(add_query_arg( array('tab' => 'default', 'delete' => $report->id), $currenturl ))."'>Delete</a></td>";
			echo "</tr>";
		
		}
	}
	 ?>
</tbody>
</table>

<?php
}
?>
<script>
	jQuery(document).ready(function() {
		jQuery('#reports_table').DataTable({
			<?php if(! isset($_GET['view'])){ ?>
				"order": [[ 3, "desc" ]]
			<?php }
			else{ ?>
				"order": [[ 2, "desc" ]]
			<?php } ?>
		});
	} );
</script>