<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login1.php"); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Chat | Back2Me</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex justify-center items-center h-screen">
    <div class="w-full max-w-md bg-gray-800 p-4 rounded-lg shadow-lg">
        <h2 class="text-lg font-semibold text-center text-yellow-400">Select a User to Chat</h2>
        
        <!-- List of Users -->
        <div id="user-list" class="max-h-60 overflow-y-auto">
            <?php
            $users = $conn->query("SELECT id, name FROM users WHERE id != $user_id");
            while ($row = $users->fetch_assoc()):
            ?>
                <a href="chat.php?receiver_id=<?= $row['id'] ?>" 
                   class="block p-2 bg-gray-700 rounded my-2 hover:bg-gray-600">
                    <?= htmlspecialchars($row['name']) ?>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- Stored Chats -->
        <h3 class="text-lg font-semibold mt-4">Previous Chats</h3>
        <div id="chat-list" class="max-h-40 overflow-y-auto">
            <?php
            $chats = $conn->query("
                SELECT DISTINCT users.id, users.name FROM messages 
                JOIN users ON (messages.sender_id = users.id OR messages.receiver_id = users.id)
                WHERE (messages.sender_id = $user_id OR messages.receiver_id = $user_id) 
                AND users.id != $user_id
            ");
            while ($row = $chats->fetch_assoc()):
            ?>
                <a href="chat.php?receiver_id=<?= $row['id'] ?>" 
                   class="block p-2 bg-gray-700 rounded my-2 hover:bg-gray-600">
                    <?= htmlspecialchars($row['name']) ?>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
