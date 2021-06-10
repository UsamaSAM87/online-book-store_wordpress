<?php
/*
Plugin Name: ExpiringLinkPlugin
Author: Chan Lim
Version: 1.0
*/

//takes two parameters, a tag and a callback
add_shortcode('e', 'expiring_link');
//create functions that return some type of information
function expiring_link($post) {
    
    //get the current date for the post with the function, get_the_date()
    //then convert it to the value of second
    $post_date = strtotime(get_the_date('Y-m-d'));
    /*echo $post_date;*/
    
    //set the expire date
    $expiry = strtotime("2019-12-25");
    
    //check if expire date is in the past 
    if($expiry < $post_date) {
        
        $display = "<p>Sorry time's up.</p>";
    } else {
        $display = '<p>This is a promotion content that shows before the post is expired: <a href="https://www.amazon.ca/Special-Event-Perpetual-Calendar-Christmas/dp/1628490497">Join the event!</a></p>';
    } 
    
    return $display;
}
?>