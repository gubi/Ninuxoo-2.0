<?php
$osd_file = fopen("osd.xml", "w+");
$nas_name = $conf_file["NAS"]["name"];
$nas_uri = $conf_file["NAS"]["http_root"];

$osd_content = <<<OSD
<?xml version="1.0" encoding="UTF-8"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
	<ShortName>Ninuxoo $nas_name</ShortName>
	<LongName>Ninuxoo $nas_name</LongName>
	<Description>Il motore di ricerca della Community Ninux ~ $nas_name</Description>
	<Tags></Tags>
	<Developer>The Ninux Software Team</Developer>

	<Contact>contatti@ninux.org</Contact>
	<Url type="text/html" method="GET" template="$nas_uri/?q={searchTerms}"/>
	<Url type="application/opensearchdescription+xml" rel="self" template="osd.xml"/>

	<Image height="16" width="16" type="image/x-icon">$nas_uri/common/media/favicon.ico</Image>
	<Attribution>Ninux</Attribution>
	<SyndicationRight>open</SyndicationRight>
	<AdultContent>false</AdultContent>
	<Language>it</Language>
	<OutputEncoding>UTF-8</OutputEncoding>
	<InputEncoding>UTF-8</InputEncoding>
</OpenSearchDescription>
OSD;

// Write content in osd.xml file
fwrite($osd_file, $osd_content);
fclose($osd_file);
chmod("osd.xml", 0777);
?>