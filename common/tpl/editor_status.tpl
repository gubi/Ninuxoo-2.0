<?php
if($user_config["User"]["use_editor_always"]) {
	print 'L\'editor degli script &egrave; <b>attivo</b>.<br />&Egrave; possibile disattivarlo e visualizzare la legenda delle relative scorciatoie nella <a href="./Admin/Impostazioni_generali#Editor degli script">pagina delle impostazioni generali</a>.';
} else {
	print 'L\'editor degli script &egrave; <b>disattivo</b>.<br />&Egrave; possibile attivarlo nella <a href="./Admin/Impostazioni_generali#Editor degli script">pagina delle impostazioni generali</a>.';
}
?>