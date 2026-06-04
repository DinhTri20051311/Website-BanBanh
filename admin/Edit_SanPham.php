<?php
// Khởi động session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kết nối CSDL
include '../config/connect.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['tk']) || $_SESSION['vaitro'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Kiểm tra ID sản phẩm hợp lệ
if (empty($_GET['id'])) {
    header("Location: admin.php?page=TT_SanPham.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']); // ✅ Sửa: Không dùng intval vì ID là chuỗi (e.g., 'SP001'), dùng escape để tránh SQL injection

// Lấy thông tin sản phẩm
$sql = "SELECT * FROM sanpham WHERE ID_SP = '$id'";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<div class='alert alert-danger'>Không tìm thấy sản phẩm.</div>";
    exit();
}

$sp = mysqli_fetch_assoc($result);

// Xử lý cập nhật sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = mysqli_real_escape_string($conn, $_POST['tensanpham']);
    $gia = floatval($_POST['gia']);
    $maloai = mysqli_real_escape_string($conn, $_POST['maloai']);
    $hinhanh = $sp['HINHANH']; // Giữ ảnh cũ nếu không upload mới

    // Nếu có upload ảnh mới
    if (!empty($_FILES['hinhanh']['name']) && $_FILES['hinhanh']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['hinhanh']['tmp_name'];
        $file_name = time() . "_" . basename($_FILES['hinhanh']['name']); // ✅ tránh trùng tên file
        $target_path = "../assets/image/banh/" . $file_name;

        // Tạo thư mục nếu chưa có
        if (!is_dir("../assets/image/banh")) {
            mkdir("../assets/image/banh", 0777, true);
        }

        // Chỉ chấp nhận ảnh hợp lệ
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_exts)) {
            if (move_uploaded_file($file_tmp, $target_path)) {
                $hinhanh = $file_name;
            }
        }
    }

    // Cập nhật vào CSDL
    $update = "UPDATE sanpham 
               SET TENSANPHAM='$ten', GIA='$gia', MA_LOAI='$maloai', HINHANH='$hinhanh' 
               WHERE ID_SP='$id'";
    mysqli_query($conn, $update);

    // Quay lại danh sách sản phẩm
    header("Location: admin.php?page=TT_SanPham.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa sản phẩm</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <style>
        .img-preview {
            width: 120px;
            height: auto;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="container mt-4">
    <h2>✏️ Sửa sản phẩm</h2>

    <form method="POST" enctype="multipart/form-data" class="border p-4 rounded bg-light shadow-sm">
        <div class="mb-3">
            <label class="form-label">Mã sản phẩm</label>
            <input type="text" name="id_sp" class="form-control" value="<?= htmlspecialchars($sp['ID_SP']) ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Tên sản phẩm</label>
            <input type="text" name="tensanpham" class="form-control" value="<?= htmlspecialchars($sp['TENSANPHAM']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá</label>
            <input type="number" step="0.01" name="gia" class="form-control" value="<?= htmlspecialchars($sp['GIA']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Loại sản phẩm</label>
            <select name="maloai" class="form-control" required>
                <option value="">-- Chọn loại --</option>
                <option value="B" <?= $sp['MA_LOAI'] == 'B' ? 'selected' : '' ?>>Bánh</option>
                <option value="L" <?= $sp['MA_LOAI'] == 'L' ? 'selected' : '' ?>>Lít</option>
                <option value="H" <?= $sp['MA_LOAI'] == 'H' ? 'selected' : '' ?>>Hộp</option>
                <option value="C" <?= $sp['MA_LOAI'] == 'C' ? 'selected' : '' ?>>Chai</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Hình ảnh hiện tại</label><br>
            <?php if (!empty($sp['HINHANH'])): ?>
                <img src="../assets/image/banh/<?= htmlspecialchars($sp['HINHANH']) ?>" alt="Ảnh sản phẩm" class="img-preview">
            <?php else: ?>
                <span class="text-muted">Không có ảnh</span>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Chọn ảnh mới (nếu muốn thay)</label>
            <input type="file" name="hinhanh" class="form-control" accept=".jpg,.jpeg,.png,.gif">
        </div>

        <button type="submit" class="btn btn-primary">💾 Lưu thay đổi</button>
        <a href="admin.php?page=TT_SanPham.php" class="btn btn-secondary">↩️ Quay lại</a>
    </form>
</body>
</html>