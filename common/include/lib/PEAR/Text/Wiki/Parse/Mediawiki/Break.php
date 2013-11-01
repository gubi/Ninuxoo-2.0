<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
/**
 * Mediawiki: Parses for explicit line breaks.
 *
 * PHP versions 4 and 5
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Brian J. Sipos <bjs5075@rit.edu>
 * @author     Moritz Venn <ritzmo@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id: Break.php 218241 2006-08-15 16:02:06Z ritzmo $
 * @link       http://pear.php.net/package/Text_Wiki
 */

/**
 * Parses for explicit line breaks.
 * 
 * This class implements a Text_Wiki_Parse to mark explicit line breaks in the
 * source text.
 *
 * @category   Text
 * @package    Text_Wiki
 * @author Brian J. Sipos <bjs5075@rit.edu>
 * @author     Moritz Venn <ritzmo@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Text_Wiki
 * @see        Text_Wiki_Parse::Text_Wiki_Parse()
 */
class Text_Wiki_Parse_Break extends Text_Wiki_Parse {
    
    /**
    * The regular expression used to parse the source text and find
    * matches conforming to this rule.  Used by the parse() method.
    * 
    * @access public
    * @var string
    * @see parse()
    */
    var $regex = '/<br\ *\/?>/';
    
    
    /**
    * Generates a replacement token for the matched text.
    * 
    * @access public
    * @param array &$matches The array of matches from parse().
    * @return string A delimited token to be used as a placeholder in
    * the source text.
    */
    function process(&$matches)
    {    
        return $this->wiki->addToken($this->rule);
    }
}

?>
