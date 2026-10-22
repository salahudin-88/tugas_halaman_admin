<?php
// Guard: wajib login
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login_admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? $pageTitle : 'Admin Panel'; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f4f4f9; }
    .sidebar { min-height:100vh; background:#2c3e50; }
    .sidebar a { color:#fff; display:block; padding:10px 15px; text-decoration:none; }
    .sidebar a:hover { background:#34495e; }
    .content { padding:20px; }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-2 sidebar p-0">
        <div class="p-3 text-white fw-bold">Admin Panel</div>
        <a href="dashboard.php">Dashboard</a>
        <a href="manajemen_user.php">Manajemen User</a>
        <a href="../index.php?modul=mahasiswa" target="_blank">Data Mahasiswa</a>
        <a href="logout_admin.php">Logout</a>
      </div>
      <div class="col-md-10 content">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="mb-0"><?php echo isset($pageTitle) ? $pageTitle : 'Admin Panel'; ?></h4>
          <span class="text-muted">Halo, <?php echo htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
