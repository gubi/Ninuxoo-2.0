<?php
if($user_config["User"]["use_editor_always"] == "true") {
	?>
	L'editor degli script &egrave; <a href="./Admin/Impostazioni_generali#Editor degli script" title="Disattiva l'editor degli script">attivo</a>.<br />
	&Egrave; possibile disattivarlo e usare al suo posto l'editor di testo per il Markdown.
	<?php
} else {
	?>
	L'editor degli script &egrave; <a href="./Admin/Impostazioni_generali#Editor degli script" title="Attiva l'editor degli script">disattivo</a>.<br />
	Al suo posto viene usato l'editor di testo per il Markdown.
	<?php
}
?>