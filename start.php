<?php
	/**
	 * Beechat
	 * 
	 * @package beechat
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Beechannels <contact@beechannels.com>
	 * @copyright Beechannels 2007-2010
	 * @link http://beechannels.com/
	 */

	GLOBAL $CONFIG;
	
	function beechat_init()
	{
		GLOBAL $CONFIG;
			
		register_translations($CONFIG->pluginspath . "beechat/languages/");

		register_elgg_event_handler('pagesetup', 'system', 'beechat_pagesetup');

		register_action('beechat/get_statuses', false, $CONFIG->pluginspath . 'beechat/actions/get_statuses.php');
		register_action('beechat/get_icons', false, $CONFIG->pluginspath . 'beechat/actions/get_icons.php');
		register_action('beechat/get_details', false, $CONFIG->pluginspath . 'beechat/actions/get_details.php');
		register_action('beechat/get_connection', false, $CONFIG->pluginspath . 'beechat/actions/get_connection.php');
		register_action('beechat/get_state', false, $CONFIG->pluginspath . 'beechat/actions/get_state.php');
		register_action('beechat/save_state', false, $CONFIG->pluginspath . 'beechat/actions/save_state.php');

		register_plugin_hook('action', 'friends/add', 'beechat_xmpp_add_friend');
		register_plugin_hook('action', 'friends/remove', 'beechat_xmpp_remove_friend');

	
		extend_view('js/initialise_elgg', 'js/json2.js');
		extend_view('js/initialise_elgg', 'js/jquery.cookie.min.js');
		extend_view('js/initialise_elgg', 'js/jquery.scrollTo-min.js');
		extend_view('js/initialise_elgg', 'js/jquery.serialScroll-min.js');
		extend_view('js/initialise_elgg', 'js/b64.js');
		extend_view('js/initialise_elgg', 'js/sha1.js');
		extend_view('js/initialise_elgg', 'js/md5.js');
		extend_view('js/initialise_elgg', 'js/strophe.min.js');
		extend_view('js/initialise_elgg', 'js/jquery.tools.min.js');
		extend_view('css', 'beechat/screen.css');
		extend_view('js/initialise_elgg', 'beechat/beechat.js');
		extend_view('metatags', 'beechat/beechat.userjs');
		
		extend_view('footer/analytics', 'beechat/beechat');
		$domain = get_plugin_setting("domain", "beechat");
		$dbname = get_plugin_setting("dbname", "beechat");
		$dbhost = get_plugin_setting("dbhost", "beechat");
		$dbuser = get_plugin_setting("dbuser", "beechat");
		$dbpassword = get_plugin_setting("dbpassword", "beechat");

		$CONFIG->chatsettings['domain'] = $domain;
		$CONFIG->chatsettings['dbname'] = $dbname;
		$CONFIG->chatsettings['dbhost'] = $dbhost;
		$CONFIG->chatsettings['dbuser'] = $dbuser;
		$CONFIG->chatsettings['dbpassword'] = $dbpassword;

	}

	function beechat_pagesetup()
	{
		global $CONFIG;
		if (get_context() == 'settings' && isloggedin()) {
			if (get_loggedin_user()->chatenabled) {
				add_submenu_item(elgg_echo('beechat:disablechat'), $CONFIG->wwwroot . "mod/beechat/disablechat.php");
			}
			else
				add_submenu_item(elgg_echo('beechat:enablechat'), $CONFIG->wwwroot . "mod/beechat/enablechat.php");
		}
	}

	function beechat_xmpp_add_friend($hook, $entity_type, $returnvalue, $params)
	{
		GLOBAL $SESSION;
		GLOBAL $CONFIG;
		
		$jabber_domain = $CONFIG->chatsettings['domain'];
		$dbname = $CONFIG->chatsettings['dbname'];
		$dbhost = $CONFIG->chatsettings['dbhost'];
		$dsn_ejabberd = "mysql:dbname={$dbname};host={$dbhost}";
		
		$user = $CONFIG->chatsettings['dbuser'];
		$password = $CONFIG->chatsettings['dbpassword'];
		
		$friend_guid = get_input('friend', 0);
		if (!$friend_guid || !$friend = get_entity($friend_guid))
			return (false);
		
		try
		{
			$dbh_ejabberd = new PDO($dsn_ejabberd, $user, $password);
			$dbh_ejabberd->beginTransaction();
			
			$sql = 'INSERT INTO rosterusers (username, jid, nick, subscription, ask, server, type) VALUES (?, ?, ?, ?, ?, ?, ?);';
			$sth_ejabberd = $dbh_ejabberd->prepare($sql);
			
			$username = $SESSION->offsetGet('user')->username;
			$jid = $friend->username . '@' . $jabber_domain;
			$nick = $friend->name;
			$subscription = 'B';
			$ask = 'N';
			$server = 'N';
			$type = 'item';
			
			$sth_ejabberd->execute(array($username, $jid, $nick, $subscription, $ask, $server, $type));
			
			$sql = 'INSERT INTO rosterusers (username, jid, nick, subscription, ask, server, type) VALUES (?, ?, ?, ?, ?, ?, ?);';
			$sth_ejabberd = $dbh_ejabberd->prepare($sql);
			
			$username = $friend->username;
			$jid = $SESSION->offsetGet('user')->username . '@' . $jabber_domain;
			$nick = $SESSION->offsetGet('user')->name;
			
			$sth_ejabberd->execute(array($username, $jid, $nick, $subscription, $ask, $server, $type));
			
			$dbh_ejabberd->commit();
			$dbh_ejabberd = null;
		} 
		catch (PDOException $e)
		{
			error_log('beechat_xmpp_add_friend: ' . $e->getMessage());
			$dbh_ejabberd->rollBack();
			return (false);
		}
		
		return $return_value;
	}

