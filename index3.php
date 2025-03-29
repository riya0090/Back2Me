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
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-card {
            animation: fadeIn 0.4s ease-out forwards;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.45);
        }
        .chat-icon {
            transition: all 0.3s ease;
        }
        .chat-icon:hover {
            transform: scale(1.1) rotate(5deg);
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-200 min-h-screen">
    <header class="bg-gradient-to-r from-purple-800 to-purple-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-3xl font-bold text-yellow-400">Back2Me</h1>
            <nav>
                <ul class="flex flex-wrap justify-center gap-4 md:gap-6">
                    <li><a href="index.php" class="hover:text-yellow-400 transition px-2 py-1 rounded hover:bg-purple-700/50">Home</a></li>
                    <li><a href="report_lost.php" class="hover:text-yellow-400 transition px-2 py-1 rounded hover:bg-purple-700/50">Report Lost</a></li>
                    <li><a href="report_found.php" class="hover:text-yellow-400 transition px-2 py-1 rounded hover:bg-purple-700/50">Report Found</a></li>
                    <li><a href="contact.html" class="hover:text-yellow-400 transition px-2 py-1 rounded hover:bg-purple-700/50">Contact</a></li>
                </ul>
            </nav>
            <div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </a>
                <?php else: ?>
                    <a href="login1.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="hero bg-gradient-to-br from-purple-700 to-purple-500 text-white text-center py-20 px-4">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-4xl md:text-5xl font-bold mb-4">Reuniting People With Their Lost Items</h2>
            <p class="text-xl text-purple-100 mb-8">Fast, secure, and community-powered lost and found platform</p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <form method="GET" action="search.php" class="w-full sm:w-auto">
                    <div class="relative flex">
                        <input type="text" name="query" placeholder="Search for items..." 
                            class="w-full px-6 py-3 rounded-full bg-white/10 border border-white/20 focus:outline-none focus:ring-2 focus:ring-yellow-400 text-white placeholder-purple-200">
                        <button type="submit" 
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-yellow-500 text-purple-900 px-4 py-2 rounded-full font-bold hover:bg-yellow-400 transition">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <main class="container mx-auto px-4 py-10">
        <!-- Recently Found Items -->
        <section class="my-12 animate-card">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-yellow-400">Recently Found Items</h2>
                <a href="found_items.php" class="text-purple-400 hover:text-purple-300 hover:underline">View All →</a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                    $found_items = $conn->query("
                        SELECT found_items.id, found_items.item_name, found_items.location, found_items.qr_code, 
                               found_items.description, found_items.created_at, users.name, users.email 
                        FROM found_items 
                        JOIN users ON found_items.user_id = users.id 
                        ORDER BY found_items.created_at DESC LIMIT 6
                    ");
                    while ($row = $found_items->fetch_assoc()):
                ?>
                    <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg transition-all duration-300 card-hover">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-xl font-bold text-white mb-1"><?= htmlspecialchars($row['item_name']) ?></h3>
                                    <p class="text-purple-300 mb-3">Found at: <?= htmlspecialchars($row['location']) ?></p>
                                </div>
                                <?php if (!empty($row['qr_code'])): ?>
                                    <img src="<?= htmlspecialchars($row['qr_code']) ?>" alt="QR Code" class="w-16 h-16 rounded border-2 border-yellow-400">
                                <?php endif; ?>
                            </div>
                            
                            <p class="text-gray-300 mb-4 line-clamp-2"><?= htmlspecialchars($row['description']) ?></p>
                            
                            <div class="flex items-center justify-between text-sm text-gray-400 mb-4">
                                <span>Reported by: <?= htmlspecialchars($row['name']) ?></span>
                                <span><?= date("M j, Y", strtotime($row['created_at'])) ?></span>
                            </div>
                            
                            <div class="flex flex-wrap gap-2">
                                <a href="claim_item.php?item_id=<?= $row['id'] ?>" 
                                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex-1 text-center">
                                    Claim Item
                                </a>
                                <?php if (!empty($row['qr_code'])): ?>
                                    <a href="<?= htmlspecialchars($row['qr_code']) ?>" download 
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center justify-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        QR
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <!-- Recently Lost Items -->
        <section class="my-12 animate-card" style="animation-delay: 0.1s">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-yellow-400">Recently Lost Items</h2>
                <a href="lost_items.php" class="text-purple-400 hover:text-purple-300 hover:underline">View All →</a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                    $lost_items = $conn->query("
                        SELECT lost_items.id, lost_items.item_name, lost_items.location, 
                               lost_items.description, lost_items.created_at, users.name, users.email 
                        FROM lost_items 
                        JOIN users ON lost_items.user_id = users.id 
                        ORDER BY lost_items.created_at DESC LIMIT 6
                    ");
                    while ($row = $lost_items->fetch_assoc()):
                ?>
                    <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg transition-all duration-300 card-hover">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-white mb-1"><?= htmlspecialchars($row['item_name']) ?></h3>
                            <p class="text-purple-300 mb-3">Lost at: <?= htmlspecialchars($row['location']) ?></p>
                            
                            <p class="text-gray-300 mb-4 line-clamp-2"><?= htmlspecialchars($row['description']) ?></p>
                            
                            <div class="flex items-center justify-between text-sm text-gray-400 mb-4">
                                <span>Reported by: <?= htmlspecialchars($row['name']) ?></span>
                                <span><?= date("M j, Y", strtotime($row['created_at'])) ?></span>
                            </div>
                            
                            <a href="item_details.php?item_id=<?= $row['id'] ?>&type=lost" 
                               class="block bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>

    <!-- Floating Chat Icon -->
    <a href="chat_list1.php" id="chat-icon" 
       class="fixed bottom-6 right-6 bg-gradient-to-br from-purple-600 to-blue-500 text-white p-4 rounded-full shadow-xl chat-icon hover:shadow-2xl">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
    </a>

    <footer class="bg-gray-900 border-t border-gray-800 text-gray-400 text-center py-8 mt-10">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-xl font-bold text-yellow-400">Back2Me</div>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition">Terms</a>
                    <a href="#" class="hover:text-white transition">Privacy</a>
                    <a href="#" class="hover:text-white transition">FAQ</a>
                    <a href="contact.html" class="hover:text-white transition">Contact</a>
                </div>
            </div>
            <p class="mt-6">&copy; <?= date('Y') ?> Back2Me. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

<?php $conn->close(); ?>