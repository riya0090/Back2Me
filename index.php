<?php
include 'db_connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back2Me</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
    console.log("Firebase Loaded:", firebase);
    console.log("Firestore Loaded:", db);
    });
</script>

    <!-- Firebase SDK -->
    <!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/10.5.2/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.5.2/firebase-firestore.js"></script>


    <script type="module">
    // Import the functions you need from the SDKs you need
    { initializeApp } from "https://www.gstatic.com/firebasejs/11.5.0/firebase-app.js";
    //{ getAnalytics } from "https://www.gstatic.com/firebasejs/11.5.0/firebase-analytics.js";
    
    // TODO: Add SDKs for Firebase products that you want to use
    // https://firebase.google.com/docs/web/setup#available-libraries

    // Your web app's Firebase configuration
    // For Firebase JS SDK v7.20.0 and later, measurementId is optional
    const firebaseConfig = {
        apiKey: "AIzaSyABJldi60-xlpVw8PTwUU1SdRMEUg0-cP8",
        authDomain: "back2me-48fdc.firebaseapp.com",
        projectId: "back2me-48fdc",
        storageBucket: "back2me-48fdc.firebasestorage.app",
        messagingSenderId: "514817140758",
        appId: "1:514817140758:web:407301a4950455ceffed37",
        measurementId: "G-TG33SB10V0"
    };
            firebase.initializeApp(firebaseConfig);

  // ✅ Initialize Firestore (Fixes db undefined issue)
        const db = firebase.firestore();

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);
</script>

