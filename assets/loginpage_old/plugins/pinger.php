<?php
require("core/plugin-helper.php");

/** What event(s) should the plugin listen for?  **/
add_listener('dashboard', 'pinger'); // dashboard load

function pinger($args){
	$agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";$ch=curl_init();
	$markup = '<div id="pinger"><div class="col-md-3"><form action="" method="POST"><input type="text" name="url" placeholder="http://example.com" class="form-control"><br><input type="submit" name="ping" class="btn btn-success" value="Ping" style="outline:none;"></form></div></div>';
	pretify("Website Pinger", $markup, true);
	if(isset($_POST['ping'])) { 
		$url = $_POST['url'];
		if(!filter_var($url, FILTER_VALIDATE_URL)) {
			side_pretify('<div class="text-danger">Error</div>', 'Invalid domain name');
		} else {
		$ch = curl_init($url);
		    curl_setopt($ch, CURLOPT_NOBODY, true);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		    curl_exec($ch);
		    $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		    curl_close($ch);
		    if (200==$retcode) {
		    side_pretify('<div class="text-success">Online</div>', 'Looks like the website is <b>up</b>');
		    } else {
		    side_pretify('<div class="text-danger">Offline</div>', 'Looks like the website is <b>down</b>');
		    }
		}
	}
}

