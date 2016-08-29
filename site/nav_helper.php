<div id="helper_nav" class="ui-accordion ui-widget ui-helper-reset">
	<h3 class="accordion-header ui-accordion-header ui-helper-reset ui-state-default ui-accordion-icons ui-corner-all">Helper</h3>
	<div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
		<?php
			if ($client->getAccessToken()) {
				try { 
				
				} catch (Google_Service_Exception $e) {
					echo sprintf('<p>A service error occurred: <code>%s</code></p>',htmlspecialchars($e->getMessage()));
				} catch (Google_Exception $e) {
					echo sprintf('<p>An client error occurred: <code>%s</code></p>',htmlspecialchars($e->getMessage()));
				}
				$_SESSION['token'] = $client->getAccessToken();
			} else {
		 		$state = mt_rand();
				$client->setState($state);
				$_SESSION['state'] = $state;
	
				$authUrl = $client->createAuthUrl();
				echo "<p>Authorization Required<br>";
				echo "You need to <a href='".$authUrl."'>authorize access</a> before proceeding.</p>";
			}
		?>
	</div>
</div>