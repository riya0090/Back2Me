<?php

require 'phpqrcode/qrlib.php'; // Include the QR code library

include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        die("Please log in to report a found item.");
    }

    $user_id = $_SESSION['user_id'];
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $found_date = $_POST['found_date'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("INSERT INTO found_items (user_id, item_name, description, found_date, location) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $item_name, $description, $found_date, $location);

    if ($stmt->execute()) {
        echo "Found item reported successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Report Found Item</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-200 p-6">
    <h2 class="text-2xl font-semibold text-center text-yellow-400">Report Found Item</h2>
    <form action="report_found.php" method="POST" class="max-w-lg mx-auto bg-gray-800 p-6 shadow-lg rounded-lg">
        <label class="block mb-2 text-gray-300">Item Name</label>
        <input type="text" name="item_name" placeholder="Item Name" required class="w-full p-2 mb-4 border rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
        
        <label class="block mb-2 text-gray-300">Description</label>
        <textarea name="description" placeholder="Description" required class="w-full p-2 mb-4 border rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400"></textarea>
        
        <label class="block mb-2 text-gray-300">Found Date</label>
        <input type="date" name="found_date" required class="w-full p-2 mb-4 border rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
        
        <label class="block mb-2 text-gray-300">Location Found</label>
        <input type="text" name="location" placeholder="Location Found" required class="w-full p-2 mb-4 border rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
        
        <button type="submit" class="w-full bg-purple-600 text-white p-2 rounded hover:bg-purple-700 transition">Report Found Item</button>
    </form>
</body>
</html>