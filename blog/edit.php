<?php

require_once(__DIR__ . '/../db.php');
require_once(__DIR__ . '/../utils.php');
require_once(__DIR__ . '/../auth/verify.php');

cors();

api_handle_options();
if($_SERVER['REQUEST_METHOD'] != 'PUT') api_invalid();

/* check if the admin has logged in */
if(!verify_login($db, $db_prefix, $cookies_domain, $cookies_path)) api_respond(401, null, 'This feature is only available to logged in admins');

$data = json_decode(file_get_contents('php://input'), true); // parse JSON

try {
    $stmt = null; // to be prepared
    if(isset($data['id'])) {
        /* update post */
        $stmt = $db->prepare('SELECT id FROM ' . $db_prefix . 'posts WHERE id = :id');
        $stmt->bindValue(':id', intval($data['id']), PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if($stmt->fetch() == false) api_respond(404, null, 'Post ID not found');

        $stmt = $db->prepare('UPDATE ' . $db_prefix . 'posts SET title = :title, content = :content WHERE id = :id');
        $stmt->bindValue(':id', intval($data['id']), PDO::PARAM_INT);
    } else {
        /* create new post */
        $stmt = $db->prepare('INSERT INTO ' . $db_prefix . 'posts (title, content) VALUES (:title, :content)');
    }
    $stmt->bindParam(':title', $data['title']);
    $stmt->bindParam(':content', $data['content']);
    $stmt->execute();
    api_respond(200, null, null);
} catch(PDOException $e) {
    api_respond_exception($e, 'Cannot create/edit post due to server error');
}

?>