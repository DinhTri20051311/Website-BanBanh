<?php
if (!isset($_SESSION)) session_start();

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

// Lấy trang cần hiển thị
$page = $_GET['page'] ?? 'Menu_admin.php';
if (!str_ends_with($page, '.php')) $page .= '.php';

$allowed_pages = [
    'Menu_admin.php',
    'TT_SanPham.php',
    'Add_SanPham.php',
    'Edit_SanPham.php',
    'orders.php',
    'customers.php',
    'settings.php',
    'QL_khachhang.php',
    'QL_donhang.php',
    'xacnhan_donhang.php',
    'hoadon_list.php',
    'QL_tintuc.php',
    'Add_TinTuc.php',
    'edit_tintuc.php',
    'QL_PhanHoi.php'
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang quản trị - Bánh Ngon</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

<!-- Thanh navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
        <!-- Nút toggle sidebar -->
        <button class="btn btn-outline-light me-2 d-lg-none" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
            ☰
        </button>
        <a class="navbar-brand fw-bold" href="#">Admin Bánh Ngon</a>

        <!-- Thông tin người dùng -->
        <div class="d-flex align-items-center ms-auto">
            <span class="text-white me-3">Xin chào, <strong><?= htmlspecialchars($_SESSION['tk']) ?></strong> 👋</span>
            <form method="POST" onsubmit="return confirmLogout();">
                <button type="submit" name="logout" class="btn btn-danger btn-sm">Đăng xuất</button>
            </form>
        </div>
    </div>
</nav>

<!-- Sidebar (Offcanvas) -->
<div class="offcanvas offcanvas-start bg-light sidebar-lg" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">


    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="sidebarLabel">Quản trị hệ thống</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <nav class="list-group list-group-flush">
            <a href="admin.php?page=Menu_admin.php" class="list-group-item list-group-item-action">Dashboard</a>
            <a href="admin.php?page=TT_SanPham.php" class="list-group-item list-group-item-action">Quản lý sản phẩm</a>
            <a href="admin.php?page=QL_donhang.php" class="list-group-item list-group-item-action">Đơn hàng</a>
            <a href="admin.php?page=QL_khachhang.php" class="list-group-item list-group-item-action">Khách hàng</a>
            <a href="admin.php?page=QL_PhanHoi.php" class="list-group-item list-group-item-action">Quản lý phản hồi</a>
        </nav>
    </div>
</div>

<!-- Nội dung chính -->
<main class="container-fluid" style="margin-top: 70px;">
    <div class="row">
        <!-- Sidebar cố định cho màn hình lớn -->
        <div class="col-lg-2 d-none d-lg-block border-end bg-white vh-100 position-fixed">
            <nav class="nav flex-column mt-4">
                <a class="nav-link" href="admin.php?page=Menu_admin.php">Dashboard</a>
                <a class="nav-link" href="admin.php?page=TT_SanPham.php">Quản lý sản phẩm</a>
                <a class="nav-link" href="admin.php?page=QL_donhang.php">Đơn hàng</a>
                <a class="nav-link" href="admin.php?page=QL_khachhang.php">Khách hàng</a>
                <a class="nav-link" href="admin.php?page=QL_PhanHoi.php">Quản lý phản hồi</a>
            </nav>
        </div>

        <!-- Nội dung -->
   <div class="col-12 col-lg-10 offset-lg-2 p-4">


            <?php
            $file_path = __DIR__ . '/' . basename($page);
            if (in_array($page, $allowed_pages) && file_exists($file_path)) {
                include $file_path;
            } else {
                echo "<div class='alert alert-danger'>Không tìm thấy trang yêu cầu.</div>";
            }
            ?>
        </div>
    </div>
</main>

<script>
function confirmLogout() {
    return confirm("Bạn có chắc chắn muốn đăng xuất không?");
}
</script>

</body>
</html>