<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
/**
 * Mediawiki: Parse for URLS in the source text.
 *
 * PHP versions 4 and 5
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Paul M. Jones <pmjones@php.net>
 * @author     Moritz Venn <ritzmo@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id: Url.php 284183 2009-07-16 11:52:10Z rodrigosprimo $
 * @link       http://pear.php.net/package/Text_Wiki
 */

/**
 * Parse for URLS in the source text.
 * 
 * Various URL markings are supported: inline (the URL by itself),
 * inline (where the URL is enclosed in square brackets), and named
 * reference (where the URL is enclosed in square brackets and has a
 * name included inside the brackets).  E.g.:
 *
 * inline      -- http://example.com
 * undescribed -- [http://example.com]
 * described   -- [http://example.com Example Description]
 * described   -- [http://www.example.com|Example Description]
 *
 * When rendering a URL token, this will convert URLs pointing to a .gif,
 * .jpg, or .png image into an inline <img /> tag (for the 'xhtml'
 * format).
 *
 * Token options are:
 * 
 * 'type' => ['inline'|'footnote'|'descr'] the type of URL
 * 
 * 'href' => the URL link href portion
 * 
 * 'text' => the displayed text of the URL link
 * 
 * @category   Text
 * @package    Text_Wiki
 * @author     Paul M. Jones <pmjones@php.net>
 * @author     Moritz Venn <ritzmo@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Text_Wiki
 * @see        Text_Wiki_Parse::Text_Wiki_Parse()
 */
class Text_Wiki_Parse_Url extends Text_Wiki_Parse {
    
    /**
    * Keeps a running count of numbered-reference URLs.
    * 
    * @access public
    * @var int
    */
    var $footnoteCount = 0;
    
    
    /**
    * URL schemes recognized by this rule.
    * 
    * @access public
    * @var array
    */
    var $conf = array(
        'schemes' => array(
            'http://',
            'https://',
            'ftp://',
            'gopher://',
            'news://',
            'file://',
            'mailto:'
        )
    );
    
    
    /**
    * Constructor.
    * 
    * We override the constructor so we can comment the regex nicely.
    * 
    * @access public
    */
    function Text_Wiki_Parse_Url(&$obj)
    {
        parent::Text_Wiki_Parse($obj);
        
        // convert the list of recognized schemes to a regex-safe string,
        // where the pattern delim is a slash
        $tmp = array();
        $list = $this->getConf('schemes', array());
        foreach ($list as $val) {
            $tmp[] = preg_quote($val, '/');
        }
        $schemes = implode('|', $tmp);
        
        // build the regex
        $this->regex =
            "($schemes)" . // allowed schemes
            "(" . // start pattern
            "[^ \\/\"\'{$this->wiki->delim}]*\\/" . // no spaces, backslashes, slashes, double-quotes, single quotes, or delimiters;
            ")*" . // end pattern
            "[^ \\t\\n\\/\"\'{$this->wiki->delim}]*" .
            "[A-Za-z0-9\\/?=&~_()]";
    }
    
    
    /**
    * Find three different kinds of URLs in the source text.
    *
    * @access public
    */
    function parse()
    {
        // -------------------------------------------------------------
        // 
        // Described-reference (named) URLs.
        // 

        // the regular expression for this kind of URL
        $tmp_regex = '/\[(' . $this->regex . ')[ |]([^\]]+)\]/';

        // use a custom callback processing method to generate
        // the replacement text for matches.
        $this->wiki->source = preg_replace_callback(
            $tmp_regex,
            array(&$this, 'processDescr'),
            $this->wiki->source
        );

        
        // -------------------------------------------------------------
        // 
        // Unnamed-reference ('Ordinary'-style) URLs.
        // 
        
        // the regular expression for this kind of URL
        $tmp_regex = '/\[(' . $this->regex . ')\]/U';
        
        // use a custom callback processing method to generate
        // the replacement text for matches.
        $this->wiki->source = preg_replace_callback(
            $tmp_regex,
            //array(&$this, 'processFootnote'),
            array(&$this, 'processOrdinary'),
            $this->wiki->source
        );
        
        
        // -------------------------------------------------------------
        // 
        // Normal inline URLs.
        // 
        
        // the regular expression for this kind of URL
        
        $tmp_regex = '/(^|[^A-Za-z])(' . $this->regex . ')(.*?)/';
        
        // use the standard callback for inline URLs
        $this->wiki->source = preg_replace_callback(
            $tmp_regex,
            array(&$this, 'process'),
            $this->wiki->source
        );


        //$tmp_regex = '/(^|[^A-Za-z])([a-zA-Z])(.*?)/';
        $tmp_regex = '/(^|\s)([a-zA-Z0-9\-]+\.[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+)($|\s)/';
        
        // use the standard callback for inline URLs
        $this->wiki->source = preg_replace_callback(
            $tmp_regex,
            array(&$this, 'processWithoutProtocol'),
            $this->wiki->source
        );

        $tmp_regex = '/(^|\s|'.$this->wiki->delim.')<([a-zA-Z0-9\-\.%_\+\!\*\'\(\)\,]+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+)>(\s|'.$this->wiki->delim.'|$)/';
        
        // use the standard callback for inline URLs
        $this->wiki->source = preg_replace_callback(
            $tmp_regex,
            array(&$this, 'processInlineEmail'),
            $this->wiki->source
        );
    }
    
    
    /**
    * Process inline URLs.
    * 
    * @param array &$matches
    * @param array $matches An array of matches from the parse() method
    * as generated by preg_replace_callback.  $matches[0] is the full
    * matched string, $matches[1] is the first matched pattern,
    * $matches[2] is the second matched pattern, and so on.
    * @return string The processed text replacement.
    */ 
    function process(&$matches)
    {
        // set options
        $options = array(
            'type' => 'inline',
            'href' => $matches[2],
            'text' => $matches[2]
        );
        
        // tokenize
        return $matches[1] . $this->wiki->addToken($this->rule, $options) . $matches[5];
    }

