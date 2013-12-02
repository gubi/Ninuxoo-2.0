<?php
    /**
     * Making a SPARQL SELECT query
     *
     * This example creates a new SPARQL client, pointing at the
     * dbpedia.org endpoint. It then makes a SELECT query that
     * returns all of the countries in DBpedia along with an
     * english label.
     *
     * Note how the namespace prefix declarations are automatically
     * added to the query.
     *
     * @package    EasyRdf
     * @copyright  Copyright (c) 2009-2013 Nicholas J Humfrey
     * @license    http://unlicense.org/
     */

    set_include_path(get_include_path() . PATH_SEPARATOR . '../lib/');
    require_once "EasyRdf.php";
    require_once "html_tag_helpers.php";

    // Setup some additional prefixes for DBpedia
    EasyRdf_Namespace::set('category', 'http://dbpedia.org/resource/Category:');
    EasyRdf_Namespace::set('dbpedia', 'http://dbpedia.org/resource/');
    EasyRdf_Namespace::set('dbo', 'http://dbpedia.org/ontology/');
    EasyRdf_Namespace::set('dbp', 'http://dbpedia.org/property/');

    $sparql = new EasyRdf_Sparql_Client('http://dbpedia.org/sparql');
?>
<?php
    $result = $sparql->query(
        'PREFIX dbo: <http://dbpedia.org/ontology/>
	PREFIX res: <http://dbpedia.org/resource/>
	PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
	PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
	SELECT DISTINCT ?uri ?string 
	WHERE {
		?uri rdf:type dbo:Film .
		?uri dbo:director res:Steven_Spielberg .
		OPTIONAL {?uri rdfs:label ?string . FILTER (lang(?string) = "en") }
	}'
    );
    header("Content-type: text/plain");
    print_r($result);
    exit();
    foreach ($result as $row) {
        echo "<li>".link_to($row->label, $row->country)."</li>\n";
    }
?>
</ul>
<p>Total number of countries: <?= $result->numRows() ?></p>

</body>
</html>
