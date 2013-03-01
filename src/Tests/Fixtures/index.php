<?php
    /**
     * I am the index file for a mythical web application.
     *
     * @author Michael Gatto <mgatto@u.arizona.edu>
     */

    /**
     * @var array is a list of favorite mediterranean fruits.
     */
    $mediterraneans = array(
        'lemon',
        'orange',
        'fig',
        'tangerine',
        );

    /**
     * print the variable's data structure.
     */
    print_r( $mediterraneans );

    function getFruit($name) {
        return $mediterraneans[$name] || "{$name} is not a mediterranean fruit";
    }

?>
