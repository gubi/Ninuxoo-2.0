<!-- CodeMirror plugin -->
<script src="common/js/codemirror/lib/codemirror.js"></script>
<link rel="stylesheet" href="common/js/codemirror/lib/codemirror.css">
<script src="common/js/codemirror/lib/util/dialog.js"></script>
<link rel="stylesheet" href="common/js/codemirror/lib/util/dialog.css">
<script src="common/js/codemirror/lib/util/searchcursor.js"></script>
<script src="common/js/codemirror/lib/util/search.js"></script>
<script src="common/js/codemirror/lib/util/simple-hint.js"></script>
<link rel="stylesheet" href="common/js/codemirror/lib/util/simple-hint.css">
<link rel="stylesheet" href="common/js/codemirror/theme/3024-day.css">
<link rel="stylesheet" href="common/js/codemirror/theme/3024-night.css">
<link rel="stylesheet" href="common/js/codemirror/theme/ambiance.css">
<link rel="stylesheet" href="common/js/codemirror/theme/base16-dark.css">
<link rel="stylesheet" href="common/js/codemirror/theme/base16-light.css">
<link rel="stylesheet" href="common/js/codemirror/theme/blackboard.css">
<link rel="stylesheet" href="common/js/codemirror/theme/cobalt.css">
<link rel="stylesheet" href="common/js/codemirror/theme/eclipse.css">
<link rel="stylesheet" href="common/js/codemirror/theme/elegant.css">
<link rel="stylesheet" href="common/js/codemirror/theme/erlang-dark.css">
<link rel="stylesheet" href="common/js/codemirror/theme/lesser-dark.css">
<link rel="stylesheet" href="common/js/codemirror/theme/mbo.css">
<link rel="stylesheet" href="common/js/codemirror/theme/midnight.css">
<link rel="stylesheet" href="common/js/codemirror/theme/monokai.css">
<link rel="stylesheet" href="common/js/codemirror/theme/neat.css">
<link rel="stylesheet" href="common/js/codemirror/theme/night.css">
<link rel="stylesheet" href="common/js/codemirror/theme/paraiso-dark.css">
<link rel="stylesheet" href="common/js/codemirror/theme/paraiso-light.css">
<link rel="stylesheet" href="common/js/codemirror/theme/rubyblue.css">
<link rel="stylesheet" href="common/js/codemirror/theme/solarized.css">
<link rel="stylesheet" href="common/js/codemirror/theme/the-matrix.css">
<link rel="stylesheet" href="common/js/codemirror/theme/tomorrow-night-eighties.css">
<link rel="stylesheet" href="common/js/codemirror/theme/twilight.css">
<link rel="stylesheet" href="common/js/codemirror/theme/vibrant-ink.css">
<link rel="stylesheet" href="common/js/codemirror/theme/xq-dark.css">
<link rel="stylesheet" href="common/js/codemirror/theme/xq-light.css">
<script src="common/js/codemirror/lib/util/javascript-hint.js"></script>
<script src="common/js/codemirror/lib/util/xml-hint.js"></script>
<script src="common/js/codemirror/lib/util/match-highlighter.js"></script>
<script src="common/js/codemirror/lib/util/xml-hint.js"></script>
<script src="common/js/codemirror/lib/util/overlay.js"></script>
<script src="common/js/codemirror/lib/util/searchcursor.js"></script>
<script src="common/js/codemirror/mode/apl/apl.js"></script>
<script src="common/js/codemirror/mode/asterisk/asterisk.js"></script>
<script src="common/js/codemirror/mode/clike/clike.js"></script>
<script src="common/js/codemirror/mode/clojure/clojure.js"></script>
<script src="common/js/codemirror/mode/cobol/cobol.js"></script>
<script src="common/js/codemirror/mode/markdown/markdown.js"></script>
<script src="common/js/codemirror/mode/ninuxoo/ninuxoo.js"></script>
<script type="text/javascript">
CodeMirror.connect(window, "resize", function() {
	var showing = document.body.getElementsByClassName("CodeMirror-fullscreen")[0];
	if (!showing) return;
	showing.CodeMirror.getScrollerElement().style.height = winHeight() + "px";
});
CodeMirror.commands.autocomplete = function(cm) {
	CodeMirror.simpleHint(cm, CodeMirror.javascriptHint);
}
</script>