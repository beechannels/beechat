<?php
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

global $CONFIG;
admin_gatekeeper();

// get ejabberd db settings from elgg.
$domain = get_plugin_setting("domain", "beechat");
$dbname = get_plugin_setting("dbname", "beechat");
$dbhost = get_plugin_setting("dbhost", "beechat");
$dbuser = get_plugin_setting("dbuser", "beechat");
$dbpassword = get_plugin_setting("dbpassword", "beechat");

$jabber_domain = $domain;
 
$dbh_elgg = null;
$dbh_ejabberd = null;
 
$dsn_elgg = 'mysql:dbname='.$CONFIG->dbname.';host='.$CONFIG->dbhost;
$dsn_ejabberd = 'mysql:dbname='.$dbname.';host='.$dbhost;

$dbprefix = $CONFIG->dbprefix;
 
$user = $dbuser;
$password = $dbpassword;
 
$relationship_type = 'friend';
 
try {
  $dbh_elgg = new PDO($dsn_elgg, $CONFIG->dbuser, $CONFIG->dbpass);
 
  $sql = 'SELECT guid, name, username FROM '.$dbprefix.'users_entity';
  $sth = $dbh_elgg->prepare($sql);
  $sth->execute();
 
  $users = array();
  while ($row = $sth->fetch(PDO::FETCH_ASSOC))
    $users[$row['guid']] = $row;
 
  $sql  = 'SELECT guid_one, guid_two FROM '.$dbprefix.'entity_relationships ';
  $sql .= 'WHERE relationship = ?;';
  $sth = $dbh_elgg->prepare($sql);
 
  $sth->bindParam(1, $relationship_type);
  $sth->execute();
 
  $dbh_ejabberd = new PDO($dsn_ejabberd, $user, $password);
  $dbh_ejabberd->beginTransaction();
 
  while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
    $sql = 'INSERT INTO rosterusers (username, jid, nick, subscription, ask, server, type) VALUES (?, ?, ?, ?, ?, ?, ?);';
    $sth_ejabberd = $dbh_ejabberd->prepare($sql);
 
 
    $username = $users[$row['guid_one']]['username'];
    $jid = $users[$row['guid_two']]['username'] . '@' . $jabber_domain;
    $nick = $users[$row['guid_two']]['name'];
    $subscription = 'B';
    $ask = 'N';
    $server = 'N';
    $type = 'item';
 
    $sth_ejabberd->execute(array($username, $jid, $nick, $subscription, $ask, $server, $type));
 
    echo $username . ' registered ' . $jid . ' as a friend in his roster.' . "\n";
  }
 
  $dbh_ejabberd->commit();
 
  $dbh_elgg = null;
  $dbh_ejabberd = null;
} catch (PDOException $e) {
  if ($dbh_ejabberd != null)
    $dbh_ejabberd->rollBack();
  echo $e->getMessage();
}
?>
 

