function optimize_name(string) {
	return string.replace(/\s+/g, "_").replace(/[^a-zA-Z0-9\ \-\_\~\:]+/g, "-");
}
$(document).ready(function() {
	$("#page_name").bind("keyup change", function() {
		$("#script_name").text('"' + optimize_name($("#page_name").val()) + '"');
		$("#rename_suggestion").css({"display": "inline"}).find("span").text(optimize_name($("#page_name").val()));
	});
	$("#page_name").val(optimize_name($("#page_name").val()));
	$("#page_name").bind("change", function() {
		$("#page_name").val(optimize_name($("#page_name").val()));
		$("#rename_suggestion").delay(1000).fadeOut(300);
	});
	$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
});