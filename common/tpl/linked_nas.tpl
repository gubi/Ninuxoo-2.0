<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script src="common/js/include/common.js"></script>
<script src="common/js/include/linked_nas.js"></script>

<div class="panel panel-default">
	<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-transfer"></span>&nbsp;&nbsp;Nel vicinato<small class="help-block">NAS rilevati nelle vicinanze</small></span></div>
	<div class="panel-body">
		A seguire l'elenco dei NAS nelle vicinanze.<br />
		Per attivare o meno il collegamento con uno dei quali fare click sul flag corrispondente nella colonna dello stato.
		<br />
		Il prossimo controllo dei NAS presenti sar&agrave; fra <strong><span id="counter"></span></strong>...
	</div>
	<table class="table" id="finded_nas">
		<thead>
			<tr>
				<td></td>
				<th>Nome</th>
				<th>Proprietario</th>
				<th>Zona</th>
				<th>Raggiungibilit&agrave;</th>
				<th class="mark_btns" style="text-align: center;">Stato<table cellpadding="0" cellspacing="0"><tr><td style="width: 50%;"><small>Trusted</small></td><td><small>Untrusted</small></td></tr></table></th>
			</tr>
		</thead>
		<tbody>
			<tr id="no_nas">
				<td colspan="6" align="center"><img src="common/media/img/loader.gif" style="vertical-align: -6px;" />&nbsp;&nbsp;&nbsp;<span class="info">Scansiono la zona...</span></td>
			</tr>
		</tbody>
	</table>
</div>
<a class="btn btn-warning right" href="javascript:void(0);" id="add_nas_ip">Aggiungi un NAS conosciuto&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-plus"></span></a>
<br />
<br />
<br />
<div class="panel panel-default">
	<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;In attesa di autorizzazione<small class="help-block">NAS che hanno richiesto la connessione e che attendono di essere collegati</small></span></div>
	<div class="panel-body">
		<?php
		foreach(glob("common/include/conf/trusted/*.pem~") as $filename) {
			$info = pathinfo($filename);
			$files[] = $info["filename"];
		}
		if(is_array($file)) {
			?>
			<hr />
			<ul>
				<?php
				foreach($filename as $file) {
					print '<li>' . $file . '</li>';
				}
				?>
			</ul>
			<?php
		}
		?>
	</div>
</div>