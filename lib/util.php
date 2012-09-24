<?php
/**
 * Some common utilities
 */

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

/*
assert(strcmp(quote_encode("\\\""), "\\\\\\\"") == 0);
*/

?>
