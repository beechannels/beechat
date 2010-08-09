<?php
        require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	if (isloggedin()) {
		get_loggedin_user()->chatdisabled = false;
		system_message(elgg_echo("beechat:enabled"));
	}
	forward($_SERVER['HTTP_REFERER']);
?>