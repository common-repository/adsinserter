<div class="wrap">

	<h1><a href="https://app.adsinserter.com" target="_blank">AdsInserter</a> Settings</h1>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'adsinserter' );
		settings_errors('adsinserter_options', true);
		do_settings_sections( 'AdsInserter' );
		submit_button();
		?>
	</form>

	<h3>Documentation</h3>

	<ul>
		<li><a href="https://adsinserter.com/docs/insert-placement" target="_blank">How create placement ?</a></li>
		<li><a href="https://adsinserter.com/docs/unit-settings#iteration" target="_blank">How display unit on a specific position?</a></li>
	</ul>


</div>
