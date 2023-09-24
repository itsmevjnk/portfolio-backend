<?php

require_once(__DIR__ . '/../db.php');
require_once(__DIR__ . '/../utils.php');

cors();

api_handle_options();
if($_SERVER['REQUEST_METHOD'] != 'POST') api_invalid();

$_POST = json_decode(file_get_contents('php://input'), true); // parse JSON passed into login

if(!isset($_POST['user']) || !isset($_POST['password']))
    api_respond(400, null, 'Incomplete input');

$pswd_hash = hash('sha256', $_POST['password'], false); // generate password hash

try {
    /* get user ID */
    $stmt = $db->prepare('SELECT id FROM ' . $db_prefix . 'admins WHERE user = :user AND pswd = :pswd');
    $stmt->bindParam(':user', $_POST['user']);
    $stmt->bindParam(':pswd', $pswd_hash);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $usr_search = $stmt->fetch();
    if($usr_search == false) api_respond(404, null, 'Incorrect user or password');
    $id = $usr_search['id'];

    /* search for usable token */
    $token = '';
    $stmt = $db->prepare('SELECT user FROM ' . $db_prefix . 'auth WHERE token = :token AND user = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':token', $token);
    do {
        $token = bin2hex(random_bytes(32));
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $tok_search = $stmt->fetch();
        if($tok_search == false) break; // token is unique
    } while(true);

    $stmt = $db->prepare('INSERT INTO ' . $db_prefix . 'auth (token, user) VALUES (:token, :id)');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':token', $token);
    $stmt->execute();

    setcookie('id', strval($id), time()+60*60*24*7, $cookies_path, $cookies_domain);
    setcookie('token', $token, time()+60*60*24*7, $cookies_path, $cookies_domain);
    api_respond(200, array('id' => $id, 'token' => $token), null);
} catch(PDOException $e) {
    api_respond_exception($e, 'Cannot log in due to server error');
}

?>