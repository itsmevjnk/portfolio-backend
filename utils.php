<?php

function cors() {
    if(isset($_SERVER['HTTP_ORIGIN'])) {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
        header('Access-Control-Allow-Headers: Content-Type');
    }
}

function api_respond($code, $payload, $message = null) {
    http_response_code($code);
    header("Content-Type: application/json; charset=UTF-8");
    exit(json_encode(array('time' => time(), 'payload' => $payload, 'message' => $message)));
}

function api_respond_exception($e, $message) {
    api_respond(500, array('exception' => $e->getMessage()), $message);
}

function api_respond_text($code, $message) {
    http_response_code($code);
    header("Content-Type: text/plain; charset=UTF-8");
    exit($message);
}

function api_invalid() {
    api_respond_text(400, 'Cannot ' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI']);
}

function api_handle_options() {
    if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) api_invalid();

?>