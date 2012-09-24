<?php

require_once("access.php");

$model = array();
$model["channel"] = array();
$ph = popen("$g_vpn -rx 'core show channels'", "r");
$title = true;
while (($line = fgets($ph)) !== false) {
	if ($title) {
		$title = false;
		continue;
	}
	$matches = array();
	if (preg_match("/^(\d+)\s+active\s+channels$/", $line,
		       $matches) == 1) {
		$model["active-channels"] = $matches[1];
		continue;
	}
	if (preg_match("/^(\d+)\s+of\s+(\d+)\s+max\s+active\s+calls\s+\(\s*([0-9.%]+).*$/",
		       $line, $matches) == 1) {
		$model["active-calls"] = $matches[1] . "/" . $matches[2] . "(" .
				$matches[3] . ")";
		continue;
	}
	if (preg_match("/^(\d+)\s+calls\s+processed$/", $line, $matches) == 1) {
		$model["call-processed"] = $matches[1];
		continue;
	}
	$segs = preg_split("/\s+/", $line, 4);
	$chan = array();
	$chan["name"] = $segs[0];
	$chan["location"] = $segs[1];
	$chan["state"] = $segs[2];
	$chan["application"] = $segs[3];
	$model["channel"][] = $chan;
}
pclose($ph);

render("Status", "status", $model);

?>
