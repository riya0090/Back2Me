<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login1.php");
    exit();
}
include 'db_connect.php';
$user_id = $_SESSION['user_id'];
$receiver_id = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 2;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat | Back2Me</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .chat-container {
            height: 65vh;
        }
        .messages-container {
            height: calc(100% - 60px);
        }
        .message-bubble {
            max-width: 80%;
            word-wrap: break-word;
        }
        .sent-message {
            background-color: #4f46e5;
            border-radius: 1rem 1rem 0 1rem;
        }
        .received-message {
            background-color: #374151;
            border-radius: 1rem 1rem 1rem 0;
        }
        .scrollbar-custom::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-custom::-webkit-scrollbar-track {
            background: #1f2937;
        }
        .scrollbar-custom::-webkit-scrollbar-thumb {
            background-color: #4b5563;
            border-radius: 3px;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-gray-800 rounded-xl shadow-xl overflow-hidden">
        <!-- Chat Header -->
        <div class="bg-gradient-to-r from-purple-800 to-purple-600 p-4 flex items-center">
            <a href="chat_list.php" class="mr-3 text-white hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            <h2 id="receiver-name" class="text-lg font-semibold flex-1 text-center">
                Loading...
            </h2>
        </div>

        <!-- Chat Messages -->
        <div class="chat-container p-4">
            <div id="chat-box" class="messages-container overflow-y-auto scrollbar-custom mb-4 space-y-3"></div>
            
            <!-- Message Input -->
            <div class="flex items-center bg-gray-700 rounded-lg p-1">
                <input 
                    type="text" 
                    id="message" 
                    class="flex-grow bg-gray-700 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 rounded-l-lg" 
                    placeholder="Type your message..."
                    onkeypress="if(event.keyCode === 13) sendMessage()"
                >
                <button 
                    onclick="sendMessage()" 
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-r-lg transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        let receiver_id = <?php echo $receiver_id; ?>;
        let user_id = <?php echo $user_id; ?>;

        // Load Receiver Name
        function loadReceiverName() {
            fetch("get_receiver_name.php?receiver_id=" + receiver_id)
                .then(response => response.text())
                .then(name => {
                    document.getElementById("receiver-name").textContent = name;
                })
                .catch(error => console.error("Error loading name:", error));
        }

        // Load Messages
        function loadMessages() {
            fetch("fetch_messages.php?receiver_id=" + receiver_id)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("chat-box").innerHTML = data;
                    scrollToBottom();
                })
                .catch(error => console.error("Error loading messages:", error));
        }

        // Send Message
        function sendMessage() {
            let messageInput = document.getElementById("message");
            let message = messageInput.value.trim();
            
            if (message === "") {
                alert("Please enter a message");
                return;
            }

            fetch("save_messages.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "receiver_id=" + receiver_id + "&message=" + encodeURIComponent(message)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    messageInput.value = "";
                    loadMessages();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Failed to send message");
            });
        }

        // Auto-scroll to bottom
        function scrollToBottom() {
            const chatBox = document.getElementById("chat-box");
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // Initialize
        loadReceiverName();
        loadMessages();
        setInterval(loadMessages, 2000); // Refresh every 2 seconds

        // Focus input on load
        document.getElementById("message").focus();
    </script>
</body>
</html>