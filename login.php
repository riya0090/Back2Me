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
                echo "<script>alert('Login successful! Redirecting...'); window.location.href='index.php';</script>";
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
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-semibold text-center text-blue-600 mb-4">Login to Back2Me</h2>

        <?php if (!empty($message)): ?>
            <p class="text-center text-red-600"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label class="block mb-2 text-gray-700">Email</label>
            <input type="email" name="email" required class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400">

            <label class="block mb-2 text-gray-700">Password</label>
            <input type="password" name="password" required class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400">

            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition">Login</button>
        </form>
        
        <p class="text-center text-gray-600 mt-4">Don't have an account? <a href="register.php" class="text-blue-600 hover:underline">Sign Up</a></p>
    </div>
</body>
</html>
