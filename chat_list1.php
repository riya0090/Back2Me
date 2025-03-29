<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
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
    <style>
        .user-card:hover {
            transform: translateX(4px);
            background-color: rgba(74, 85, 104, 0.5);
        }
        .scroll-container {
            scrollbar-width: thin;
            scrollbar-color: #4a5568 #2d3748;
        }
        .scroll-container::-webkit-scrollbar {
            width: 6px;
        }
        .scroll-container::-webkit-scrollbar-track {
            background: #2d3748;
        }
        .scroll-container::-webkit-scrollbar-thumb {
            background-color: #4a5568;
            border-radius: 3px;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-200 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-gray-800 rounded-xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-800 to-purple-600 p-4">
            <h2 class="text-xl font-bold text-center text-white">
                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                Select Chat
            </h2>
        </div>

        <!-- Content -->
        <div class="p-4">
            <!-- All Users -->
            <div class="mb-6">
                <h3 class="text-md font-semibold text-yellow-400 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    All Users
                </h3>
                <div id="user-list" class="scroll-container max-h-60 overflow-y-auto space-y-2 pr-2">
                    <?php
                    $users = $conn->query("SELECT id, name, email FROM users WHERE id != $user_id ORDER BY name ASC");
                    while ($row = $users->fetch_assoc()):
                    ?>
                        <a href="chat.php?receiver_id=<?= $row['id'] ?>" 
                           class="flex items-center p-3 bg-gray-700 rounded-lg transition-all duration-200 user-card">
                            <div class="bg-purple-600 w-8 h-8 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                <?= strtoupper(substr($row['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="font-medium"><?= htmlspecialchars($row['name']) ?></div>
                                <div class="text-xs text-gray-400"><?= htmlspecialchars($row['email']) ?></div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Previous Chats -->
            <div>
                <h3 class="text-md font-semibold text-yellow-400 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    Previous Chats
                </h3>
                <div id="chat-list" class="scroll-container max-h-60 overflow-y-auto space-y-2 pr-2">
                    <?php
                    $chats = $conn->query("
                        SELECT DISTINCT users.id, users.name, users.email FROM messages 
                        JOIN users ON (messages.sender_id = users.id OR messages.receiver_id = users.id)
                        WHERE (messages.sender_id = $user_id OR messages.receiver_id = $user_id) 
                        AND users.id != $user_id
                        ORDER BY messages.timestamp DESC
                    ");
                    if ($chats->num_rows > 0) {
                        while ($row = $chats->fetch_assoc()):
                    ?>
                            <a href="chat.php?receiver_id=<?= $row['id'] ?>" 
                               class="flex items-center p-3 bg-gray-700 rounded-lg transition-all duration-200 user-card">
                                <div class="bg-green-600 w-8 h-8 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                    <?= strtoupper(substr($row['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="font-medium"><?= htmlspecialchars($row['name']) ?></div>
                                    <div class="text-xs text-gray-400"><?= htmlspecialchars($row['email']) ?></div>
                                </div>
                            </a>
                        <?php endwhile;
                    } else { ?>
                        <div class="text-center text-gray-400 py-4">
                            No previous chats found
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>