<?php
require_once(__DIR__ . '/utils.php');

$db_host = 'YOUR_DB_HOST';
$db_user = 'YOUR_DB_USER';
$db_pswd = 'YOUR_DB_PASSWORD';
$db_name = 'YOUR_DB_NAME';
$db_prefix = 'YOUR_DB_PREFIX';

$cookies_domain = null;
$cookies_path = '/';

if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) api_invalid();
?>