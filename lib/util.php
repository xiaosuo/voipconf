<?php
/**
 * Some common utilities
 */

require_once("lib/ini.php");
require_once("lib/ext.php");

function render($title, $template, $model = null)
{
	require("template/header.php");
	require("template/$template.php");
	require("template/footer.php");
}

function quote_encode($str)
{
	return str_replace('"', "\\\"", str_replace("\\", "\\\\", $str));
}

function get_switch($filename, $username)
{
	$switches = array();
	$ini = new Ini();
	$ini->load($filename);
	foreach ($ini->sections() as $host) {
		if ($ini->get($host, "context") == $username) {
			$switch["host"] = $ini->get($host, "host");
			$switch["call-limit"] = $ini->get($host, "call-limit");
			$switches[] = $switch;
		}
	}

	return $switches;
}

function get_gateway($filename, $username) {

	$ext = new ExtUsr();
	$ext->load($filename);

	return array_values($ext->get($username));
}

/*
assert(strcmp(quote_encode("\\\""), "\\\\\\\"") == 0);
*/

?>
