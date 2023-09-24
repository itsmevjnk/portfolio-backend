<?php

require_once(__DIR__ . '/../db.php');
require_once(__DIR__ . '/../utils.php');
require_once(__DIR__ . '/../config.php');

cors();

api_handle_options();
if($_SERVER['REQUEST_METHOD'] != 'DELETE') api_invalid();

if(isset($_COOKIE['id']) && isset($_COOKIE['token'])) {
    /* remove from database */
    try {
        $id = intval($_COOKIE['id']);
        $stmt = $db->prepare('DELETE FROM ' . $db_prefix . 'auth WHERE token = :token AND user = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $_COOKIE['token']);
        $stmt->execute();
    } catch(PDOException $e) {
        api_respond_exception($e, 'Cannot log out on server side due to server error');
    }
}

/* remove cookies */
setcookie('id', '', time() - 3600, $cookies_path, $cookies_domain);
setcookie('token', '', time() - 3600, $cookies_path, $cookies_domain);

api_respond(200, null, null);

?>