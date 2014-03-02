<?php
/**
* Ninuxoo 2.0
*
* PHP Version 5.3
*
* @copyright 2013 Alessandro Gubitosi / Gubi (http://iod.io)
* @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3
* @link https://github.com/gubi/Ninuxoo-2.0
*/

/**
* A class for search Wikipedia page using its API
*
* This class call Wikipedia search API to retrieve the page name for a given text<br>
* For more info about Wikipedia's API visit http://en.wikipedia.org/w/api.php
* Example of usage:
* <pre>
* $wikipedia = new wikipedia(); 
* $wikipedia->srlimit(1);
* $wikipedia->srprop("");
* $res = $wikipedia->search($search_term);
* print_r($res);
* </pre>
*
* @package	Ninuxoo 2.0
* @author		Alessandro Gubitosi <gubi.ale@iod.io>
* @license 		http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3
* @access		public
* @link		https://github.com/gubi/Ninuxoo-2.0/blob/master/common/include/classes/wikipedia.class.php
*/
class wikipedia{
	/**
	* Construct
	*
	* Initialize the class
	*
	* @global string $this->api_url Base Wikipedia api url
	* @param string $lang
	* @return void
	*/
	function __construct($lang = "it") {
		$this->api_url = "http://" . $lang . ".wikipedia.org/w/api.php";
	}
	
	/**
	* Search for all page titles (or content) that has this value
	*
	* This parameter is required
	*
	* @param string $srsearch Search term
	* @access public
	* @return string
	*/
	public function srsearch($srsearch) {
		if (strlen(trim($srsearch)) == 0) {
			print "Nessuna query da ricercare";
			exit();
		} else {
			return $srsearch;
		}
	}
	/**
	* The format of the output
	*
	* One value: `json`, `jsonfm`, `php`, `phpfm`, `wddx`, `wddxfm`, `xml`, `xmlfm`, `yaml`, `yamlfm`, `rawfm`, `txt`, `txtfm`, `dbg`, `dbgfm`, `dump`, `dumpfm`, `none`<br>
	* Default: <tt>json</tt>
	*
	* @param string $format
	* @return string $this->srformat
	* @access public
	*/
	public function format($format = "json") {
		$this->srformat = $format;
		return $this->srformat;
	}
	/**
	* The namespace(s) to enumerate
	*
	* Values (separate with '<tt>|</tt>'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109, 118, 119, 710, 711, 446, 447, 828, 829<br>
	* Maximum number of values 50 (500 for bots)<br>
	* Default: <tt>0</tt>
	*
	* @param int|array $namespace
	* @return int $this->namespace
	* @access public
	*/
	public function srnamespace($namespace = 0) {
		if (!is_array($namespace)) {
			if(is_numeric($namespace) && $namespace > 0) {
				$this->namespace = $namespace;
			} else {
				$this->namespace = null;
			}
		} else {
			$this->namespace = implode("|", $namespace);
		}
		return $this->namespace;
	}
	/**
	* Search inside the text or titles
	*
	* One value: `title`, `text`, `nearmatch`
	*
	* @param string $what
	* @return null|string $this->what
	* @access public
	*/
	public function srwhat($what = "title") {
		if(strlen(trim($what)) > 0 && $what !== "title") {
			$this->what = $what;
		} else {
			$this->what = null;
		}
		return $this->what;
	}
	/**
	* What metadata to return
	*
	* Values (separate with '<tt>|</tt>'): `totalhits`, `suggestion`<br>
	* Default: <tt>totalhits|suggestion</tt>
	*
	* @param string|array $info
	* @return null|string $this->info
	* @access public
	*/
	public function srinfo($info = array("totalhits", "suggestion")) {
		if(!is_array($info)) {
			$this->info = $info;
		} else {
			$this->info = implode("|", $info);
			if($this->info == "totalhits|suggestion") {
				$this->info = null;
			}
		}
		return $this->info;
	}
	/**
	* What properties to return
	*
	* `size`: Adds the size of the page in bytes<br>
	* `wordcount`: Adds the word count of the page<br>
	* `timestamp`: Adds the timestamp of when the page was last edited<br>
	* `score`: Adds the score (if any) from the search engine<br>
	* `snippet`: Adds a parsed snippet of the page<br>
	* `titlesnippet`: Adds a parsed snippet of the page title<br>
	* `redirectsnippet`: Adds a parsed snippet of the redirect title<br>
	* `redirecttitle`: Adds the title of the matching redirect<br>
	* `sectionsnippet`: Adds a parsed snippet of the matching section title<br>
	* `sectiontitle`: Adds the title of the matching section<br>
	* `hasrelated`: Indicates whether a related search is available
	* 
	* Default: <tt>size|wordcount|timestamp|snippet</tt>
	*
	* @param string|array $prop
	* @return null|string $this->prop
	* @access public
	*/
	public function srprop($prop = array("size", "wordcount", "timestamp", "snippet")) {
		if(!is_array($prop)) {
			$this->prop = $prop;
		} else {
			$this->prop = implode("|", $prop);
			if($this->prop == "size|wordcount|timestamp|snippet") {
				$this->prop = null;
			}
		}
		return $this->prop;
	}
	/**
	* Include redirect pages in the search
	*
	* @param bool $redirects
	* @return null|true $this->redirects
	* @access public
	*/
	public function srredirects($redirects = false) {
		if($redirects) {
			$this->redirects = true;
		} else {
			$this->redirects = null;
		}
		return $this->redirects;
	}
	/**
	* Use this value to continue paging (return by query)
	*
	* Default: <tt>0</tt>
	*
	* @param int $offset
	* @return null|int $this->offset
	* @access public
	*/
	public function sroffset($offset = 0) {
		if(is_numeric($offset) && $offset > 0) {
			$this->offset = $offset;
		} else {
			$this->offset = null;
		}
		return $this->offset;
	}
	/**
	* How many total pages to return
	*
	* No more than 50 (500 for bots) allowed<br>
	* Default: <tt>10</tt>
	*
	* @param int $limit
	* @return null|int $this->limit
	* @access public
	*/
	public function srlimit($limit = 10) {
		if(is_numeric($limit) && $limit !== 10) {
			$this->limit = $limit;
		} else {
			$this->limit = null;
		}
		return $this->limit;
	}
	/**
	* Which search backend to use, if not the default
	*
	* One value: `LuceneSearch`, `CirrusSearch`<br>
	* Default: <tt>LuceneSearch</tt>
	*
	* @param string $backend
	* @return null|string $this->backend
	* @access public
	*/
	public function srbackend($backend = "LuceneSearch") {
		if(strlen(trim($backend)) > 0 && $backend !== "LuceneSearch") {
			$this->backend = $backend;
		} else {
			$this->backend = null;
		}
		return $this->backend;
	}
	
