<?php

/*
Plugin Name: Page And Post Secondary Title
Plugin URI: https://papst.com/
Description: Plugin to add and merge title or secondary title.
Version: 1.0
Author: wpzita
Author URI: https://wpzita.com/
Text Domain: papst
*/

/*Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit; 

/*costant for path and url*/
define('PAPST_URL', plugin_dir_url(__FILE__));
define('PAPST_PATH', plugin_dir_path(__FILE__));
/*including file where classes exists*/
include_once( PAPST_PATH . 'inc/papst_back_class.php' );
include_once( PAPST_PATH . 'inc/papst_front_class.php' );
include_once( PAPST_PATH . 'inc/papst_meta_class.php' );
add_action( "plugins_loaded" , "Papst_Title_init" );
function Papst_Title_init(){
	$papst_obj_front = new Papst_FrontEnd();
	$papst_obj = new Papst_metaBox();
}
