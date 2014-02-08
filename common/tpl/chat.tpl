<link rel="stylesheet" href="common/js/jquery-ui-1.10.3.custom/css/ui-lightness/jquery-ui-1.10.3.custom.min.css" type="text/css" media="screen" />
<script src="common/js/include/chat.js"></script>

<span id="chat_panel_width"><?php print $GLOBALS["user_settings"]["Chat"]["panel_width"]; ?></span>
<div id="chat" class="ui-widget-content<?php print (($GLOBALS["user_settings"]["Chat"]["panel_status"] == "closed") ? " closed" : " open") ?><?php print " " . $GLOBALS["user_settings"]["Chat"]["chat_window"] ?>" style="width: <?php print $GLOBALS["user_settings"]["Chat"]["panel_width"]; ?>px; right: -<?php print $GLOBALS["user_settings"]["Chat"]["panel_width"]; ?>px;">
	<div class="panel panel-default right col-lg-2" style="width: <?php print $GLOBALS["user_settings"]["Chat"]["panel_width"]; ?>px;">
		<div class="panel-heading">
			<div class="dropdown">
				<?php
				switch ($GLOBALS["user_settings"]["Chat"]["chat_status"]) {
					case "online":
						$status = '<span class="text-success"><span class="fa fa-user"></span></span>';
						$status_online = '<span class="text-success_"><span class="fa fa-check"></span>&nbsp;In linea</span>';
						$status_do_not_disturb = '<span class="text-danger"><span class="fa fa-times"></span>&nbsp;Non disturbare</span>';
						$status_out = '<span class="text-muted"><span class="fa fa-clock-o"></span>&nbsp;Assente</span>';
						break;
					case "do_not_disturb":
						$status = '<span class="text-danger"><span class="fa fa-user"></span></span>';
						$status_online = '<span class="text-success"><span class="fa fa-check"></span>&nbsp;In linea</span>';
						$status_do_not_disturb = '<span class="text-danger_"><span class="fa fa-times"></span>&nbsp;Non disturbare</span>';
						$status_out = '<span class="text-muted"><span class="fa fa-clock-o"></span>&nbsp;Assente</span>';
						break;
					case "out":
						$status = '<span class="text-muted"><span class="fa fa-user"></span></span>';
						$status_online = '<span class="text-success"><span class="fa fa-check"></span>&nbsp;In linea</span>';
						$status_do_not_disturb = '<span class="text-danger"><span class="fa fa-times"></span>&nbsp;Non disturbare</span>';
						$status_out = '<span class="text-muted_"><span class="fa fa-clock-o"></span>&nbsp;Assente</span>';
						break;
				}
				?>
				<span id="user_id" style="display: none;"><?php print md5($GLOBALS["user_settings"]["User"]["email"] . $GLOBALS["user_settings"]["Chat"]["personal_message"]); ?></span>
				<span id="user_email" style="display: none;"><?php print $GLOBALS["user_settings"]["User"]["email"]; ?></span>
				<a href="javascript:void(0);" class="btn-default" id="change_status_btn" style="margin-top: -5px; padding: 5px 10px;" data-toggle="dropdown"><?php print $status; ?>&nbsp;<span class="caret"></span></a> <?php print $GLOBALS["user_settings"]["Chat"]["nick"]; ?>
				<ul class="dropdown-menu dropdown-menu-right">
					<li <?php print (($GLOBALS["user_settings"]["Chat"]["chat_status"] == "online") ? 'class="active"' : "") ?>><a class="chat_status" id="chat_online" href="javascript:void(0);"><?php print $status_online; ?></a></li>
					<li <?php print (($GLOBALS["user_settings"]["Chat"]["chat_status"] == "do_not_disturb") ? 'class="active"' : "") ?>><a class="chat_status" id="chat_do_not_disturb" href="javascript:void(0);"><?php print $status_do_not_disturb; ?></a></li>
					<li <?php print (($GLOBALS["user_settings"]["Chat"]["chat_status"] == "out") ? 'class="active"' : "") ?>><a class="chat_status" id="chat_out" href="javascript:void(0);"><?php print $status_out; ?></a></li>
					<li class="divider"></li>
					<li><a href="http://192.168.36.210/Dashboard/Impostazioni_personali#Chat"><span class="fa fa-gear"></span>&nbsp;Impostazioni...</a></li>
				</ul>
				<a class="btn-default right" id="panel_position_btn" href="javascript:void(0)" title="<?php print ($GLOBALS["user_settings"]["Chat"]["chat_window"] == "floating") ? "Sgancia dalla pagina" : "Aggancia alla pagina"; ?>"><span class="fa<?php print ($GLOBALS["user_settings"]["Chat"]["chat_window"] == "floating") ? " fa-caret-square-o-left" : " fa-caret-square-o-up"; ?> text-muted"></span></a>
			</div>
		</div>
		<div class="panel-body" id="online_peoples"></div>
		<div class="panel-footer" style="display: none;">
			<form method="post" action="" onsubmit="return false;">
				<div class="input-group">
					<span class="input-group-btn">
						<a href="javascript:void(0);" class="btn btn-default smiley_btn" rel="popover" data-original-title="Faccine"><span class="fa fa-smile-o"></span></a>
					</span>
					<input id="chat_message" type="text" class="form-control" placeholder="Messaggio..." />
					<input type="submit" style="display: none;" />
				</div>
			</form>
		</div>
	</div>
</div>
<div id="popover_content_wrapper" style="display: none">
	<?php
	require_once("common/include/lib/smileys.php");
	foreach($smileys as $sk => $sv) {
		if($sk > 1 && $smileys[($sk-1)]["group"] !== $smileys[$sk]["group"]) {
			print "<br /><hr />";
		}
		if($smileys[($sk-1)]["class"] !== $smileys[$sk]["class"]) {
			print '<button class="smiley_btn btn" title="' . $smileys[$sk]["title"] . '" onclick="$.set_smiley(\'' . $smileys[$sk]["shortcut"] . '\', $(this))"><span class="fa ' . $smileys[$sk]["class"] . '" style="' . $smileys[$sk]["style"] . '"></span></button>';
		}
	}
	?>
</div>