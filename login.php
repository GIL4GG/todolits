<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'todo_app');
if ($conn->connect_error) die("Koneksi database gagal: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $_POST['email']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['profile_picture'] = $user['profile_picture'];
            header('Location: index.php');
            exit;
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Email tidak ditemukan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="login-box p-4 bg-white shadow rounded" style="width: 100%; max-width: 360px;">
        <h3 class="text-center mb-4">Login</h3>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center p-2"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3 input-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                <button type="button" class="btn btn-outline-secondary toggle-password" onclick="togglePassword()">
                    👁️
                </button>
            </div>
            <button type="submit" class="btn btn-primary w-100">Masuk</button>
        </form>

        <p class="mt-3 text-center text-muted" style="font-size: 0.9rem;">Belum punya akun? <a href="register.php">Daftar</a></p>
    </div>

    <script>
        function togglePassword() {
            const field = document.getElementById("password");
            field.type = field.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>
