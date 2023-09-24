<?php

require_once(__DIR__ . '/../db.php');
require_once(__DIR__ . '/../utils.php');

cors();

api_handle_options();
if($_SERVER['REQUEST_METHOD'] != 'GET') api_invalid();

if(!isset($_GET['id'])) api_respond(400, null, 'Post ID not given');

$id = intval($_GET['id']);

try {
    $stmt = $db->prepare('SELECT title, ctime, content FROM ' . $db_prefix . 'posts WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $payload = $stmt->fetch();

    if($payload == false) api_respond(404, null, 'Post not found');
    api_respond(200, $payload);
} catch(PDOException $e) {
    api_respond_exception($e, 'Cannot query post');
}

?>