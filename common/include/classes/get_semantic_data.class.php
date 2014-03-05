<?php
/**
* Ninuxoo 2.0
*
* PHP Version 5.3
*
* @copyright 2013-2014 Alessandro Gubitosi / Gubi (http://iod.io)
* @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3
* @link https://github.com/gubi/Ninuxoo-2.0
*/

/**
* A class for search and get semantic data from Wikipedia and dbpedia
*
* This class search in Wikipedia API for a given term and returns the relative page name with its own common data taken with semantic SPARQL query
*
* @package	Ninuxoo 2.0
* @author		Alessandro Gubitosi <gubi.ale@iod.io>
* @license 		http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3
* @access		public
* @link		https://github.com/gubi/Ninuxoo-2.0/blob/master/common/include/classes/get_semantic_data.class.php
* @uses		wikipedia.class.php Wikipedia class
* @uses		EasyRdf.php EasyRdf
*/
class semantic_data{
	/**
	* Construct
	*
	* Initialize the class
	*
	* @global string $this->class_dir Current class directory
	* @global string $this->lib_dir Lib directory, based on $this->class_dir
	* @global string $this->startime The time in which the script starts
	* @global string $this->wikipedia Wikipedia initialized class
	* @global string $this->easyrdf EasyRdf initialized class
	* @see semantic_data::start_time() Start time
	* @see semantic_data::set_prefix() Set prefix
	* @see wikipedia-::srlimit() Sr limit
	* @see wikipedia-::srprop() Sr prop
	* @return void
	*/
	function __construct() {
		$this->class_dir = __DIR__;
		$this->lib_dir = str_replace("classes", "lib", $this->class_dir);
		require_once($this->class_dir . "/wikipedia.class.php");
		require_once($this->lib_dir . "/easyrdf/lib/EasyRdf.php");
		
		$this->startime = $this->start_time();
		
		$this->wikipedia = new wikipedia();
		$this->wikipedia->srlimit(1);
		$this->wikipedia->srprop("");
		
		$this->set_prefix();
		$this->easyrdf = new EasyRdf_Sparql_Client("http://it.dbpedia.org/sparql");
	}
	
