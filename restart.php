<?php
/**
 * Restart the service
 */

require_once("access.php");

exec("$g_vpn -rx 'core restart now'");

header("Location: " . $_SERVER["HTTP_REFERER"]);

?>
