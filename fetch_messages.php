<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access";
    exit();
}

$user_id = $_SESSION['user_id'];
$receiver_id = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 0;

if ($receiver_id == 0) {
    echo "Invalid receiver";
    exit();
}

// Fetch messages between both users
$stmt = $conn->prepare("
    SELECT sender_id, message, timestamp FROM messages 
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) 
    ORDER BY timestamp ASC
");
$stmt->bind_param("iiii", $user_id, $receiver_id, $receiver_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $class = ($row['sender_id'] == $user_id) ? 'bg-blue-500 ml-auto' : 'bg-gray-600 mr-auto';
    echo "<div class='p-2 my-1 rounded $class w-fit max-w-xs'>
            <strong>" . ($row['sender_id'] == $user_id ? 'You' : 'Them') . ":</strong> " . htmlspecialchars($row['message']) . "
          </div>";
}

$stmt->close();
$conn->close();
?>
