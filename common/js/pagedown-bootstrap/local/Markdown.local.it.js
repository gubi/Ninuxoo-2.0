// Usage:
//
// var myConverter = new Markdown.Editor(myConverter, null, { strings: Markdown.local.it });

(function () {
        Markdown.local = Markdown.local || {};
        Markdown.local.it = {
		bold: "Grassetto <strong> Ctrl+B",
		boldexample: "testo grassetto",

		italic: "Corsivo <em> Ctrl+I",
		italicexample: "testo corsivo",

		link: "Collegamento ipertestuale <a> Ctrl+L",
		linkdescription: "inserisci una descrizione qui",
		linkdialog: "<p><b>Inserisci un collegamento</b><br />con questo ordine:</p><p><tt>http://example.com/ \"titolo opzionale\"</tt></p>",

		quote: "Citazione <blockquote> Ctrl+Q",
		quoteexample: "Citazione",

		code: "Codice <pre><code> Ctrl+K",
		codeexample: "inserisci del codice qui",

		image: "Immagine <img> Ctrl+G",
		imagedescription: "inserisci la descrizione qui",
		imagedialog: "<p><b>Inserisci l'immagine</b><br />con questo ordine:</p><p><tt>http://example.com/images/diagram.jpg \"titolo opzionale\"</tt><br /><br>Oppure <a href='http://www.google.com/search?q=free+image+hosting' target='_blank'>carica un'immagine</a></p>",

		olist: "Elenco numerato <ol> Ctrl+O",
		ulist: "Elenco ordinato <ul> Ctrl+U",
		litem: "Elemento di un elenco",

		heading: "Intestazione <h1>/<h2> Ctrl+H",
		headingexample: "Intestazione",

		hr: "Riga orizzontale <hr> Ctrl+R",

		undo: "Annulla - Ctrl+Z",
		redo: "Ripeti - Ctrl+Y",
		redomac: "Ripeti - Ctrl+Shift+Z",

		help: "Aiuto su formattazione Markdown"
	};
})();