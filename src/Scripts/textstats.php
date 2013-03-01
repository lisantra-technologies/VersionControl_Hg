<?php

include_once 'TextStatistics.php';

/*
 * The first argument is always the current script's filename,
 * therefore $argv[0] is the script's name.
 */
$tutorial_file = $argv[1];

if ( is_dir($tutorial_file) ) {
    //recurse
}
elseif ( is_file(basename($tutorial_file)) ) {
    $tutorial_text = file_get_contents($tutorial_file);
}
else {
    throw new Exception('Its not a real file, man!');
}


$texter = new TextStatistics();
print <<<END
The tutorial text from the file: $tutorial_file is at the
{$texter->flesch_kincaid_grade_level($tutorial_text)} level.

Here are some statistics about the text:

* Total length: {$texter->text_length()}
* Sentence count: {$texter->sentence_count()}
* Word count: {$texter->word_count()}
* Average Words per Sentence: {$texter->average_words_per_sentence()}
* Average Syllables per Word: {$texter->average_syllables_per_word()}

END;



