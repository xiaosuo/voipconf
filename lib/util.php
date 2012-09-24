<?php
/**
 * Some common utilities
 */

// Remove a file or directory recursively
// TODO: remove this unused function
function remove_recursive($path)
{
	if (is_dir($path)) {
		foreach (scandir($path) as $f) {
			if ($f == "." || $f == "..")
				continue;
			remove_recursive($path . "/" . $f);
		}
		rmdir($path);
	} else {
		unlink($path);
	}
}

function render($title, $template, $model = null)
{
	require("template/header.php");
	require("template/$template.php");
	require("template/footer.php");
}

?>
