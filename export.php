<?php
/**
 * Export the configurations by MAC address and file type
 */

$access_role = "reader";
require_once("access.php");
require_once("lib/validate.php");

Header("Content-Type: text/plain; charset=UTF-8");

if (($mac = valid_mac($_GET["mac"])) === false)
	exit(1);

require_once("lib/ini.php");
$chan = new Ini();
$chan->load($g_chan_sync);
foreach ($chan->sections() as $user) {
	if ($chan->get($user, "mac") == $mac) {
		$username = $user;
		break;
	}
}
if (!isset($username))
	exit(1);

switch ($_GET["type"]) {
case "sbo":
?>
[<?php echo $username; ?>]
type=friend
host=<?php echo $g_serv_ip; ?>

secret=<?php echo $chan->get($username, "secret"); ?>

context=<?php echo $username; ?>

sbo_tunnel=yes
<?php
	break;
case "chan_sync":
?>
[<?php echo $username; ?>]
authname=<?php echo $username; ?>

secret=<?php echo $chan->get($username, "secret"); ?>

host=<?php echo $g_serv_ip; ?>

port=<?php echo $g_serv_port; ?>

context=<?php echo $username; ?>

disallow=all
allow=g729,g723
<?php
	break;
case "ael":
	require_once("lib/ext.php");
	$contents = file_get_contents($g_ext_usr);
	while (strlen($contents) > 0) {
		$len = ExtParser::extractBlock($contents);
		if ($len === false)
			break;
		$block = substr($contents, 0, $len);
		$segs = preg_split("/\s+/", $block);
		if ($segs[1] == $username) {
			echo $block;
			break;
		}
		$contents = ltrim(substr($contents, $len));
	}
	break;
case "sbo-general-register":
	echo "$username:" . $chan->get($username, "secret") .
	     "@$g_serv_ip:$g_serv_port\n";
	break;
default:
	exit(1);
}
?>
