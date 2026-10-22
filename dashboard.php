<?php
$pageTitle = "Dashboard";
require_once 'header_admin.php';

// Statistik dari db_web
$jmlMhs = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM biodata_mhs"))['c'] ?? 0);
$jmlAdmin = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM admin_users"))['c'] ?? 0);
$jmlKrs = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM krs_mahasiswa"))['c'] ?? 0);
?>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h6 class="text-muted">Data Mahasiswa</h6>
        <h2 class="mb-0"><?php echo $jmlMhs; ?></h2>
        <small class="text-muted">biodata_mhs</small>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h6 class="text-muted">Admin User</h6>
        <h2 class="mb-0"><?php echo $jmlAdmin; ?></h2>
        <small class="text-muted">admin_users</small>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h6 class="text-muted">Jadwal KRS</h6>
        <h2 class="mb-0"><?php echo $jmlKrs; ?></h2>
        <small class="text-muted">krs_mahasiswa</small>
      </div>
    </div>
  </div>
</div>

<div class="mt-4">
  <a href="manajemen_user.php" class="btn btn-primary">Kelola Manajemen User</a>
</div>
<?php require_once 'footer_admin.php'; ?>
