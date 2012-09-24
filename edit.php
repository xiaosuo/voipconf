<?php
/**
 * View the configuration
 */

require_once("access.php");
require_once("lib/ini.php");
require_once("lib/ext.php");
require_once("lib/validate.php");

function invalid_entry($model, $focus)
{
	$model["focus"] = $focus;
	$model["message"] = "Invalid Entry";
	render("Edit", "edit", $model);
	exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	/* validate all the entries */
	$model = $_POST;
	if (valid_mac($_GET["mac"]) !== false)
		$model["mac"] = $_GET["mac"];
	if (valid_username($model["username"]) === false)
		invalid_entry($model, "username");
	if (valid_password($model["password"]) === false)
		invalid_entry($model, "password");
	if (valid_mac($model["mac"]) === false)
		invalid_entry($model, "mac");
	$model["mac"] = strtoupper($model["mac"]);
	foreach ($model["switch"] as $i => $switch) {
		if (valid_ip($switch["host"]) === false)
			invalid_entry($model, "switch[$i][host]");
		if (valid_call_limit($switch["call-limit"]) === false)
			invalid_entry($model, "switch[$i][call-limit]");
	}
	foreach ($model["gateway"] as $i => $gateway) {
		if (valid_ip($gateway["host"]) === false)
			invalid_entry($model, "gateway[$i][host]");
		if (valid_port($gateway["port"]) === false)
			invalid_entry($model, "gateway[$i][port]");
		if (valid_pefix($gateway["prefix"]) === false)
			invalid_entry($model, "gateway[$i][prefix]");
	}

	// FIXME: check for duplicate usernames, mac, switches, gateways

	$ini = new Ini();
	$ini->load($g_chan_sync);
	foreach ($ini->sections() as $user) {
		if ($ini->get($user, "mac") == $model["mac"]) {
			$ini->deleteSection($user);
			$old_user = $user;
			break;
		}
	}
	$ini->add($model["username"], "authname", $model["username"]);
	$ini->add($model["username"], "secret", $model["password"]);
	$ini->add($model["username"], "host", "dynamic");
	$ini->add($model["username"], "disallow", "all");
	$ini->add($model["username"], "allow", "g729,g723");
	$ini->add($model["username"], "mac", $model["mac"]);
	$ini->dump($g_chan_sync);

	$ini->load($g_sip);
	if (isset($old_user)) {
		foreach ($ini->sections() as $host) {
			if ($ini->get($host, "context") == $old_user)
				$ini->deleteSection($host);
		}
	}
	foreach ($model["switch"] as $switch) {
		$ini->add($switch["host"], "type", "friend");
		$ini->add($switch["host"], "host", $switch["host"]);
		$ini->add($switch["host"], "context", $model["username"]);
		$ini->add($switch["host"], "call-limit", $switch["call-limit"]);
	}
	$ini->dump($g_sip);

	$ext = new ExtUsr();
	$ext->load($g_ext_usr);
	if (isset($old_user))
		$ext->delete($old_user);
	foreach ($model["gateway"] as $gateway)
		$ext->add($model["username"], $gateway);
	$ext->dump($g_ext_usr);

	$ext = new ExtAel();
	$ext->load($g_ext_ael);
	if (isset($old_user))
		$ext->delete($old_user);
	$ext->add($model["username"]);
	$ext->dump($g_ext_ael);

	$model["message"] = "OK";
	render("Edit", "edit", $model);
	exit;
}

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
