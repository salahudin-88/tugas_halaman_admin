<?php
// ============================================================
// Login Admin (area /admin) - autentikasi ke db_web.admin_users
// ============================================================
require_once __DIR__ . '/../koneksi.php';

// Jika sudah login, alihkan ke dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$galat = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $galat = "Username dan Password wajib diisi.";
    } else {
        $stmt = mysqli_prepare($conn,
            "SELECT id, username, nama_lengkap FROM admin_users WHERE username = ? AND password = ?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        $data = mysqli_stmt_get_result($stmt)->fetch_assoc();
        mysqli_stmt_close($stmt);

        if ($data) {
            $_SESSION['admin_id']       = (int)$data['id'];
            $_SESSION['admin_username'] = $data['username'];
            $_SESSION['admin_nama']     = $data['nama_lengkap'];
            header('Location: dashboard.php');
            exit;
        } else {
            $galat = "Username atau Password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f4f4f9; }
    .card-login { max-width:400px; margin:80px auto; }
  </style>
</head>
<body>
  <div class="card card-login shadow">
    <div class="card-body">
      <h3 class="text-center mb-3">Login Admin</h3>
      <p class="text-muted text-center small">Area administrasi — db_web.admin_users</p>
      <?php if ($galat !== ''): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($galat, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>
      <form method="post" action="login_admin.php">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Masuk</button>
      </form>
    </div>
  </div>
</body>
</html>
