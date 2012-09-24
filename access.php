<?php
/**
 * Provide the access control
 */

require_once("conf.php");
require_once("lib/util.php");

function require_access($error_message = "")
{
	if (basename($_SERVER["PHP_SELF"]) == "access.php")
		render("Login", "access", $error_message);
	else
		header("Location: " . dirname($_SERVER["PHP_SELF"]) .
		       "/access.php");
	exit;
}

function session_init($role)
{
	// reader doesn't need authentication
	if ($role == "reader")
		return;
	session_start();
	if ($_SESSION["role"] == "writer")
		return;
	require_access();
}

$from_me = (basename($_SERVER["PHP_SELF"]) == "access.php");

if ($from_me && $_GET["access"] == "logout") {
	session_start();
	$_SESSION["role"] = "";
	require_access();
}

if ($from_me && isset($_POST["submit"])) {
	if ($_POST["username"] != $g_username ||
	    $_POST["password"] != $g_password)
		require_access("Invalid username or password.");
	session_start();
	$_SESSION["role"] = "writer";
}

if (!isset($access_role))
	$access_role = "writer";

session_init($access_role);

if ($from_me) {
	header("Location: " . dirname($_SERVER["PHP_SELF"]) . "/status.php");
	exit;
}
?>
