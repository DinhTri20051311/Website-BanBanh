<?php
include '../config/connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền admin
if (!isset($_SESSION['tk']) || $_SESSION['vaitro'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$tablename = "user";
$query = "SELECT * FROM $tablename";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}

$labelsMap = [
    'ID_USER' => 'Mã người dùng', 
    'TK'      => 'Tài khoản',
    'USER'    => 'Tên đăng nhập',
    'MK'      => 'Mật khẩu',
    'EMAIL'   => 'Email',
    'VAITRO'  => 'Vai trò'
];
$maskColumns = ['password','pass','pwd'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý khách hàng</title>
<link href="../assets/bootstrap-5.3.8-dis/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">

<script src="../assets/js/bootstrap.bundle.min.js"></script>
<style>
/* Thêm một chút khoảng đệm cho container để nội dung không quá sát lề */
.container {
    padding-top: 1.5rem;
    padding-bottom: 1.5rem;
}

td input, td select {
    width: 100%;
    border: none;
    background: transparent;
    outline: none;
}
td input[readonly] {
    background-color: transparent;
    color: #555;
}
.password-field {
    position: relative;
}
.password-field i {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #333;
}

/* 🌈 Hàng tiêu đề dùng chung màu xám */
.table thead th {
    background-color: #4b4b4b; /* xám đậm */
    color: #fff;              /* chữ trắng */
    text-align: center;
    font-weight: 700;
    border-color: #3a3a3a;
}

/* Giữ bảng gọn đẹp */
.table td { vertical-align: middle; }
.table tbody tr:nth-child(even) { background-color: #f8f8f8; } /* xen kẽ màu nhạt */
.table tbody tr:hover { background-color: #eee; }              /* hover nhẹ */
</style>
</head>
<body>
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>👥 Danh sách khách hàng</h2>
    <a href="admin.php?page=Menu_admin" class="btn btn-secondary">← Quay lại</a>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead>
        <tr>
          <?php
          $fields = mysqli_fetch_fields($result);
          foreach ($fields as $f) {
              $col = $f->name;
              if (in_array($col, $maskColumns)) continue;
              $label = $labelsMap[$col] ?? ucfirst($col);
              echo "<th>$label</th>";
          }
          ?>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php
        mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_assoc($result)): ?>
        <tr data-id="<?= $row['ID_USER'] ?>">
          <?php foreach ($row as $col => $val): ?>
            <?php if ($col === 'MK'): ?>
              <td class="password-field">
                <input type="password" class="form-control form-control-sm" name="MK"
                       value="<?= htmlspecialchars($val) ?>" readonly>
                <i class="bi bi-eye toggle-pass"></i>
              </td>
            <?php elseif ($col === 'ID_USER'): ?>
              <td><?= htmlspecialchars($val) ?></td>
            <?php elseif ($col === 'VAITRO'): ?>
              <td>
                <select name="VAITRO" class="form-select form-select-sm" disabled>
                  <option value="user" <?= trim($val) === 'user' ? 'selected' : '' ?>>User</option>
                  <option value="admin" <?= trim($val) === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
              </td>
            <?php else: ?>
              <td><input type="text" class="form-control form-control-sm"
                         name="<?= $col ?>" value="<?= htmlspecialchars($val) ?>" readonly></td>
            <?php endif; ?>
          <?php endforeach; ?>
          <td class="text-center">
            <div>
              <button class="btn btn-warning btn-sm edit-btn"><i class="bi bi-pencil"></i> Chỉnh sửa</button>
              <button class="btn btn-success btn-sm save-btn d-none"><i class="bi bi-save"></i> Lưu</button>
            </div>
            <div class="mt-1">
              <button class="btn btn-danger btn-sm delete-btn"><i class="bi bi-trash"></i> Xóa</button>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
// Hiện/ẩn mật khẩu
document.querySelectorAll('.toggle-pass').forEach(icon => {
  icon.addEventListener('click', () => {
    const input = icon.previousElementSibling;
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
  });
});

// Bật chế độ chỉnh sửa
document.querySelectorAll('.edit-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const row = btn.closest('tr');
    const inputs = row.querySelectorAll('input');
    const selects = row.querySelectorAll('select');
    const saveBtn = row.querySelector('.save-btn');

    inputs.forEach(inp => inp.removeAttribute('readonly'));
    selects.forEach(sel => sel.removeAttribute('disabled'));

    btn.classList.add('d-none');
    saveBtn.classList.remove('d-none');
  });
});

// Lưu thông tin người dùng
document.querySelectorAll('.save-btn').forEach(btn => {
  btn.addEventListener('click', async () => {
    const row = btn.closest('tr');
    const id = row.dataset.id;
    const data = { action: 'update', id };

    row.querySelectorAll('input, select').forEach(inp => {
      data[inp.name] = inp.value;
      if (inp.tagName === 'INPUT') inp.setAttribute('readonly', true);
      if (inp.tagName === 'SELECT') inp.setAttribute('disabled', true);
    });

    try {
      const res = await fetch('update_user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });

      const result = await res.json();
      alert(result.message);

      if (result.success) {
        window.location.reload();
      }
    } catch (err) {
      alert('Lỗi khi lưu dữ liệu!');
      console.error(err);
    }
  });
});

// Xóa người dùng
document.querySelectorAll('.delete-btn').forEach(btn => {
  btn.addEventListener('click', async () => {
    if (!confirm('Bạn có chắc muốn xóa người dùng này không?')) return;

    const row = btn.closest('tr');
    const id = row.dataset.id;

    try {
      const res = await fetch('update_user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'delete', id })
      });

      const result = await res.json();
      alert(result.message);

      if (result.success) {
        window.location.reload();
      }
    } catch (err) {
      alert('❌ Lỗi: ' + err.message);
      console.error(err);
    }
  });
});
</script>

</body>
</html>

