<!-- Treeview -->
<link rel="stylesheet" href="common/js/jquery.treeview/jquery.treeview.css" type="text/css" media="screen" />
<script type="text/javascript" src="common/js/jquery.treeview/jquery.treeview.js"></script>
<script type="text/javascript" src="common/js/jquery.treeview/jquery.treeview.edit.js"></script>
<script type="text/javascript" src="common/js/jquery.treeview/jquery.treeview.async.js"></script>

<?php if(isset($_GET["s"]) && trim($_GET["s"]) == "advanced") { ?>
	<!--Chosen-->
	<link rel="stylesheet" href="common/js/chosen/chosen.css" />
	<script type="text/javascript" src="common/js/chosen/chosen-bootstrap.jquery.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		$("select").chosen();
	});
	</script>
<?php } ?>
<?php if(!isset($_GET["s"]) || trim($_GET["s"]) == "") { ?>
	<script type="text/javascript">
	$(document).ready(function() {
		get_stats();
	});
	</script>
<?php } ?>
<!--Zoombox-->
<script type="text/javascript" src="common/js/zoombox/zoombox.js"></script>
<link rel="stylesheet" href="common/js/zoombox/zoombox.css" />
<script type="text/javascript">

$.extend({getUrlVars: function(){ var vars = [], hash; var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&'); for(var i = 0; i < hashes.length; i++) { hash = hashes[i].split('='); vars.push(hash[0]); vars[hash[0]] = hash[1]; } return vars; }, _GET: function(name){ return $.getUrlVars()[name]; }});

function get_stats(){
	$.getJSON("API/index.php", {action: "local_search", op: "resourcestats"}, function(resource) {
		$("#resstats").html();
		$("#resstats").fadeOut(300, function(){
			$(this).html(resource.result + " files indicizzati da questa risorsa locale").fadeIn(300);
		});
	});
}
$(document).ready(function() {
	$("#advanced_search_btn").mousedown(function(){
		var href = $("#search_input").val();
		$("#advanced_search_btn").attr("href", $("#advanced_search_btn").attr("href") + "&q=" + href);
	});
	$("#searchform input[type=search]").focus().parent().switchClass("", "focusedInput");
	
	$(".resultstree").html("");
	$("#logo a[title]").tooltip({placement: "left"});
});
</script>