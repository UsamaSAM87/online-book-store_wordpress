<?php
/*
Plugin Name: ReplaceWordsPlugin
Author: Chan Lim
Version: 1.0
*/

//create a callback function that gets called when WordPress runs the filter
function replace_words($comments) {
    
    //create a set of words to remove from comments
    $bad_words = array('fuck', 'bitch', 'asshole', 'dope', 'narcotic');
    
    //for loop for replacing the matched words in the set with the particular word
    foreach($bad_words as $value){
        
        $comments = str_replace($value,'LOVE', $comments);
    }
    
    return $comments;
}
//add the callback function to a hook
add_filter('comment_text', 'replace_words');

?>