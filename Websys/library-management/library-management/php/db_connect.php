<?php
$conn = new mysqli("localhost", "root", "", "shelfwise_library");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
