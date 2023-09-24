<?php

require_once(__DIR__ . '/../db.php');
require_once(__DIR__ . '/../utils.php');

cors();

api_handle_options();
if($_SERVER['REQUEST_METHOD'] != 'GET') api_invalid();

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : null;
$start = (isset($_GET['start']) ? intval($_GET['start']) : 0) + 1;

try {
    $stmt = $db->prepare('SELECT a.id, a.ctime, a.title FROM (SELECT id, ctime, title, ROW_NUMBER() OVER (ORDER BY ctime DESC) AS n FROM ' . $db_prefix . 'posts) a WHERE a.n >= :start ORDER BY a.n' . (($limit == null) ? '' : ' LIMIT :limit'));
    if($limit != null) $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->execute();

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    api_respond(200, $stmt->fetchAll());
} catch(PDOException $e) {
    api_respond_exception($e, 'Cannot query blog posts');
}

?>