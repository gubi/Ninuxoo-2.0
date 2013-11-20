<div id="top_menu">
	<div>
		<ul>
			<?php
			if($has_config) {
				?>
				<li><a href="?op=whatsnew" id="whatsnew" title="Files di recente indicizzazione">Novit&agrave;</a></li>
				<li><a href="./Meteo" title="Dati meteo in tempo reale">Meteo</a></li>
				<?php
			}
			?>
			<li class="separator">&nbsp;</li>
			<li><a href="http://10.168.177.178:8888/" title="Ascolta la musica condivisa in Rete">Juke Box</a></li>
			<li><a href="http://ninuxoo.ninux.org/cgi-bin/proxy_wiki.cgi?url=Elenco_Telefonico_rete_VoIP_di_ninux.org" title="Elenco telefonico interno">VoIP</a></li>
			
			<li class="separator">&nbsp;</li>
			<li><a href="http://blog.ninux.org/" title="Blog della Community">Blog</a></li>
			<li><a href="http://wiki.ninux.org/" title="Wiki documentativo">Wiki</a></li>
			<li><a href="http://10.162.0.85/" title="Controlla la posta<br />(indirizzi @ninux.org)">Posta</a></li>
		</ul>
		<ul>
			<li><a href="javascript:void(0);" onclick="login()">Accedi</a></li>
		</ul>
	</div>
</div>