<?php
$pageTitle = "Manajemen User";
require_once 'header_admin.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$galat = '';

// ---- HAPUS ----
if ($action === 'hapus' && $id > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM admin_users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        echo "<div class='alert alert-success'>Admin user berhasil dihapus.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menghapus: " . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8') . "</div>";
    }
    mysqli_stmt_close($stmt);
    $action = 'list';
}

// ---- TAMBAH (POST) ----
if ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username']     ?? '');
    $password     = trim($_POST['password']     ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');

    if ($username === '' || $password === '' || $nama_lengkap === '') {
        $galat = "Username, Password, dan Nama Lengkap wajib diisi.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        $galat = "Username harus 3-50 karakter (huruf, angka, underscore).";
    } else {
        $cek = mysqli_prepare($conn, "SELECT COUNT(*) AS jml FROM admin_users WHERE username = ?");
        mysqli_stmt_bind_param($cek, "s", $username);
        mysqli_stmt_execute($cek);
        $row = mysqli_stmt_get_result($cek)->fetch_assoc();
        mysqli_stmt_close($cek);

        if ((int)$row['jml'] > 0) {
            $galat = "Username {$username} sudah terdaftar.";
        } else {
            $stmt = mysqli_prepare($conn,
                "INSERT INTO admin_users (username, password, nama_lengkap) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $username, $password, $nama_lengkap);
            if (mysqli_stmt_execute($stmt)) {
                echo "<div class='alert alert-success'>Admin user berhasil ditambahkan.</div>";
                $action = 'list';
            } else {
                $galat = "Gagal menyimpan: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// ---- EDIT (POST) ----
if ($action === 'edit' && $id > 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username']     ?? '');
    $password     = trim($_POST['password']     ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');

    if ($username === '' || $nama_lengkap === '') {
        $galat = "Username dan Nama Lengkap wajib diisi.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        $galat = "Username harus 3-50 karakter (huruf, angka, underscore).";
    } else {
        $cek = mysqli_prepare($conn,
            "SELECT COUNT(*) AS jml FROM admin_users WHERE username = ? AND id <> ?");
        mysqli_stmt_bind_param($cek, "si", $username, $id);
        mysqli_stmt_execute($cek);
        $row = mysqli_stmt_get_result($cek)->fetch_assoc();
        mysqli_stmt_close($cek);

        if ((int)$row['jml'] > 0) {
            $galat = "Username {$username} sudah digunakan admin lain.";
        } else {
            if ($password === '') {
                $stmt = mysqli_prepare($conn,
                    "UPDATE admin_users SET username=?, nama_lengkap=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "ssi", $username, $nama_lengkap, $id);
            } else {
                $stmt = mysqli_prepare($conn,
                    "UPDATE admin_users SET username=?, password=?, nama_lengkap=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "sssi", $username, $password, $nama_lengkap, $id);
            }
            if (mysqli_stmt_execute($stmt)) {
                echo "<div class='alert alert-success'>Admin user berhasil diperbarui.</div>";
                $action = 'list';
            } else {
                $galat = "Gagal memperbarui: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// ---- Ambil data untuk edit ----
$editData = null;
if ($action === 'edit' && $id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM admin_users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $editData = mysqli_stmt_get_result($stmt)->fetch_assoc();
    mysqli_stmt_close($stmt);
    if (!$editData) {
        echo "<div class='alert alert-danger'>Data admin user tidak ditemukan.</div>";
        $action = 'list';
    }
}
?>

<?php if ($action === 'list'): ?>
  <div class="mb-3">
    <a href="manajemen_user.php?action=tambah" class="btn btn-success">+ Tambah Admin User</a>
  </div>
  <div class="table-responsive">
    <table class="table table-striped table-bordered">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Username</th>
          <th>Nama Lengkap</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM admin_users ORDER BY id ASC");
        $no = 1;
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td><strong>" . htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') . "</strong></td>";
                echo "<td>" . htmlspecialchars($row['nama_lengkap'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>";
                echo "<a href='manajemen_user.php?action=edit&id=" . (int)$row['id'] . "' class='btn btn-sm btn-primary me-1'>Edit</a>";
                echo "<a href='manajemen_user.php?action=hapus&id=" . (int)$row['id'] . "' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin hapus?');\">Hapus</a>";
                echo "</td>";
                echo "</tr>";
            }
            mysqli_free_result($result);
        }
        ?>
      </tbody>
    </table>
  </div>

<?php elseif ($action === 'tambah'): ?>
  <h5>Tambah Admin User</h5>
  <?php if ($galat !== ''): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($galat, ENT_QUOTES, 'UTF-8'); ?></div>
  <?php endif; ?>
  <form method="post" action="manajemen_user.php?action=tambah" style="max-width:420px;">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required
             value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Nama Lengkap</label>
      <input type="text" name="nama_lengkap" class="form-control" required
             value="<?php echo htmlspecialchars($_POST['nama_lengkap'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="manajemen_user.php" class="btn btn-secondary">Batal</a>
  </form>

<?php elseif ($action === 'edit' && $editData): ?>
  <h5>Edit Admin User (ID <?php echo (int)$editData['id']; ?>)</h5>
  <?php if ($galat !== ''): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($galat, ENT_QUOTES, 'UTF-8'); ?></div>
  <?php endif; ?>
  <form method="post" action="manajemen_user.php?action=edit&id=<?php echo (int)$editData['id']; ?>" style="max-width:420px;">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required
             value="<?php echo htmlspecialchars($_POST['username'] ?? $editData['username'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Password (baru, kosongkan bila tidak diubah)</label>
      <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Nama Lengkap</label>
      <input type="text" name="nama_lengkap" class="form-control" required
             value="<?php echo htmlspecialchars($_POST['nama_lengkap'] ?? $editData['nama_lengkap'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="manajemen_user.php" class="btn btn-secondary">Batal</a>
  </form>
<?php endif; ?>

<?php require_once 'footer_admin.php'; ?>
