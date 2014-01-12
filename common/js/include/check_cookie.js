function checkCookie(){
	var cookieEnabled=(navigator.cookieEnabled)? true : false   
	if (typeof navigator.cookieEnabled=="undefined" && !cookieEnabled){ 
		document.cookie="testcookie";
		cookieEnabled=(document.cookie.indexOf("testcookie")!=-1)? true : false;
	}
	return (cookieEnabled) ? true : false;
}
$(document).ready(function() {
	if(!checkCookie()) {
		$(".progress-bar").switchClass("progress-bar-warning", "progress-bar-danger");
		$("#search_loader > .help-block").html('<span class="fa fa-times"></span>&nbsp;&nbsp;Non &egrave; possibile effettuare ricerche poich&eacute; i cookie non sono abilitati').switchClass("help-block", "text-danger");
	}
});
