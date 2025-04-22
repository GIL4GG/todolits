<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'todo_app');
if ($conn->connect_error) die("Koneksi database gagal: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $profile_picture = "default.jpg";

    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Semua kolom wajib diisi.";
    } elseif (!preg_match('/^[A-Za-z ]+$/', $full_name)) {
        $error = "Nama hanya boleh huruf dan spasi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email tidak valid.";
    } elseif (strlen($password) < 8) {
        $error = "Password minimal 8 karakter.";
    } elseif (!preg_match('/^[A-Za-z0-9]+$/', $password)) {
        $error = "Password hanya huruf dan angka.";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check->bind_param('s', $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "Email sudah digunakan.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, profile_picture) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $full_name, $email, $hashed, $profile_picture);
            $success = $stmt->execute() ? "Registrasi berhasil. Silakan login." : "Terjadi kesalahan. Coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color:rgb(12, 85, 212);
            font-family: 'Segoe UI', sans-serif;
        }
        .register-box {
            max-width: 400px;
            width: 100%;
            padding: 25px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #005ecb;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="register-box">
        <h4 class="text-center mb-3">Daftar Akun</h4>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <input type="text" name="full_name" class="form-control" placeholder="Nama Lengkap" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="mb-3">
                <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </form>

        <p class="mt-3 text-center text-muted" style="font-size: 0.9rem;">Sudah punya akun? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
