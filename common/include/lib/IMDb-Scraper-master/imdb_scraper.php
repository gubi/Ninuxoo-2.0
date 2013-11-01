<?php

/*

IMDb Scraper v. 1.0 - 14th of September, 2011

Scrapes information about movie and tv show titles from IMDb (imdb.it).

By Aram Kocharyan
http://ak.net84.net/php/imdb-scraper/
akarmenia@gmail.com
twitter.com/akarmenia

*/

// Utility functions
require_once('util.php');

// Prevent timeout
set_time_limit(0);
ini_set('max_execution_time', 0);

Class IMDbScraper {
	
	// Performs an IMDb search and returns the info for the best match using the given query title and year
	public static function get($title, $year = NULL) {
		if ( ($result = self::find($title, $year = NULL)) !== FALSE ) {
			return self::info($result['id']);
		} else {
			return FALSE;
		}
	}
	
	// Return array of info for a given IMDb id string. eg. 'tt0206512'
	public static function info($id) {
		if (!is_string($id)) {
			throw new Exception("The title must be a string");
		} else {
			$id = preg_replace('#[^t\d]#', '', $id);
		}
		
		$url = 'http://www.imdb.it/title/' . $id . '/';
		
		if ( ($html = curl_get_html($url)) !== FALSE ) {
			$info = self::scrape_info($html);
			$info['id'] = $id;
			$info['url'] = $url;
			return $info;
		} else {
			return FALSE;
		}
		
	}
	
	// Returns the list of IMDb search results for the given title query.
	function search($title) {
		if ( !is_string($title) ) {
			throw new Exception("The title '".$title."' is not valid");
		}
		$url = 'http://www.imdb.it/find?s=tt&q=' . urlencode($title);
		$html = curl_get_html($url);
		
		return self::scrape_search($html);
	}
	
	// Performs an IMDb search and finds the best match to the given title and year.
	function find($title, $year = NULL) {
		if ( !is_string($title) || empty($title) ) {
			throw new Exception("The title is not valid");
		}
		$query = $title;
		if ( is_string($year) ) {
			$year = intval($year);
		}
		if ( is_int($year) ) {
			$query .= ' ' . $year;
		}
		
		// Get results for the search query
		$results = self::search($query);
		if ( empty($results) ) {
			return FALSE;
		}
		
		// Remove any queries that don't match the year
		if ($year !== NULL) {
			$subset = array();
			foreach ($results as $r) {
				if ( intval($r[2]) == $year ) {
					// Add result into subset, year matches
					$subset[] = $r;
				}
			}
		}
		// If no year is provided, or it was and we were left with no results, use the original results
		if ($year === NULL || empty($subset))  {
			$subset = $results;
		}
		
		// Break title query into words
		$query_bits = explode(' ', $title);
		// Get the search result titles
		$titles = array();
		foreach ($results as $r) {
			$titles[] = $r[1];
		}
		// Run a search using the words and see how many matches each search result gets
		$counts = substr_count_arrays($titles, $query_bits);
		
		// TODO check the results and see if the counts are equal (no good matches)
		
		// Get the highest count, or if they are all equal use the first result
		$highest_index = 0;
		$highest_count = $counts[0];
		for ($i = 1; $i < count($counts); $i++) {
			if ($counts[$i] > $highest_count) {
				$highest_index = $i;
			}
		}
		
		// Create an associative array, now that we have our result
		$splitted_id = explode("/", $subset[$highest_index][0]);
		$result['id'] = $splitted_id[0];
		$result['title'] = $subset[$highest_index][1];
		$result['year'] = $subset[$highest_index][2];
		
		return $result;
	}
	
	// Returns an associative array of IMDb information scrapped from an HTML string.
	public static function scrape_info($html) {
		$result = array();
		
		$result['nome'] = utf8_encode(regex_get('#<h1.*?>(.*?)<span#msi', $html, 1));
		$result['descrizione'] = utf8_encode(regex_get('#<h5>Trama:</h5>\s+<div.*?>(.*?)<a.*></a></div>#msi', $html, 1));
		$date = regex_get('#datetime="(\d+)#msi', $html, 1, 'num');
		if (empty($date)) {
			$date = clean_num(regex_get('#<title>[^\(]*\(([^\)]+)\)#msi', $html, 1, 'num'));
		}
		$result['anno'] = $date;
		$result['durata'] = regex_get('#<h5>Durata:</h5><div.*?>(.*?)</div>#msi', $html, 1);
		$result['titolo_originale'] = regex_get('#<h5>Alias:</h5><div.*?>\s+(.*?)<a.*?>#msi', $html, 1);
		$result['poster'] = preg_replace("#._V1._(.*?)_(.*?)_.jpg#", "._V1._SX900_SY900_.jpg", regex_get('#<img.*?id="primary-poster".*?src="(.*?)"#msi', $html, 1));
		
		// Only for Movies
		$result['regia'] = utf8_encode(regex_get('#<h5>Regista:</h5>\s+<div.*?>(.*?)</div>#msi', $html, 1));
		$result['sceneggiatura'] = utf8_encode(str_replace(array("(", ")"), array('<span style="display: inline;">(', ")</span>"), implode(")<br />", explode(") ", str_replace("Vedi altro", "", preg_replace("/\s+/", " ", regex_get('#<h5>Sceneggiatori:</h5>\s+<div.*?>(.*?)</div>#msi', $html, 1)))))));
		// Only for TV shows
		$result['data_uscita'] = str_replace(array("(", ")"), array('<span style="display: inline;">(', ")</span>"), regex_get('#<h5>Data di uscita:</h5>\s+<div.*?>(.*?)<a.*?>#msi', $html, 1));
		
		$result['cast'] = array();
		if (preg_match_all('#<a href="/name/.*?".*?>(\w+ \w+|\w+ \w+ \w+)</a>#msi', $html, $cast)) {
			$result['cast'] = utf8_encode($cast[1]);
		}
		
		$result['genere'] = utf8_encode(regex_get('#<h5>Genere:</h5>\s+<div.*?>(.*?)</div>#msi', $html, 1));
		$result['nazionalita'] = utf8_encode(regex_get('#<h5>Nazionalit&\#xE0;:</h5><div.*?>(.*?)</div>#msi', $html, 1));
		$result['colore'] = regex_get('#<h5>Colore:</h5><div.*?>(.*?)</div>#msi', $html, 1);
		$result['risoluzione'] = regex_get('#<h5>Aspect Ratio:</h5><div.*?>(.*?)<a.*?>#msi', $html, 1);
		$result['sonoro'] = regex_get('#<h5>Sonoro:</h5><div.*?>(.*?)</div>#msi', $html, 1);
		
		$result['rating'] = regex_get('#<div class="starbar-meta">\s+<b>(.*?)/.*</b>#msi', $html, 1, 'num');
		$result['max-rating'] = regex_get('#<div class="starbar-meta">\s+<b>.*?/(.*?)</b>#msi', $html, 1, 'num');
		$result['voter-count'] = regex_get('#<a href="ratings".*?>(.*?)</a>#msi', $html, 1, 'num');
		$result['user-review-count'] = regex_get('#"reviewCount">(.*?)<#msi', $html, 1, 'num');
		$result['critic-review-count'] = regex_get('#(\d+) external critic#msi', $html, 1, 'num');
		
		return $result;
	}
	
	// Returns an array of search results for the given HTML string of an IMDB search page.
	// Each result is an array: (title ID, title, year)
	public static function scrape_search($html) {
		$results = array();
		if (preg_match_all('#<a\s*href\s*=\s*"([^)]*?)"[^>]*?>([^<]*)</a>\s*\((\d*)\)#msi', $html, $matches)) {
			for ($i = 0; $i < count($matches[0]); $i++) {
				$results[$i] = array( imdb_url_id($matches[1][$i]),
									  clean_str($matches[2][$i]),
									  clean_str($matches[3][$i]) );
			}
		}
		return $results;
	}
	 
}

?>
