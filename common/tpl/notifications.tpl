<script src="common/js/include/notifications.js"></script>

<div class="panel panel-default">
	<div class="panel-heading">
		<span class="lead text-primary">
			<span class="fa fa-comments-o"></span>&nbsp;&nbsp;Chat di gruppo<span id="check_loader" class="right info"><img src="common/media/img/loader.gif" /></span><small class="help-block">Messaggistica in multicast</small>
		</span>
	</div>
	<div class="panel-body right">
		<form action="" method="get" onsubmit="return false">
			<div class="input-group">
				<!-- USE TWITTER TYPEAHEAD JSON WITH API TO SEARCH -->
				<input class="form-control" id="system-search" style="height: 2.3em;" placeholder="Filtra messaggi per..." required />
				<span class="input-group-btn">
						<button type="submit" class="btn btn-default" onclick="filter($('#system-search'));"><i class="glyphicon glyphicon-search"></i></button>
				</span>
			</div>
		</form>
	</div>
	<table class="table">
		<thead>
			<tr>
				<td style="width: 20px;"></td>
				<th>Nome</th>
				<th>Messaggio</th>
			</tr>
		</thead>
		<tbody id="dash_notifications">
			<tr><td colspan="3" align="center"><span class="info">Rilevo aggiornamenti...</span></td></tr>
		</tbody>
	</table>
</div>
<hr />
<div class="panel panel-default">
	<div class="panel-heading"><span class="lead text-primary"><span class="fa fa-comment-o"></span>&nbsp;&nbsp;Saluta gli altri</span></div>
	
	<form id="editor_frm" method="post" action="" onsubmit="return false;">
		<div class="panel-body">
			<fieldset id="send_notice_area">
				<div class="input-group">
					<input type="hidden" id="user_data" value="<?php print $user["name"]; ?>" />
					<input type="hidden" id="send_previous_notice" value="" />
					<input type="text" class="form-control" id="send_notice" style="height: 2.3em;" placeholder="Scrivi un messaggio" value="" />
					<span class="input-group-btn">
						<button type="submit" class="btn btn-primary" id="send_notice_btn">Invia&nbsp;&nbsp;<span class="glyphicon glyphicon-share-alt"></span></button>
					</span>
				</div>
			</fieldset>
		</div>
	</form>
</div>