    // TODO: check if this is supported by Mediawiki parser (apparently it is not)
    function processWithoutProtocol(&$matches)
    {
        // set options
        $options = array(
            'type' => 'inline',
            'href' => 'http://'.$matches[2],
            'text' => $matches[2]
        );
        
        // tokenize
        return $matches[1] . $this->wiki->addToken($this->rule, $options) . $matches[4];
    }

    // TODO: check if this is supported by Mediawiki parser (apparently it is not)
    function processInlineEmail(&$matches)
    {
        // set options
        $options = array(
            'type' => 'inline',
            'href' => 'mailto://'.$matches[2],
            'text' => $matches[2]
        );
        
        // tokenize
        return $matches[1] . $this->wiki->addToken($this->rule, $options) . $matches[4];
    }    
    
    /**
    * Process numbered (footnote) URLs.
    * 
    * Token options are:
    *
    * @param array &$matches
    * @param array $matches An array of matches from the parse() method
    * as generated by preg_replace_callback.  $matches[0] is the full
    * matched string, $matches[1] is the first matched pattern,
    * $matches[2] is the second matched pattern, and so on.
    * @return string The processed text replacement.
    */ 
    function processFootnote(&$matches)
    {
        // keep a running count for footnotes 
        $this->footnoteCount++;
        
        // set options
        $options = array(
            'type' => 'footnote',
            'href' => $matches[1],
            'text' => $this->footnoteCount
        );
        
        // tokenize
        return $this->wiki->addToken($this->rule, $options);
    }
    
     function processOrdinary(&$matches)
    {
    	// keep a running count for footnotes 
        $this->footnoteCount++;
        
        // set options
        $options = array(
            'type' => 'descr',
            'href' => $matches[1],
            'text' => $matches[1]
        );
        
        // tokenize
        return $this->wiki->addToken($this->rule, $options);
    }
    
    
    /**
    * Process described-reference (named-reference) URLs.
    * 
    * Token options are:
    *     'type' => ['inline'|'footnote'|'descr'] the type of URL
    *     'href' => the URL link href portion
    *     'text' => the displayed text of the URL link
    * 
    * @param array &$matches
    * @param array $matches An array of matches from the parse() method
    * as generated by preg_replace_callback.  $matches[0] is the full
    * matched string, $matches[1] is the first matched pattern,
    * $matches[2] is the second matched pattern, and so on.
    * @return string The processed text replacement.
    * 
    */ 
    
    function processDescr(&$matches)
    {
        // set options
        $options = array(
            'type' => 'descr',
            'href' => $matches[1],
            'text' => $matches[4]
        );

        // tokenize
        return $this->wiki->addToken($this->rule, $options);
    }
}
?>
