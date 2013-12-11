<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script src="common/js/include/common.js"></script>
<script src="common/js/include/linked_nas.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	check_nas(1);
});
</script>
<fieldset class="frm">
	<legend>NAS nel vicinato</legend>
	<p>
		A seguire l'elenco dei NAS nelle vicinanze.<br />
		Per attivare o meno il collegamento con uno dei quali fare click sul flag corrispondente nella colonna dello stato .
	</p>
	<p>
		Il prossimo controllo dei NAS presenti sar&agrave; tra <span id="counter"></span>...<br />
		&Egrave possibile impostare il tempo di refresh nelle <a href="./Admin/Impostazioni_generali">impostazioni generali</a>
	</p>
	<hr />
	<table cellpadding="10" cellspacing="10" id="finded_nas">
		<tr>
			<td></td>
			<td><strong>Nome</strong></td>
			<td><strong>Proprietario</strong></td>
			<td><strong>Zona</strong></td>
			<td><strong>Raggiungibilit&agrave;</strong></td>
			<td class="mark_btns"><strong>Stato</strong><table cellpadding="0" cellspacing="0"><tr><td style="width: 50%;"><small>Trusted</small></td><td><small>Untrusted</small></td></tr></table></td>
		</tr>
		<tr id="no_nas">
			<td colspan="6" align="center"><img src="common/media/img/loader.gif" style="vertical-align: -6px;" />&nbsp;&nbsp;&nbsp;<span class="info">Scansiono la zona...</span></td>
		</tr>
	</table>
</fieldset>