	/**
	* Search with API
	*
	* @param string $term Search term
	* @see wikipedia::__construct() Construct
	* @return array
	* @access public
	*/
	public function search($term) {
		$obj = new stdClass();
		$obj->action = "query";
		$obj->list = "search";
		$obj->format = isset($this->srformat) ? $this->srformat : $this->format();
		
		$obj->srnamespace = isset($this->namespace) ? $this->namespace : $this->srnamespace();
		$obj->srwhat = isset($this->what) ? $this->what : $this->srwhat();
		$obj->srinfo = isset($this->info) ? $this->info : $this->srinfo();
		$obj->srprop = isset($this->prop) ? $this->prop : $this->srprop();
		$obj->srredirects = isset($this->redirects) ? $this->redirects : $this->srredirects();
		$obj->sroffset = isset($this->offset) ? $this->offset : $this->sroffset();
		$obj->srlimit = isset($this->limit) ? $this->limit : $this->srlimit();
		$obj->srbackend = isset($this->backend) ? $this->backend : $this->srbackend();
		$obj->srsearch = $this->srsearch($term);
		
		$url = $this->api_url . "?" . rawurldecode(http_build_query($obj));
		$str = json_decode(file_get_contents($url), 1);
		array_walk_recursive($str, function(&$value) {
			$value = utf8_decode(html_entity_decode($value));
		});
		foreach($str["query"]["search"] as $k => $v) {
			$res[$k]["title"] = str_replace(" ", "_", $v["title"]);
		}
		return $res;
	}
}
?>