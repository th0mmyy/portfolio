<?php
// admin/delete.php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../db.php';

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id) {
    // Delete physical files
    $photos = db()->prepare("SELECT filename FROM project_photos WHERE project_id = ?");
    $photos->execute([$id]);
    foreach ($photos->fetchAll(PDO::FETCH_COLUMN) as $fn) {
        @unlink(__DIR__ . '/../../uploads/' . $fn);
    }
    db()->prepare("DELETE FROM projects WHERE id = ?")->execute([$id]);
}
header('Location: index.php?ok=' . urlencode('Progetto eliminato.'));
exit;