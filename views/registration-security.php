<?php

echo'<div><div class="mo_wpns_setting_layout">';
echo'	<h3>Block Registerations from fake users</h3>
		<div class="mo_wpns_subheading">
			Disallow Disposable / Fake / Temporary email addresses
		</div>
			
		<form id="mo_wpns_enable_fake_domain_blocking" method="post" action="">
			<input type="hidden" name="option" value="mo_wpns_enable_fake_domain_blocking">
			<input type="checkbox" name="mo_wpns_enable_fake_domain_blocking" '.esc_attr($domain_blocking).' onchange="document.getElementById(\'mo_wpns_enable_fake_domain_blocking\').submit();"> Enable blocking registrations from fake users.
		</form>
</div></div></div>';
			
			