function beechat_xmpp_remove_friend($hook, $entity_type, $returnvalue, $params)
{
  	GLOBAL $SESSION;
	GLOBAL $CONFIG;
		
		$jabber_domain = $CONFIG->chatsettings['domain'];
		$dbname = $CONFIG->chatsettings['dbname'];
		$dbhost = $CONFIG->chatsettings['dbhost'];
		$dsn_ejabberd = "mysql:dbname={$dbname};host={$dbhost}";
		
		$user = $CONFIG->chatsettings['dbuser'];
		$password = $CONFIG->chatsettings['dbpassword'];
	
	if (!$friend = get_entity(get_input('friend', 0)))
		return (false);

	try {
		$dbh_ejabberd = new PDO($dsn_ejabberd, $user, $password);
		$dbh_ejabberd->beginTransaction();
		
		$sql = 'DELETE FROM rosterusers WHERE username = ? AND jid = ?;';
		$sth_ejabberd = $dbh_ejabberd->prepare($sql);
		
		$username = $SESSION->offsetGet('user')->username;
		$jid = $friend->username . '@' . $jabber_domain;
		
		$sth_ejabberd->execute(array($username, $jid));
		
		$sql = 'DELETE FROM rosterusers WHERE username = ? AND jid = ?;';
		$sth_ejabberd = $dbh_ejabberd->prepare($sql);
		
		$username = $friend->username;
		$jid = $SESSION->offsetGet('user')->username . '@' . $jabber_domain;
		
		$sth_ejabberd->execute(array($username, $jid));
		
		$dbh_ejabberd->commit();
		$dbh_ejabberd = null;	
	} 
	catch (PDOException $e)
	{
		error_log('beechat_xmpp_remove_friend: ' . $e->getMessage());
		$dbh_ejabberd->rollBack();
		return (false);
	}
	
	return $return_value;
}

register_elgg_event_handler('init', 'system', 'beechat_init');
?>
