<?php
/**
 * The global configurations
 */

error_reporting(0);

$g_username = "test";
$g_password = "1234";

$g_conf_dir = "test/conf/server";
$g_bin_dir = "test/bin";

$g_serv_ip = $_SERVER["SERVER_ADDR"];
$g_serv_port = 4350;

$g_chan_sync = "$g_conf_dir/chan_sync.conf";
$g_sip = "$g_conf_dir/sip.conf";
$g_ext_ael = "$g_conf_dir/extensions.ael";
$g_ext_usr = "$g_conf_dir/extensions.usr";
$g_vpn = "$g_bin_dir/vpn";
?>
