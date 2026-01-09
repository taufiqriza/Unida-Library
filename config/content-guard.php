<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mask Character
    |--------------------------------------------------------------------------
    |
    | This character will replace every letter in the blocked word.
    | Example: If set to '*', the word "judi" becomes "****".
    |
    */

    'mask_char' => '*',

    /*
    |--------------------------------------------------------------------------
    | Leet Speak Substitution Map
    |--------------------------------------------------------------------------
    |
    | This list maps alphabetic characters to their visual lookalikes (numbers
    | or symbols) often used by spammers to bypass filters.
    |
    | IMPORTANT: For regex special characters like $ . + * ? ^ [ ] ( ) { } | \
    | you MUST use double backslashes (\\) to escape them properly.
    |
    */

    'substitution_map' => [
        'a' => '(a|4|@|à|á|â)',
        'b' => '(b|8|ß)',
        'c' => '(c|\\(|\\{|k)',
        'e' => '(e|3|€|é|è)',
        'g' => '(g|6|9)',
        'i' => '(i|1|!|\\|)',
        'l' => '(l|1|!|\\|)',
        'o' => '(o|0|@|ø)',
        's' => '(s|5|\\$|z)',  // Escaped $
        't' => '(t|7|\\+)',    // Escaped +
        'u' => '(u|v|ü|µ)',
        'x' => '(x|\\*|×)',
        'z' => '(z|2|s)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Blocked Keywords
    |--------------------------------------------------------------------------
    |
    | By default, IndoGuard loads thousands of words from the internal
    | src/Dictionary directory (e.g., gambling, profanity).
    |
    | Use this array ONLY if you want to add specific custom keywords
    | that are not yet covered by our default dictionary.
    |
    */

    'keywords' => [
        // 'competitor_name',
        // 'specific_spam_keyword',
    ],

];