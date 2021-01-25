<?php
/*
Plugin Name: IMAGE ALT TAGER
Plugin URI: http://jmkwebs.co.uk/
Description: Simple plugin that easily changes or adds image alt tags in your wordpress website.
Version: 1.0
Author: Justas Piliukaitis
Author URI: http://jmkwebs.co.uk
License: GPL
*/


if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
    add_action( 'admin_notices', create_function( '', "echo '<div class=\"error\"><p>".__('IMG ALT TAGER requires PHP 5.3 to function properly. Please upgrade PHP or deactivate IMG ALT TAGER.', 'img-alt-tager') ."</p></div>';" ) );
    return;
}

//Installing database
function jmk_install() {
    global $wpdb;
    $table = $wpdb->prefix."jmk_alt_tags";
    $structure = "CREATE TABLE $table (
        id INT(9) NOT NULL AUTO_INCREMENT,
		jmk_randomize VARCHAR(4) NOT NULL DEFAULT 'No',
		jmk_content VARCHAR(4) NOT NULL DEFAULT 'Yes',
		jmk_widgets VARCHAR(4) NOT NULL DEFAULT 'Yes',
        jmk_tags VARCHAR(4000) NOT NULL,
	UNIQUE KEY id (id)
    );";
    $wpdb->query($structure);
	
}
register_activation_hook( __FILE__, 'jmk_install' );

//Admin menu
function jmk_admin_menu() {
    include('jmk_admin_menu.php');
}

/*Register admin page's css will be used in an upgraded version
function my_admin_init(){
    wp_register_style('my_style', plugins_url('css/custom-style.css', __FILE__));
}

function my_admin_enqueue_styles(){
    wp_enqueue_style('my_style');
}
*/

//Adding Admin menu to settings panel
function jmk_admin_actions() {
	add_options_page("IMG ALT TAGER", "IMG ALT TAGER", 1, "IMG_ALT_TAGER", "jmk_admin_menu");
	//add_action( 'wp_enqueue_scripts', 'my_admin_enqueue_styles' );
}
add_action('admin_menu', 'jmk_admin_actions');

// Executing plugin after all other plugins are loaded
run_plugin_with_settings();

function run_plugin_with_settings(){
	global $wpdb;
	$id = 1;
	$table = $wpdb->prefix."jmk_alt_tags";
	$result = $wpdb->get_row("SELECT * FROM $table WHERE ID = " . $id);
	if($result){
		foreach($result as $detail => $item){
			if($detail === 'jmk_content'){
				$ContentBox = $item;
			}
			if($detail === 'jmk_widgets'){
				$WidgetBox = $item;
			}			
		}
		
		if($ContentBox == "Yes" && $WidgetBox == "Yes"){
			add_action( 'plugins_loaded', 'change_content' );
			add_action( 'plugins_loaded', 'change_widget' );
		}elseif($ContentBox == "Yes"){
			add_action( 'plugins_loaded', 'change_content' );
		}elseif($WidgetBox == "Yes"){
			add_action( 'plugins_loaded', 'change_widget' );	
		}else{
		
		}
	}
}


function change_header(){
	//TODO
}

//Getting the full content with shortcodes
function change_content(){
	add_filter( 'the_content', 'do_shortcode');
	$content = get_the_content();
	$content = apply_filters('the_content', $content);
	add_filter('the_content', 'jmk_content');
}

function change_widget(){	
	//TODO Read custom widgets
	//add_filter( 'dynamic_sidebar_params', 'jmk_widget' );
	add_filter( 'widget_text', 'jmk_widget', 99 );
}

//Getting TAGS from DB & changing header with ALT TAGS
function jmk_header( $content ) {
	global $wpdb;
	$id = 1;
	$table = $wpdb->prefix."jmk_alt_tags";
	$result = $wpdb->get_row("SELECT * FROM $table WHERE ID = " . $id);
	if($result){
		foreach($result as $detail => $item){
			if($detail === 'jmk_tags'){
				$jmktags = $item;
			}
			if($detail === 'jmk_randomize'){
				$randomize = $item;
			}
			if($detail === 'jmk_content'){
				$ContentBox = $item;
			}
			if($detail === 'jmk_widgets'){
				$WidgetBox = $item;
			}			
		}
	}
	$alt = array_map('trim', explode(",", $jmktags));
	preg_match_all('/(<img.*?>)/i', $content, $matches);
	$i = 0;
	foreach ($matches[0] as $key=>$val){
		if ($randomize == "Yes") {
			$i = rand(0, count($alt));
			$new_img = str_replace('<img', '<img alt="'.$alt[$i].'"', $val);
			$content = str_replace($matches[1][$key], $new_img, $content);
		}else{
			if (empty($alt[$i])){
				$i=0;
			}
			$new_img = str_replace('<img', '<img alt="'.$alt[$i].'"', $val);
			$content = str_replace($matches[1][$key], $new_img, $content);
			$i++;
		}
	}
	
	return $content;
}


//Getting TAGS from DB & changing content with ALT TAGS
function jmk_content( $content ) {
	global $wpdb;
	$id = 1;
	$table = $wpdb->prefix."jmk_alt_tags";
	$result = $wpdb->get_row("SELECT * FROM $table WHERE ID = " . $id);
	if($result){
		foreach($result as $detail => $item){
			if($detail === 'jmk_tags'){
				$jmktags = $item;
			}
			if($detail === 'jmk_randomize'){
				$randomize = $item;
			}		
		}
	}
	
	$alt = array_map('trim', explode(",", $jmktags));
	preg_match_all('/(<img.*?>)/i', $content, $matches);
	$i = 0;
	foreach ($matches[0] as $key=>$val){
		if ($randomize == "Yes") {
			$i = rand(0, count($alt));
			$new_img = str_replace('<img', '<img alt="'.$alt[$i].'"', $val);
			$content = str_replace($matches[1][$key], $new_img, $content);
		}else{
			if (empty($alt[$i])){
				$i=0;
			}
			$new_img = str_replace('<img', '<img alt="'.$alt[$i].'"', $val);
			$content = str_replace($matches[1][$key], $new_img, $content);
			$i++;
		}
	}
	
	return $content;
}

//Getting TAGS from DB & changing widget content with ALT TAGS
function jmk_widget($content){
	global $wpdb;
	$id = 1;
	$table = $wpdb->prefix."jmk_alt_tags";
	$result = $wpdb->get_row("SELECT * FROM $table WHERE ID = " . $id);
	global $wpdb;
	$id = 1;
	$table = $wpdb->prefix."jmk_alt_tags";
	$result = $wpdb->get_row("SELECT * FROM $table WHERE ID = " . $id);
	if($result){
		foreach($result as $detail => $item){
			if($detail === 'jmk_tags'){
				$jmktags = $item;
			}
			if($detail === 'jmk_randomize'){
				$randomize = $item;
			}		
		}
	}
	$alt = array_map('trim', explode(",", $jmktags));
	preg_match_all('/(<img.*?>)/i', $content, $matches);
	$i = 0;
	foreach ($matches[0] as $key=>$val){
		if ($randomize == "Yes") {
			$i = rand(0, count($alt));
			$new_img = str_replace('<img', '<img alt="'.$alt[$i].'"', $val);
			$content = str_replace($matches[1][$key], $new_img, $content);
		}else{
			if (empty($alt[$i])){
				$i=0;
			}
			$new_img = str_replace('<img', '<img alt="'.$alt[$i].'"', $val);
			$content = str_replace($matches[1][$key], $new_img, $content);
			$i++;
		}
	}
	
	return $content;
}

?>