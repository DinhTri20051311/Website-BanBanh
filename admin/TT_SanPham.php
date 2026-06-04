<?php
include '../config/connect.php';

// Khởi động session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền admin
if (!isset($_SESSION['tk']) || $_SESSION['vaitro'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $sql = "DELETE FROM sanpham WHERE ID_SP = '$id'";
    mysqli_query($conn, $sql);
    header("Location: admin.php?page=TT_SanPham&deleted=1");
    exit();
}

// Xử lý gắn / bỏ HOT sản phẩm
if (isset($_GET['toggle_hot'])) {
    $id = mysqli_real_escape_string($conn, $_GET['toggle_hot']);

    $check = mysqli_query($conn, "SELECT HOT_SP FROM sanpham WHERE ID_SP = '$id'");
    if ($check && mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $newStatus = ($row['HOT_SP'] == 1) ? 0 : 1;
        mysqli_query($conn, "UPDATE sanpham SET HOT_SP = '$newStatus' WHERE ID_SP = '$id'");
        header("Location: admin.php?page=TT_SanPham&hot_updated=1");
        exit();
    }
}

// Lấy danh sách sản phẩm
$sql = "SELECT * FROM sanpham";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sản phẩm</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <style>
        .img-thumb {
            width: 80px;
            height: auto;
            object-fit: cover;
            border-radius: 4px;
        }
        .btn-hot {
            background-color: #ff9800;
            color: white;
            border: none;
        }
        .btn-hot:hover {
            background-color: #e68900;
        }
    </style>
</head>
<body>
    <h2>🧁 Danh sách sản phẩm</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">✅ Sản phẩm đã được xóa thành công.</div>
    <?php endif; ?>

    <?php if (isset($_GET['hot_updated'])): ?>
        <div class="alert alert-info">🔥 Cập nhật trạng thái Hot thành công!</div>
    <?php endif; ?>

    <a href="admin.php?page=Add_SanPham.php" class="btn btn-success mb-3">➕ Thêm sản phẩm</a>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Hình ảnh</th>
                <th>ID</th>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Mã loại</th>
                <th>Hot</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($sp = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>
                        <?php if (!empty($sp['HINHANH'])): ?>
                            <img src="../assets/image/banh/<?= $sp['HINHANH'] ?>" class="img-thumb" alt="Ảnh sản phẩm">
                        <?php else: ?>
                            <span class="text-muted">Không có ảnh</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $sp['ID_SP'] ?></td>
                    <td><?= htmlspecialchars($sp['TENSANPHAM']) ?></td>
                    <td><?= number_format($sp['GIA'], 0, ',', '.') ?>₫</td>
                    <td><?= htmlspecialchars($sp['MA_LOAI']) ?></td>
                    <td>
                        <?php if ($sp['HOT_SP'] == 1): ?>
                            <span class="badge bg-danger">🔥 Hot</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Không</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="admin.php?page=Edit_SanPham&id=<?= $sp['ID_SP'] ?>" class="btn btn-warning btn-sm">✏️</a>
                        <a href="admin.php?page=TT_SanPham&delete=<?= $sp['ID_SP'] ?>"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')"
                            class="btn btn-danger btn-sm">🗑️</a>
                        <a href="admin.php?page=TT_SanPham&toggle_hot=<?= $sp['ID_SP'] ?>" 
                            class="btn btn-hot btn-sm">
                            <?= $sp['HOT_SP'] == 1 ? '❌ Bỏ Hot' : '🔥 Đánh dấu Hot' ?>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
