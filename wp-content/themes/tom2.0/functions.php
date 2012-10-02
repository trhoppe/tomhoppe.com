<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'before_widget' => '<div id="%1$s" class="panel %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>',
    ));

/** @ignore */

?>