<?php
session_start();
include 'db_connect.php';
require 'phpqrcode/qrlib.php';

if (!isset($_SESSION['user_id'])) {
    die("Please log in to report a found item.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $found_date = $_POST['found_date'];
    $location = $_POST['location'];
    $question1 = $_POST['question1'];
    $answer1 = $_POST['answer1'];
    $question2 = $_POST['question2'];
    $answer2 = $_POST['answer2'];

    $stmt = $conn->prepare("INSERT INTO found_items 
        (user_id, item_name, description, found_date, location, question1, answer1, question2, answer2, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issssssss", $user_id, $item_name, $description, $found_date, $location, $question1, $answer1, $question2, $answer2);
    
    if ($stmt->execute()) {
        $found_id = $conn->insert_id;
        $qr_data = "https://yourwebsite.com/view_item.php?id=$found_id"; // Change to your actual domain
        $qr_filename = "found_$found_id.png";
        
        // Generate QR code
        QRcode::png($qr_data, $qr_filename, QR_ECLEVEL_L, 10);

        // Store QR path in database
        $updateStmt = $conn->prepare("UPDATE found_items SET qr_code = ? WHERE id = ?");
        $updateStmt->bind_param("si", $qr_filename, $found_id);
        $updateStmt->execute();
        $updateStmt->close();

        $success_message = <<<HTML
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4 animate-fade-in">
                <div class="text-center">
                    <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-green-500 mb-2">Item Reported Successfully!</h3>
                    <p class="text-gray-300 mb-6">Your found item has been registered in our system.</p>
                    
                    <div class="bg-white p-4 rounded-lg mb-6 shadow-lg transform transition hover:scale-105">
                        <div class="flex justify-center mb-3">
                            <img src="$qr_filename" alt="QR Code" class="w-40 h-40">
                        </div>
                        <p class="text-gray-800 text-sm font-medium">Scan to view item details</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="$qr_filename" download class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download QR
                        </a>
                        <button onclick="window.location.href='report_found.php'" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                            Report Another Item
                        </button>
                    </div>
                </div>
            </div>
        </div>
HTML;
    } else {
        $error_message = "<div class='text-center text-red-500 bg-red-900/50 p-4 rounded-lg'>Error reporting item. Please try again.</div>";
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
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
        .qr-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.45);
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-200 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-2xl bg-gray-800 p-8 rounded-xl shadow-2xl">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-yellow-400 mb-2">Report Found Item</h2>
            <p class="text-gray-400">Help reunite lost items with their owners</p>
        </div>
        
        <?php if (isset($error_message)) echo $error_message; ?>
        
        <form action="report_found.php" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Item Name</label>
                    <input type="text" name="item_name" required 
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none input-focus text-white placeholder-gray-400">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Found Date</label>
                    <input type="date" name="found_date" required 
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none input-focus text-white">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                <textarea name="description" rows="3" required 
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none input-focus text-white placeholder-gray-400"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Location Found</label>
                <input type="text" name="location" required 
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none input-focus text-white placeholder-gray-400">
            </div>
            
            <div class="pt-4 border-t border-gray-700">
                <h3 class="text-lg font-semibold text-yellow-400 mb-4">Verification Questions</h3>
                <p class="text-sm text-gray-400 mb-4">Set questions that only the real owner would know to verify ownership</p>
                
                <div class="space-y-6">
                    <div class="bg-gray-700/50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Question 1</label>
                        <input type="text" name="question1" placeholder="e.g., What brand is this item?" required 
                            class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg mb-3 focus:outline-none input-focus text-white placeholder-gray-400">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Correct Answer</label>
                        <input type="text" name="answer1" required 
                            class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg focus:outline-none input-focus text-white">
                    </div>
                    
                    <div class="bg-gray-700/50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Question 2</label>
                        <input type="text" name="question2" placeholder="e.g., Where did you lose it?" required 
                            class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg mb-3 focus:outline-none input-focus text-white placeholder-gray-400">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Correct Answer</label>
                        <input type="text" name="answer2" required 
                            class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg focus:outline-none input-focus text-white">
                    </div>
                </div>
            </div>
            
            <button type="submit" 
                class="w-full bg-gradient-to-r from-purple-600 to-blue-500 text-white font-medium py-3 px-6 rounded-lg hover:from-purple-700 hover:to-blue-600 transition-all duration-300 shadow-lg transform hover:scale-[1.01]">
                Report Found Item
            </button>
        </form>
    </div>

    <?php if (isset($success_message)) echo $success_message; ?>
</body>
</html>