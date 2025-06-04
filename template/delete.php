<?php
include 'db.php';
$id = $_GET['id'];
if ($id) {
    $conn->query("DELETE FROM collections WHERE id=$id");
}
header("Location: index.php");
exit();
?>
