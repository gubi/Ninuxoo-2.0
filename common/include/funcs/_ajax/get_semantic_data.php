<?php
header("Content-type: text/plain; charset=utf-8");
require_once("../../lib/easyrdf/lib/EasyRdf.php");
function is_remote_file($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	// don't download content
	curl_setopt($ch, CURLOPT_NOBODY, 1);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if(curl_exec($ch)!==FALSE) {
		return true;
	} else {
		return false;
	}
}
function clean_text($text) {
	$text = trim(preg_replace("/(disc)(\s+|)(\d+)|(cd)(\s+|)(\d+)/i", "", preg_replace("/[^a-zA-Z0-9 .]/", "", preg_replace("/(\[.*?\])|(\(.*?\))|[0-9]{3,}/i", "", str_replace("_", " ", $text)))));
	if(strpos($text, " ") === false) {
		return $text;
	} else {
		foreach(explode(" ", $text) as $search_item) {
			if(strlen($search_item) >= 3) {
				$s[] = $search_item;
			}
		}
		return implode(" ", $s);
	}
}

EasyRdf_Namespace::set("rdf", "http://www.w3.org/1999/02/22-rdf-syntax-ns#");
EasyRdf_Namespace::set("rdfs", "http://www.w3.org/2000/01/rdf-schema#");
EasyRdf_Namespace::set("xs", "http://www.w3.org/2001/XMLSchema#");
EasyRdf_Namespace::set("xsd", "http://www.w3.org/2001/XMLSchema#");
EasyRdf_Namespace::set("mo", "http://purl.org/ontology/mo/");
EasyRdf_Namespace::set("tl", "http://purl.org/NET/c4dm/timeline.owl#");
EasyRdf_Namespace::set("event", "http://purl.org/NET/c4dm/event.owl#");
EasyRdf_Namespace::set("foaf", "http://xmlns.com/foaf/0.1/");
EasyRdf_Namespace::set("dc", "http://purl.org/dc/elements/1.1/");
EasyRdf_Namespace::set("dcterms", "http://purl.org/dc/terms/");
EasyRdf_Namespace::set("dbo", "http://dbpedia.org/ontology/");
EasyRdf_Namespace::set("dbp", "http://it.dbpedia.org/property/");
EasyRdf_Namespace::set("dbpp", "http://dbpedia.org/property/");
EasyRdf_Namespace::set("dbr", "http://dbpedia.org/resource/");

$easyrdf = new EasyRdf_Sparql_Client("http://it.dbpedia.org/sparql");
$s = clean_text(rawurldecode($_GET["title"]));

