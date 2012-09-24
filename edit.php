<?php
/**
 * View the configuration
 */

require_once("access.php");
require_once("lib/ini.php");
require_once("lib/ext.php");
require_once("lib/validate.php");

function __invalid_entry($model, $focus, $message)
{
	$model["focus"] = $focus;
	$model["message"] = $message;
	render("Edit", "edit", $model);
	exit;
}

function invalid_entry($model, $focus)
{
	__invalid_entry($model, $focus, "Invalid Entry");
}

$model = $_POST;
if ($_GET["mac"] != "") {
	$model["mac"] = $_GET["mac"];
	$model["mode"] = "edit";
	$model["focus"] = "password";
} else {
	$model["mode"] = "add";
	$model["focus"] = "mac";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	/* validate all the entries */
	if (valid_mac($model["mac"]) === false)
		invalid_entry($model, "mac");
	if (valid_username($model["username"]) === false)
		invalid_entry($model, "username");
	if (valid_password($model["password"]) === false)
		invalid_entry($model, "password");
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

	$chan = new Ini();
	$chan->load($g_chan_sync);
	foreach ($chan->sections() as $user) {
		if ($chan->get($user, "mac") == $model["mac"]) {
			if ($model["mode"] == "add")
				__invalid_entry($model, "mac", "Duplicate MAC Address");
			$chan->deleteSection($user);
			$old_user = $user;
			break;
		}
	}
	if (in_array($model["username"], $chan->sections()))
		__invalid_entry($model, "username", "Duplicate Username");
	$chan->add($model["username"], "authname", $model["username"]);
	$chan->add($model["username"], "secret", $model["password"]);
	$chan->add($model["username"], "host", "dynamic");
	$chan->add($model["username"], "disallow", "all");
	$chan->add($model["username"], "allow", "g729,g723");
	$chan->add($model["username"], "mac", $model["mac"]);

	$sip = new Ini();
	$sip->load($g_sip);
	$hosts = array();
	foreach ($sip->sections() as $host) {
		if ($sip->get($host, "context") == $old_user)
			$sip->deleteSection($host);
		else
			$hosts[] = $host;
	}
	foreach ($model["switch"] as $i => $switch) {
		if (in_array($switch["host"], $hosts))
			__invalid_entry($model, "switch[$i][host]",
					"Duplicate Switch Host");
		else
			$hosts[] = $switch["host"];
		$sip->add($switch["host"], "type", "friend");
		$sip->add($switch["host"], "host", $switch["host"]);
		$sip->add($switch["host"], "context", $model["username"]);
		$sip->add($switch["host"], "call-limit", $switch["call-limit"]);
	}

	$usr = new ExtUsr();
	$usr->load($g_ext_usr);
	if (isset($old_user))
		$usr->delete($old_user);
	foreach ($model["gateway"] as $gateway)
		$usr->add($model["username"], $gateway);

	$ael = new ExtAel();
	$ael->load($g_ext_ael);
	if (isset($old_user))
		$ael->delete($old_user);
	$ael->add($model["username"]);

	$chan->dump($g_chan_sync);
	$sip->dump($g_sip);
	$usr->dump($g_ext_usr);
	$ael->dump($g_ext_ael);

	header("Location: " . dirname($_SERVER["PHP_SELF"]) . "/list.php");
	exit;
}

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
$model["switch"] = get_switch($g_sip, $model["username"]);

// load gateway info by username
$model["gateway"] = get_gateway($g_ext_usr, $model["username"]);

render("Edit", "edit", $model);
?>
