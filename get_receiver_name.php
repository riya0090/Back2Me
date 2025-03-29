<?php
session_start();
include 'db_connect.php'; // Ensure this file connects to your database

if (isset($_GET['receiver_id'])) {
    $receiver_id = intval($_GET['receiver_id']);
    
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $receiver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo htmlspecialchars($row['name']);
    } else {
        echo "Unknown User";
    }
    
    $stmt->close();
}
?>
