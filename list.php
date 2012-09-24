<?php
/**
 * List, new and delete the configurations by MAC address
 */

require_once("access.php");
require_once("lib/ini.php");
require_once("lib/ext.php");

if (!empty($_GET["del"])) {
	require_once("lib/validate.php");
	if ($_GET["del"] == "all") {
		$ini = new Ini();
		$ini->load($g_chan_sync);
		foreach ($ini->sections() as $user) {
			if ($user != "general")
				$ini->deleteSection($user);
		}
		$ini->dump($g_chan_sync);

		$ini->load($g_sip);
		foreach ($ini->sections() as $host) {
			if ($host != "general")
				$ini->deleteSection($host);
		}
		$ini->dump($g_sip);

		file_put_contents($g_ext_ael, "");
		file_put_contents($g_ext_usr, "");

		reload_all($g_vpn);
	} else {
		$mac = valid_mac($_GET["del"]);
		if ($mac !== false) {
			$ini = new Ini();
			$ini->load($g_chan_sync);
			foreach ($ini->sections() as $user) {
				if ($ini->get($user, "mac") == $mac) {
					$ini->deleteSection($user);
					$ini->dump($g_chan_sync);
					$target_user = $user;
					break;
				}
			}

			if (isset($target_user)) {
				$ini->load($g_sip);
				foreach ($ini->sections() as $host) {
					if ($ini->get($host, "context") == $target_user)
						$ini->deleteSection($host);
				}
				$ini->dump($g_sip);
			}

			if (isset($target_user)) {
				$ext = new ExtUsr();
				$ext->load($g_ext_usr);
				$ext->delete($target_user);
				$ext->dump($g_ext_usr);
			}

			if (isset($target_user)) {
				$ext = new ExtAel();
				$ext->load($g_ext_ael);
				$ext->delete($target_user);
				$ext->dump($g_ext_ael);
			}

			reload_all($g_vpn);
		}
	}
}

$ini = new Ini();
$ini->load($g_chan_sync);
$model = array();
foreach ($ini->sections() as $user) {
	if ($user == "general")
		continue;
	$conf = array();
	$conf["mac"] = $ini->get($user, "mac");
	$conf["username"] = $user;
	$conf["switch"] = get_switch($g_sip, $user);
	$conf["gateway"] = get_gateway($g_ext_usr, $user);
	$model[] = $conf;
}
render("Configurations", "list", $model);
?>
