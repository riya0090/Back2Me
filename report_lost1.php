<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login1.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $lost_date = $_POST['lost_date'];
    $location = $_POST['location'];

    // Handle QR Code Image Upload
    $target_dir = "uploads/qr_codes/"; // Directory to store QR images
    $qr_code_path = ""; 

    if (!empty($_FILES["qr_code"]["name"])) {
        $file_name = uniqid("qr_", true) . "_" . basename($_FILES["qr_code"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type (allow only PNG, JPG, JPEG)
        $allowed_types = ["png", "jpg", "jpeg"];
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["qr_code"]["tmp_name"], $target_file)) {
                $qr_code_path = $target_file;
            } else {
                echo "<script>alert('Error uploading QR code.');</script>";
            }
        } else {
            echo "<script>alert('Only PNG, JPG, and JPEG files are allowed.');</script>";
        }
    }

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO lost_items (user_id, item_name, description, lost_date, location) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $item_name, $description, $lost_date, $location);

    if ($stmt->execute()) {
        echo "<script>alert('Lost item reported successfully!'); window.location.href='index1.php';</script>";
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Lost Item</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-200">
    <div class="max-w-lg mx-auto bg-gray-800 p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-center text-yellow-500 mb-4">Report Lost Item</h2>
        <form action="report_lost1.php" method="POST" enctype="multipart/form-data">
            <label class="block text-gray-400 mb-2">Item Name</label>
            <input type="text" name="item_name" required class="w-full p-2 border rounded mb-4 bg-gray-700 text-white">

            <label class="block text-gray-400 mb-2">Description</label>
            <textarea name="description" required class="w-full p-2 border rounded mb-4 bg-gray-700 text-white"></textarea>

            <label class="block text-gray-400 mb-2">Lost Date</label>
            <input type="date" name="lost_date" required class="w-full p-2 border rounded mb-4 bg-gray-700 text-white">

            <label class="block text-gray-400 mb-2">Last Seen Location</label>
            <input type="text" name="location" required class="w-full p-2 border rounded mb-4 bg-gray-700 text-white">

            <button type="submit" class="w-full bg-purple-600 text-white p-2 rounded hover:bg-purple-700">Report Lost Item</button>
        </form>
    </div>
</body>
</html>
