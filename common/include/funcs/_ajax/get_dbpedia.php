<?php
header("Content-type: text/plain; charset=utf-8;");

class sparql_dbpedia_film {
	public function __construct($params) {
		$this->params = $params;
	}
	private function adjust_wiki_url($url) {
		$headers = get_headers($url);
		$status = substr($headers[0], 9, 3);
		return ($status == 200) ? $url : str_replace("http://upload.wikimedia.org/wikipedia/commons/", "http://upload.wikimedia.org/wikipedia/it/", $url);
	}
	private function utf8_json_encode($arr) {
		array_walk_recursive($arr,
			function (&$item, $key) {
				if (is_string($item)) {
					$item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
				}
			}
		);
		return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
	}
	private function dbtrim($string, $to_list = true) {
		if($to_list){
			return preg_replace("/\* (.*?)\:/i", "<li style=\"margin-left: 10px;\"><a href=\"http://it.wikipedia.org/wiki/$1\" target=\"_blank\">$1</a>:", str_replace(array("\r\n", "\r", "\n"), "</li>", str_replace(array("http://it.dbpedia.org/resource/", "_"), array("", " "), $string)));
		} else {
			return str_replace(array("http://it.dbpedia.org/resource/", "_"), array("", " "), $string);
		}
	}
	private function set_link($string) {
		return "<a href=\"http://it.wikipedia.org/wiki/" . $this->dbtrim($string, false) . "\" target=\"_blank\">" . $this->dbtrim($string, false) . "</a>";
	}
	private function set_easyrdf() {
		require_once("../../lib/easyrdf/lib/EasyRdf.php");
		
		EasyRdf_Namespace::set("rdfs", "http://www.w3.org/1999/02/22-rdf-syntax-ns#");
		EasyRdf_Namespace::set("rdfs", "http://www.w3.org/2000/01/rdf-schema#");
		EasyRdf_Namespace::set("xs", "http://www.w3.org/2001/XMLSchema#");
		EasyRdf_Namespace::set("foaf", "http://xmlns.com/foaf/0.1/");
		EasyRdf_Namespace::set("dc", "http://purl.org/dc/elements/1.1/");
		EasyRdf_Namespace::set("dcterms", "http://purl.org/dc/terms/");
		EasyRdf_Namespace::set("dbo", "http://dbpedia.org/ontology/");
		EasyRdf_Namespace::set("dbp", "http://it.dbpedia.org/property/");
		EasyRdf_Namespace::set("dbr", "http://dbpedia.org/resource/");
		
		$easyrdf = new EasyRdf_Sparql_Client("http://it.dbpedia.org/sparql");
		
		return $easyrdf;
	}
	public function fetch_by_title() {
		$easyrdf = $this->set_easyrdf();
		$year_clause = (isset($this->params["year"]) && trim($this->params["year"]) !== "" && is_numeric($this->params["year"])) ? " and ?annouscita = " . $this->params["year"] . " or ?annouscita = " . $this->params["year"] : "";
		$year_clause2 = (trim($this->params["year"]) !== "") ? 'FILTER(xs:integer(?year) = ' . $this->params["year"] . ')' : "";
		
		switch($this->params["type"]) {
			default:
				$query = 'SELECT DISTINCT ?title ?original_title ?abstract ?year ?comment ?depiction ?caption ?wikiLink ?dbLink ?homepage ?externalLink ' .
						'WHERE {' .
						'	?film rdf:type dbo:Film ;' .
						'		 foaf:name ?title ;' .
						'		 dbo:abstract ?abstract .' .
						'	?film rdfs:comment ?comment .' .
						'	?film foaf:depiction ?depiction .' .
						'	OPTIONAL{ ' .
						'		{ ?film dbp:anno ?year ' . $year_clause2 . ' . }' .
						'		UNION' .
						'		{ ?film dbp:annouscita ?year ' . $year_clause2 . ' . }' .
						'		FILTER (bound(?year))' .
						'	}' .
						'	OPTIONAL{ ?film dbp:titolooriginale ?original_title }' .
						'	OPTIONAL{ ?film dbp:didascalia ?caption }' .
						'	OPTIONAL{ ?film foaf:isPrimaryTopicOf ?wikiLink }' .
						'	OPTIONAL{ ?film rdfs:label ?dbLink }' .
						'	OPTIONAL{ ?film foaf:homepage ?homepage }' .
						'	OPTIONAL{ ?film dbo:wikiPageExternalLink ?externalLink }' .
						'	FILTER (regex(?title, "' . $this->params["title"] . '") && lang(?abstract) = "it")' .
						'} LIMIT 100';
				break;
			case "film":
				$query = 'SELECT DISTINCT ?title ?abstract ?year ?year2 ?runtime ?genre ?audio ?color ?ratio ?country ' .
						'WHERE {' .
						'	?film rdf:type dbo:Film .' .
						'	?film foaf:name ?title .' .
						'	?film dbo:abstract ?abstract .' .
						'	OPTIONAL{ ?film dbp:anno ?year }' .
						'	OPTIONAL{ ?film dbp:annouscita ?year2 }' .
						'	OPTIONAL{ ?film dbp:durata ?runtime }' .
						'	OPTIONAL{ ?film dbp:genere ?genre }' .
						'	OPTIONAL{ ?film dbp:tipoaudio ?audio }' .
						'	OPTIONAL{ ?film dbp:tipocolore ?color }' .
						'	OPTIONAL{ ?film dbp:ratio ?ratio }' .
						'	OPTIONAL{ ?film dbo:country ?country }' .
						'	FILTER (regex(?title, "' . $this->params["title"] . '") && lang(?abstract) = "it" ' . $year_clause . ')' .
						'} LIMIT 100';
				break;
			case "production":
				$query = 'SELECT DISTINCT ?title ?abstract ?year ?year2 ?producer ?executiveProducer ?company ' .
						'WHERE {' .
						'	?film rdf:type dbo:Film .' .
						'	?film foaf:name ?title .' .
						'	?film dbo:abstract ?abstract .' .
						'	OPTIONAL{ ?film dbp:anno ?year }' .
						'	OPTIONAL{ ?film dbp:annouscita ?year2 }' .
						'	OPTIONAL{ ?film dbo:producer ?producer }' .
						'	OPTIONAL{ ?film dbo:executiveProducer ?executiveProducer }' .
						'	OPTIONAL{ ?film dbo:productionCompany ?company }' .
						'	FILTER (regex(?title, "' . $this->params["title"] . '") && lang(?abstract) = "it" ' . $year_clause . ')' .
						'} LIMIT 100';
				break;
			case "distribution":
				$query = 'SELECT DISTINCT ?title ?abstract ?year ?year2 ?distributor ?distributorIt ' .
						'WHERE {' .
						'	?film rdf:type dbo:Film .' .
						'	?film foaf:name ?title .' .
						'	?film dbo:abstract ?abstract .' .
						'	OPTIONAL{ ?film dbp:anno ?year }' .
						'	OPTIONAL{ ?film dbp:annouscita ?year2 }' .
						'	OPTIONAL{ ?film dbo:distributor ?distributor }' .
						'	OPTIONAL{ ?film dbp:distribuzioneitalia ?distributorIt }' .
						'	FILTER (regex(?title, "' . $this->params["title"] . '") && lang(?abstract) = "it" ' . $year_clause . ')' .
						'} LIMIT 100';
				break;
			case "cast":
				$query = 'SELECT DISTINCT ?title ?abstract ?year ?writer ?director ?screenwriter ?scenographer ?subject ?editing ?starring ?actors ?voiceActors ' .
						'WHERE {' .
						'	?film rdf:type dbo:Film .' .
						'	?film foaf:name ?title .' .
						'	?film dbo:abstract ?abstract .' .
						'	OPTIONAL{ ?film dbp:anno ?year }' .
						'	OPTIONAL{ ?film dbo:writer ?writer }' .
						'	OPTIONAL{ ?film dbo:director ?director }' .
						'	OPTIONAL{ ?film dbp:sceneggiatore ?screenwriter }' .
						'	OPTIONAL{ ?film dbp:scenografo ?scenographer }' .
						'	OPTIONAL{ ?film dbp:soggetto ?subject }' .
						'	OPTIONAL{ ?film dbo:editing ?editing }' .
						'	OPTIONAL{ ?film dbo:starring ?starring }' .
						'	OPTIONAL{ ?film dbp:attori ?actors }' .
						'	OPTIONAL{ ?film dbp:doppiatoriitaliani ?voiceActors }' .
						'	FILTER (regex(?title, "' . $this->params["title"] . '") && lang(?abstract) = "it" ' . $year_clause . ')' .
						'} LIMIT 100';
				break;
		}
		$result = $easyrdf->query($query);
		foreach($result as $k => $res) {
			switch($this->params["type"]) {
				default:
					$film_data[$k]["title"] = trim($res->title);
					$film_data[$k]["original_title"] = trim($res->original_title);
					$film_data[$k]["abstract"] = trim($res->abstract);
					$film_data[$k]["comment"] = trim($res->comment);
					$film_data[$k]["depiction"] = $this->adjust_wiki_url(trim($res->depiction));
					$film_data[$k]["caption"] = trim($res->caption);
					$film_data[$k]["wikiLink"] = trim($res->wikiLink);
					$film_data[$k]["dbLink"] = trim("http://it.dbpedia.org/page/" . str_replace(" ", "_", $res->dbLink));
					$film_data[$k]["homepage"] = trim($res->homepage);
					$film_data[$k]["externalLink"] = trim($res->externalLink);
					break;
				case "film":
					$film_data[$k]["title"] = trim($res->title);
					$film_data[$k]["abstract"] = trim($res->abstract);
					$film_data[$k]["film"]["year"] = trim((strlen($res->year) > 0) ? $res->year : $res->year2);
					$film_data[$k]["film"]["runtime"] = (trim($res->runtime)/60) == 1 ? (trim($res->runtime)/60) . " minuto" : (trim($res->runtime)/60) . " minuti";
					$film_data[$k]["film"]["genre"] = trim($res->genre);
					$film_data[$k]["film"]["audio"] = trim($res->audio);
					$film_data[$k]["film"]["color"] = trim($res->color);
					$film_data[$k]["film"]["ratio"] = trim($res->ratio);
					$film_data[$k]["film"]["country"] = $this->dbtrim($res->country);
					break;
				case "production":
					$film_data[$k]["title"] = trim($res->title);
					$film_data[$k]["abstract"] = trim($res->abstract);
					$film_data[$k]["production"]["producer"] = $this->set_link($res->producer);
					$film_data[$k]["production"]["executiveProducer"] = $this->set_link($res->executiveProducer);
					$film_data[$k]["production"]["company"] = $this->set_link($res->company);
					break;
				case "distribution":
					$film_data[$k]["title"] = trim($res->title);
					$film_data[$k]["abstract"] = trim($res->abstract);
					$film_data[$k]["distribution"]["distributor"] = $this->set_link($res->distributor);
					$film_data[$k]["distribution"]["distributorIt"] = $this->set_link($res->distributorIt);
					break;
				case "cast":
					$film_data[$k]["title"] = trim($res->title);
					$film_data[$k]["abstract"] = trim($res->abstract);
					$film_data[$k]["cast"]["writer"] = $this->set_link($res->writer);
					$film_data[$k]["cast"]["director"] = $this->set_link($res->director);
					$film_data[$k]["cast"]["screenwriter"] = $this->set_link($res->screenwriter);
					$film_data[$k]["cast"]["scenographer"] = $this->set_link($res->scenographer);
					$film_data[$k]["cast"]["subject"] = $this->set_link($res->subject);
					$film_data[$k]["cast"]["editing"] = $this->set_link($res->editing);
					$film_data[$k]["cast"]["starring"] = $this->set_link($res->starring);
					$film_data[$k]["cast"]["actors"] = $this->dbtrim($res->actors);
					$film_data[$k]["cast"]["voiceActors"] = $this->dbtrim($res->voiceActors);
					break;
			}
		}
		$film_data["_items_count"] = count($film_data);
		$film_data[$k]["query"] = str_replace("\t", "\n", stripslashes($query));
		return $this->export($film_data);
	}
	private function export($data) {
		switch($this->params["format"]) {
			case "json":
			default:
				return $this->utf8_json_encode($data);
				break;
			case "array":
				return $data;
				break;
		}
	}
}

if(isset($_GET["title"]) && trim($_GET["title"]) !== "") {
	$params = array(
		"title" => $_GET["title"], 
		"year" => $_GET["year"], 
		"format" => $_GET["format"],
		"type" => $_GET["type"]
	);
	$sparql = new sparql_dbpedia_film($params);
	$data = $sparql->fetch_by_title();
	print_r($data);
	//print_r($result);
} else {
	print "no parameters";
}
?>