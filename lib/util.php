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

?>
