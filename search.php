<?php
include 'db_connect.php';

$search_query = $_GET['query'] ?? '';
$results = [];

if (!empty($search_query)) {
    $stmt = $conn->prepare("
        SELECT id, item_name, description, lost_date, location, 'lost' AS type FROM lost_items 
        WHERE item_name LIKE ? OR description LIKE ? OR location LIKE ?
        UNION 
        SELECT id, item_name, description, found_date AS lost_date, location, 'found' AS type 
        FROM found_items 
        WHERE item_name LIKE ? OR description LIKE ? OR location LIKE ?
        ORDER BY lost_date DESC
    ");

    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("ssssss", $search_param, $search_param, $search_param, $search_param, $search_param, $search_param);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Items - Back2Me</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .search-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .type-badge {
            top: -0.5rem;
            right: -0.5rem;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Search Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Search Lost & Found Items</h1>
            <p class="text-gray-600">Find what you're looking for in our database</p>
        </div>

        <!-- Search Form -->
        <form action="search.php" method="GET" class="mb-10">
            <div class="flex shadow-lg rounded-lg overflow-hidden">
                <input 
                    type="text" 
                    name="query" 
                    value="<?= htmlspecialchars($search_query) ?>" 
                    placeholder="Try 'wallet', 'keys', 'phone'..." 
                    class="flex-grow p-4 border-0 focus:ring-2 focus:ring-purple-500 focus:outline-none"
                    required
                >
                <button 
                    type="submit" 
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 font-semibold transition-colors"
                >
                    Search
                </button>
            </div>
        </form>

        <!-- Results Section -->
        <?php if (!empty($search_query)): ?>
            <div class="mb-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">
                    <?= count($results) ?> result<?= count($results) !== 1 ? 's' : '' ?> for "<?= htmlspecialchars($search_query) ?>"
                </h2>
            </div>

            <?php if (!empty($results)): ?>
                <div class="grid gap-4 md:grid-cols-2">
                    <?php foreach ($results as $item): ?>
                        <div class="bg-white rounded-lg shadow-md p-5 relative search-card transition-all duration-200">
                            <div class="absolute type-badge px-3 py-1 rounded-full text-xs font-bold 
                                <?= $item['type'] === 'found' ? 'bg-green-500 text-white' : 'bg-yellow-500 text-gray-800' ?>">
                                <?= ucfirst($item['type']) ?>
                            </div>
                            
                            <h3 class="text-lg font-bold text-gray-800 mb-1 pr-6"><?= htmlspecialchars($item['item_name']) ?></h3>
                            <p class="text-sm text-gray-500 mb-2">
                                <span class="font-medium"><?= date('M j, Y', strtotime($item['lost_date'])) ?></span> • 
                                <?= htmlspecialchars($item['location']) ?>
                            </p>
                            <p class="text-gray-600 mb-3 line-clamp-2"><?= htmlspecialchars($item['description']) ?></p>
                            <a href="item_details.php?id=<?= $item['id'] ?>&type=<?= $item['type'] ?>" 
                               class="text-purple-600 hover:text-purple-800 font-medium text-sm">
                                View details →
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-700 mb-1">No items found</h3>
                    <p class="text-gray-500">Try different search terms or check back later</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-700 mb-1">What are you looking for?</h3>
                <p class="text-gray-500">Enter your search query above to find lost or found items</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>