</head>
<body class="bg-gray-900 text-gray-200">
    <header class="bg-purple-700 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Back2Me</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="index.php" class="hover:underline">Home</a></li>
                    <li><a href="report_lost1.php" class="hover:underline">Report Lost Item</a></li>
                    <li><a href="report_found.php" class="hover:underline">Report Found Item</a></li>
                    <li><a href="search.php" class="hover:underline">Search</a></li>
                    <li><a href="contact.php" class="hover:underline">Contact</a></li>
                </ul>
            </nav>
            <div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="hero bg-purple-600 text-white text-center py-16">
        <h2 class="text-4xl font-semibold">Find Your Lost Items</h2>
        <p class="mt-2">Report or search for lost and found items easily.</p>
        <div class="mt-4 flex justify-center">
            
        <button onclick="window.location.href='search.php'" 
        class="bg-yellow-500 px-4 py-2 rounded-md font-semibold hover:bg-yellow-600">
             Search
        </button>

        </div>
    </section>

    <!-- Recently Found Items -->
    <section class="recent-items container mx-auto my-10 p-6 bg-gray-800 shadow-lg rounded-lg">
        <h2 class="text-2xl font-semibold mb-4">Recently Found Items</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php
                $found_items = $conn->query("
                    SELECT found_items.item_name, found_items.location, found_items.created_at, users.name, users.email 
                    FROM found_items 
                    JOIN users ON found_items.user_id = users.id 
                    ORDER BY found_items.created_at DESC LIMIT 6
                ");
                while ($row = $found_items->fetch_assoc()):
            ?>
                <div class="p-4 bg-gray-700 rounded-lg shadow">
                    <h3 class="text-lg font-bold"><?= htmlspecialchars($row['item_name']) ?></h3>
                    <p class="text-gray-300">Found at: <?= htmlspecialchars($row['location']) ?></p>
                    <p class="text-sm text-gray-400">Reported by: <strong><?= htmlspecialchars($row['name']) ?></strong> (<?= htmlspecialchars($row['email']) ?>)</p>
                    <p class="text-sm text-gray-400">Reported on: <?= date("F j, Y, g:i a", strtotime($row['created_at'])) ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Recently Lost Items -->
    <section class="recent-items container mx-auto my-10 p-6 bg-gray-800 shadow-lg rounded-lg">
        <h2 class="text-2xl font-semibold mb-4">Recently Lost Items</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php
                $lost_items = $conn->query("
                    SELECT lost_items.item_name, lost_items.location, lost_items.created_at, users.name, users.email 
                    FROM lost_items 
                    JOIN users ON lost_items.user_id = users.id 
                    ORDER BY lost_items.created_at DESC LIMIT 6
                ");
                while ($row = $lost_items->fetch_assoc()):
            ?>
                <div class="p-4 bg-gray-700 rounded-lg shadow">
                    <h3 class="text-lg font-bold"><?= htmlspecialchars($row['item_name']) ?></h3>
                    <p class="text-gray-300">Lost at: <?= htmlspecialchars($row['location']) ?></p>
                    <p class="text-sm text-gray-400">Reported by: <strong><?= htmlspecialchars($row['name']) ?></strong> (<?= htmlspecialchars($row['email']) ?>)</p>
                    <p class="text-sm text-gray-400">Reported on: <?= date("F j, Y, g:i a", strtotime($row['created_at'])) ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-400 text-center py-4 mt-10">
        <p>&copy; 2025 Back2Me. All rights reserved.</p>
    </footer>
    <!-- Floating Chat Button -->
<!-- Floating Chat Button -->
<div id="chat-icon" class="fixed bottom-4 right-4 bg-purple-600 text-white p-4 rounded-full shadow-lg cursor-pointer">
    💬 Chat
</div>

<!-- Chat Box -->
<div id="chat-box" class="hidden fixed bottom-16 right-4 bg-gray-800 p-4 rounded-lg shadow-lg w-80">
    <h2 class="text-yellow-400 text-lg font-semibold">Chat</h2>
    <input type="text" id="searchUser" placeholder="Search user..." class="w-full p-2 rounded bg-gray-700 text-white my-2">
    <div id="chatList" class="max-h-60 overflow-y-auto text-white"></div>
    <div id="chatMessages" class="hidden max-h-60 overflow-y-auto border-t mt-2 pt-2"></div>
    <input type="text" id="messageInput" class="w-full p-2 rounded bg-gray-700 text-white mt-2 hidden" placeholder="Type a message...">
    <button id="sendMessage" class="hidden bg-purple-500 text-white px-4 py-2 rounded mt-2">Send</button>
</div>

<script>
    document.getElementById('chat-icon').addEventListener('click', function() {
        document.getElementById('chat-box').classList.toggle('hidden');
    });

    // Initialize Firebase
    const db = firebase.firestore();

    // Fetch all users
    document.addEventListener("DOMContentLoaded", function() {
        const chatList = document.getElementById("chatList");

        db.collection("users").get().then((querySnapshot) => {
            chatList.innerHTML = ""; // Clear loading text
            querySnapshot.forEach((doc) => {
                let user = doc.data();
                let userElement = document.createElement("div");
                userElement.classList.add("p-2", "hover:bg-gray-700", "cursor-pointer", "rounded");
                userElement.innerHTML = `<strong>${user.name}</strong> (${user.email})`;
                userElement.onclick = () => openChat(user.email);
                chatList.appendChild(userElement);
            });
        });
    });

    let currentChatUser = "";

    // Open Chat with Selected User
    function openChat(email) {
        currentChatUser = email;
        document.getElementById("chatMessages").classList.remove("hidden");
        document.getElementById("messageInput").classList.remove("hidden");
        document.getElementById("sendMessage").classList.remove("hidden");

        loadMessages(email);
    }

    // Load Messages in Real Time
    function loadMessages(email) {
        const chatMessages = document.getElementById("chatMessages");
        chatMessages.innerHTML = "<p class='text-gray-400 text-sm'>Loading messages...</p>";

        db.collection("chats").doc(email).collection("messages").orderBy("timestamp")
        .onSnapshot((snapshot) => {
            chatMessages.innerHTML = "";
            snapshot.forEach((doc) => {
                let msg = doc.data();
                let msgElement = document.createElement("div");
                msgElement.classList.add("p-2", "rounded", msg.sender === "me" ? "bg-blue-500 text-white" : "bg-gray-700 text-white");
                msgElement.innerHTML = `<strong>${msg.sender}:</strong> ${msg.text}`;
                chatMessages.appendChild(msgElement);
            });
        });
    }

    // Send Message
    document.getElementById("sendMessage").addEventListener("click", function() {
        const messageInput = document.getElementById("messageInput");
        const message = messageInput.value.trim();

        if (message !== "") {
            db.collection("chats").doc(currentChatUser).collection("messages").add({
                sender: "me",
                text: message,
                timestamp: firebase.firestore.FieldValue.serverTimestamp()
            }).then(() => {
                messageInput.value = "";
            });
        }
    });
</script>


<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("chat-icon").addEventListener("click", function() {
        document.getElementById("chat-box").classList.toggle("hidden");
    });
});

</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chatList = document.getElementById("chatList");

        // Fetch users from Firestore
        db.collection("users").get().then((querySnapshot) => {
            chatList.innerHTML = ""; // Clear loading text
            querySnapshot.forEach((doc) => {
                let user = doc.data();
                let userElement = document.createElement("div");
                userElement.classList.add("p-2", "hover:bg-gray-700", "cursor-pointer", "rounded");
                userElement.innerHTML = `<strong>${user.name}</strong> (${user.email})`;
                userElement.onclick = () => startChat(user.email);
                chatList.appendChild(userElement);
            });
        }).catch((error) => {
            chatList.innerHTML = "<p class='text-red-400'>Error loading users.</p>";
        });
    });

    function startChat(email) {
        alert("Chat feature coming soon for: " + email);
    }
</script>

</body>
</html>

<?php $conn->close(); ?>
