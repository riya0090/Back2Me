<?php
include 'db_connect.php';
session_start();

$message = ""; // Store success/error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($name) && !empty($email) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Encrypt password

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $message = "Registration successful! Redirecting to login...";
            echo "<script>alert('$message'); window.location.href='login1.php';</script>";
            exit;
        } else {
            $message = "Error: Email already exists.";
        }

        $stmt->close();
    } else {
        $message = "All fields are required.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Back2Me</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-900 text-gray-200">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-semibold text-center text-purple-400 mb-4">Create an Account</h2>

        <?php if (!empty($message)): ?>
            <p class="text-center text-red-500"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label class="block mb-2 text-gray-300">Full Name</label>
            <input type="text" name="name" required class="w-full p-2 border rounded mb-4 bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">

            <label class="block mb-2 text-gray-300">Email</label>
            <input type="email" name="email" required class="w-full p-2 border rounded mb-4 bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">

            <label class="block mb-2 text-gray-300">Password</label>
            <input type="password" name="password" required class="w-full p-2 border rounded mb-4 bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">

            <button type="submit" class="w-full bg-purple-600 text-white p-2 rounded hover:bg-purple-700 transition">Register</button>
        </form>

        <p class="text-center text-gray-400 mt-4">Already have an account? <a href="login1.php" class="text-purple-400 hover:underline">Login</a></p>
    </div>
</body>
</html>