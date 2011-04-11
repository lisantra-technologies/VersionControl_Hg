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
    /**
     * Implemented output types.
     *
     * Array is the default, by nature of the code.
     *
     * @var mixed
     */
    public static $formats = array(
        'json', 'serialize', 'raw', 'yaml', //xml
    );

    //Having run() return an VersionControl_Hg_Command_Output object
    //might just be useful, sort of how PDO / Doctrine can...
    //though we might be overengineering, just a wee tad bit.

    /**
     * Class constructor.
     */
    public function __construct()
    {
    }

    /**
     * Convert array to PHP JSON text format
     *
     * @param array $output is the passed-in, parsed output from the cli
     */
    public function toJson(array $output) {
        //output must be in UTF-8...
        return json_encode($output);
    }

    /**
     * Convert array to YAML text format
     *
     * @param array $output is the passed-in, parsed output from the cli
     */
    public function toYaml(array $output) {
        if ( extension_loaded('yaml') ) {
            throw new VersionControl_Hg_Command_Exception(
                VersionControl_Hg_Command_Exception::BAD_ARGUMENT,
                "The required PECL Yaml extension is not installed. "
            );
        }

        return yaml_emit($output);
    }

    /**
     * Convert array to PHP serialized text format
     *
     * @param array $output is the passed-in, parsed output from the cli
     *
     * @TODO Hmmm, how to reconstruct from the array? or get it before parsing??
     */
    public function toRaw(array $output) {
        $raw = "";

        foreach ( $output as $line ) {
           $raw .= $line . PHP_EOL;
        }

        return $raw;
    }

    /**
     * Convert array to PHP serialized text format
     *
     * @param array $output is the passed-in, parsed output from the cli
     */
    public function toSerialize(array $output) {
        return serialize($output);
    }

    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     *
     * @TODO This should probably be implemented in each command which can output to XML, since the vocabulary will be different in each case.
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
}
