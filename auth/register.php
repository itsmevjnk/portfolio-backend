<?php

require_once(__DIR__ . '/../db.php');
require_once(__DIR__ . '/../utils.php');

cors();

api_handle_options();
if($_SERVER['REQUEST_METHOD'] != 'POST') api_invalid();

$_POST = json_decode(file_get_contents('php://input'), true); // parse JSON passed into register

if(!isset($_POST['user']) || !isset($_POST['password']))
    api_respond(400, null, 'Incomplete input');

try {
    /* check if there is already an account */
    $stmt = $db->prepare('SELECT id FROM ' . $db_prefix . 'admins');
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $usr = $stmt->fetch();
    if($usr != false) api_respond(403, null, 'Admin account already exists'); // we only allow one admin account

    /* finally create an account */
    $pswd_hash = hash('sha256', $_POST['password'], false); // generate password hash
    $stmt = $db->prepare('INSERT INTO ' . $db_prefix . 'admins (user, pswd) VALUES (:user, :pswd)');
    $stmt->bindParam(':user', $_POST['user']);
    $stmt->bindParam(':pswd', $pswd_hash);
    $stmt->execute();

    api_respond(200, null, null);
} catch(PDOException $e) {
    api_respond_exception($e, 'Cannot create account due to server error');
}

?>