	/**
	* Indents a flat JSON string to make it more human-readable
	*
	* Taken from Dave Perrett blog
	*
	* @url http://www.daveperrett.com/articles/2008/03/11/format-json-with-php/
	* @param string $json The original JSON string to process
	* @return string Indented version of the original JSON string
	* @access public
	*/
	private function indent($json) {
		$result = "";
		$pos	= 0;
		$strLen = strlen($json);
		$indentStr = "\t";
		$newLine = "\n";
		$prevChar	= "";
		$outOfQuotes = true;

		for ($i=0; $i<=$strLen; $i++) {
			// Grab the next character in the string.
			$char = substr($json, $i, 1);

			// Are we inside a quoted string?
			if ($char == '"' && $prevChar != '\\') {
				$outOfQuotes = !$outOfQuotes;
				// If this character is the end of an element,
				// output a new line and indent the next line.
			} else if(($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine;
				$pos --;
				$result .= str_repeat($indentStr, $pos);
			}
			// Add the character to the result string.
			$result .= $char;
			// If the last character was the beginning of an element,
			// output a new line and indent the next line.
			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}

				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}

			$prevChar = $char;
		}
		return $result;
	}

	/**
	* Active debug status
	*
	* @param bool $status Is debug status (usually "true" when calling)
	* @global $this->debug Debug status
	* @access public
	*/
	public function debug($status) {
		$this->debug = $status;
	}
	
	/**
	* Set debug output
	*
	* @param string $out `array`, `object` or `all` for print each one (default `all`).<br>You can combine with format() using `html` for html output inside formatted values
	* @global $this->output Debug output
	* @access public
	*/
	public function output($out = "all") {
		$this->output = $out;
	}
	
	/**
	* Similar to output() but for API export data
	*
	* @param string $format `array`, `json`, `jsonp` or `html` (efault is `jsonp`)
	* @global $this->format Display format
	* @access public
	*/
	public function format($format = "jsonp") {
		$this->format = $format;
	}
	
	/**
	* Start calculating execution time
	*
	* @return int $mtime The start of execution time
	* @access  public
	*/
	public function start_time() {
		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		$mtime = $mtime[1] + $mtime[0];
		return $mtime;
	}
	/**
	* End calculating execution time
	*
	* @param int $startime The time start of execution
	* @return int The end of execution time
	* @access public
	*/
	public function end_time($startime) {
		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		$mtime = $mtime[1] + $mtime[0];
		return round($mtime - $startime, 5);
	}
	
	/**
	* Default semantic prefixes for Ninuxoo
	*
	* @param string $type The type of result to return: `semantic` or `array`
	* @return string|array The Ninuxoo default semantic prefixes
	* @access private
	*/
	private function get_default_prefixes($type = "array") {
		$default_prefixes = array(
			"dbo" => "http://dbpedia.org/ontology/",
			"dbp" => "http://it.dbpedia.org/property/",
			"dbpp" => "http://dbpedia.org/property/",
			"dbr" => "http://dbpedia.org/resource/",
			"dc" => "http://purl.org/dc/elements/1.1/",
			"dcterms" => "http://purl.org/dc/terms/",
			"event" => "http://purl.org/NET/c4dm/event.owl#",
			"foaf" => "http://xmlns.com/foaf/0.1/",
			"mo" => "http://purl.org/ontology/mo/",
			"rdf" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
			"rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
			"tl" => "http://purl.org/NET/c4dm/timeline.owl#",
			"xs" => "http://www.w3.org/2001/XMLSchema#",
			"xsd" => "http://www.w3.org/2001/XMLSchema#"
		);
		switch($type) {
			case "semantic":
				foreach($default_prefixes as $owl => $url) {
					$prefix .= "PREFIX " . $owl . ": <" . $url . ">\n";
				}
				return $prefix . "\n";
				break;
			case "array":
				return $default_prefixes;
				break;
		}
	}
	
	/**
	* Set EasyRdf prefixes
	*
	* @param bool $return_prefix_array Return or not the array of Ninuxoo semantic prefixes
	* @see semantic_data::get_default_prefixes() Get default prefixes
	* @return array|void The Ninuxoo default semantic prefixes
	* @access private
	*/
	private function set_prefix($return_prefix_array = false) {
		if($return_prefix_array) {
			return $this->get_default_prefixes();
		} else {
			foreach($this->get_default_prefixes() as $owl => $url) {
				EasyRdf_Namespace::set($owl, $url);
			}
		}
	}
	
	/**
	* Check if file exists remotely
	*
	* @param string $url The url of file
	* @return bool File exists
	* @access private
	*/
	private function is_remote_file($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if(curl_exec($ch) !==FALSE) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	* Clean text for Wikipedia search
	*
	* @param string $text The url of file
	* @return bool File exists
	* @access private
	*/
	private function clean_text($text) {
		$text = trim(preg_replace("/(disc)(\s+|)(\d+)|(cd)(\s+|)(\d+)/i", "", preg_replace("/(\[.*?\])|(\(.*?\))/i", "", str_replace("_", " ", $text))));
		
		if(strpos($text, " ") === false) {
			return trim($text);
		} else {
			foreach(explode(" ", $text) as $search_item) {
				if(strlen($search_item) >= 2) {
					$s[] = $search_item;
				}
			}
			return implode(" ", $s);
		}
	}
	
	/**
	* Construct the semantic query OPTIONALS
	*
	* @param array $optional Array of optional semantic keys
	* @return string Semantic query OPTIONALS part
	* @access private
	*/
	private function construct_optional($optional) {
		$nt = ($this->debug) ? "\n	" : "";
		if(is_array($optional) && count($optional) > 0) {
			foreach($optional as $sk => $sv) {
				$opt .= "OPTIONAL { ?item " . $sk . " ?" . $sv . " } . " . $nt;
			}
		} else {
			$opt = "";
		}
		return $opt;
	}
	
	/**
	* Create the semantic query
	*
	* @param string $owl Owl to query
	* @param array $optional Array of optional semantic keys
	* @param string $filter Results results by given label
	* @param int $limit Limit the results to show
	* @see semantic_data::debug() Debug
	* @see semantic_data::construct_optional() Construct optionals
	* @return string Semantic query
	* @access private
	*/
	private function create_sematic_query($owl, $optional = array(), $filter = "", $limit = 1) {
		$n = ($this->debug) ? "\n" : "";
		$nt = ($this->debug) ? "\n	" : "";
		$query = "SELECT * WHERE {" . $nt . "?item a dbo:" . $owl . " . " . $nt . "?item rdfs:label ?label . " . $nt . "?item dbo:abstract ?abstract . " . $nt . $nt . $this->construct_optional($optional) . $nt . (!trim($filter) ? "" : 'FILTER (str(?label) = "' . $filter . '")') . $n . '} LIMIT ' . $limit . $n;
		return $query;
	}
	
	/**
	* Parse the output
	*
	* @param array $result Array of semantic query result
	* @param string $wiki Wikipedia result
	* @param string $query Created semantic query
	* @see semantic_data::is_remote_file() Is remote file
	* @see semantic_data::debug() Debug
	* @see semantic_data::get_default_prefixes() Get default prefixes
	* @see semantic_data::output() Output
	* @see semantic_data::format() Format
	* @see semantic_data::startime() Start time
	* @see semantic_data::end_time() End time
	* @return string|void Search result parsed
	* @access private
	*/
	private function process($result, $wiki, $query) {
		foreach($result[0] as $k => $row) {
			$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
			if(preg_match($reg_exUrl, $row, $url) && $k !== "immagine" && $k !== "thumbnail" && $k !== "item") {
				$f = (pathinfo($url[0]));
				if($this->output == "html") {
					$res[$k] = preg_replace($reg_exUrl, '<a target="_blank" href="' . str_replace("dbpedia.org/resource", "wikipedia.org/wiki", $url[0]) . '">' . str_replace(array("_", "Categoria:"), array(" ", ""), $f["basename"]) . '</a>', $row);
				} else {
					$res[$k]["text"] = str_replace(array("_", "Categoria:"), array(" ", ""), $f["basename"]);
					$res[$k]["link"] = str_replace("dbpedia.org/resource", "wikipedia.org/wiki", $url[0]);
				}
			} else {
				if($k == "item") {
					$res["risorsa_dbpedia"] = trim($row);
				}
				if($this->is_remote_file(trim($result[0]->immagine))) {
					$res["immagine"] = trim($result[0]->immagine);
				} else {
					if($this->is_remote_file(trim($result[0]->thumbnail))) {
						$res["thumbnail"] = trim($result[0]->thumbnail);
					} else {
						$pi = pathinfo(trim($result[0]->thumbnail));
						$res["thumbnail"] = str_replace("commons", "it", trim($result[0]->thumbnail));
						$res["immagine"] = str_replace("commons/thumb", "it", trim($pi["dirname"]));
					}
				}
				if(!trim($res["thumbnail"])) {
					unset($res["thumbnail"]);
				}
				if(!trim($res["immagine"])) {
					unset($res["immagine"]);
				}
				if(isset($res["durata"]) && strlen(trim($res["durata"])) > 0) {
					$res["durata_totale"] = gmdate("H:i:s", round($res["durata"]));
				}
				if(isset($res["audio"]) && strpos(trim($res["audio"]), "sonor") !== false) {
					$res["audio"] = "si";
				}
				if(isset($res["colore"]) && strpos(trim($res["colore"]), "color") !== false) {
					$res["colore"] = "si";
				}
				if($k !== "item" && $k !== "thumbnail") {
					if($this->output == "html") {
						$res[$k] = str_replace(array("> ", " </"), array(">", "</"), preg_replace("/\*(\ |)(.*?)\:(.*?)(\n|$)/", '<dt><small>$2</small></dt><dd><small>$3</small></dd>', trim($row)));
						if(strpos($res[$k], "<dt>") !== false) {
							$res[$k] = '<dl class="dl-horizontal collapse" id="' . $k . '_link">' . $res[$k] . "</dl>";
						}
					} else {
						$obj = trim($row);
						if(preg_match_all("/\*(|\s+)(.*?)\n/ms", $obj, $m)) {
							if(is_array($m[2])) {
								foreach($m[2] as $mmk => $mmv) {
									if(strpos($mmv, ":") !== false) {
										list($a, $r) = explode(":", $mmv);
										$res[$k][trim($a)] = trim($r);
									}
								}
							} else {
								$res[$k] = $m[2];
							}
						} else {
							$res[$k] = $obj;
						}
					}
				}
			}
		}
		if($this->debug) {
			header("Content-type: text/plain; charset=utf-8");
			
			print "Search result in Wikipedia: " . $wiki . "\n\n";
			print "dbpedia page: " . $res["risorsa_dbpedia"]. "\n\n";
			print "SPARQL ENDPOINT:\nhttp://it.dbpedia.org/sparql\n\n";
			print "QUERY IN ENDPOINT:\nhttp://it.dbpedia.org/sparql?query=" . urlencode($this->get_default_prefixes("semantic") . $query) . "\n\n";
			print "SEMANTIC QUERY:\n" . str_repeat("*", 150) . "\n\n";
			print $this->get_default_prefixes("semantic");
			print $query;
			print "\n\n" . str_repeat("*", 150) . "\n\n";
			print "QUERY RESULT:\n" . str_repeat("-", 150) . "\n\n";
			
			$this->output = ($this->output == "") ? "all" : $this->output;
			switch($this->output) {
				case "all":
					print_r($result);
					print_r($res);
					break;
				case "array":
					print_r($res);
					break;
				case "object":
					print_r($res);
					break;
			}
			$end_time = $this->end_time($this->startime);
			print "\n" . str_repeat("-", 150) . "\nNinuxoo 2.0 Semantic query engine\nQuery generata automaticamente il " . date("d/m/Y \a\l\l\e h:i:s") . "\nTempo di esecuzione: " . $end_time . " secondi.\n\n";
		} else {
			$end_time = $this->end_time($this->startime);
			$res["time"] = $end_time;
			$this->format = ($this->format == "") ? "jsonp" : $this->format;
			
			switch($this->format) {
				case "array":
					header("Content-type: text/plain; charset=utf-8");
					print_r($res);
					break;
				case "json":
					header("Content-type: text/plain; charset=utf-8");
					print json_encode($res);
					break;
				case "jsonp":
					header("Content-type: text/plain; charset=utf-8");
					print $this->indent(json_encode($res));
					break;
				case "html":
					header("Content-type: text/html");
					
					foreach($res as $rk => $rv) {
						$th .= "<th>" . $rk . "</th>";
						$td .= "<td>" . $rv . "</td>";
					}
					print '<table border="1"><tr>' . $th . "</tr><tr>" . mb_convert_encoding($td, "HTML-ENTITIES", "UTF-8") . "</tr></table>";
					break;
			}
		}
	}
	
	/**
	* Start query for audio data
	*
	* @param string $album Album label to search
	* @see semantic_data::clean_text() Clean text
	* @see semantic_data::create_sematic_query() Create sematic query
	* @see wikipedia-::search() Wikipedia > Search
	* @see easyrdf::query() EasyRdf > Query
	* @return string|void Search result parsed
	* @access private
	*/
	public function audio($album) {
		$wiki = $this->wikipedia->search($this->clean_text($album));
		
		if(count($wiki[0]) > 0) {
			$title = str_replace("_", " ", $wiki[0]["title"]);
			$optional = array(
				"dbp:titolo" => "titolo",
				"dbp:artista" => "artista",
				"rdfs:comment" => "commento",
				"dbp:registrato" => "registrazione",
				"dbo:totalDiscs" => "dischi",
				"dbo:totalTracks" => "tracce",
				"dbp:durata" => "durata",
				"foaf:depiction" => "immagine",
				"dbo:thumbnail" => "thumbnail",
				"dbp:anno" => "anno",
				"dbp:genere" => "genere",
				"dbp:precedente" => "disco_precedente",
				"dbp:successivo" => "disco_successivo",
				"dbp:tipoAlbum" => "tipo_album. ",
				"foaf:isPrimaryTopicOf" => "pagina_Wikipedia"
			);
			$query = $this->create_sematic_query("Album", $optional, $title);
			$result = $this->easyrdf->query($query);
			
			if(count($result) > 0) {
				$this->process($result, $title, $query);
			} else {
				print "no results";
				exit();
			}
		} else {
			print "no results";
			exit();
		}
	}
	
	/**
	* Start query for book data
	*
	* @param string $libro Book title to search
	* @see semantic_data::clean_text() Clean text
	* @see semantic_data::create_sematic_query() Create sematic query
	* @see wikipedia-::search() Wikipedia > Search
	* @see easyrdf::query() EasyRdf > Query
	* @return string|void Search result parsed
	* @access private
	*/
	public function book($libro) {
		$wiki = $this->wikipedia->search($this->clean_text($libro));
		
		if(count($wiki[0]) > 0) {
			$title = str_replace("_", " ", $wiki[0]["title"]);
			$optional = array(
				"dbp:titolo" => "titolo",
				"dbp:autore" => "autore",
				"rdfs:comment" => "commento",
				"dbp:lingua" => "lingua",
				"dbp:annoorig" => "anno",
				"dbo:genere" => "genere",
				"dbp:immagine" => "immagine",
				"dbo:thumbnail" => "thumbnail",
				"dbp:sottogenere" => "sottogenere",
				"dbp:protagonista" => "protagonista",
				"dbp:tipo" => "tipo",
				"foaf:isPrimaryTopicOf" => "pagina_Wikipedia"
			);
			$query = $this->create_sematic_query("Book", $optional, $title);
			$result = $this->easyrdf->query($query);
			
			if(count($result) > 0) {
				$this->process($result, $title, $query);
			} else {
				print "no results";
				exit();
			}
		} else {
			print "no results";
			exit();
		}
	}
	
	/**
	* Start query for film data
	*
	* @param string $film Film title to search
	* @see semantic_data::clean_text() Clean text
	* @see semantic_data::create_sematic_query() Create sematic query
	* @see wikipedia-::search() Wikipedia > Search
	* @see easyrdf::query() EasyRdf > Query
	* @return string|void Search result parsed
	* @access private
	*/
	public function film($film) {
		$wiki = $this->wikipedia->search($this->clean_text($film));
		
		if(count($wiki[0]) > 0) {
			$title = str_replace("_", " ", $wiki[0]["title"]);
			$optional = array(
				"dbp:titoloitaliano" => "titolo_in_italiano",
				"dbp:titolooriginale" => "titolo_originale",
				"dbp:attori" => "attori",
				"rdfs:annouscita" => "anno",
				"rdfs:comment" => "commento",
				"dbp:casaproduzione" => "casa_di_produzione",
				"foaf:depiction" => "immagine",
				"dbo:thumbnail" => "thumbnail",
				"dbp:didascalia" => "didascalia",
				"dbp:distribuzioneitalia" => "distribuzione_in_Italia",
				"dbp:doppiatoriitaliani" => "doppiatori",
				"dbp:durata" => "durata",
				"dbp:fotografo" => "fotografia",
				"dbp:montatore" => "montaggio",
				"dbp:produttore" => "produzione",
				"dbp:sceneggiatore" => "sceneggiatura",
				"dbp:scenografo" => "scenografia",
				"dbp:soggetto" => "soggetto",
				"dbp:regista" => "regia",
				"dbp:tipoaudio" => "audio",
				"dbp:tipocolore" => "colore",
				"dbp:genere" => "genere ",
				"foaf:isPrimaryTopicOf" => "pagina_Wikipedia"
			);
			$query = $this->create_sematic_query("Film", $optional, $title);
			$result = $this->easyrdf->query($query);
			
			if(count($result) > 0) {
				$this->process($result, $title, $query);
			} else {
				print "no results";
				exit();
			}
		} else {
			print "no results";
			exit();
		}
	}
	
	/**
	* Start query for artist data
	*
	* @param string $person Artist name to search
	* @see semantic_data::clean_text() Clean text
	* @see semantic_data::create_sematic_query() Create sematic query
	* @see wikipedia-::search() Wikipedia > Search
	* @see easyrdf::query() EasyRdf > Query
	* @return string|void Search result parsed
	* @access private
	*/
	public function person($person) {
		$wiki = $this->wikipedia->search($this->clean_text($person));
		if(count($wiki[0]) > 0) {
			$title = str_replace("_", " ", $wiki[0]["title"]);
			$optional = array(
				"dbp:nome" => "nome",
				"dbp:cognome" => "cognome",
				"dbo:formerName" => "formerName",
				"rdfs:comment" => "commento",
				"rdfs:contenuto" => "contenuto",
				"dbp:nazione" => "nazione",
				"dbp:nazionalità" => "nazionalita",
				"dbp:postnazionalità" => "postnazionalita",
				"dbp:profession" => "professione",
				"dbp:tipoArtista" => "tipo_di_artista",
				"dbp:numeroAlbumLive" => "album_dal_vivo",
				"dbp:numeroTotaleAlbumPubblicati" => "totale_album",
				"dbp:attività" => "attivita",
				"dbp:attivitàaltre" => "altre_attivita",
				"dbp:immagine" => "immagine",
				"foaf:depiction" => "depiction",
				"dbo:thumbnail" => "thumbnail",
				"dbp:genere" => "genere",
				"dbp:annonascita" => "anno_di_nascita",
				"dbp:annomorte" => "anno_di_morte",
				"dbo:birthPlace" => "luogo_nascita",
				"dbo:deathPlace" => "luogo_di_morte",
				"dbp:annoInizioAttività" => "inizio_attivita",
				"dbp:annoFineAttività" => "fine_attivita",
				"dbp:tombeFamose" => "luogo_di_sepoltura",
				"foaf:isPrimaryTopicOf" => "pagina_Wikipedia"
			);
			$query = $this->create_sematic_query("Person", $optional, $title);
			$result = $this->easyrdf->query($query);
			
			if(count($result) > 0) {
				$this->process($result, $title, $query);
			} else {
				print "no results";
				exit();
			}
		} else {
			print "no results";
			exit();
		}
	}
}
?>