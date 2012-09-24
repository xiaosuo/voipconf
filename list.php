<?php
/**
 * List, new and delete the configurations by MAC address
 */

require_once("access.php");
require_once("lib/ini.php");

if (!empty($_GET["del"])) {
	require_once("lib/validate.php");
	if ($_GET["del"] == "all") {
		// TODO
	} else {
		$mac = valid_mac($_GET["del"]);
		if ($mac !== false) {
			$ini = new Ini();
			$ini->load($g_chan_sync);
			foreach ($ini->sections() as $user) {
				if ($ini->get($user, "mac") == $mac) {
					$ini->deleteSection($user);
					$ini->dump($g_chan_sync);
					break;
				}
			}
		}
	}
}

$ini = new Ini();
$ini->load($g_chan_sync);
$users = $ini->sections();
$macs = array();
foreach ($users as $user)
	$macs[] = $ini->get($user, "mac");
render("List", "list", $macs);
?>
