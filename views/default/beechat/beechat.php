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

if (isloggedin() && get_loggedin_user()->chatenabled) {

?>	 
<div id="beechat">
  <div id="beechat_left">
    <a id="beechat_tooltip_trigger" href="<?php echo $vars['url']; ?>"><img src="<?php echo $vars['config']->staticurl; ?>mod/theme_beebac2/graphics/favicon.ico" /></a>
    <div class="tooltip tooltipchat">
      <h3><?php echo elgg_echo('beechat:icons:home'); ?></h3>
    </div>
  </div>
  <div id="beechat_center">
    <span id="beechat_center_prev" class="prev"></span>
    <div id="beechat_scrollboxes"><ul></ul></div>
    <span id="beechat_center_next" class="next"></span>
  </div>
  <div id="beechat_right">
    <span id="beechat_contacts_button" class="offline">
      <?php echo elgg_echo('beechat:contacts:button'); ?>
    </span>
  </div>
  <div id="beechat_contacts">
    <div id="beechat_contacts_top">
      <span class="beechat_label"><?php echo elgg_echo('beechat:contacts:button'); ?></span>
      <div id="beechat_contacts_controls">
	<span id="beechat_contacts_control_minimize" class="beechat_control" title="<?php echo elgg_echo('beechat:box:minimize'); ?>">_</span>
      </div>
      <br clear="all" />
    </div>
    <div id="beechat_availability_switcher">
      <span id="beechat_current_availability"></span>
      <span class="beechat_availability_switcher_control_down" id="beechat_availability_switcher_control"></span>
    </div>
    <div id="beechat_contacts_content">
      <ul id="beechat_contacts_list"></ul>
      <ul id="beechat_availability_switcher_list">
	<li class="beechat_left_availability_chat"><?php echo elgg_echo('beechat:availability:available'); ?></li>
	<li class="beechat_left_availability_dnd"><?php echo elgg_echo('beechat:availability:dnd'); ?></li>
	<li class="beechat_left_availability_away"><?php echo elgg_echo('beechat:availability:away'); ?></li>
	<li class="beechat_left_availability_xa"><?php echo elgg_echo('beechat:availability:xa'); ?></li>
	<li class="beechat_left_availability_offline"><?php echo elgg_echo('beechat:availability:offline'); ?></li>
      </ul>
    </div>
    <div id="beechat_contacts_bottom">
      <span id="beechat_contacts_bottom_bar"></span>
    </div>
  </div>
  <div id="beechat_chatboxes"></div>
</div>
<!-- SOUNDS -->
<!--
<embed src="<?php echo $vars['config']->staticurl; ?>mod/beechat/sounds/newmessage.wav" autostart=false width=0 height=0
       id="beechat_sounds_new_message"
       enablejavascript="true" />
-->

<?php
        $ts = time();
        $token = generate_action_token($ts);
?>

<script>
	$(window).load(function () {
		var e = document.createElement('script');
		e.async = true;
		e.type = 'text/javascript';
                e.innerHTML = 'init_beechat("<?php echo $ts; ?>","<?php echo $token; ?>");';
                document.getElementById('beechat').appendChild(e);

	})
</script>       

<?php
 }
?>
