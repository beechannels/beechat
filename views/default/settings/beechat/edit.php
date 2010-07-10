<?php
/**
 *	Barter Plugin
 *	@package Barters
 **/
	$domain = get_plugin_setting("domain", "beechat");
	$dbname = get_plugin_setting("dbname", "beechat");
	$dbhost = get_plugin_setting("dbhost", "beechat");
	$dbuser = get_plugin_setting("dbuser", "beechat");
	$dbpassword = get_plugin_setting("dbpassword", "beechat");
?>
<p>
	<?php echo elgg_echo('beechat:domain'); ?>
	<?php echo elgg_view('input/text', array('internalname' => 'params[domain]','value' => $domain)); ?>
	<?php echo elgg_echo('beechat:dbname'); ?>
	<?php echo elgg_view('input/text', array('internalname' => 'params[dbname]','value' => $dbname)); ?>
	<?php echo elgg_echo('beechat:dbhost'); ?>
	<?php echo elgg_view('input/text', array('internalname' => 'params[dbhost]','value' => $dbhost)); ?>
	<?php echo elgg_echo('beechat:dbuser'); ?>
	<?php echo elgg_view('input/text', array('internalname' => 'params[dbuser]','value' => $dbuser)); ?>
	<?php echo elgg_echo('beechat:dbpassword'); ?>
	<?php echo elgg_view('input/password', array('internalname' => 'params[dbpassword]','value' => $dbpassword)); ?>

</p>

