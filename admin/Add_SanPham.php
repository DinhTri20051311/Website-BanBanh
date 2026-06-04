<?php
if (!isset($_SESSION)) session_start();
include __DIR__ . '/../config/connect.php'; // ✅ dùng __DIR__ để tránh lỗi đường dẫn

// Kiểm tra quyền admin
if (!isset($_SESSION['tk']) || $_SESSION['vaitro'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Xử lý đăng xuất
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Tạo mã sản phẩm tự động
$sql = "SELECT ID_SP FROM sanpham ORDER BY ID_SP DESC LIMIT 1";
$result = mysqli_query($conn, $sql);
$lastID = mysqli_fetch_assoc($result);

if ($lastID) {
    $number = (int)substr($lastID['ID_SP'], 2);
    $newID = 'SP' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
} else {
    $newID = 'SP001';
}

// Xử lý khi gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_sp'];
    $ten = $_POST['tensanpham'];
    $gia = $_POST['gia'];
    $maloai = $_POST['maloai'];
    $hinhanh = '';

    if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['hinhanh']['tmp_name'];
        $file_name = basename($_FILES['hinhanh']['name']);
        $target_dir = __DIR__ . '/../assets/image/banh/'; // ✅ đường dẫn tuyệt đối nội bộ
        $target_path = $target_dir . $file_name;

        // Di chuyển file upload
        if (move_uploaded_file($file_tmp, $target_path)) {
            $hinhanh = $file_name;
        }
    }

    $sql = "INSERT INTO sanpham (ID_SP, TENSANPHAM, GIA, MA_LOAI, HINHANH)
            VALUES ('$id', '$ten', '$gia', '$maloai', '$hinhanh')";
    mysqli_query($conn, $sql);
    header("Location: admin.php?page=TT_SanPham");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sản phẩm mới</title>

    <!-- ✅ Đường dẫn CSS/JS tương đối -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="content">
        <div class="main-content">
            <h3 class="mb-4">➕ Thêm sản phẩm mới</h3>
            <form method="POST" enctype="multipart/form-data" class="border p-4 rounded shadow-sm bg-light">
                <div class="mb-3">
                    <label class="form-label">Mã sản phẩm</label>
                    <input type="text" name="id_sp" class="form-control" value="<?= $newID ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tên sản phẩm</label>
                    <input type="text" name="tensanpham" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giá</label>
                    <input type="text" name="gia" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Loại sản phẩm</label>
                    <select name="maloai" class="form-control" required>
                        <option value="">-- Chọn loại --</option>
                        <option value="B">Bánh</option>
                        <option value="L">Lít</option>
                        <option value="H">Hộp</option>
                        <option value="C">Chai</option>
                        <option value="T">Thùng</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Hình ảnh sản phẩm</label>
                    <input type="file" name="hinhanh" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Thêm sản phẩm</button>
                <a href="admin.php?page=TT_SanPham" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
</body>
</html>
