<?php
/**
 * Contains the definition of the Output Formatter class
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Output
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link        http://pear.php.net/package/VersionControl_Hg
 */

/**
 * Format output
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Output
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link        http://pear.php.net/package/VersionControl_Hg
 */
class VersionControl_Hg_Command_Output_Formatter
{
    const FETCH_RAW = 'raw'; //AKA text / plain text
    const FETCH_ARRAY = 'array'; //default!
    //const FETCH_OBJECT = 'object'; //but why?? seriously, why?
    const FETCH_XML = 'xml';
    const FETCH_JSON = 'json';
    const FETCH_YAML = 'yaml';
    const FETCH_SERIALIZE = 'serialize';

    public static $formats = array(
        'raw', 'serialize', 'array', 'xml', 'json', 'yaml',
    );

    protected $fetch_mode;

    //Having run() return an VersionControl_Hg_Command_Output object
    //might just be useful, sort of how PDO / Doctrine can...
    //though we might be overengineering, just a wee tad bit.

    //we probably really don't need the mode set upon instantiation
    public function __construct()
    {
    }

    //public function parse() {}

    public function getFetchMode() {
        return $this->fetch_mode;
    }

    protected function setFetchMode($mode) {
        $this->$fetch_mode = $mode;
    }

    //Hmmm, how to reconstruct
    public function toRaw(array $output) {
        return var_export($output);
    }

    public function toSerialize(array $output) {
        return serialize($output);
    }

    //public function toArray() {}
    //public function toAssoc() {}
    //public function toObject() {}

/**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public static function toXML( $data, $rootNodeName = 'ResultSet', &$xml=null ) {

        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        //if ( ini_get('zend.ze1_compatibility_mode') == 1 ) ini_set ( 'zend.ze1_compatibility_mode', 0 );
        if ( is_null( $xml ) ) $xml = simplexml_load_string( "" );

        // loop through the data passed in.
        foreach( $data as $key => $value ) {

            // no numeric keys in our xml please!
            if ( is_numeric( $key ) ) {
                $numeric = 1;
                $key = $rootNodeName;
            }

            // delete any char not allowed in XML element names
            $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

            // if there is another array found recrusively call this function
            if ( is_array( $value ) ) {
                $node = self::is_assoc( $value ) || $numeric ? $xml->addChild( $key ) : $xml;

                // recrusive call.
                if ( $numeric ) $key = 'anon';
                self::toXml( $value, $key, $node );
            } else {

                // add single node.
                $value = htmlentities( $value );
                $xml->addChild( $key, $value );
            }
        }

        // pass back as XML
        return $xml->asXML();

    // if you want the XML to be formatted, use the below instead to return the XML
        //$doc = new DOMDocument('1.0');
        //$doc->preserveWhiteSpace = false;
        //$doc->loadXML( $xml->asXML() );
        //$doc->formatOutput = true;
        //return $doc->saveXML();
    }

    // determine if a variable is an associative array
    public static function isAssoc( $array ) {
        return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
    }

    public function toJson(array $output) {
        //output must be in UTF-8...
        return json_encode($output);
    }

    public function toYaml(array $output) {}
}
