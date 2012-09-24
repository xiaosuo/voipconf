<?php
/**
 * Validation utilities
 */

/**
 * Validate the MAC address.
 * Return the uppercase version on success.
 * Return false on error.
 */
function valid_mac($mac)
{
	if (strstr($mac, "\n") !== false)
		return false;
	if (preg_match("/^([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$/s", $mac) != 1)
		return false;
	return strtoupper($mac);
}

/**
 * Validate the IP address.
 * Return the IP address on success.
 * Return false on error.
 */
function valid_ip($ip)
{
	$segs = explode(".", $ip);
	if (count($segs) != 4)
		return false;
	foreach ($segs as $seg) {
		$int = intval($seg);
		if (strcmp(strval($int), $seg) !== 0)
			return false;
		if ($int < 0 || $int > 255)
			return false;
	}

	return $ip;
}

/**
 * Validate the port.
 * Return the port on success.
 * Return false on error
 */
function valid_port($port)
{
	$int = intval($port);
	if (strcmp(strval($int), $port) !== 0 || $int < 0 || $int > 65535)
		return false;

	return $int;
}

/**
 * Validate the call limit
 * Return the call limit on success.
 * Return false on error
 */
function valid_call_limit($limit)
{
	$int = intval($limit);
	if (strcmp(strval($int), $limit) !== 0 || $int < 0)
		return false;

	return $int;
}

/**
 * Validate the username
 * Return the username on success
 * Return false on error
 */
function valid_username($username)
{
	if (strstr($username, "\n") !== false)
		return false;
	if (preg_match("/^\S+$/s", $username) != 1)
		return false;
	if ($username == "general" || $username == "default")
		return false;

	return $username;
}

/**
 * Validate the password
 * Return the password on success
 * Return false on error
 */
function valid_password($password)
{
	if (strstr($password, "\n") !== false)
		return false;
	if (preg_match("/^\S+$/s", $password) != 1)
		return false;

	return $password;
}

/**
 * Validate the prefix
 * Return the prefix on success
 * Return false on error
 */
function valid_pefix($prefix)
{
	if (strstr($prefix, "\n") !== false)
		return false;
	if (preg_match("/^\S+$/s", $prefix) != 1)
		return false;

	return $prefix;
}

/*
assert(valid_mac("00:11:22:33:44:ff") == "00:11:22:33:44:FF");
assert(valid_mac("00:aa:bb:cc:ed:FF") == "00:AA:BB:CC:ED:FF");
assert(valid_mac("\n00:aa:bb:cc:ed:FF") === false);
assert(valid_mac("00:aa:bb:cc:ed:FF\n") === false);
assert(valid_mac("xx:xx:xx:xx:xx:xx") === false);
assert(valid_mac("00:00:00:00:00") === false);
assert(valid_ip("10.0.0.1") == "10.0.0.1");
assert(valid_ip("10.10.0") === false);
assert(valid_ip(".0.0.1") === false);
assert(valid_ip("10.0.0.") === false);
assert(valid_ip("10.00.0.01") === false);
assert(valid_ip("10.10.0.256") === false);
assert(valid_ip("10.10.-0.256") === false);
assert(valid_ip("10.10.-1.256") === false);
assert(valid_port("80") == 80);
assert(valid_port("20p") === false);
assert(valid_port("-1") === false);
assert(valid_port("65536") === false);
assert(valid_call_limit("64") == 64);
assert(valid_call_limit("01") === false);
assert(valid_call_limit("-1") === false);
assert(valid_username("user01") == "user01");
assert(valid_username(" x") === false);
assert(valid_username("") === false);
assert(valid_username("x y") === false);
assert(valid_username("x ") === false);
assert(valid_username("x\ny") === false);
assert(valid_username("x\n") === false);
assert(valid_username("\nx") === false);
*/
?>
