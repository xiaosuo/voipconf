<?php
/**
 * View the configuration
 */

require_once("access.php");
require_once("lib/ini.php");
require_once("lib/ext.php");

unset($model);
$model["mac"] = $_GET["mac"];

// load generic info by mac
$ini = new Ini();
$ini->load($g_chan_sync);
foreach ($ini->sections() as $user) {
	if ($ini->get($user, "mac") == $model["mac"]) {
		$model["username"] = $ini->get($user, "authname");
		$model["password"] = $ini->get($user, "secret");
		break;
	}
}

// load switch info by username
$model["switch"] = array();
$ini->load($g_sip);
foreach ($ini->sections() as $host) {
	if ($ini->get($host, "context") == $model["username"]) {
		$switch["host"] = $ini->get($host, "host");
		$switch["call-limit"] = $ini->get($host, "call-limit");
		$model["switch"][] = $switch;
	}
}

// load gateway info by username
$ext = new ExtUsr();
$ext->load($g_ext_usr);
$model["gateway"] = array_values($ext->get($model["username"]));

render("Edit", "edit", $model);
?>
