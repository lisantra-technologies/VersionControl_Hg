<?php

    $fixture = file_get_contents('H:\Development\_Webroot\Trunk\Tests\Fixtures\output-status.txt');

    $fields = array('status', 'entity');

    //split into lines first

    //account for the different line endings on the 3 platforms: Win32, Mac and *nix.
    $lines = preg_split( '/\r\n|\r|\n/', $fixture );

    //split each line into columns
    foreach( $lines as $a_line ) {
       $output[] = preg_split( '/[\s]+/', $a_line );
    }

    //list() idiom might be best here
    foreach ( $output as $row_num => $row ) {
        //counts of field and output lengths must match.
        $field_length = count( $fields );
        $output_row_length = count( $row );

        //loop through the variable-length output row.
        foreach ( $row as $position => $value ) {
            $result[$row_num][$fields[$position]] = $value;
        }
    }

    print_r( $result );

?>