if(strpos($s, " ") === false) {
	$filters[] = 'str(?label) = "' . $s . '"';
} else {
	foreach(explode(" ", $s) as $search_item) {
		if(strlen($search_item) > 3) {
			$filters[] = 'regex(?label, "' . $search_item . '", "i")';
		}
	}
}
switch(strtolower($_GET["type"])) {
	case "book":
		$query = <<<Select
SELECT * WHERE {
	?item a <http://dbpedia.org/ontology/Book> . 
	?item	rdfs:label ?label .
	?item	dbp:titolo ?titolo .
	?item	dbo:abstract ?abstract .
	
	OPTIONAL { ?item	dbp:autore ?autore }.
	OPTIONAL { ?item	rdfs:comment ?commento }.
	OPTIONAL { ?item	dbp:lingua ?lingua }.
	OPTIONAL { ?item	dbp:annoorig ?anno }.
	OPTIONAL { ?item	dbo:genere ?genere }.
	OPTIONAL { ?item	dbp:immagine ?immagine }.
	OPTIONAL { ?item	dbo:thumbnail ?thumbnail }.
	OPTIONAL { ?item	dbp:sottogenere ?sottogenere }.
	OPTIONAL { ?item	dbp:protagonista ?protagonista }.
	OPTIONAL { ?item	dbp:tipo ?tipo.  }.
	OPTIONAL { ?item	foaf:isPrimaryTopicOf ?pagina_Wikipedia }.
	
Select;
		$query .= "	FILTER (" . implode(" && ", $filters) . ")\n";
		$query .= "} LIMIT 1";
		break;
	case "album":
		$filt[] = "bif:contains(?label, '\"" . clean_text($_GET["album"]) . "\"')";
		if(isset($_GET["artist"]) && trim($_GET["artist"]) !== "") {
			$filt[] = 'regex(?artista, "' . trim($_GET["artist"]) . '", "i")';
		}
		$query = <<<Select
SELECT * WHERE {
	?item a <http://dbpedia.org/ontology/Album> . 
	?item	rdfs:label ?label .
	?item	dbp:titolo ?titolo .
	?item	dbo:abstract ?abstract .
	
	OPTIONAL { ?item	dbp:artista ?artista }.
	OPTIONAL { ?item	rdfs:comment ?commento }.
	OPTIONAL { ?item	dbp:registrato ?registrazione }.
	OPTIONAL { ?item	dbo:totalDiscs ?dischi }.
	OPTIONAL { ?item	dbo:totalTracks ?tracce }.
	OPTIONAL { ?item	dbp:durata ?durata }.
	OPTIONAL { ?item	foaf:depiction ?immagine }.
	OPTIONAL { ?item	dbo:thumbnail ?thumbnail }.
	OPTIONAL { ?item	dbp:anno ?anno }.
	OPTIONAL { ?item	dbp:genere ?genere }.
	OPTIONAL { ?item	dbp:precedente ?disco_precedente }.
	OPTIONAL { ?item	dbp:successivo ?disco_successivo }.
	OPTIONAL { ?item	dbp:tipoAlbum ?tipo_album.  }.
	OPTIONAL { ?item	foaf:isPrimaryTopicOf ?pagina_Wikipedia }.
	
Select;
		$query .= "	FILTER (" . implode(" && ", $filt) . ")\n";
		$query .= "} LIMIT 1";
		break;
	case "film":
		$query = <<<Select
SELECT * WHERE {
	?item a <http://dbpedia.org/ontology/Film> . 
	?item	rdfs:label ?label .
	?item	dbp:titoloitaliano ?titolo .
	?item	dbo:abstract ?abstract .
	
	OPTIONAL { ?item	dbp:titoloitaliano ?titoloitaliano }.
	OPTIONAL { ?item	dbp:titolooriginale ?titolooriginale }.
	OPTIONAL { ?item	dbp:attori ?attori }.
	OPTIONAL { ?item	rdfs:annouscita ?anno }.
	OPTIONAL { ?item	rdfs:comment ?commento }.
	OPTIONAL { ?item	dbp:casaproduzione ?casaproduzione }.
	OPTIONAL { ?item	foaf:depiction ?immagine }.
	OPTIONAL { ?item	dbo:thumbnail ?thumbnail }.
	OPTIONAL { ?item	dbp:didascalia ?didascalia }.
	OPTIONAL { ?item	dbp:distribuzioneitalia ?distribuzioneitalia }.
	OPTIONAL { ?item	dbp:doppiatoriitaliani ?doppiatori }.
	OPTIONAL { ?item	dbp:durata ?durata }.
	OPTIONAL { ?item	dbp:fotografo ?fotografo }.
	OPTIONAL { ?item	dbp:montatore ?montatore }.
	OPTIONAL { ?item	dbp:produttore ?produttore }.
	OPTIONAL { ?item	dbp:sceneggiatore ?sceneggiatore }.
	OPTIONAL { ?item	dbp:scenografo ?scenografo }.
	OPTIONAL { ?item	dbp:soggetto ?soggetto }.
	OPTIONAL { ?item	dbp:regista ?regista }.
	OPTIONAL { ?item	dbp:tipoaudio ?audio }.
	OPTIONAL { ?item	dbp:tipocolore ?colore }.
	OPTIONAL { ?item	dbp:genere ?genere  }.
	OPTIONAL { ?item	foaf:isPrimaryTopicOf ?pagina_Wikipedia }.
	
Select;
		$query .= "	FILTER (" . implode(" && ", $filters) . ")\n";
		$query .= "} LIMIT 1";
		break;
	case "person":
		if(isset($_GET["artist"]) && trim($_GET["artist"]) !== "") {
			$filt[] = 'regex(?label, "' . trim($_GET["artist"]) . '", "i")';
		} else {
			$filt = $filters;
		}
		$query = <<<Select
SELECT * WHERE {
	?item a <http://dbpedia.org/ontology/Person> . 
	?item	rdfs:label ?label .
	?item	dbo:abstract ?abstract .
	
	OPTIONAL { ?item	dbp:nome ?nome }.
	OPTIONAL { ?item	dbp:cognome ?cognome }.
	OPTIONAL { ?item	dbo:formerName ?formerName }.
	OPTIONAL { ?item	rdfs:comment ?commento }.
	OPTIONAL { ?item	rdfs:contenuto ?contenuto }.
	OPTIONAL { ?item	dbp:nazione ?nazione }.
	OPTIONAL { ?item	dbp:nazionalità ?nazionalita }.
	OPTIONAL { ?item	dbp:postnazionalità ?postnazionalita }.
	OPTIONAL { ?item	dbp:profession ?professione }.
	OPTIONAL { ?item	dbp:tipoArtista ?tipo_di_artista }.
	OPTIONAL { ?item	dbp:numeroAlbumStudio ?album_in_studio }.
	OPTIONAL { ?item	dbp:numeroAlbumLive ?album_dal_vivo }.
	OPTIONAL { ?item	dbp:numeroTotaleAlbumPubblicati ?totale_album }.
	OPTIONAL { ?item	dbp:attività ?attivita }.
	OPTIONAL { ?item	dbp:attivitàaltre ?altre_attivita }.
	OPTIONAL { ?item	dbp:immagine ?immagine }.
	OPTIONAL { ?item	dbo:thumbnail ?thumbnail }.
	OPTIONAL { ?item	dbp:genere ?genere }.
	OPTIONAL { ?item	dbo:birthPlace ?luogo_nascita }.
	OPTIONAL { ?item	dbp:annonascita ?anno_nascita }.
	OPTIONAL { ?item	dbp:annoInizioAttività ?fondazione }.
	OPTIONAL { ?item	dbp:annoFineAttività ?scioglimento }.
	OPTIONAL { ?item	dbo:deathPlace ?luogo_morte }.
	OPTIONAL { ?item	dbp:annomorte ?anno_morte }.
	OPTIONAL { ?item	dbp:tombeFamose ?luogo_sepoltura }.
	OPTIONAL { ?item	foaf:isPrimaryTopicOf ?pagina_Wikipedia }.
	
Select;
		$query .= "	FILTER (" . implode(" && ", $filt) . ")\n";
		$query .= "} LIMIT 1";
		break;
	default:
		$owl = ucfirst(trim($_GET["type"]));
		$item_label = ($owl == "Thing" || isset($_GET["search_type"]) && $_GET["search_type"] == "direct") ? "?item	<http://www.w3.org/2000/01/rdf-schema#label> ?label ." : "?item a <http://dbpedia.org/ontology/$owl> . ?item	rdfs:label ?label .";
		$query = <<<Select
SELECT * WHERE {
	$item_label
	
	OPTIONAL { ?item	dbp:nome ?nome }.
	OPTIONAL { ?item	rdfs:comment ?commento }.
	OPTIONAL { ?item	dbp:estensione ?estensione }.
	OPTIONAL { ?item	dbp:estensioneDi ?estensione_di }.
	OPTIONAL { ?item	rdfs:contenuto ?contenuto }.
	OPTIONAL { ?item	dbp:genere ?genere }.
	OPTIONAL { ?item	dbp:mime ?mime }.
	OPTIONAL { ?item	dbp:standard ?standard }.
	OPTIONAL { ?item	dbp:sviluppatore ?sviluppatore }.
	OPTIONAL { ?item	foaf:depiction ?immagine }.
	OPTIONAL { ?item	dbo:thumbnail ?thumbnail }.
	OPTIONAL { ?item	dbo:format ?formato }.
	OPTIONAL { ?item	dcterms:subject ?area_di_afferenza }.
	OPTIONAL { ?item	dbo:wikiPageRedirects ?riferimento }.
	OPTIONAL { ?item	dbo:wikiPageExternalLink ?link_esterno }.
	OPTIONAL { ?item	foaf:isPrimaryTopicOf ?pagina_Wikipedia }.
	
Select;
		$query .= "	FILTER (" . implode(" && ", $filters) . ")\n";
		$query .= "} LIMIT 1";
		break;
		
}
if($_GET["debug"] == "true") {
	print_r($query);
	print "\n\n";
}
$result = $easyrdf->query($query);
if($_GET["debug"] == "true") {
	print_r($result);
}
if(count($result) > 0) {
	foreach($result[0] as $k => $row) {
		
		$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
		if(preg_match($reg_exUrl, $row, $url) && $k !== "immagine" && $k !== "thumbnail" && $k !== "item") {
			$f = (pathinfo($url[0]));
			$res[$k] = preg_replace($reg_exUrl, '<a target="_blank" href="' . str_replace("dbpedia.org/resource", "wikipedia.org/wiki", $url[0]) . '">' . str_replace(array("_", "Categoria:"), array(" ", ""), $f["basename"]) . '</a>', $row);
		} else {
			if(is_remote_file(trim($result[0]->immagine))) {
				$res["immagine"] = trim($result[0]->immagine);
			} else {
				if(is_remote_file(trim($result[0]->thumbnail))) {
					$res["immagine"] = trim($result[0]->thumbnail);
				} else {
					$pi = pathinfo(trim($result[0]->thumbnail));
					$res["immagine"] = str_replace("commons/thumb", "it", trim($pi["dirname"]));
				}
			}
			if(isset($res["durata"]) && strlen(trim($res["durata"])) > 0) {
				$res["durata_totale"] = gmdate("H:i:s", round($res["durata"]));
			}
			if($k !== "item" && $k !== "thumbnail") {
				$res[$k] = trim($row);
			}
		}
	}
	if($_GET["debug"] == "true") {
		print_r($res);
	}
} else {
	$res = null;
}
print json_encode($res);
?>