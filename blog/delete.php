<?php

require_once(__DIR__ . '/../db.php');
require_once(__DIR__ . '/../utils.php');
require_once(__DIR__ . '/../auth/verify.php');

cors();

api_handle_options();
if($_SERVER['REQUEST_METHOD'] != 'DELETE') api_invalid();

/* check if the admin has logged in */
if(!verify_login($db, $db_prefix, $cookies_domain, $cookies_path)) api_respond(401, null, 'This feature is only available to logged in admins');

if(!isset($_GET['id'])) api_respond(400, null, 'ID not given');

try {
    $stmt = $db->prepare('DELETE FROM ' . $db_prefix . 'posts WHERE id = :id');
    $stmt->bindValue(':id', intval($_GET['id']), PDO::PARAM_INT);
    $stmt->execute();
    if($stmt->rowCount() > 0)
        api_respond(200, null, null);
    else
        api_respond(404, null, 'Cannot find post ID');
} catch(PDOException $e) {
    api_respond_exception($e, 'Cannot delete post due to server error');
}

?>