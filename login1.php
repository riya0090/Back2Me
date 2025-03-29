<?php
include 'db_connect.php';
session_start();

$message = ""; // Store success or error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Prepare statement to fetch hashed password from database
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $hashed_password);

        if ($stmt->fetch()) {
            // Verify entered password against stored hashed password
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                echo "<script>alert('Login successful! Redirecting...'); window.location.href='index3.php';</script>";
                exit;
            } else {
                $message = "Invalid email or password.";
            }
        } else {
            $message = "Invalid email or password.";
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
    <title>Login | Back2Me</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-900 text-gray-200">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-semibold text-center text-purple-400 mb-4">Login to Back2Me</h2>

        <?php if (!empty($message)): ?>
            <p class="text-center text-red-500"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form action="login1.php" method="POST">
            <label class="block mb-2 text-gray-300">Email</label>
            <input type="email" name="email" required class="w-full p-2 border rounded bg-gray-700 text-gray-200 mb-4 focus:outline-none focus:ring-2 focus:ring-purple-500">

            <label class="block mb-2 text-gray-300">Password</label>
            <input type="password" name="password" required class="w-full p-2 border rounded bg-gray-700 text-gray-200 mb-4 focus:outline-none focus:ring-2 focus:ring-purple-500">

            <button type="submit" class="w-full bg-purple-600 text-white p-2 rounded hover:bg-purple-700 transition">Login</button>
        </form>
        
        <p class="text-center text-gray-400 mt-4">Don't have an account? <a href="register.php" class="text-purple-400 hover:underline">Sign Up</a></p>
    </div>
</body>
</html>