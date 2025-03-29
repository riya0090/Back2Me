<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        echo "Registration successful!";
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
    <title>Register | Back2Me</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-semibold text-center text-blue-600 mb-4">Create an Account</h2>
        <form action="register.php" method="POST">
            <label class="block mb-2 text-gray-700">Full Name</label>
            <input type="text" name="name" required class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400">

            <label class="block mb-2 text-gray-700">Email</label>
            <input type="email" name="email" required class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400">

            <label class="block mb-2 text-gray-700">Password</label>
            <input type="password" name="password" required class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400">

            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition">Register</button>
        </form>
        <p class="text-center text-gray-600 mt-4">Already have an account? <a href="login.php" class="text-blue-600 hover:underline">Login</a></p>
    </div>
</body>
</html>