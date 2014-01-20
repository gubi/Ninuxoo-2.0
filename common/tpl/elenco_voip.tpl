<p>Contenuto acquisito dalla pagina <a href="http://wiki.ninux.org/Elenco_Telefonico_rete_VoIP_di_ninux.org" target="_blank">Elenco Telefonico rete VoIP di ninux.org</a> sul Wiki ufficiale</p>
<hr />
<?php
require_once("common/include/lib/simplehtmldom_1_5/simple_html_dom.php");

if($own = @file_get_html("http://wiki.ninux.org/Elenco_Telefonico_rete_VoIP_di_ninux.org")) {
	$ret = $own->find("#content", 0);
	
	print $ret->innertext;
}
?>
