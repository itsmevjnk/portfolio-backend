<?php

require_once(__DIR__ . '/../db.php');
require_once(__DIR__ . '/../utils.php');
require_once(__DIR__ . '/../config.php');

function verify_login($db, $db_prefix, $cookies_domain, $cookies_path) {
    $result = true;

    if(isset($_COOKIE['id']) && isset($_COOKIE['token'])) {
        /* we have an ID and a token - so let's check them */
        $id = intval($_COOKIE['id']);
        $stmt = $db->prepare('SELECT user FROM ' . $db_prefix . 'auth WHERE user = :id AND token = :token');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $_COOKIE['token']);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = ($stmt->fetch() != false);
    } else $result = false;
    
    if(!$result){
        /* invalidate any broken sessions */
        setcookie('id', '', time() - 3600, $cookies_path, $cookies_domain);
        setcookie('token', '', time() - 3600, $cookies_path, $cookies_domain);
    }

    return $result;
}

if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    /* script is called directly - let's do what it's meant to do */

    cors();

    api_handle_options();
    if($_SERVER['REQUEST_METHOD'] != 'GET') api_invalid();

    try {
        api_respond(200, array('valid' => verify_login($db, $db_prefix, $cookies_domain, $cookies_path)));
    } catch(PDOException $e) {
        api_respond_exception($e, 'Cannot verify due to server error');
    }
}

?>