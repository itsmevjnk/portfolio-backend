<?php

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/utils.php');

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pswd);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("SET time_zone='+00:00'");
    if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']))
        api_respond(200, null, 'Database connected successfully'); // db.php was directly called
} catch(PDOException $e) {
    api_respond_exception($e, 'Database connection failed');
}

?>