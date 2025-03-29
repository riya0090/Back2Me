<?php
session_start();
include 'db_connect.php';

if (!isset($_GET['item_id'])) {
    die("Invalid item request.");
}

$item_id = intval($_GET['item_id']);
$stmt = $conn->prepare("SELECT item_name, question1, answer1, question2, answer2 FROM found_items WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    die("Item not found.");
}

$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $claim_answer1 = strtolower(trim($_POST['answer1']));
    $claim_answer2 = strtolower(trim($_POST['answer2']));
    
    // ✅ Compare user input with correct answers
    if ($claim_answer1 === strtolower($item['answer1']) && $claim_answer2 === strtolower($item['answer2'])) {
        $success = true;
    } else {
        echo "<p class='text-red-500 text-center'>Incorrect answers! Please try again.</p>";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Item | Back2Me</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex justify-center items-center h-screen">
    <div class="w-full max-w-md bg-gray-800 p-4 rounded-lg shadow-lg">
        <h2 class="text-lg font-semibold text-center mb-4">Claim Item: <?= htmlspecialchars($item['item_name']) ?></h2>
        
        <?php if ($success): ?>
            <p class="text-green-500 text-center font-bold">✅ Verification Successful! You can now claim this item.</p>
        <?php else: ?>
            <form method="POST">
                <label class="block mb-2"><?= htmlspecialchars($item['question1']) ?></label>
                <input type="text" name="answer1" required class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400">
                
                <label class="block mb-2"><?= htmlspecialchars($item['question2']) ?></label>
                <input type="text" name="answer2" required class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400">
                
                <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Submit Claim</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
