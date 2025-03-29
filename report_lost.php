<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $lost_date = $_POST['lost_date'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("INSERT INTO lost_items (user_id, item_name, description, lost_date, location) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $item_name, $description, $lost_date, $location);

    if ($stmt->execute()) {
        $success_message = <<<HTML
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4 animate-fade-in">
                <div class="text-center">
                    <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-green-500 mb-2">Item Reported Successfully!</h3>
                    <p class="text-gray-300 mb-6">Your lost item has been registered in our system.</p>
                    
                    <div class="flex justify-center gap-3">
                        <a href="lost_items.php" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                            View Lost Items
                        </a>
                        <button onclick="window.location.href='report_lost.php'" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                            Report Another
                        </button>
                    </div>
                </div>
            </div>
        </div>
HTML;
    } else {
        $error_message = "<div class='text-center text-red-500 bg-red-900/50 p-4 rounded-lg mb-6'>Error reporting item: " . $stmt->error . "</div>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Lost Item - Back2Me</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.45);
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-200 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-2xl bg-gray-800 p-8 rounded-xl shadow-2xl">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-yellow-400 mb-2">Report Lost Item</h2>
            <p class="text-gray-400">Help others identify and return your lost belongings</p>
        </div>

        <?php if (isset($error_message)) echo $error_message; ?>

        <form action="report_lost.php" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Item Name</label>
                <input type="text" name="item_name" placeholder="e.g., iPhone 12, Wallet, Keys" required 
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none input-focus text-white placeholder-gray-400">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                <textarea name="description" rows="3" placeholder="Include distinctive features, color, brand, etc." required 
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none input-focus text-white placeholder-gray-400"></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Date Lost</label>
                    <input type="date" name="lost_date" required 
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none input-focus text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Last Seen Location</label>
                    <input type="text" name="location" placeholder="e.g., Main Library, Room 205" required 
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none input-focus text-white placeholder-gray-400">
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-gradient-to-r from-purple-600 to-blue-500 text-white font-medium py-3 px-6 rounded-lg hover:from-purple-700 hover:to-blue-600 transition-all duration-300 shadow-lg transform hover:scale-[1.01]">
                Report Lost Item
            </button>
        </form>
    </div>

    <?php if (isset($success_message)) echo $success_message; ?>
</body>
</html>