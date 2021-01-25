<?php
/*
Plugin Name: Absolute Relative Links
Version: 1.2
Plugin URI: http://mathibus.com/archives/2005/02/relative-links
Description: When activated, this plugin filters WordPress' output, removing redundant stuff from on-site links.
Author: Mathias Bynens
Author URI: http://mathibus.com/
*/

update_option('gzipcompression', 0);

function mj_get_root($url) { // not everybody has his blog running from the root
	while(substr_count($url, '/') > 2) { // all we need is the :// from the protocol
		$array = explode('/', $url);
		array_pop($array);
		$url = implode('/', $array);
	}
	return $url;
}

function mj_relative_links($str) {
	global $feed;
	$url = mj_get_root(get_settings('siteurl'));
/*
	Uncomment the following line, and replace "http://mathibus.com" with
	your untrailingslashed root URI to speed things up a little.
	Don't forget to remove the above line saying $url = get_root(get_settings('siteurl'));
	if you do so.
*/
//	$url = 'http://mathibus.com';
	$str = str_replace("'" . $url ."/", "'/", $str);	// <a href="xtp://yoursite.com/whatever/">
	$str = str_replace('"' . $url . '/', '"/', $str);	// <a href='xtp://yoursite.com/whatever/'>
	$str = str_replace('"' . $url . '"', '"/"', $str);	// <a href="xtp://yoursite.com">
	$str = str_replace("'" . $url . "'", "'/'", $str);	// <a href='xtp://yoursite.com">
	return $str;
}

if((!strstr($_SERVER['REQUEST_URI'], $url . '/wp-admin')) && (!$feed)) ob_start('mj_relative_links'); // do not filter in feeds or in admin section

?>