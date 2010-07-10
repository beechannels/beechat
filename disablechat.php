<?php
        require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	if (isloggedin()) {
		get_loggedin_user()->chatenabled = false;
		system_message(elgg_echo("beechat:disabled"));
	}
	forward($_SERVER['HTTP_REFERER']);
?>
